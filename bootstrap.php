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

error_reporting(E_ALL | E_STRICT);

class Runtime
{
	static public function autoload($class)
	{
		if (substr($class, 0, 39) == 'ContaoCommunityAlliance_Composer_Check_') {
			$file = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . implode(
					DIRECTORY_SEPARATOR,
					explode('_', $class)
				) . '.php';

			if (file_exists($file)) {
				require($file);
				return true;
			}
		}
		return false;
	}

	/**
	 * @var array
	 */
	static public $errors = array();

	static public function error_logger($errno, $errstr, $errfile = null, $errline = null, array $errcontext = null)
	{
		self::$errors[] = array(
			'errno'      => $errno,
			'errstr'     => $errstr,
			'errfile'    => $errfile,
			'errline'    => $errline,
			'errcontext' => $errcontext,
		);
	}

	/**
	 * @var ContaoCommunityAlliance_Composer_Check_L10N_SimpleTranslator
	 */
	static public $translator;
}

spl_autoload_register('Runtime::autoload');
set_error_handler('Runtime::error_logger', E_ALL);
Runtime::$translator = new ContaoCommunityAlliance_Composer_Check_L10N_SimpleTranslator();
