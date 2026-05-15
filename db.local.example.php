<?php
// Keep the real db.local.php outside public_html on Hostinger so Git deploys
// cannot overwrite it.
return [
    'host' => 'localhost',
    'name' => 'your_database_name',
    'user' => 'your_database_user',
    'pass' => 'your_database_password',
    'charset' => 'utf8mb4',
];
