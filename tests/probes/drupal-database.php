<?php
// Exercise Drupal's real database classes without needing a full service container.
// Reads the disposable site's config; all writes use a connection-local temp table.
$loader = require getcwd() . '/vendor/autoload.php';
$loader->addPsr4('Drupal\\mysql\\', getcwd() . '/web/core/modules/mysql/src');
$options = [
    'host' => 'db', 'port' => 3306, 'database' => 'db', 'username' => 'db', 'password' => 'db',
    'driver' => 'mysql', 'namespace' => 'Drupal\\mysql\\Driver\\Database\\mysql', 'prefix' => '',
    'isolation_level' => 'READ COMMITTED',
];
$pdo = \Drupal\mysql\Driver\Database\mysql\Connection::open($options);
$db = new \Drupal\mysql\Driver\Database\mysql\Connection($pdo, $options);
function emit($name, $value) { echo $name, ':', json_encode($value), "\n"; }
emit('drupal_driver', [$db->driver(), get_class($pdo), $pdo->getAttribute(PDO::ATTR_STRINGIFY_FETCHES)]);
emit('select', $db->query('SELECT :n AS n', [':n' => 42])->fetchField());
emit('config', $db->query('SELECT name FROM {config} WHERE name=:name', [':name' => 'core.extension'])->fetchField());
$db->query('CREATE TEMPORARY TABLE phpr_drupal_probe (id INT PRIMARY KEY, value VARCHAR(50))');
$db->query('INSERT INTO phpr_drupal_probe VALUES (:id, :value)', [':id' => 1, ':value' => "Drupal's database"]);
emit('fetch_assoc', $db->query('SELECT * FROM phpr_drupal_probe')->fetchAssoc());
emit('fetch_all', $db->query('SELECT * FROM phpr_drupal_probe')->fetchAllKeyed());
$pdo->beginTransaction();
$db->query('UPDATE phpr_drupal_probe SET value=:value WHERE id=:id', [':value' => 'changed', ':id' => 1]);
$pdo->rollBack();
emit('rollback', $db->query('SELECT value FROM phpr_drupal_probe')->fetchField());
