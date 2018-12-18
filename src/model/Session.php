<?php

namespace App\model;

use App\db\DBAdapter;
use App\model\Field;

class Session extends baseModel
{
    const SEEDS_FOR_PLANT = 100;
    const WATER_FOR_GROW_UP = 100;
    const HARVEST = 200;
    const HARVEST_SELLING_COUNT = 25;
    const HARVEST_PRICE = 200;
    const WATER_PRICE = 50;
    const BUY_WATER_AMOUNT = 50;
    const SEEDS_PRICE = 25;
    const BUY_SEEDS_COUNT = 50;

    public function init(User $user)
    {
        $this->user_id = $user->getId();
        $this->money = 0;
        $this->seeds = 100;
        $this->water = 100;
        $this->harvest = 0;

        $this->save();

        for ($x = 0; $x < 5; $x++) {
            for ($y = 0; $y < 5; $y++) {
                $field = $this->container->make(Field::class);
                $field->init($this, $x, $y);
            }
        }
    }

    protected function insert(): bool
    {
        $sqlInsert = "INSERT INTO session (user_id, money, seeds, water, harvest) VALUES (?, ?, ?, ?, ?)";
        $db = $this->getDbAdapter();
        $result = $db->prepareAndExecute($sqlInsert, [$this->user_id, $this->money, $this->seeds, $this->water, $this->harvest]);
        $this->session_id = $db->lastInsertId();
        return $result;
    }

    protected function update(): bool
    {
        $sqlUpdate = "UPDATE session SET money=?, seeds=?, water=?, harvest=? WHERE session_id=?";
        $db = $this->getDbAdapter();
        return $db->prepareAndExecute($sqlUpdate, [$this->money, $this->seeds, $this->water, $this->harvest, $this->session_id]);
    }

    protected function delete(): bool
    {
        $sqlDelete = "DELETE FROM session WHERE session_id=? LIMIT 1";
        $db = $this->getDbAdapter();
        return $db->prepareAndExecute($sqlDelete, [$this->getId()]);
    }

    public function getById($id): bool
    {
        $sql = "SELECT * FROM session WHERE session_id=? LIMIT 1";
        return $this->getByField($sql, [$id]);
    }

    public function getId()
    {
        return $this->session_id;
    }

    public function getByUser(User $user): bool
    {
        $sql = "SELECT * FROM session WHERE user_id=? LIMIT 1";
        return $this->getByField($sql, [$user->getId()]);
    }

    public function getAllFields(): array
    {
        $sql = "SELECT field_id FROM field WHERE session_id=? ORDER BY position_x ASC, position_y ASC";
        $db = $this->getDbAdapter();
        $db->prepareAndExecute($sql, [$this->getId()]);
        $db->setFetchAssoc();
        $idArray = $db->fetchAll();
        $filedArray = [];
        foreach ($idArray as $field_id) {
            $field = $this->container->make(Field::class);
            $field->getById($field_id['field_id']);
            $filedArray[] = $field;
        }
        return $filedArray;
    }

    public function fieldClick($field_id): int
    {
        $field = $this->container->make(Field::class);
        $field->getById($field_id);
        if ($field->session_id != $this->session_id) {
            return -1;
        }
        $result = $field->click($this->water, $this->seeds);

        if ($result == Field::PLANTED) {
            $this->seeds -= Session::SEEDS_FOR_PLANT;
        } elseif ($result == Field::GROW || $result == Field::READY) {
            $this->water -= Session::WATER_FOR_GROW_UP;
        } elseif ($result == Field::EMPTY) {
            $this->seeds += Session::SEEDS_FOR_PLANT / 2;
            $this->harvest += Session::HARVEST;
        }

        $this->update();
        return $field->state;
    }

    public function cheat()
    {
        $this->money += Session::HARVEST_PRICE;
        $this->update();
    }

    public function buyWater(): bool
    {
        if ($this->money >= Session::WATER_PRICE) {
            $this->money -= Session::WATER_PRICE;
            $this->water += Session::BUY_WATER_AMOUNT;
            $this->update();
            return true;
        }
        return false;
    }

    public function buySeeds(): bool
    {
        if ($this->money >= Session::SEEDS_PRICE) {
            $this->money -= Session::SEEDS_PRICE;
            $this->seeds += Session::BUY_SEEDS_COUNT;
            $this->update();
            return true;
        }
        return false;
    }

    public function sellHarvest(): bool
    {
        if ($this->harvest >= Session::HARVEST_SELLING_COUNT) {
            $this->harvest -= Session::HARVEST_SELLING_COUNT;
            $this->money += Session::HARVEST_PRICE;
            $this->update();
            return true;
        }
        return false;
    }
}