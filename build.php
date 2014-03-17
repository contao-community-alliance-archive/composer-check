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

	require __DIR__ . DIRECTORY_SEPARATOR . 'bootstrap.php';

$build = new ContaoCommunityAlliance_Composer_Check_Compiler();

$arg = reset($argv);
do {
	if ($arg == '-o' || $arg == '--optimize') {
		$build->setOptimize(true);
	}
	else if ($arg == '-O' || $arg == '--obfuscate') {
		$build->setObfuscate(true);
	}
	else if ($arg == '-f' || $arg == '--file') {
		$build->setFilename(next($argv));
	}
} while ($arg = next($argv));

$build->compile();
