<?php
// Устанавливаем кодировку
header('Content-Type: text/html; charset=UTF-8');

// Подключение к БД
$host = 'localhost';
$dbname = 'u68917';
$user = 'u68917';
$pass = '1300093';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к БД: " . $e->getMessage());
}

// Проверяем метод отправки
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Валидация данных
$errors = [];
$fieldErrors = [];

// ФИО
$full_name = trim($_POST['full_name'] ?? '');
if (empty($full_name)) {
    $errors[] = 'ФИО обязательно для заполнения';
    $fieldErrors['full_name'] = 'Поле обязательно для заполнения';
} elseif (!preg_match('/^[а-яА-ЯёЁa-zA-Z\s\-]+$/u', $full_name)) {
    $errors[] = 'ФИО должно содержать только буквы и пробелы';
    $fieldErrors['full_name'] = 'Допустимы только буквы и пробелы';
} elseif (strlen($full_name) > 150) {
    $errors[] = 'ФИО должно быть не длиннее 150 символов';
    $fieldErrors['full_name'] = 'Максимальная длина - 150 символов';
}

// Телефон
$phone = trim($_POST['phone'] ?? '');
if (empty($phone)) {
    $errors[] = 'Телефон обязателен для заполнения';
    $fieldErrors['phone'] = 'Поле обязательно для заполнения';
} elseif (!preg_match('/^\+?[0-9\s\-\(\)]{10,20}$/', $phone)) {
    $errors[] = 'Неверный формат телефона. Допустимы цифры, пробелы, скобки и дефисы';
    $fieldErrors['phone'] = 'Допустимы цифры, пробелы, скобки и дефисы';
}

// Email
$email = trim($_POST['email'] ?? '');
if (empty($email)) {
    $errors[] = 'Email обязателен для заполнения';
    $fieldErrors['email'] = 'Поле обязательно для заполнения';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Некорректный email. Пример правильного формата: example@mail.com';
    $fieldErrors['email'] = 'Пример правильного формата: example@mail.com';
}

// Дата рождения
$birth_date = $_POST['birth_date'] ?? '';
if (empty($birth_date)) {
    $errors[] = 'Укажите дату рождения';
    $fieldErrors['birth_date'] = 'Поле обязательно для заполнения';
} elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $birth_date)) {
    $errors[] = 'Неверный формат даты. Используйте формат ГГГГ-ММ-ДД';
    $fieldErrors['birth_date'] = 'Используйте формат ГГГГ-ММ-ДД';
}

// Пол
$gender = $_POST['gender'] ?? '';
if (!in_array($gender, ['male', 'female'])) {
    $errors[] = 'Укажите пол';
    $fieldErrors['gender'] = 'Поле обязательно для заполнения';
}

// Языки программирования
$languages = $_POST['languages'] ?? [];
if (empty($languages)) {
    $errors[] = 'Выберите хотя бы один язык программирования';
    $fieldErrors['languages'] = 'Выберите хотя бы один вариант';
}

// Биография
$bio = trim($_POST['bio'] ?? '');
if (!empty($bio) && !preg_match('/^[а-яА-ЯёЁa-zA-Z0-9\s\.,!?\-]+$/u', $bio)) {
    $errors[] = 'Биография содержит недопустимые символы';
    $fieldErrors['bio'] = 'Допустимы буквы, цифры и основные знаки препинания';
}

// Чекбокс
if (!isset($_POST['contract']) || $_POST['contract'] !== 'on') {
    $errors[] = 'Необходимо принять условия соглашения';
    $fieldErrors['contract'] = 'Необходимо принять условия';
}

// Если есть ошибки - сохраняем их в cookies и перенаправляем обратно
if (!empty($errors)) {
    // Сохраняем введенные пользователем значения
    foreach ($_POST as $key => $value) {
        if (is_array($value)) {
            setcookie($key, json_encode($value), 0, '/');
        } else {
            setcookie($key, $value, 0, '/');
        }
    }
    
    // Сохраняем ошибки
    setcookie('form_errors', json_encode($errors), 0, '/');
    setcookie('field_errors', json_encode($fieldErrors), 0, '/');
    
    header('Location: index.php');
    exit;
}

// Если ошибок нет - сохраняем данные в БД и в cookies на год
try {
    $pdo->beginTransaction();
    
    // 1. Сохраняем основную информацию
    $stmt = $pdo->prepare("INSERT INTO applications 
        (full_name, phone, email, birth_date, gender, bio, contract_agreed, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    
    $stmt->execute([
        $full_name,
        $phone,
        $email,
        $birth_date,
        $gender,
        $bio,
        isset($_POST['contract']) ? 1 : 0
    ]);

    $app_id = $pdo->lastInsertId();
    
    // 2. Сохраняем языки программирования
    $stmt = $pdo->prepare("INSERT INTO application_languages 
        (application_id, language_id) VALUES (?, ?)");
    
    foreach ($languages as $lang_id) {
        $stmt->execute([$app_id, $lang_id]);
    }
    
    $pdo->commit();
    
    // Сохраняем данные в cookies на 1 год
    $cookieExpire = time() + 60 * 60 * 24 * 365; // 1 год
    setcookie('full_name', $full_name, $cookieExpire, '/');
    setcookie('phone', $phone, $cookieExpire, '/');
    setcookie('email', $email, $cookieExpire, '/');
    setcookie('birth_date', $birth_date, $cookieExpire, '/');
    setcookie('gender', $gender, $cookieExpire, '/');
    setcookie('languages', json_encode($languages), $cookieExpire, '/');
    setcookie('bio', $bio, $cookieExpire, '/');
    setcookie('contract', 'on', $cookieExpire, '/');
    
    // Удаляем возможные старые ошибки
    setcookie('form_errors', '', time() - 3600, '/');
    setcookie('field_errors', '', time() - 3600, '/');
    
    // Сообщение об успехе
    echo '<!DOCTYPE html>
    <html lang="ru">
    <head>
        <title>Успех</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <header>
            <h1>Данные сохранены!</h1>
        </header>
        <div class="main">
            <p>Спасибо! Ваша заявка №'.$app_id.' успешно сохранена.</p>
            <p><a href="index.php">Вернуться к форме</a></p>
        </div>
    </body>
    </html>';

} catch (Exception $e) {
    $pdo->rollBack();
    
    // Сохраняем ошибку в cookies
    setcookie('form_errors', json_encode(['Ошибка сохранения: ' . $e->getMessage()]), 0, '/');
    header('Location: index.php');
    exit;
}
?>