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

class build
{
	public function run()
	{
		$phar = new Phar('composer-check.phar', null, 'composer-check.phar');
		$phar->setSignatureAlgorithm(\Phar::SHA1);
		$phar->startBuffering();

		$this->add($phar, 'assets');
		$this->add($phar, 'src');
		$this->add($phar, 'bootstrap.php');
		$this->add($phar, 'console.php');
		$this->add($phar, 'index.php');

		$phar->setStub(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'stub.php'));
		$phar->stopBuffering();
	}

	/**
	 * Recursively add files and directories to the phar.
	 *
	 * @param Phar   $phar
	 * @param string $path
	 */
	protected function add(Phar $phar, $relative)
	{
		$path = __DIR__ . DIRECTORY_SEPARATOR . $relative;

		if (is_dir($path)) {
			foreach (scandir($path) as $child) {
				if ($child[0] != '.') {
					$this->add($phar, $relative . DIRECTORY_SEPARATOR . $child);
				}
			}
		}
		else if (is_file($path)) {
			$phar->addFromString($relative, file_get_contents($path));
		}
	}
}

$build = new build();
$build->run();
