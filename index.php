<?php

if (!class_exists('Runtime')) {
	require __DIR__ . DIRECTORY_SEPARATOR . 'bootstrap.php';
}

$controller = new ContaoCommunityAlliance_Composer_Check_Controller();
$controller->run();
