<?php

/**
 * System Check for the Contao Composer Client
 *
 * PHP Version 5.3
 *
 * @copyright  bit3 UG 2013
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @package    composer-check
 * @license    LGPL-3.0+
 * @link       http://c-c-a.org
 */

if (!class_exists('Runtime')) {
	require __DIR__ . DIRECTORY_SEPARATOR . 'bootstrap.php';
}

$controller = new ContaoCommunityAlliance_Composer_Check_Controller();
$controller->run();
