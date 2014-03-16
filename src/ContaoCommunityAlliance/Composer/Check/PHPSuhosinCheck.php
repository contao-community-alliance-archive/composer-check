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
class ContaoCommunityAlliance_Composer_Check_PHPSuhosinCheck
	implements ContaoCommunityAlliance_Composer_Check_CheckInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function run()
	{
		if(!extension_loaded('suhosin')) {
			$state       = ContaoCommunityAlliance_Composer_Check_StatusInterface::STATE_OK;
			$summary     = Runtime::$translator->translate(
				'php_suhosin',
				'summary_disabled'
			);
			$description = Runtime::$translator->translate(
				'php_suhosin',
				'description_disabled'
			);
		}
		else if (strpos(ini_get('suhosin.executor.include.whitelist'), 'phar') !== false) {
			$state       = ContaoCommunityAlliance_Composer_Check_StatusInterface::STATE_WARN;
			$summary     = Runtime::$translator->translate(
				'php_suhosin',
				'summary_whitelisted'
			);
			$description = Runtime::$translator->translate(
				'php_suhosin',
				'description_whitelisted'
			);
		}
		else {
			$state       = ContaoCommunityAlliance_Composer_Check_StatusInterface::STATE_ERROR;
			$summary     = Runtime::$translator->translate(
				'php_suhosin',
				'summary_enabled'
			);
			$description = Runtime::$translator->translate(
				'php_suhosin',
				'description_enabled'
			);
		}

		return new ContaoCommunityAlliance_Composer_Check_Status(
			'php_suhosin', $state, $summary, $description
		);
	}
}