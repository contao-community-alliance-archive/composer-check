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

class console
{
	public function run()
	{
		global $argv;

		$shortopts = 'l:h';
		$longopts = array('lang:', 'help');
		$options = getopt($shortopts, $longopts);

		if (isset($options['l'])) {
			Runtime::$translator->setLanguage($options['l']);
		}
		if (isset($options['lang'])) {
			Runtime::$translator->setLanguage($options['lang']);
		}

		if (isset($options['h']) || isset($options['help'])) {
			$this->help();
			$this->shutdown();
		}

		/**
		 * Run checks
		 */
		$selectedChecks = array();
		foreach ($argv as $arg) {
			if (isset(ContaoCommunityAlliance_Composer_Check_CheckRunner::$checks[$arg])) {
				$selectedChecks[] = $arg;
			}
		}
		if (empty($selectedChecks)) {
			$selectedChecks = array_keys(ContaoCommunityAlliance_Composer_Check_CheckRunner::$checks);
		}

		$this->runChecks($selectedChecks);
		$this->shutdown();
	}

	protected function help()
	{
		echo <<<EOF

  +-----------------------------------------------+
  |                                               |
  |  System Check for the Contao Composer Client  |
  |                                               |
  +-----------------------------------------------+

usage: php composer-check.phar [options] [checks]

Options:
  --help  -h  show this help
  --lang  -l  show messages in specific language (expect this help)

Available checks:
EOF;

		foreach (ContaoCommunityAlliance_Composer_Check_CheckRunner::$checks as $key => $class) {
			$description = Runtime::$translator->translate('checks', $key, array('%class%' => $class));

			printf(PHP_EOL . '  %-16s %s', $key, $description);
		}

		$this->shutdown();
	}

	protected function runChecks($selectedChecks)
	{
		$runner = new ContaoCommunityAlliance_Composer_Check_CheckRunner();
		$multipleStatus = $runner->runChecks($selectedChecks);

		foreach ($multipleStatus as $status) {
			printf(PHP_EOL . '[%s] %s', $status->getCheck(), $status->getState());
			printf(PHP_EOL . ' * %s', wordwrap($status->getSummary(), 72, "\n * "));
			printf(PHP_EOL . '   > %s', wordwrap($status->getDescription(), 70, "\n   > "));
			print(PHP_EOL);
		}
	}

	/**
	 * Run a single check and return the status.
	 *
	 * @param string $class
	 *
	 * @return ContaoCommunityAlliance_Composer_Check_Status
	 */
	protected function runCheck($class)
	{
	}

	protected function shutdown()
	{
		if (count(Runtime::$errors)) {
			echo <<<EOF


Some errors occurred:
EOF;

			foreach (Runtime::$errors as $error) {
				printf(
					PHP_EOL . '  [%d] %s',
					$error['errno'],
					$error['errstr']
				);
				printf(
					PHP_EOL . '  in %s:%d',
					$error['errfile'],
					$error['errline']
				);
			}
		}

		echo PHP_EOL;

		exit;
	}
}

$console = new console();
$console->run();
