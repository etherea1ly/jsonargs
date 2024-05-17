<?php
if (count($argv) < 5 || $argv[1] !== '-f' || !in_array($argv[3], ['-add', '-remove'])) {
    die("Форма: php ar.php -f <json_file_path> (-add|-remove) <ключ> [значение]\n");
}

$json_file_path = $argv[2];
$action = $argv[3];
$key = $argv[4];
$value = isset($argv[5]) ? $argv[5] : null;

if (!file_exists($json_file_path)) {
    die("Файл не найден.\n");
}

$json_data = file_get_contents($json_file_path);

$data = json_decode($json_data, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    die("Ошибка декодирования JSON: " . json_last_error_msg() . "\n");
}

if ($action === '-add') {
    if (isset($data[$key])) {
        die("Ключ '{$key}' уже существует.\n");
    }

    $decoded_value = json_decode($value, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $data[$key] = $decoded_value;
    } else {
        $data[$key] = $value;
    }
    
    echo "Данные добавлены.\n";
} elseif ($action === '-remove') {
    if (!isset($data[$key])) {
        die("Ключ '{$key}' не существует.\n");
    }
    unset($data[$key]);
    echo "Успешно удалено.\n";
} else {
    die("Неверное действие. Используйте -add или -remove.\n");
}

$new_json_data = json_encode($data, JSON_PRETTY_PRINT);
if ($new_json_data === false) {
    die("Ошибка кодирования JSON: " . json_last_error_msg() . "\n");
}

if (file_put_contents($json_file_path, $new_json_data) === false) {
    die("Ошибка при записи JSON файла.\n");
}