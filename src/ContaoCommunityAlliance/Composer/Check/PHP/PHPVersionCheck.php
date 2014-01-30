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
class ContaoCommunityAlliance_Composer_Check_PHP_PHPVersionCheck
	implements ContaoCommunityAlliance_Composer_Check_CheckInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function run()
	{
		$version = phpversion();

		if (version_compare($version, '5.3.2', '<')) {
			$state       = ContaoCommunityAlliance_Composer_Check_StatusInterface::STATE_ERROR;
			$summary     = Runtime::$translator->translate(
				'php_version_check',
				'summary_unsupported',
				array('%version%' => $version)
			);
			$description = Runtime::$translator->translate(
				'php_version_check',
				'description_unsupported',
				array('%version%' => $version)
			);
		}
		else if (version_compare($version, '5.4', '<')) {
			$state       = ContaoCommunityAlliance_Composer_Check_StatusInterface::STATE_WARN;
			$summary     = Runtime::$translator->translate(
				'php_version_check',
				'summary_5.3.2+',
				array('%version%' => $version)
			);
			$description = Runtime::$translator->translate(
				'php_version_check',
				'description_5.3.2+',
				array('%version%' => $version)
			);
		}
		else {
			$state       = ContaoCommunityAlliance_Composer_Check_StatusInterface::STATE_OK;
			$summary     = Runtime::$translator->translate('php_version_check', 'summary_5.4+', array('%version%' => $version));
			$description = Runtime::$translator->translate(
				'php_version_check',
				'description_5.4+',
				array('%version%' => $version)
			);
		}

		return new ContaoCommunityAlliance_Composer_Check_Status(
			'php_version', $state, $summary, $description
		);
	}
}