<?php

/**
 * System Check for the Contao Composer Client
 *
 * PHP Version 5.1
 *
 * @copyright  bit3 UG 2013
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @package    composer-check
 * @license    LGPL-3.0+
 * @link       http://c-c-a.org
 */
class ContaoCommunityAlliance_Composer_Check_CheckRunner
{
	public static $checks = array(
		// PHP
		'php_version'              => 'ContaoCommunityAlliance_Composer_Check_PHPVersionCheck',
		'memory_limit'             => 'ContaoCommunityAlliance_Composer_Check_MemoryLimitCheck',
		'php_curl'                 => 'ContaoCommunityAlliance_Composer_Check_PHPCurlCheck',
		// Process
		'process_execute_detached' => 'ContaoCommunityAlliance_Composer_Check_ExecuteDetachedCheck',
		// Contao
		'contao_safe_mode_hack'    => 'ContaoCommunityAlliance_Composer_Check_ContaoSafeModeHackCheck',
	);

	/**
	 * Run all checks.
	 *
	 * @return ContaoCommunityAlliance_Composer_Check_StatusInterface[]
	 */
	public function runAll()
	{
		return $this->runChecks(array_keys(self::$checks));
	}

	/**
	 * Run multiple checks.
	 *
	 * @param array $selectedChecks
	 *
	 * @return ContaoCommunityAlliance_Composer_Check_StatusInterface[]
	 */
	public function runChecks(array $selectedChecks)
	{
		$multipleStatus = array();

		foreach ($selectedChecks as $selectedCheck) {
			$multipleStatus[] = $this->runCheck($selectedCheck);
		}

		return $multipleStatus;
	}

	/**
	 * Run a single check
	 *
	 * @param string $selectedChecks
	 *
	 * @return ContaoCommunityAlliance_Composer_Check_StatusInterface
	 */
	public function runCheck($selectedCheck)
	{
		try {
			$class = self::$checks[$selectedCheck];
			/** @var ContaoCommunityAlliance_Composer_Check_CheckInterface $object */
			$object = new $class();
			return $object->run();
		}
		catch (Exception $e) {
			return new ContaoCommunityAlliance_Composer_Check_Status(
				$selectedCheck,
				ContaoCommunityAlliance_Composer_Check_StatusInterface::STATE_ERROR,
				$e->getMessage(),
				$e->getTraceAsString()
			);
		}
	}
}
