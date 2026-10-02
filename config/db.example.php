<?php
// Modèle de config/db.php : copier ce fichier en config/db.php et mettre le vrai mot de passe
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$enLocal = str_starts_with($host, 'localhost') || str_starts_with($host, '127.0.0.1');

if ($enLocal) {
    return [
        'host' => 'localhost', 'port' => 3306,
        'database' => 'music', 'username' => 'root', 'password' => '',
    ];
}

return [
    'host' => 'mysql-houda.alwaysdata.net', 'port' => 3306,
    'database' => 'houda_music', 'username' => 'houda_music',
    'password' => 'METS_TON_MOT_DE_PASSE_ICI',
];