<?php

copy(__DIR__.'/../composer.json', __DIR__.'/../composer.json.bak');

$composerJson = json_decode(file_get_contents(__DIR__.'/../composer.json'), true);

$composerJson['config']['platform']['ext-amqp'] = '1.7';
$composerJson['config']['platform']['ext-rdkafka'] = '3.3';
$composerJson['config']['platform']['ext-gearman'] = '1.1';

file_put_contents(__DIR__.'/../composer.json', json_encode($composerJson, JSON_PRETTY_PRINT));