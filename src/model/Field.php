<?php

namespace App\model;

class Field extends baseModel
{
    const NO_ACTION = -1;

    const EMPTY = 0;
    const PLOWED = 1;
    const PLANTED = 2;
    const GROW = 10;
    const READY = 12;

    const SYMBOLS_FOR_DRAW = [
        0 => '_',
        1 => '#',
        2 => '.',
        10 => '^',
        11 => '^^',
        12 => '/\\'
    ];

    public function init(Session $session, int $x, int $y)
    {
        $this->session_id = $session->getId();
        $this->position_x = $x;
        $this->position_y = $y;
        $this->state = Field::EMPTY;

        $this->save();
    }

    protected function insert(): bool
    {
        $sqlInsert = "INSERT INTO field (session_id, position_x, position_y, state) VALUES (?, ?, ?, ?)";
        $db = $this->getDbAdapter();
        $result = $db->prepareAndExecute($sqlInsert, [$this->session_id, $this->position_x, $this->position_y, $this->state]);
        $this->field_id = $db->lastInsertId();
        return $result;
    }

    protected function update(): bool
    {
        $sqlUpdate = "UPDATE field SET state=? WHERE field_id=?";
        $db = $this->getDbAdapter();
        return $db->prepareAndExecute($sqlUpdate, [$this->state, $this->field_id]);
    }

    protected function delete(): bool
    {
        $sqlDelete = "DELETE FROM field WHERE field_id=? LIMIT 1";
        $db = $this->getDbAdapter();
        return $db->prepareAndExecute($sqlDelete, [$this->getId()]);
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM field WHERE field_id=? LIMIT 1";
        return $this->getByField($sql, [$id]);
    }

    public function getId()
    {
        return $this->field_id;
    }

    public function draw(): string
    {
        return self::getDrawSymbol($this->state);
    }

    public static function getDrawSymbol($state): string
    {
        return Field::SYMBOLS_FOR_DRAW[$state];
    }

    public function click($waterAmount, $seedsCount): int
    {
        if ($this->state == Field::EMPTY) {
            $this->state = Field::PLOWED;
            $this->update();
            return Field::PLOWED;
        }

        if ($this->state == Field::PLOWED) {
            if ($seedsCount >= Session::SEEDS_FOR_PLANT) {
                $this->state = Field::PLANTED;
                $this->update();
                return Field::PLANTED;
            } else {
                return Field::NO_ACTION;
            }
        }

        if ($this->state == Field::PLANTED) {
            if ($waterAmount >= Session::WATER_FOR_GROW_UP) {
                $this->state = Field::GROW;
                $this->update();
                return Field::GROW;
            } else {
                return Field::NO_ACTION;
            }
        }

        if ($this->state < Field::READY) {
            if ($waterAmount >= Session::WATER_FOR_GROW_UP) {
                $this->state++;
                $this->update();
                if ($this->state == Field::READY) {
                    return Field::READY;
                }
                return Field::GROW;
            } else {
                return Field::NO_ACTION;
            }
        }

        if ($this->state == Field::READY) {
            $this->state = Field::EMPTY;
            $this->update();
            return Field::EMPTY;
        }

    }
}