<?php

declare(strict_types=1);

namespace GeorgRinger\NewsTagsuggest\Repository;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Registry;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * This file is part of the "news_tagsuggest" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
class SuggestRegistryRepository
{
    public const ID_PREFIX = '9999999999999';
    private const TABLE = 'tx_news_tagsuggest_item';
    protected Connection $connection;

    public function __construct()
    {
        $this->connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable(self::TABLE);
    }

    public function get(int $id): ?string
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $row = $queryBuilder
            ->select('*')
            ->from(self::TABLE)
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($id, Connection::PARAM_INT)),
                $queryBuilder->expr()->eq('userid', $queryBuilder->createNamedParameter($this->getBackendUser()->user['uid'], Connection::PARAM_INT))
            )
            ->executeQuery()
            ->fetchAssociative();

        if (!$row) {
            return null;
        }
        return $row['title'];
    }

    public function set(string $value): int
    {
        $connection = $this->connection;
        $connection->insert(
            self::TABLE,
            [
                'title' => $value,
                'crdate' => time(),
                'userid' => $this->getBackendUser()->user['uid'],
            ]
        );

        return (int)$connection->lastInsertId();
    }

    public function cleanup(): void
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->delete(self::TABLE)
            ->where(
                $queryBuilder->expr()->lt('crdate', $queryBuilder->createNamedParameter(time() - 60 * 60 * 24 * 30, Connection::PARAM_INT))
            )
            ->executeStatement();
    }


    private function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }

}
