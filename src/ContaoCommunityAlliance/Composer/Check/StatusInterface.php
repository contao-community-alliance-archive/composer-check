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

interface ContaoCommunityAlliance_Composer_Check_StatusInterface
{
	const STATE_UNKNOWN = 'unknown';

	const STATE_OK = 'ok';

	const STATE_WARN = 'warning';

	const STATE_ERROR = 'error';

	/**
	 * Return the check name.
	 *
	 * @return string
	 */
	public function getCheck();

	/**
	 * Return the state of the status.
	 *
	 * @return string
	 */
	public function getState();

	/**
	 * Return the summary of the status.
	 *
	 * @return string
	 */
	public function getSummary();

	/**
	 * Return detailed description of the status.
	 *
	 * @return string
	 */
	public function getDescription();
}
