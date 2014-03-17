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
class ContaoCommunityAlliance_Composer_Check_L10N_StaticTranslationBuilder
{
	/**
	 * Translate a key with arguments.
	 */
	public function build()
	{
		$translations = array();

		foreach (scandir(__DIR__) as $language) {
			if ($language[0] == '.') {
				continue;
			}

			foreach (scandir(__DIR__ . DIRECTORY_SEPARATOR . $language) as $filename) {
				if ($filename[0] == '.') {
					continue;
				}

				$domain = str_replace('.xliff', '', $filename);

				$file = __DIR__ . DIRECTORY_SEPARATOR . $language . DIRECTORY_SEPARATOR . $filename;

				$translations[$language][$domain] = array();

				$doc = new DOMDocument();
				$doc->load($file);

				$transUnitNodes = $doc->getElementsByTagName('trans-unit');
				for ($i = 0; $i < $transUnitNodes->length; $i++) {
					/** @var DOMElement $transUnitNode */
					$transUnitNode = $transUnitNodes->item($i);

					$id = $transUnitNode->getAttribute('id');

					$targetNodes = $transUnitNode->getElementsByTagName('target');
					if ($targetNodes->length) {
						$translations[$language][$domain][$id] = $targetNodes->item(0)->textContent;
						continue;
					}

					$sourceNodes = $transUnitNode->getElementsByTagName('source');
					if ($sourceNodes->length) {
						$translations[$language][$domain][$id] = $sourceNodes->item(0)->textContent;
						continue;
					}
				}
			}
		}

		return $translations;
	}
}