<?php

require_once 'database.php';

class Item
{


    public function update($name, $phone, $key)
    {
        // Обновление элемента
        $db = new Database();
        $db->query("UPDATE items SET name = :name, phone = :phone, key = :key, updated_at = NOW() WHERE id = :id");
        $db->bind(':id', $this->id);
        $db->bind(':name', $name);
        $db->bind(':phone', $phone);
        $db->bind(':key', $key);
        $db->execute();

        // Создание записи истории изменений
        $history = new ItemHistory($this->id, $this->name, $this->phone, $this->key);
        $history->save();
    }


}

class ItemHistory
{
    private $itemId;
    private $name;
    private $phone;
    private $key;
    private $createdAt;

    public function __construct($itemId, $name, $phone, $key)
    {
        $this->itemId = $itemId;
        $this->name = $name;
        $this->phone = $phone;
        $this->key = $key;
        $this->createdAt = date('Y-m-d H:i:s');
    }

    public function save()
    {
        $db = new Database();
        $db->query("INSERT INTO item_history (item_id, name, phone, key, created_at) VALUES (:itemId, :name, :phone, :key, :createdAt)");
        $db->bind(':itemId', $this->itemId);
        $db->bind(':name', $this->name);
        $db->bind(':phone', $this->phone);
        $db->bind(':key', $this->key);
        $db->bind(':createdAt', $this->createdAt);
        $db->execute();
    }

}

