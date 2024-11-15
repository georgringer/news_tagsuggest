<?php

declare(strict_types=1);

namespace GeorgRinger\NewsTagsuggest\Hooks;

/**
 * This file is part of the "news_tagsuggest" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use GeorgRinger\NewsTagsuggest\Repository\SuggestRegistryRepository;
use TYPO3\CMS\Backend\Utility\BackendUtility as BackendUtilityCore;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class DataHandlerHook
{

    protected DataHandler $localDataHandler;

    public function processDatamap_preProcessFieldArray(&$fieldArray, $table, $id, DataHandler $parentObject): void
    {
        if ($table !== 'tx_news_domain_model_news') {
            return;
        }
        if (!str_contains($fieldArray['tags'] ?? '', 'tx_news_domain_model_tag_' . SuggestRegistryRepository::ID_PREFIX)) {
            return;
        }
        $this->localDataHandler = $this->getLocalDataHandlerInstance($parentObject);
        $targetPage = $this->getTargetPage($id, (int)($fieldArray['pid'] ?? 0));
        $fieldArray['tags'] = $this->createTags($fieldArray['tags'], $targetPage);
    }

    protected function createTags(string $tagList, int $targetPage): string
    {
        $finalList = [];
        $list = explode(',', $tagList);
        foreach ($list as $newTag) {
            $potentialNewId = str_replace('tx_news_domain_model_tag_', '', $newTag);

            if (!str_starts_with($potentialNewId, SuggestRegistryRepository::ID_PREFIX)) {
                $finalList[] = $newTag;
                continue;
            }

            $registryId = str_replace(SuggestRegistryRepository::ID_PREFIX, '', $potentialNewId);

            $tagTitle = $this->getSuggestRepository()->get((int)$registryId);

            $identifier = StringUtility::getUniqueId('NEW');
            $commandArray = [];
            $commandArray['tx_news_domain_model_tag'][$identifier] = [
                'pid' => $targetPage,
                'title' => $tagTitle
            ];
            $this->localDataHandler->start($commandArray, []);
            $this->localDataHandler->process_datamap();

            if (isset($this->localDataHandler->substNEWwithIDs[$identifier])) {
                $finalList[] = 'tx_news_domain_model_tag_' . $this->localDataHandler->substNEWwithIDs[$identifier];
            } else {
                // todo logging error log from localDataHandler
            }
        }

        $this->getSuggestRepository()->cleanup();

        return implode(',', $finalList);
    }

    /**
     * @param int|string $possibleRecordId
     * @param int $possiblePid
     * @return int
     */
    protected function getTargetPage($possibleRecordId, int $possiblePid): int
    {
        $targetPage = 0;
        if (!str_starts_with((string)$possibleRecordId, 'NEW')) {
            $newsRecord = BackendUtilityCore::getRecord('tx_news_domain_model_news', $possibleRecordId);
            $targetPage = $newsRecord['pid'];
        } elseif ($possiblePid) {
            $targetPage = $possiblePid;
        }

        $pagesTsConfig = BackendUtilityCore::getPagesTSconfig($targetPage);
        return (int)($pagesTsConfig['tx_news.']['tagPid'] ?? $targetPage);
    }

    private function getLocalDataHandlerInstance(DataHandler $parentDataHandler): DataHandler
    {
        $localDataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $localDataHandler->copyTree = $parentDataHandler->copyTree;
        $localDataHandler->enableLogging = $parentDataHandler->enableLogging;
        // Transformations should NOT be carried out during copy
        $localDataHandler->dontProcessTransformations = true;
        // make sure the isImporting flag is transferred, so all hooks know if
        // the current process is an import process
        $localDataHandler->isImporting = $parentDataHandler->isImporting;
        $localDataHandler->bypassAccessCheckForRecords = $parentDataHandler->bypassAccessCheckForRecords;
        $localDataHandler->bypassWorkspaceRestrictions = $parentDataHandler->bypassWorkspaceRestrictions;
        return $localDataHandler;
    }

    private function getSuggestRepository(): SuggestRegistryRepository
    {
        return GeneralUtility::makeInstance(SuggestRegistryRepository::class);
    }

    private function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }

}
