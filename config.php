<?php
// Configuración general
define('DB_PATH', __DIR__ . '/database.db');
define('APP_NAME', 'Mood Tracker');
// Valores posibles de ánimo
define('MOODS', [
'very_bad' => ['emoji' => '', 'label' => 'Muy mal'],
'bad' => ['emoji' => '', 'label' => 'Mal'],
'neutral' => ['emoji' => '', 'label' => 'Neutral'],
'good' => ['emoji' => '', 'label' => 'Bien'],
'very_good' => ['emoji' => '', 'label' => 'Muy bien'],
]);