<?php
if ($argc < 4) {
    die("Usage: php script.php <command> <filename> <key> [<value>]\n");
}

$command = $argv[1];
$filename = $argv[2];
$key = $argv[3];
$value = $argv[4] ?? null;

if (file_exists($filename)) {
    $jsonContent = file_get_contents($filename);
    $data = json_decode($jsonContent, true);
    if ($data === null) {
        $data = [];
    }
} else {
    $data = [];
}

switch ($command) {
    case 'add':
        if ($value === null) {
            die("Value is required for the 'add' command.\n");
        }

        
        $keys = explode('.', $key);
        $temp =& $data;
        foreach ($keys as $keyPart) {
            if (!isset($temp[$keyPart])) {
                $temp[$keyPart] = [];
            }
            $temp =& $temp[$keyPart];
        }
        $temp = json_decode($value, true) ?? $value;

       
        file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));

        echo "Data added successfully.\n";
        break;

    case 'remove':
        
        $keys = explode('.', $key);
        $temp =& $data;
        $lastKey = array_pop($keys);

        foreach ($keys as $keyPart) {
            if (!isset($temp[$keyPart])) {
                echo "Key not found.\n";
                exit;
            }
            $temp =& $temp[$keyPart];
        }

        if (isset($temp[$lastKey])) {
            unset($temp[$lastKey]);
            
            file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));
            echo "Data removed successfully.\n";
        } else {
            echo "Key not found.\n";
        }
        break;

    default:
        die("Invalid command. Use 'add' or 'remove'.\n");
}