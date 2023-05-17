<?php

require_once 'models/Item.php';

use PHPUnit\Framework\TestCase;

class ItemTest extends TestCase
{
    public function testCreateItem()
    {
        // Предположим, что у вас есть методы setUp() и tearDown(),
        // которые создают и удаляют временную базу данных для тестов

        // Создание тестовых данных
        $name = 'Test Item';
        $phone = '1234567890';
        $key = 'testkey';

        // Создание элемента
        $item = Item::create($name, $phone, $key);

        // Проверка, что элемент был создан успешно
        $this->assertInstanceOf(Item::class, $item);
        $this->assertEquals($name, $item->getName());
        $this->assertEquals($phone, $item->getPhone());
        $this->assertEquals($key, $item->getKey());
    }

}
