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
class ContaoCommunityAlliance_Composer_Check_ExecuteDetachedCheck
	implements ContaoCommunityAlliance_Composer_Check_CheckInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function run()
	{
		if (false) {
		}
		else {
			$state       = ContaoCommunityAlliance_Composer_Check_StatusInterface::STATE_WARN;
			$summary     = Runtime::$translator->translate('process_execute_detached', 'summary_unsupported');
			$description = Runtime::$translator->translate('process_execute_detached', 'description_unsupported');
		}

		return new ContaoCommunityAlliance_Composer_Check_Status(
			'process_execute_detached', $state, $summary, $description
		);
	}
}