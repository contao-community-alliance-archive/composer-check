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
class ContaoCommunityAlliance_Composer_Check_Status implements ContaoCommunityAlliance_Composer_Check_StatusInterface
{
	/**
	 * @var string
	 */
	protected $check;

	/**
	 * @var string
	 */
	protected $state;

	/**
	 * @var string
	 */
	protected $summary;

	/**
	 * @var string
	 */
	protected $description;

	public function __construct(
		$check,
		$state = ContaoCommunityAlliance_Composer_Check_StatusInterface::STATE_UNKNOWN,
		$summary = '',
		$description = ''
	) {
		$this->check       = $check;
		$this->state       = $state;
		$this->summary     = $summary;
		$this->description = $description;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getCheck()
	{
		return $this->check;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getState()
	{
		return $this->state;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSummary()
	{
		return $this->summary;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDescription()
	{
		return $this->description;
	}
}
