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
class ContaoCommunityAlliance_Composer_Check_L10N_SimpleStaticTranslator
{
	/**
	 * @var string
	 */
	protected $language = 'en';

	/**
	 * @var array
	 */
	protected $translations = array();

	/**
	 * @param mixed $language
	 */
	public function setLanguage($language)
	{
		if ($this->language == $language) {
			return $this;
		}

		$this->language = (string) $language;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getLanguage()
	{
		return $this->language;
	}

	/**
	 * @param array $translations
	 */
	public function setTranslations(array $translations)
	{
		$this->translations = $translations;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getTranslations($domain, $language = null)
	{
		if (!$language) {
			$language = $this->language;
		}

		$translations = $this->translations['en'][$domain];

		if (isset($this->translations[$language][$domain])) {
			$translations = array_merge(
				$translations,
				$this->translations[$language][$domain]
			);
		}

		return $translations;
	}

	/**
	 * Translate a key with arguments.
	 */
	public function translate($domain, $key, array $arguments = array())
	{
		$translations = $this->getTranslations($domain);

		if (isset($translations[$key])) {
			$string = $translations[$key];
		}
		else {
			$string = $key;
		}

		if (count($arguments)) {
			$string = str_replace(
				array_keys($arguments),
				array_values($arguments),
				$string
			);
		}

		// parse some markdown syntax
		if (PHP_SAPI != 'cli') {
			$string = preg_replace('~`([^`]*?)`~', '<code>$1</code>', $string);
			$string = preg_replace('~\*\*\*([^\*]*?)\*\*\*~', '<strong><em>$1</em></strong>', $string);
			$string = preg_replace('~\*\*([^\*]*?)\*\*~', '<strong>$1</strong>', $string);
			$string = preg_replace('~\*([^\*]*?)\*~', '<em>$1</em>', $string);
		}

		return $string;
	}
}