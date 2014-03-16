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
class ContaoCommunityAlliance_Composer_Check_ContaoSafeModeHackCheck
	implements ContaoCommunityAlliance_Composer_Check_CheckInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function run()
	{
		$directory = getcwd();

		$state       = ContaoCommunityAlliance_Composer_Check_StatusInterface::STATE_OK;
		$summary     = Runtime::$translator->translate(
			'contao_safe_mode_hack',
			'summary_disabled'
		);
		$description = Runtime::$translator->translate(
			'contao_safe_mode_hack',
			'description_disabled'
		);

		do {
			$localconfigPath = $directory
				. DIRECTORY_SEPARATOR . 'system'
				. DIRECTORY_SEPARATOR . 'config'
				. DIRECTORY_SEPARATOR . 'localconfig.php';

			if (file_exists($localconfigPath)) {
				$localconfig = file_get_contents($localconfigPath);

				if (preg_match(
						'~\$GLOBALS\[\'TL_CONFIG\'\]\[\'useFTP\'\]\s*=\s*(true|false);~',
						$localconfig,
						$matches
					) && $matches[1] == 'true'
				) {
					$state       = ContaoCommunityAlliance_Composer_Check_StatusInterface::STATE_ERROR;
					$summary     = Runtime::$translator->translate(
						'contao_safe_mode_hack',
						'summary_enabled'
					);
					$description = Runtime::$translator->translate(
						'contao_safe_mode_hack',
						'description_enabled'
					);
				}

				break;
			}

			$directory = dirname($directory);
		}
		while ($directory != '.' && $directory != '/' && $directory);

		return new ContaoCommunityAlliance_Composer_Check_Status(
			'contao_safe_mode_hack', $state, $summary, $description
		);
	}
}