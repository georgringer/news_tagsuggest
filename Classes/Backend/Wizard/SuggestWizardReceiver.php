<?php

declare(strict_types=1);

namespace GeorgRinger\NewsTagsuggest\Backend\Wizard;

/**
 * This file is part of the "news_tagsuggest" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Backend\Form\Wizard\SuggestWizardDefaultReceiver;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Utility\StringUtility;

class SuggestWizardReceiver extends SuggestWizardDefaultReceiver
{

    public const DELIMITER = '__--__';

    public function queryTable(&$params, $recursionCounter = 0)
    {
        $rows = parent::queryTable($params, $recursionCounter);

        $searchString = strtolower($params['value']);
        $matchRow = array_filter($rows, static function ($value) use ($searchString) {
            return strtolower($value['label']) === $searchString;
        });

        if (empty($matchRow)) {
            $newUid = StringUtility::getUniqueId('NEW');
            $rows[$this->table . '_' . $newUid] = [
                'class' => '',
                'label' => $params['value'],
                'path' => '',
                'sprite' => $this->iconFactory->getIconForRecord($this->table, [], Icon::SIZE_SMALL)->render(),
                'style' => '',
                'table' => $this->table,
                'text' => sprintf($this->getLanguageService()->sL('LLL:EXT:news/Resources/Private/Language/locallang_be.xlf:tag_suggest'), $params['value']),
                'uid' => $newUid . self::DELIMITER . $params['value'],
            ];
        }

        return $rows;
    }
}
