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
class ContaoCommunityAlliance_Composer_Check_PHPProcessExecuteDetachedCheck
	implements ContaoCommunityAlliance_Composer_Check_CheckInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function run()
	{
		$disabledFunctions = explode(',', ini_get('disable_functions'));
		$disabledFunctions = array_map('trim', $disabledFunctions);

		if (function_exists('shell_exec') && !in_array('shell_exec', $disabledFunctions)) {
			$state       = ContaoCommunityAlliance_Composer_Check_StatusInterface::STATE_OK;
			$summary     = Runtime::$translator->translate('php_process_execute_detached', 'summary_supported');
			$description = Runtime::$translator->translate('php_process_execute_detached', 'description_supported');
		}
		else {
			$state       = ContaoCommunityAlliance_Composer_Check_StatusInterface::STATE_WARN;
			$summary     = Runtime::$translator->translate('php_process_execute_detached', 'summary_unsupported');
			$description = Runtime::$translator->translate('php_process_execute_detached', 'description_unsupported');
		}

		return new ContaoCommunityAlliance_Composer_Check_Status(
			'php_process_execute_detached', $state, $summary, $description
		);
	}
}