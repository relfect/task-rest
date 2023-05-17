<?php

function validateItemFields($name, $phone, $key, $existingItem = null)
{
    if (empty($name) || empty($phone) || empty($key)) {
        return false;
    }

    // Валидация поля "name"
    if (strlen($name) > 255) {
        return false;
    }

    // Валидация поля "phone"
    if (!preg_match('/^\+?\d{1,15}$/', $phone)) {
        return false;
    }

    // Валидация поля "key"
    if (strlen($key) !== 25) {
        return false;
    }

    // Дополнительная валидация уникальности ключа "key" (при обновлении элемента)
    if ($existingItem && $existingItem['key'] !== $key) {
        $existingItemByKey = $this->itemModel->getItemByKey($key);
        if ($existingItemByKey) {
            return false;
        }
    }

    return true;
}

