<?php

namespace App\model;

class User extends baseModel
{
    public function getId()
    {
        return $this->user_id;
    }

    public function verifyPassword(string $password)
    {
        return password_verify($password, $this->password);
    }

    public function setAndHashPassword(string $password)
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    protected function update(): bool
    {
        $sqlUpdate = "UPDATE user SET password=?, access_token=? WHERE user_id=?";
        $db = $this->getDbAdapter();
        return $db->prepareAndExecute($sqlUpdate, [$this->password, $this->access_token, $this->user_id]);
    }

    protected function insert(): bool
    {
        $user = $this->container->make(User::class);
        if ($user->getByName($this->name)) {
            return false;
        }
        $sqlInsert = "INSERT INTO user (name, password, access_token) VALUES (?, ?, ?)";
        $db = $this->getDbAdapter();
        $result = $db->prepareAndExecute($sqlInsert, [$this->name, $this->password, $this->access_token]);
        $this->user_id = $db->lastInsertId();
        return $result;
    }

    protected function delete(): bool
    {
        $sqlDelete = "DELETE FROM user WHERE user_id=? LIMIT 1";
        $db = $this->getDbAdapter();
        return $db->prepareAndExecute($sqlDelete, [$this->getId()]);
    }

    public function getByName(string $name)
    {
        $sql = "SELECT * FROM user WHERE name=? LIMIT 1";
        return $this->getByField($sql, [$name]);
    }

    public function findByToken(string $token)
    {
        $sql = "SELECT * FROM user WHERE access_token=? LIMIT 1";
        return $this->getByField($sql, [$token]);
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM user WHERE user_id=? LIMIT 1";
        return $this->getByField($sql, [$id]);
    }

}