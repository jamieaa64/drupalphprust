<?php
function emit($name, $value) { echo $name, ':', json_encode($value), "\n"; }
set_error_handler(function ($code, $message) { emit('warning', [$code, $message]); return true; });
foreach ([true, false] as $emulate) {
    $pdo = PDO::connect('mysql:host=db;dbname=db;charset=utf8mb4', 'db', 'db', [PDO::ATTR_EMULATE_PREPARES => $emulate, Pdo\Mysql::ATTR_MULTI_STATEMENTS => false]);
    emit('factory', [get_class($pdo), extension_loaded('pdo_mysql')]);
    $pdo->exec('CREATE TEMPORARY TABLE phpr_edge (id INT PRIMARY KEY, n DECIMAL(20,2), big BIGINT UNSIGNED, f DOUBLE, v VARBINARY(50))');
    $pdo->exec("INSERT INTO phpr_edge VALUES (1,123456789012345678.12,18446744073709551615,1.5,NULL)");
    emit('types', $pdo->query('SELECT * FROM phpr_edge')->fetch(PDO::FETCH_ASSOC));
    $s = $pdo->prepare('UPDATE phpr_edge SET v=:v WHERE id=:id');
    $s->bindValue('v', "\0\xff'\\", PDO::PARAM_LOB);
    $s->bindValue('id', 1, PDO::PARAM_INT);
    $s->execute();
    emit('binary', bin2hex($pdo->query('SELECT v FROM phpr_edge')->fetchColumn()));
    $s = $pdo->prepare('SELECT ? AS a, ? AS b, ? AS c');
    $s->bindValue(1, null, PDO::PARAM_NULL);
    $s->bindValue(2, true, PDO::PARAM_BOOL);
    $s->bindValue(3, 42, PDO::PARAM_INT);
    $s->execute();
    emit('bound_types', $s->fetch(PDO::FETCH_NUM));
    $pdo->exec('BEGIN');
    emit('manual_begin', $pdo->inTransaction());
    try { $pdo->beginTransaction(); } catch (PDOException $e) { emit('nested', $e->getMessage()); }
    $pdo->exec('ROLLBACK');
    emit('manual_rollback', $pdo->inTransaction());
    try { $pdo->commit(); } catch (PDOException $e) { emit('no_transaction', $e->getMessage()); }
    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, false);
    emit('autocommit_off', $pdo->getAttribute(PDO::ATTR_AUTOCOMMIT));
    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, true);
    emit('autocommit_on', $pdo->getAttribute(PDO::ATTR_AUTOCOMMIT));
    $s = $pdo->prepare('SELECT ? AS n LIMIT ?');
    $s->bindValue(1, 'ok'); $s->bindValue(2, 1, PDO::PARAM_INT);
    $s->execute(); emit('limit', $s->fetchColumn());
    $s->closeCursor(); emit('closed', $s->fetch());
    $s->execute(); emit('reexecute', $s->fetchColumn());
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
    $s = $pdo->prepare('SELECT ?');
    emit('empty_params', [$s->execute([]), $s->errorCode()]);
    emit('too_many', [$s->execute([1, 2]), $s->errorCode()]);
}
