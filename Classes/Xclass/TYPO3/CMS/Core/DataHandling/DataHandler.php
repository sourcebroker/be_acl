<?php
declare(strict_types=1);

namespace JBartels\BeAcl\Xclass\TYPO3\CMS\Core\DataHandling;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\DataHandling\DataHandler as BaseDataHandler;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Core\Type\VirtualRecord;

/**
 * This is a workaround for the issue occurring in TYPO3 v13.
 * Because of permission changes inside TYPO3\CMS\Core\DataHandling\DataHandler it is not possible to edit records.
 * There is also no way to hook into \TYPO3\CMS\Core\DataHandling\DataHandler::hasPagePermission and that is why
 * this xclass was required.
 *
 * Since TYPO3 13.4.33 the core calls hasPageContextPermission() everywhere, hasPagePermission() is only a
 * deprecated b/w compatible wrapper. Therefore both methods have to be extended.
 */
class DataHandler extends BaseDataHandler
{
    public function hasPagePermission(int $perms, array $page, bool $useDeleteClause = true): bool
    {
        if (parent::hasPagePermission($perms, $page, $useDeleteClause)) {
            return true;
        }

        return $this->isGrantedByAcl($perms, $page);
    }

    public function hasPageContextPermission(string $table, int $perms, array|VirtualRecord $page, bool $useDeleteClause = true): bool
    {
        if (parent::hasPageContextPermission($table, $perms, $page, $useDeleteClause)) {
            return true;
        }
        if (!is_array($page)) {
            // VirtualRecord (e.g. root level) is not covered by page ACLs.
            return false;
        }

        return $this->isGrantedByAcl($perms, $page);
    }

    protected function isGrantedByAcl(int $perms, array $page): bool
    {
        if ($page === [] || !($GLOBALS['BE_USER'] ?? null) instanceof BackendUserAuthentication) {
            return false;
        }

        return (new Permission($GLOBALS['BE_USER']->calcPerms($page)))->isGranted($perms);
    }
}
