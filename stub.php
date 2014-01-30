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

Phar::mapPhar('composer-check.phar');

require 'phar://composer-check.phar/bootstrap.php';

// run console mode
if (PHP_SAPI == 'cli') {
	require 'phar://composer-check.phar/console.php';
}

// run web mode
else {
	Phar::webPhar();
}

__HALT_COMPILER();
