<?php
/**
 * User: MaslovAN
 * Date: 22.12.2016
 * Time: 14:48
 */


ini_set('memory_limit', '-1');
ini_set('max_execution_time', 0);

require_once(__DIR__ . '/bootstrap.php');

$app = new app\App();
$app->run();
