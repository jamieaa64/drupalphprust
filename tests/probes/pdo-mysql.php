<?php
// Only connection-local temporary tables; never touches the Drupal schema.
function connectProbe(bool $emulate = true, bool $found = true): PDO {
    return new Pdo\Mysql('mysql:host=db;dbname=db;charset=utf8mb4', 'db', 'db', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => $emulate,
        Pdo\Mysql::ATTR_FOUND_ROWS => $found,
        Pdo\Mysql::ATTR_MULTI_STATEMENTS => false,
    ]);
}
function emit($name, $value) { echo $name, ':', json_encode($value), "\n"; }
foreach ([true, false] as $emulate) {
    $pdo = connectProbe($emulate);
    emit('driver', [$pdo instanceof PDO, $pdo->getAttribute(PDO::ATTR_DRIVER_NAME), $pdo->getAttribute(PDO::ATTR_EMULATE_PREPARES)]);
    $pdo->exec('CREATE TEMPORARY TABLE phpr_pdo_probe (id INT AUTO_INCREMENT PRIMARY KEY, value VARBINARY(255), n INT NULL) ENGINE=InnoDB');
    $value = "quote' slash\\ nul\0\n café 😀 :fake ?";
    $insert = $pdo->prepare('INSERT INTO phpr_pdo_probe (value,n) VALUES (:value,:n)');
    $insert->bindValue(':value', $value, PDO::PARAM_LOB);
    $insert->bindValue(':n', 7, PDO::PARAM_INT);
    emit('insert', [$insert->execute(), $insert->rowCount(), $pdo->lastInsertId()]);
    $s = $pdo->prepare('SELECT id,value,n,NULL AS empty_value FROM phpr_pdo_probe WHERE id = :id');
    $s->execute(['id' => 1]);
    emit('fetch', $s->fetch(PDO::FETCH_ASSOC));
    emit('exhausted', $s->fetch());
    emit('found_rows', $pdo->exec('UPDATE phpr_pdo_probe SET n=7 WHERE id=1'));
    $pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, true);
    emit('stringify', $pdo->query('SELECT id,n FROM phpr_pdo_probe')->fetch(PDO::FETCH_NUM));
    emit('txn_start', [$pdo->inTransaction(), $pdo->beginTransaction(), $pdo->inTransaction()]);
    $pdo->exec('UPDATE phpr_pdo_probe SET n=8');
    $pdo->exec('SAVEPOINT probe');
    $pdo->exec('UPDATE phpr_pdo_probe SET n=9');
    $pdo->exec('ROLLBACK TO SAVEPOINT probe');
    emit('savepoint', $pdo->query('SELECT n FROM phpr_pdo_probe')->fetchColumn());
    $pdo->rollBack();
    emit('rollback', [$pdo->inTransaction(), $pdo->query('SELECT n FROM phpr_pdo_probe')->fetchColumn()]);
    $s = $pdo->prepare("SELECT ':literal ?' AS text, :x AS value /* :ignored */ -- :comment\n");
    $s->execute(['x' => 'safe']);
    emit('lexer', $s->fetch(PDO::FETCH_NUM));
    try { $s->execute(['wrong' => 'oops']); } catch (PDOException $e) { emit('bad_params', $e->getCode()); }
    try { $pdo->query('SELECT absent FROM phpr_pdo_probe'); } catch (PDOException $e) { emit('sql_error', [$e->getCode(), $e->errorInfo[1]]); }
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
    emit('silent', [$pdo->query('SELECT absent FROM phpr_pdo_probe'), $pdo->errorCode()]);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET sql_mode='NO_BACKSLASH_ESCAPES'");
    // PDO's placeholder scanner itself is not NO_BACKSLASH_ESCAPES-aware.
    $quoteValue = "quote' nul\0 café";
    emit('quote', $pdo->query('SELECT ' . $pdo->quote($quoteValue))->fetchColumn() === $quoteValue);
    $pdo->exec("SET sql_mode=''");
}
$pdo = connectProbe(true, false);
$pdo->exec('CREATE TEMPORARY TABLE phpr_pdo_probe (id INT PRIMARY KEY)');
$pdo->exec('INSERT INTO phpr_pdo_probe VALUES (1)');
emit('changed_rows', $pdo->exec('UPDATE phpr_pdo_probe SET id=1'));
$s = $pdo->prepare('SELECT :x AS a, :x AS b');
$s->execute(['x' => "Robert'); DROP TABLE phpr_pdo_probe;--"]);
emit('repeated', $s->fetch(PDO::FETCH_NUM));
emit('table_intact', $pdo->query('SELECT COUNT(*) FROM phpr_pdo_probe')->fetchColumn());
$s = $pdo->prepare('SELECT ? AS value');
$bound = 3;
$s->bindParam(1, $bound, PDO::PARAM_INT);
$bound = 4;
$s->execute();
emit('reference', $s->fetchColumn());
try { $pdo->query('SELECT 1; DROP TABLE phpr_pdo_probe'); } catch (PDOException $e) { emit('multiple_statements', $e->getCode()); }
emit('still_intact', $pdo->query('SELECT COUNT(*) FROM phpr_pdo_probe')->fetchColumn());
