<?php

$jsonPath = __DIR__ . '/calculator.json';

if (file_exists($jsonPath)) {
    return json_decode(file_get_contents($jsonPath), true);
}

return [
    'library' => [],
    'formula_types' => [],
    'compat_rules' => []
];
