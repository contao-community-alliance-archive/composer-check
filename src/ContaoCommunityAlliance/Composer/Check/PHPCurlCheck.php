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
class ContaoCommunityAlliance_Composer_Check_PHPCurlCheck
	implements ContaoCommunityAlliance_Composer_Check_CheckInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function run()
	{
		if (function_exists('curl_init')) {
			$state       = ContaoCommunityAlliance_Composer_Check_StatusInterface::STATE_OK;
			$summary     = Runtime::$translator->translate(
				'php_curl',
				'summary_enabled'
			);
			$description = Runtime::$translator->translate(
				'php_curl',
				'description_enabled'
			);
		}
		else {
			$state       = ContaoCommunityAlliance_Composer_Check_StatusInterface::STATE_ERROR;
			$summary     = Runtime::$translator->translate(
				'php_curl',
				'summary_disabled'
			);
			$description = Runtime::$translator->translate(
				'php_curl',
				'description_disabled'
			);
		}

		return new ContaoCommunityAlliance_Composer_Check_Status(
			'php_curl', $state, $summary, $description
		);
	}
}