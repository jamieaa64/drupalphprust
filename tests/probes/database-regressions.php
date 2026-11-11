<?php
// Existing SQLite and mysqli APIs must survive the new PDO driver.
function emit($name, $value) { echo $name, ':', json_encode($value), "\n"; }
$pdo = new Pdo\Sqlite('sqlite::memory:');
$pdo->exec('CREATE TABLE t (id INTEGER PRIMARY KEY, value TEXT)');
$s = $pdo->prepare('INSERT INTO t VALUES (?, ?)');
$s->execute([1, 'before']);
$pdo->beginTransaction();
$pdo->exec("UPDATE t SET value='during'");
$pdo->rollBack();
emit('sqlite', [$pdo->getAttribute(PDO::ATTR_DRIVER_NAME), $pdo->query('SELECT * FROM t')->fetch(PDO::FETCH_ASSOC), $pdo->inTransaction()]);
$pdo->createFunction('probe_double', fn($v) => $v * 2, 1);
emit('sqlite_function', $pdo->query('SELECT probe_double(21)')->fetchColumn());
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$db = new mysqli('db', 'db', 'db', 'db');
$db->query('CREATE TEMPORARY TABLE phpr_mysqli_probe (id INT PRIMARY KEY, value INT)');
$db->query('INSERT INTO phpr_mysqli_probe VALUES (1, 7)');
$db->query('UPDATE phpr_mysqli_probe SET value=7');
emit('mysqli_changed_rows', $db->affected_rows);
$s = $db->prepare('SELECT value FROM phpr_mysqli_probe WHERE id=?');
$id = 1;
$s->bind_param('i', $id);
$s->execute();
emit('mysqli_prepared', $s->get_result()->fetch_assoc());
emit('mysqli_error_mode', (new mysqli_driver())->report_mode);
