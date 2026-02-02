<?php
header('Content-Type: application/json');

/**
 * Функція для відправки в Telegram через file_get_contents (найбільш сумісний метод)
 */
function sendToTelegram($message) {
    $token  = "8580129535:AAGT2aed8qWrzw37PHIuSRlFtr_QcVjRTTQ"; // Твій токен
    $chatId = "-5159975255"; // Твій ID чату
    $url = "https://api.telegram.org/bot{$token}/sendMessage";

    $postData = [
        'chat_id'    => $chatId,
        'parse_mode' => 'html',
        'text'       => $message,
    ];

    $options = [
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => http_build_query($postData),
            'ignore_errors' => true
        ]
    ];

    $context  = stream_context_create($options);
    return @file_get_contents($url, false, $context);
}

// Отримуємо дані через $_POST (оскільки ми використовуємо FormData в JS)
$fName  = $_POST['firstName'] ?? '';
$lName  = $_POST['lastName'] ?? '';
$phone  = $_POST['phone'] ?? '';
$course = $_POST['course'] ?? '';
$format = $_POST['format'] ?? '';

if (empty($fName) || empty($phone)) {
    echo json_encode(['success' => false, 'error' => 'Заповніть ім’я та телефон']);
    exit;
}

$text = "<b>#новазаявка</b>\n"
      . "<b>Студент:</b> $fName $lName\n"
      . "<b>Телефон:</b> $phone\n"
      . "<b>Курс:</b> $course\n"
      . "<b>Формат:</b> $format";

$response = sendToTelegram($text);

if ($response) {
    $result = json_decode($response, true);
    if (isset($result['ok']) && $result['ok']) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $result['description'] ?? 'API Error']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Хостинг блокує вихідні запити']);
}