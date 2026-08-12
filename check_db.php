<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=san_alfonso', 'root', '');

echo "=== TABLAS EN LA BD ===\n";
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
foreach ($tables as $t) echo "- $t\n";

echo "\n=== COLUMNAS POR TABLA ===\n";
$target = ['users','especialistas','especialidades','pacientes','citas','historias_clinicas'];
foreach ($target as $table) {
    if (!in_array($table, $tables)) { echo "\n!! TABLA '$table' NO EXISTE !!\n"; continue; }
    echo "\n--- $table ---\n";
    $cols = $pdo->query("DESCRIBE $table")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $c) {
        $key = '';
        if ($c['Key'] === 'PRI') $key = 'PK';
        elseif ($c['Key'] === 'MUL') $key = 'FK/IDX';
        elseif ($c['Key'] === 'UNI') $key = 'UQ';
        printf("  %-30s %-25s %s\n", $c['Field'], $c['Type'], $key);
    }
}
