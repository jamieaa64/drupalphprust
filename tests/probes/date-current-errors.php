<?php
foreach ([DateTime::class,DateTimeImmutable::class] as $class) {
    foreach (['','now'] as $input) {
        DateTime::createFromFormat('Y-m-d','bad');
        echo DateTime::getLastErrors()===false ? 'clean' : 'error',"\n";
        $date=new $class($input,new DateTimeZone('UTC'));
        var_dump(DateTime::getLastErrors());
        DateTimeImmutable::createFromFormat('Y-m-d','2026-02-31');
        echo DateTime::getLastErrors()===false ? 'clean' : 'warning',"\n";
        $date=new $class($input);var_dump(DateTimeImmutable::getLastErrors());
        DateTime::createFromFormat('Y-m-d','bad');
        $date=$date->setTimestamp(1794528000);
        echo DateTime::getLastErrors()===false ? 'clean' : 'retained',"\n";
    }
}
