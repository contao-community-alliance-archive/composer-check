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

interface ContaoCommunityAlliance_Composer_Check_CheckInterface
{
	/**
	 * Run the check and return the status.
	 *
	 * @return ContaoCommunityAlliance_Composer_Check_StatusInterface
	 */
	public function run();
}
