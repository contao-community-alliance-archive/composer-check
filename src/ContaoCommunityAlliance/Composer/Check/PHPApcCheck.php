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
class ContaoCommunityAlliance_Composer_Check_PHPApcCheck
	implements ContaoCommunityAlliance_Composer_Check_CheckInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function run()
	{
		if(extension_loaded('apcu')) {
			$state       = ContaoCommunityAlliance_Composer_Check_StatusInterface::STATE_OK;
			$summary     = Runtime::$translator->translate(
				'php_apc',
				'summary_apcu_enabled'
			);
			$description = Runtime::$translator->translate(
				'php_apc',
				'description_apcu_enabled'
			);
		}
		else if (!function_exists('apc_clear_cache')) {
			$state       = ContaoCommunityAlliance_Composer_Check_StatusInterface::STATE_OK;
			$summary     = Runtime::$translator->translate(
				'php_apc',
				'summary_disabled'
			);
			$description = Runtime::$translator->translate(
				'php_apc',
				'description_disabled'
			);
		}
		else {
			$state       = ContaoCommunityAlliance_Composer_Check_StatusInterface::STATE_ERROR;
			$summary     = Runtime::$translator->translate(
				'php_apc',
				'summary_enabled'
			);
			$description = Runtime::$translator->translate(
				'php_apc',
				'description_enabled'
			);
		}

		return new ContaoCommunityAlliance_Composer_Check_Status(
			'php_apc', $state, $summary, $description
		);
	}
}