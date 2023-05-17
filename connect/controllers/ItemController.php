<?php





class ItemController
{


    public function create($data)
    {
        // ... (Валидация полей и создание элемента)

        return ['status' => 201, 'data' => ['message' => 'Item created']];
    }

    public function update($data)
    {
        // ... (Валидация полей и обновление элемента)

        return ['status' => 200, 'data' => ['message' => 'Item updated']];
    }

    public function delete($data)
    {
        $id = isset($data['id']) ? $data['id'] : '';

        if (empty($id)) {
            return ['status' => 400, 'data' => ['error' => 'Missing item ID']];
        }

        // Удаление элемента
        if ($this->itemModel->deleteItem($id)) {
            return ['status' => 200, 'data' => ['message' => 'Item deleted']];
        } else {
            return ['status' => 500, 'data' => ['error' => 'Failed to delete item']];
        }
    }

    public function get($data)
    {
        $id = isset($data['id']) ? $data['id'] : '';

        if (empty($id)) {
            return ['status' => 400, 'data' => ['error' => 'Missing item ID']];
        }

        // Получение элемента
        $item = $this->itemModel->getItem($id);
        if ($item) {
            return ['status' => 200, 'data' => $item];
        } else {
            return ['status' => 404, 'data' => ['error' => 'Item not found']];
        }
    }

    public function getAll()
    {
        // Получение всех элементов
        $items = $this->itemModel->getAllItems();
        return ['status' => 200, 'data' => $items];
    }



    /**
     * Метод авторизации пользователя
     * @param string $username Логин пользователя
     * @param string $password Пароль пользователя
     * @return array|null Массив с данными пользователя или null, если авторизация не удалась
     */
    public function login($username, $password) {
        // Проверяем, что пользователь существует и пароль верный
        $user = User::findByUsernameAndPassword($username, $password);
        if ($user) {
            // Генерируем токен и сохраняем его для пользователя
            $token = $this->generateToken($user['id']);
            User::saveToken($user['id'], $token);

            // Возвращаем данные пользователя и токен
            return array(
                'user' => $user,
                'token' => $token
            );
        }
        return null;
    }

    /**
     * Метод для генерации токена для пользователя
     * @param int $userId Идентификатор пользователя
     * @return string Сгенерированный токен
     */
    private function generateToken($userId) {
        $secretKey = 'mysecretkey'; // Секретный ключ для генерации токена
        $payload = array(
            'userId' => $userId,
            'exp' => time() + 3600 // Время жизни токена - 1 час
        );
        return JWT::encode($payload, $secretKey);
    }

    /**
     * Метод для проверки токена
     * @param string $token Токен для проверки
     * @return bool Возвращает true, если токен действителен, иначе - false
     */
    private function validateToken($token) {
        $secretKey = 'mysecretkey'; // Секретный ключ для генерации токена
        try {
            $payload = JWT::decode($token, $secretKey, array('HS256'));
            // Проверяем, что время жизни токена еще не истекло
            return $payload->exp > time();
        } catch (Exception $e) {
            // Ошибка декодирования токена
            return false;
        }
    }

    /**
     * Метод для добавления записи об изменении сущности Item в БД
     * @param int $itemId Идентификатор изменяемой записи
     * @param array $oldValues Старые значения полей записи
     * @param array $newValues Новые значения полей записи
     * @param string $action Действие, которое привело к изменению записи (create/update/delete)
     */
    private function logItemChange($itemId, $oldValues, $newValues, $action) {
        $userId = $_SESSION['user']['id']; // Идентификатор пользователя, выполнившего изменение
        $time = time(); // Время изменения записи

        // Сохраняем информацию об изменении в БД
        ItemHistory::create($itemId, $oldValues, $newValues, $action, $userId, $time);
    }
}




