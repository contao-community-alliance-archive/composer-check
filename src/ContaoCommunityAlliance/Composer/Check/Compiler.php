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
class ContaoCommunityAlliance_Composer_Check_Compiler
{
	/**
	 * @var bool
	 */
	protected $optimize = false;

	/**
	 * @var bool
	 */
	protected $obfuscate = false;

	/**
	 * @var string
	 */
	protected $filename = 'composer-check.php';

	/**
	 * @param boolean $optimize
	 */
	public function setOptimize($optimize)
	{
		$this->optimize = (bool) $optimize;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getOptimize()
	{
		return $this->optimize;
	}

	/**
	 * @param boolean $obfuscate
	 */
	public function setObfuscate($obfuscate)
	{
		$this->obfuscate = (bool) $obfuscate;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getObfuscate()
	{
		return $this->obfuscate;
	}

	/**
	 * @param string $filename
	 */
	public function setFilename($filename)
	{
		$this->filename = (string) $filename;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getFilename()
	{
		return $this->filename;
	}

	public function compile()
	{
		$this->build();
	}

	protected function build()
	{
		$rootPath = dirname(dirname(dirname(dirname(__DIR__))));
		$basePath = $rootPath
			. DIRECTORY_SEPARATOR . 'src'
			. DIRECTORY_SEPARATOR . 'ContaoCommunityAlliance'
			. DIRECTORY_SEPARATOR . 'Composer'
			. DIRECTORY_SEPARATOR . 'Check'
			. DIRECTORY_SEPARATOR;

		$version  = trim(
			shell_exec(
				sprintf(
					'cd %s; git describe 2>/dev/null || git rev-parse --short --verify HEAD 2>/dev/null',
					escapeshellarg($rootPath)
				)
			)
		);
		$datetime = trim(shell_exec('git show -s --format=%ci HEAD'));

		$sr = array(
			'@version@'  => $version,
			'@datetime@' => $datetime,
		);

		$stream = fopen($rootPath . DIRECTORY_SEPARATOR . $this->filename, 'w');

		// init file
		$this->initFile($stream, $sr, $rootPath);

		// generic classes
		$this->appendFile($stream, $basePath . 'StatusInterface.php', $sr);
		$this->appendFile($stream, $basePath . 'Status.php', $sr);
		$this->appendFile($stream, $basePath . 'CheckInterface.php', $sr);
		$this->appendFile($stream, $basePath . 'CheckRunner.php', $sr);

		// php checks
		$this->appendFile($stream, $basePath . 'PHPAllowUrlFopenCheck.php', $sr);
		$this->appendFile($stream, $basePath . 'PHPApcCheck.php', $sr);
		$this->appendFile($stream, $basePath . 'PHPCurlCheck.php', $sr);
		$this->appendFile($stream, $basePath . 'PHPMemoryLimitCheck.php', $sr);
		$this->appendFile($stream, $basePath . 'PHPProcOpenCheck.php', $sr);
		$this->appendFile($stream, $basePath . 'PHPShellExecCheck.php', $sr);
		$this->appendFile($stream, $basePath . 'PHPSuhosinCheck.php', $sr);
		$this->appendFile($stream, $basePath . 'PHPVersionCheck.php', $sr);

		// contao checks
		$this->appendFile($stream, $basePath . 'ContaoSafeModeHackCheck.php', $sr);

		// translator
		$this->appendFile($stream, $basePath . 'L10N' . DIRECTORY_SEPARATOR . 'SimpleStaticTranslator.php', $sr);

		// web controller
		$this->appendFile($stream, $basePath . 'Controller.php', $sr);

		// finish file
		$this->finishFile($stream, $sr, $rootPath);

		fclose($stream);
	}

	protected function initFile($stream, $sr, $rootPath)
	{
		fwrite(
			$stream,
			<<<EOF
<?php

/**
 * System Check for the Contao Composer Client
 *
 * PHP Version 5.3
 *
 * @copyright 2013,2014 ContaoCommunityAlliance
 * @author    Tristan Lins <tristan.lins@bit3.de>
 * @package   contao-community-alliance/composer-check
 * @license   LGPL-3.0+
 * @link      http://c-c-a.org
 */

error_reporting(E_ALL | E_STRICT);

EOF
		);

		$php = <<<EOF
<?php

class Runtime
{
	static public \$errors = array();

	static public function error_logger(\$errno, \$errstr, \$errfile = null, \$errline = null, array \$errcontext = null)
	{
		self::\$errors[] = array(
			'errno'      => \$errno,
			'errstr'     => \$errstr,
			'errfile'    => \$errfile,
			'errline'    => \$errline,
			'errcontext' => \$errcontext,
		);
	}

	static public \$translator;
}

set_error_handler('Runtime::error_logger', E_ALL);
EOF;

		$this->appendCode($stream, $php, $sr);
	}

	protected function finishFile($stream, $sr, $rootPath)
	{
		$translationBuilder = new ContaoCommunityAlliance_Composer_Check_L10N_StaticTranslationBuilder();
		$translations       = $translationBuilder->build();
		$translations       = var_export($translations, true);

		$assets = $this->serializeAssets($rootPath);
		$assets = var_export($assets, true);

		$php = <<<EOF
<?php

Runtime::\$translator = new ContaoCommunityAlliance_Composer_Check_L10N_SimpleStaticTranslator();
Runtime::\$translator->setTranslations($translations);

if (isset(\$_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
	\$acceptedLanguages = explode(',', \$_SERVER['HTTP_ACCEPT_LANGUAGE']);
	foreach (\$acceptedLanguages as \$acceptedLanguage) {
		\$acceptedLanguage = preg_replace('~;.*$~', '', \$acceptedLanguage);
		if (strlen(\$acceptedLanguage) == 2) {
			Runtime::\$translator->setLanguage(\$acceptedLanguage);
			break;
		}
	}
}

if (isset(\$_SERVER['PATH_INFO']) && strlen(\$_SERVER['PATH_INFO']) > 1) {
	\$pathInfo = \$_SERVER['PATH_INFO'];
	\$assets   = $assets;
	\$asset    = \$assets[\$pathInfo];

	header('Content-Type: ' . \$asset['type']);
	echo \$asset['content'];
	exit;
}
else {
	\$controller = new ContaoCommunityAlliance_Composer_Check_Controller();
	\$controller->setBasePath(basename(__FILE__) . '/');
	\$controller->run();
}

EOF;

		$this->appendCode($stream, $php, $sr);
	}

	protected function serializeAssets($rootPath)
	{
		$assets = array();

		$finfo = finfo_open();

		$iterator = new RecursiveDirectoryIterator($rootPath . DIRECTORY_SEPARATOR . 'assets');
		$iterator = new RecursiveIteratorIterator($iterator);

		/** @var \SplFileInfo $file */
		foreach ($iterator as $file) {
			$pathname = $file->getPathname();
			$pathInfo = str_replace($rootPath . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $pathname);
			$mime     = trim(shell_exec(sprintf('xdg-mime query filetype %s', escapeshellarg($pathname))));

			$assets[$pathInfo] = array(
				'type'    => $mime,
				'content' => file_get_contents($pathname),
			);
		}

		finfo_close($finfo);

		return $assets;
	}

	protected function appendFile($stream, $file, $sr)
	{
		$php = file_get_contents($file);

		$this->appendCode($stream, $php, $sr);
	}

	protected function appendCode($stream, $php, $sr)
	{
		$php = str_replace(array_keys($sr), array_values($sr), $php);
		$php = $this->stripWhitespace($php);
		$php = trim($php);
		$php = preg_replace('~^<\?php~', '', $php);
		$php = trim($php);

		fwrite($stream, $php);
		fwrite($stream, PHP_EOL);
	}

	/**
	 * Removes whitespace from a PHP source string while preserving line numbers.
	 *
	 * This function is adapted from Composer Compiler.
	 *
	 * Original (c) Nils Adermann <naderman@naderman.de>
	 *              Jordi Boggiano <j.boggiano@seld.be>
	 *
	 * @see https://github.com/composer/composer/blob/master/src/Composer/Compiler.php
	 *
	 * @param  string $source A PHP string
	 *
	 * @return string The PHP string with the whitespace removed
	 */
	private function stripWhitespace($source)
	{
		if (!$this->optimize || !function_exists('token_get_all')) {
			return $source;
		}

		$output = '';
		foreach (token_get_all($source) as $token) {
			if (is_string($token)) {
				$output .= $token;
			}
			elseif (in_array($token[0], array(T_COMMENT, T_DOC_COMMENT))) {
				continue;
			}
			elseif (T_WHITESPACE === $token[0]) {
				$whitespace = $token[1];

				if ($this->obfuscate) {
					// normalize newlines to \n
					$whitespace = preg_replace('{(?:\r\n|\r|\n)}', ' ', $whitespace);
					// trim leading spaces
					$whitespace = preg_replace('{\n +}', ' ', $whitespace);
					// reduce multiple newlines
					$whitespace = preg_replace('{\n+}', ' ', $whitespace);
				}

				// reduce wide spaces
				$whitespace = preg_replace('{[ \t]+}', ' ', $whitespace);
				// normalize newlines to \n
				$whitespace = preg_replace('{(?:\r\n|\r|\n)}', "\n", $whitespace);
				// trim leading spaces
				$whitespace = preg_replace('{\n +}', "\n", $whitespace);
				// reduce multiple newlines
				$whitespace = preg_replace('{\n+}', "\n", $whitespace);
				$output .= $whitespace;
			}
			else if ($this->obfuscate && T_INLINE_HTML === $token[0]) {
				$html = $token[1];
				$html = preg_replace('~>[\s\n]+<~', '><', $html);
				$html = preg_replace('~>[\s\n]+<~', '><', $html);
				$html = preg_replace('~>[\s\n]+~', '> ', $html);
				$html = preg_replace('~[\s\n]+<~', ' <', $html);
				$html = preg_replace('~^\t+~', '', $html);
				$html = preg_replace('~^\s+~', ' ', $html);
				$html = preg_replace('~\s+$~', ' ', $html);
				$output .= $html;
			}
			else {
				$output .= $token[1];
			}
		}

		return $output;
	}

	protected function buildPhar()
	{
		$phar = new Phar('composer-check.phar', null, 'composer-check.phar');
		$phar->setSignatureAlgorithm(\Phar::SHA1);
		$phar->startBuffering();

		$this->addFile($phar, 'assets');
		$this->addFile($phar, 'src');
		$this->addFile($phar, 'bootstrap.php');
		$this->addFile($phar, 'console.php');
		$this->addFile($phar, 'index.php');

		$phar->setStub(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'stub.php'));
		$phar->stopBuffering();
	}

	/**
	 * Recursively add files and directories to the phar.
	 *
	 * @param Phar   $phar
	 * @param string $path
	 */
	protected function addFile(Phar $phar, $relative)
	{
		$path = __DIR__ . DIRECTORY_SEPARATOR . $relative;

		if (is_dir($path)) {
			foreach (scandir($path) as $child) {
				if ($child[0] != '.') {
					$this->addFile($phar, $relative . DIRECTORY_SEPARATOR . $child);
				}
			}
		}
		else if (is_file($path)) {
			$phar->addFromString($relative, file_get_contents($path));
		}
	}
}
