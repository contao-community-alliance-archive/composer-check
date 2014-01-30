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
		$phar = new Phar('composer-check.phar');
		$phar->setSignatureAlgorithm(\Phar::SHA1);
		$phar->startBuffering();

		$this->add($phar, __DIR__ . DIRECTORY_SEPARATOR . 'assets');
		$this->add($phar, __DIR__ . DIRECTORY_SEPARATOR . 'src');
		$this->add($phar, __DIR__ . DIRECTORY_SEPARATOR . 'bootstrap.php');
		$this->add($phar, __DIR__ . DIRECTORY_SEPARATOR . 'console.php');
		$this->add($phar, __DIR__ . DIRECTORY_SEPARATOR . 'index.php');

		$phar->setStub(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'stub.php'));
		$phar->stopBuffering();
	}

	/**
	 * Recursively add files and directories to the phar.
	 *
	 * @param Phar   $phar
	 * @param string $path
	 */
	protected function add(Phar $phar, $path)
	{
		$relative = str_replace(__DIR__ . DIRECTORY_SEPARATOR, '', $path);

		if (is_dir($path)) {
			$phar->addEmptyDir($relative);

			foreach (scandir($path) as $child) {
				if ($child[0] != '.') {
					$this->add($phar, $path . DIRECTORY_SEPARATOR . $child);
				}
			}
		}
		else if (is_file($path)) {
			$phar->addFile($path, $relative);
		}
	}
}

$build = new build();
$build->run();
