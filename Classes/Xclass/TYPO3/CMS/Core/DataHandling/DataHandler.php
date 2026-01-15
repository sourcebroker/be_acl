<?php
declare(strict_types=1);

namespace JBartels\BeAcl\Xclass\TYPO3\CMS\Core\DataHandling;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\DataHandling\DataHandler as BaseDataHandler;
use TYPO3\CMS\Core\Type\Bitmask\Permission;

/**
 * This is a workaround for the issue occurring in TYPO3 v13.
 * Because of permission changes inside TYPO3\CMS\Core\DataHandling\DataHandler it is not possible to edit records.
 * There is also no way to hook into \TYPO3\CMS\Core\DataHandling\DataHandler::hasPagePermission and that is why
 * this xclass was required.
 */
class DataHandler extends BaseDataHandler
{
    public function hasPagePermission(int $perms, array $page, bool $useDeleteClause = true): bool
    {
        $baseResult = parent::hasPagePermission($perms, $page, $useDeleteClause);

        if ($baseResult) {
            return true;
        }

        if (!$GLOBALS['BE_USER'] instanceof BackendUserAuthentication) {
            return false;
        }

        return (new Permission($GLOBALS['BE_USER']->calcPerms($page)))->isGranted($perms);
    }
}
