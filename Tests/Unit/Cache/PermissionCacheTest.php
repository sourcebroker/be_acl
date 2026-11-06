<?php

declare(strict_types=1);

namespace JBartels\BeAcl\Tests\Unit\Cache;

use JBartels\BeAcl\Cache\PermissionCache;
use JBartels\BeAcl\Cache\TimestampUtility;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Cache\Backend\TransientMemoryBackend;
use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Tests for the permission cache.
 */
final class PermissionCacheTest extends UnitTestCase
{
    private const PERMISSIONS_CLAUSE_KEY = 'testCachekey';
    private const PERMISSIONS_CLAUSE_VALUE = 'testCacheValue';

    // The cache frontend and backend create the LogManager singleton.
    protected bool $resetSingletonInstances = true;

    protected PermissionCache $permissionCache;

    protected function setUp(): void
    {
        parent::setUp();
        $backendUser = $this->createStub(BackendUserAuthentication::class);
        $backendUser->user = ['uid' => 1, 'usergroup' => '1', 'workspace_id' => 0];

        // The real classes come from the cache manager; the tests set their own caches and utilities.
        $this->permissionCache = new class () extends PermissionCache {
            protected function initializeRequiredClasses() {}
        };
        $this->permissionCache->setBackendUser($backendUser);
        $this->permissionCache->setPermissionCache(
            new VariableFrontend('tx_be_acl_permissions', new TransientMemoryBackend())
        );
    }

    #[Test]
    public function flushingCacheInvalidatesPreviouslySetFirstLevelCache(): void
    {
        $this->permissionCache->setTimestampUtility($this->timestampUtilityExpectingFlush());

        $this->permissionCache->setPermissionsClause(self::PERMISSIONS_CLAUSE_KEY, self::PERMISSIONS_CLAUSE_VALUE);
        $this->permissionCache->flushCache();

        self::assertNull($this->permissionCache->getPermissionsClause(self::PERMISSIONS_CLAUSE_KEY));
    }

    #[Test]
    public function flushingCacheInvalidatesPreviouslySetSecondLevelCache(): void
    {
        $this->permissionCache->disableFirstLevelCache();
        $this->permissionCache->setTimestampUtility($this->timestampUtilityExpectingFlush());

        $this->permissionCache->setPermissionsClause(self::PERMISSIONS_CLAUSE_KEY, self::PERMISSIONS_CLAUSE_VALUE);
        $this->permissionCache->flushCache();

        self::assertNull($this->permissionCache->getPermissionsClause(self::PERMISSIONS_CLAUSE_KEY));
    }

    #[Test]
    public function previouslySetCacheValueIsReturnedByFirstLevelCache(): void
    {
        $this->permissionCache->setPermissionsClause(self::PERMISSIONS_CLAUSE_KEY, self::PERMISSIONS_CLAUSE_VALUE);

        self::assertSame(
            self::PERMISSIONS_CLAUSE_VALUE,
            $this->permissionCache->getPermissionsClause(self::PERMISSIONS_CLAUSE_KEY)
        );
    }

    #[Test]
    public function previouslySetCacheValueIsReturnedBySecondLevelCache(): void
    {
        $timestampUtility = $this->getMockBuilder(TimestampUtility::class)
            ->onlyMethods(['permissionTimestampIsValid'])
            ->getMock();
        $timestampUtility->expects($this->once())->method('permissionTimestampIsValid')->willReturn(true);
        $this->permissionCache->setTimestampUtility($timestampUtility);
        $this->permissionCache->disableFirstLevelCache();

        $this->permissionCache->setPermissionsClause(self::PERMISSIONS_CLAUSE_KEY, self::PERMISSIONS_CLAUSE_VALUE);

        self::assertSame(
            self::PERMISSIONS_CLAUSE_VALUE,
            $this->permissionCache->getPermissionsClause(self::PERMISSIONS_CLAUSE_KEY)
        );
    }

    private function timestampUtilityExpectingFlush(): TimestampUtility&MockObject
    {
        $timestampUtility = $this->getMockBuilder(TimestampUtility::class)
            ->onlyMethods(['updateTimestamp', 'permissionTimestampIsValid'])
            ->getMock();
        $timestampUtility->expects($this->once())->method('updateTimestamp');
        $timestampUtility->expects($this->once())->method('permissionTimestampIsValid')->willReturn(false);

        return $timestampUtility;
    }
}
