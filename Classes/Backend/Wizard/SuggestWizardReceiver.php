<?php

declare(strict_types=1);

namespace GeorgRinger\NewsTagsuggest\Backend\Wizard;

/**
 * This file is part of the "news_tagsuggest" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use GeorgRinger\NewsTagsuggest\Repository\SuggestRegistryRepository;
use TYPO3\CMS\Backend\Form\Wizard\SuggestWizardDefaultReceiver;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class  SuggestWizardReceiver extends SuggestWizardDefaultReceiver
{

    public function queryTable(&$params, $recursionCounter = 0)
    {
        $rows = parent::queryTable($params, $recursionCounter);

        $searchString = strtolower($params['value']);
        $matchRow = array_filter($rows, static function ($value) use ($searchString) {
            return strtolower($value['label']) === $searchString;
        });

        if (empty($matchRow)) {
            $registry = $this->getSuggestRepository();

            $newUid = SuggestRegistryRepository::ID_PREFIX . $registry->set($params['value']);

            $rows[$this->table . '_' . $newUid] = [
                'label' => strip_tags(sprintf($this->getLanguageService()->sL('LLL:EXT:news/Resources/Private/Language/locallang_be.xlf:tag_suggest'), $params['value'])),
                'title' =>  strip_tags(sprintf($this->getLanguageService()->sL('LLL:EXT:news/Resources/Private/Language/locallang_be.xlf:tag_suggest'), $params['value'])),
                'path' => '',
                'icon' => [
                    'identifier' => 'ext-news-tag',
                    'overlay' => null,
                ],
                'table' => $this->table,
                'uid' => $newUid ,
            ];
        }

        return $rows;
    }

    private function getSuggestRepository(): SuggestRegistryRepository
    {
        return GeneralUtility::makeInstance(SuggestRegistryRepository::class);
    }
}
