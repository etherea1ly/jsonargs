<?php

define('USAGE_MESSAGE', "Использование: php script.php add/remove имя файла Ключ [Значение]\n");
define('ADD_COMMAND', 'add');
define('REMOVE_COMMAND', 'remove');
define('ERROR_INVALID_COMMAND', "Неверная команда. Используйте 'add' или 'remove'.\n");
define('ERROR_KEY_NOT_FOUND', "Ключ не найден.\n");
define('ERROR_VALUE_REQUIRED', "Значение необходимо для команды 'add'.\n");
define('SUCCESS_ADD', "Данные успешно добавлены.\n");
define('SUCCESS_REMOVE', "Данные успешно удалены.\n");

define('MIN_ARGC', 4);
define('COMMAND_INDEX', 1);
define('FILENAME_INDEX', 2);
define('KEY_INDEX', 3);
define('VALUE_INDEX', 4);

if ($argc < MIN_ARGC) {
    die(USAGE_MESSAGE);
}

$command = $argv[COMMAND_INDEX];
$filename = $argv[FILENAME_INDEX];
$key = $argv[KEY_INDEX];
$value = $argv[VALUE_INDEX] ?? null;

$data = loadData($filename);

switch ($command) {
    case ADD_COMMAND:
        if ($value === null) {
            die(ERROR_VALUE_REQUIRED);
        }

        addData($data, $key, $value);
        saveData($filename, $data);
        echo SUCCESS_ADD;
        break;

    case REMOVE_COMMAND:
        if (removeData($data, $key)) {
            saveData($filename, $data);
            echo SUCCESS_REMOVE;
        } else {
            echo ERROR_KEY_NOT_FOUND;
        }
        break;

    default:
        die(ERROR_INVALID_COMMAND);
}

function loadData($filename) {
    if (file_exists($filename)) {
        $jsonContent = file_get_contents($filename);
        $data = json_decode($jsonContent, true);
        return $data !== null ? $data : [];
    }
    return [];
}

function saveData($filename, $data) {
    file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));
}

function addData(&$data, $key, $value) {
    $keys = explode('.', $key);
    $temp =& $data;
    foreach ($keys as $keyPart) {
        if (!isset($temp[$keyPart])) {
            $temp[$keyPart] = [];
        }
        $temp =& $temp[$keyPart];
    }
    $temp = json_decode($value, true) ?? $value;
}

function removeData(&$data, $key) {
    $keys = explode('.', $key);
    $temp =& $data;
    $lastKey = array_pop($keys);

    foreach ($keys as $keyPart) {
        if (!isset($temp[$keyPart])) {
            return false;
        }
        $temp =& $temp[$keyPart];
    }

    if (isset($temp[$lastKey])) {
        unset($temp[$lastKey]);
        return true;
    }
    return false;
}
