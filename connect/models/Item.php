<?php

class Item
{
    private $id;
    private $name;
    private $phone;
    private $key;
    private $created_at;
    private $updated_at;

    public function __construct($id, $name, $phone, $key, $created_at, $updated_at)
    {
        $this->id = $id;
        $this->name = $name;
        $this->phone = $phone;
        $this->key = $key;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    public static function create($name, $phone, $key)
    {
        // Валидация полей
        if (!validateItemFields($name, $phone, $key)) {
            return false;
        }

        // Создание элемента в базе данных
        $db = new Database();
        $db->query("INSERT INTO items (name, phone, key) VALUES (:name, :phone, :key)");
        $db->bind(':name', $name);
        $db->bind(':phone', $phone);
        $db->bind(':key', $key);
        $db->execute();

        $id = $db->lastInsertId();

        return new Item($id, $name, $phone, $key, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'));
    }

    public static function update($id, $name, $phone, $key)
    {
        // Валидация полей
        if (!validateItemFields($name, $phone, $key)) {
            return false;
        }

        // Проверка существования элемента
        $existingItem = self::find($id);
        if (!$existingItem) {
            return false;
        }

        // Обновление элемента в базе данных
        $db = new Database();
        $db->query("UPDATE items SET name = :name, phone = :phone, key = :key, updated_at = :updated_at WHERE id = :id");
        $db->bind(':id', $id);
        $db->bind(':name', $name);
        $db->bind(':phone', $phone);
        $db->bind(':key', $key);
        $db->bind(':updated_at', date('Y-m-d H:i:s'));
        $db->execute();

        return new Item($id, $name, $phone, $key, $existingItem->getCreatedAt(), date('Y-m-d H:i:s'));
    }

    public static function delete($id)
    {
        // Проверка существования элемента
        $existingItem = self::find($id);
        if (!$existingItem) {
            return false;
        }

        // Удаление элемента из базы данных
        $db = new Database();
        $db->query("DELETE FROM items WHERE id = :id");
        $db->bind(':id', $id);
        $db->execute();

        return true;
    }

    public static function find($id)
    {
        // Поиск элемента в базе данных по идентификатору
        $db = new Database();
        $db->query("SELECT * FROM items WHERE id = :id");
        $db->bind(':id', $id);
        $item = $db->single();

        if ($item) {
            return new Item($item['id'], $item['name'], $item['phone'], $item['key'], $item['created_at'], $item['updated_at']);
        } else {
            return false;
        }
    }

    public static function getAll()
    {
        // Получение всех элементов из базы данных
        $db = new Database();
        $db->query("SELECT * FROM items");
        $items = $db->resultSet();

        $result = array();
        foreach ($items as $item) {
            $result[] = new Item($item['id'], $item['name'], $item['phone'], $item['key'], $item['created_at'], $item['updated_at']);
        }

        return $result;
    }

    /**
     * Метод для сохранения сущности Item
     */
    public function save() {
        // Проверяем токен
        if (!$this->validateToken($_SERVER['HTTP_TOKEN'])) {
            // Возвращаем ошибку доступа
            $this->sendResponse(403, 'Access denied');
        }

        // Добавляем проверку полей сущности
        $validationResult = $this->validateItemFields();
        if (!$validationResult['success']) {
            // Возвращаем ошибку валидации
            $this->sendResponse(400, $validationResult['message']);
        }

        // Проверяем, существует ли запись Item с таким id
        if ($this->id) {
            $oldValues = $this->getOldItemValues($this->id); // Получаем старые значения полей записи
            $this->updateItem(); // Обновляем существующую запись
            $this->logItemChange($this->id, $oldValues, $this->getItemValues(), 'update'); // Логируем изменение записи
        } else {
            $this->createItem(); // Создаем новую запись
            $this->logItemChange($this->id, [], $this->getItemValues(), 'create'); // Логируем создание записи
        }
        $this->sendResponse(200, 'Item saved');
    }
}

