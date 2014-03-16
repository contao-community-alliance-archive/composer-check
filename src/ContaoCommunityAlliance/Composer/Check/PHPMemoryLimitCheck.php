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
class ContaoCommunityAlliance_Composer_Check_PHPMemoryLimitCheck
	implements ContaoCommunityAlliance_Composer_Check_CheckInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function run()
	{
		$memoryLimit = trim(ini_get('memory_limit'));

		if ($memoryLimit == -1) {
			$memoryLimitHumanReadable = $this->bytesToHumandReadable($memoryLimit);

			$state       = ContaoCommunityAlliance_Composer_Check_StatusInterface::STATE_OK;
			$summary     = Runtime::$translator->translate(
				'php_memory_limit',
				'summary_unlimited',
				array('%memory_limit%' => $memoryLimitHumanReadable)
			);
			$description = Runtime::$translator->translate(
				'php_memory_limit',
				'description_unlimited',
				array('%memory_limit%' => $memoryLimitHumanReadable)
			);
		}
		else {
			$memoryLimit              = $this->memoryInBytes($memoryLimit);
			$memoryLimitHumanReadable = $this->bytesToHumandReadable($memoryLimit);

			if (
				function_exists('ini_set') &&
				@ini_set('memory_limit', '1024M') !== false &&
					ini_get('memory_limit') == '1024M'
			) {
				$state       = ContaoCommunityAlliance_Composer_Check_StatusInterface::STATE_OK;
				$summary     = Runtime::$translator->translate(
					'php_memory_limit',
					'summary_increased',
					array('%memory_limit%' => $memoryLimitHumanReadable)
				);
				$description = Runtime::$translator->translate(
					'php_memory_limit',
					'description_increased',
					array('%memory_limit%' => $memoryLimitHumanReadable)
				);
			}
			else if ($memoryLimit >= 1024 * 1024 * 1024) {
				$state       = ContaoCommunityAlliance_Composer_Check_StatusInterface::STATE_OK;
				$summary     = Runtime::$translator->translate(
					'php_memory_limit',
					'summary_good',
					array('%memory_limit%' => $memoryLimitHumanReadable)
				);
				$description = Runtime::$translator->translate(
					'php_memory_limit',
					'description_good',
					array('%memory_limit%' => $memoryLimitHumanReadable)
				);
			}
			else if ($memoryLimit >= 512 * 1024 * 1024) {
				$state       = ContaoCommunityAlliance_Composer_Check_StatusInterface::STATE_WARN;
				$summary     = Runtime::$translator->translate(
					'php_memory_limit',
					'summary_okay',
					array('%memory_limit%' => $memoryLimitHumanReadable)
				);
				$description = Runtime::$translator->translate(
					'php_memory_limit',
					'description_okay',
					array('%memory_limit%' => $memoryLimitHumanReadable)
				);
			}
			else {
				$state       = ContaoCommunityAlliance_Composer_Check_StatusInterface::STATE_ERROR;
				$summary     = Runtime::$translator->translate(
					'php_memory_limit',
					'summary_low',
					array('%memory_limit%' => $memoryLimitHumanReadable)
				);
				$description = Runtime::$translator->translate(
					'php_memory_limit',
					'description_low',
					array('%memory_limit%' => $memoryLimitHumanReadable)
				);
			}
		}

		return new ContaoCommunityAlliance_Composer_Check_Status(
			'php_memory_limit', $state, $summary, $description
		);
	}

	protected function memoryInBytes($value)
	{
		$unit  = strtolower(substr($value, -1, 1));
		$value = (int) $value;
		switch ($unit) {
			case 'g':
				$value *= 1024;
			// no break (cumulative multiplier)
			case 'm':
				$value *= 1024;
			// no break (cumulative multiplier)
			case 'k':
				$value *= 1024;
		}

		return $value;
	}

	protected function bytesToHumandReadable($bytes)
	{
		if ($bytes == -1) {
			return '∞';
		}

		$unit = '';

		if ($bytes >= 1024) {
			$unit = ' kiB';
			$bytes /= 1024;
		}
		if ($bytes >= 1024) {
			$unit = ' MiB';
			$bytes /= 1024;
		}
		if ($bytes >= 1024) {
			$unit = ' GiB';
			$bytes /= 1024;
		}

		return round($bytes) . $unit;
	}
}