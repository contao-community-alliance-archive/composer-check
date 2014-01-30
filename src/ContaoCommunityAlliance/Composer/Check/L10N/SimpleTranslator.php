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
class ContaoCommunityAlliance_Composer_Check_L10N_SimpleTranslator
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

		$this->language     = (string) $language;
		$this->translations = array();
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
	 * @return mixed
	 */
	public function getTranslations($domain, $language = null)
	{
		if (!$language) {
			$language = $this->language;
		}

		if (!isset($this->translations[$domain])) {
			$file = __DIR__ . DIRECTORY_SEPARATOR . $language . DIRECTORY_SEPARATOR . $domain . '.xliff';

			if (!file_exists($file)) {
				if ($language == 'en') {
					trigger_error(
						'The domain "' . $domain . '" does not exists in language ' . $language,
						E_USER_WARNING
					);
					$this->translations[$domain] = array();
				}
				else {
					trigger_error(
						'The domain "' . $domain . '" does not exists in language ' . $language . ', fallback to EN',
						E_USER_WARNING
					);
					return $this->getTranslations($domain, 'en');
				}
			}
			else {
				$this->translations[$domain] = array();

				$doc = new DOMDocument();
				$doc->load($file);

				$transUnitNodes = $doc->getElementsByTagName('trans-unit');
				for ($i = 0; $i < $transUnitNodes->length; $i++) {
					/** @var DOMElement $transUnitNode */
					$transUnitNode = $transUnitNodes->item($i);

					$id = $transUnitNode->getAttribute('id');

					$targetNodes = $transUnitNode->getElementsByTagName('target');
					if ($targetNodes->length) {
						$this->translations[$domain][$id] = $targetNodes->item(0)->textContent;
						continue;
					}

					$sourceNodes = $transUnitNode->getElementsByTagName('source');
					if ($sourceNodes->length) {
						$this->translations[$domain][$id] = $sourceNodes->item(0)->textContent;
						continue;
					}
				}
			}
		}

		return $this->translations[$domain];
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

		return $string;
	}
}