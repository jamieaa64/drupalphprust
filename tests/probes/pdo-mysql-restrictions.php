<?php
// Intentional experimental limitations: these MUST fail closed. Not Zend parity.
$pdo = new Pdo\Mysql('mysql:host=db;dbname=db;charset=utf8mb4', 'db', 'db');
$pdo->exec('CREATE TEMPORARY TABLE phpr_charset_probe (id INT PRIMARY KEY)');
$pdo->exec('INSERT INTO phpr_charset_probe VALUES (1)');
$pdo->exec('SET NAMES gbk');
try {
    $s = $pdo->prepare('SELECT id FROM phpr_charset_probe WHERE id=:id');
    $s->execute(['id' => "\xbf' OR 1=1 -- "]);
    throw new RuntimeException('Unsafe session charset was accepted');
} catch (PDOException $e) {
    if ($e->getCode() !== 'IM001') { throw $e; }
}
$pdo->exec('SET NAMES utf8mb4');
if ((int)$pdo->query('SELECT COUNT(*) FROM phpr_charset_probe')->fetchColumn() !== 1) { throw new RuntimeException('Probe table changed'); }
try {
    $pdo->query('SELECT 1; /*! DROP TABLE phpr_charset_probe */');
    throw new RuntimeException('Executable SQL comment was accepted');
} catch (PDOException $e) {
    if ($e->getCode() !== 'IM001') { throw $e; }
}
$pdo->exec("SET sql_mode='ANSI'");
try {
    $pdo->query('SELECT 1 AS "x\\"; DROP TABLE phpr_charset_probe; -- "');
    throw new RuntimeException('Multiple statements with ANSI_QUOTES were accepted');
} catch (PDOException $e) {}
if ((int)$pdo->query('SELECT COUNT(*) FROM phpr_charset_probe')->fetchColumn() !== 1) { throw new RuntimeException('Probe table changed'); }
echo "unsafe_charset_comments_and_ansi_quotes: PASS\n";
