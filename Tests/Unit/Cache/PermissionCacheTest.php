<?php
namespace JBartels\BeAcl\Tests\Unit\Cache;

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Cache\Backend\TransientMemoryBackend;
use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend;
/**
 * Tests for the permission cache.
 */
class PermissionCacheTest extends UnitTestCase {

	/**
	 * @var \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
	 */
	protected $backendUser;

	/**
	 * @var \TYPO3\CMS\Core\Cache\Frontend\FrontendInterface|\PHPUnit_Framework_MockObject_MockObject
	 */
	protected $cacheMock;

	/**
	 * @var \JBartels\BeAcl\Cache\PermissionCache|\PHPUnit_Framework_MockObject_MockObject
	 */
	protected $permissionCache;

	/**
	 * The key used for the test cache entry
	 *
	 * @var string
	 */
	protected $permissionsClauseCacheKey = 'testCachekey';

	/**
	 * The value used for the test cache entry
	 *
	 * @var string
	 */
	protected $permissionsClauseCacheValue = 'testCacheValue';

	/**
	 * Initializes the permission cache
	 */
	public function setUp() {

		/** @var \TYPO3\CMS\Core\Authentication\BackendUserAuthentication $backendUser */
		$this->backendUser = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Authentication\\BackendUserAuthentication');
	}

	/**
	 * @test
	 */
	public function flushingCacheInvalidatesPreviouslySetFirstLevelCache(): void {
		$this->initializePermissionCacheMock(array('initializeRequiredClasses'));

		/** @var \JBartels\BeAcl\Cache\TimestampUtility|\PHPUnit_Framework_MockObject_MockObject $timestampUtility */
		$timestampUtility = $this->getMock('JBartels\\BeAcl\\Cache\\TimestampUtility', array('updateTimestamp', 'permissionTimestampIsValid'));
		$timestampUtility->expects($this->once())->method('updateTimestamp');
		$timestampUtility->expects($this->once())->method('permissionTimestampIsValid')->will($this->returnValue(FALSE));
		$this->permissionCache->setTimestampUtility($timestampUtility);

		$this->permissionCache->setPermissionsClause($this->permissionsClauseCacheKey, $this->permissionsClauseCacheValue);
		$this->permissionCache->flushCache();
		$cachedValue = $this->permissionCache->getPermissionsClause($this->permissionsClauseCacheKey);
		$this->assertNull($cachedValue);
	}

	/**
	 * @test
	 */
	public function flushingCacheInvalidatesPreviouslySetSecondLevelCache(): void {

		$this->initializePermissionCacheMock(array('initializeRequiredClasses'));
		$this->permissionCache->disableFirstLevelCache();

		/** @var \JBartels\BeAcl\Cache\TimestampUtility|\PHPUnit_Framework_MockObject_MockObject $timestampUtility */
		$timestampUtility = $this->getMock('JBartels\\BeAcl\\Cache\\TimestampUtility', array('updateTimestamp', 'permissionTimestampIsValid'));
		$timestampUtility->expects($this->once())->method('updateTimestamp');
		$timestampUtility->expects($this->once())->method('permissionTimestampIsValid')->will($this->returnValue(FALSE));
		$this->permissionCache->setTimestampUtility($timestampUtility);

		$this->permissionCache->setPermissionsClause($this->permissionsClauseCacheKey, $this->permissionsClauseCacheValue);
		$this->permissionCache->flushCache();
		$cachedValue = $this->permissionCache->getPermissionsClause($this->permissionsClauseCacheKey);
		$this->assertNull($cachedValue);
	}

	/**
	 * @test
	 */
	public function previouslySetCacheValueIsReturnedByFirstLevelCache(): void {
		$this->initializePermissionCacheMock(array('initializeRequiredClasses'));
		$this->permissionCache->setPermissionsClause($this->permissionsClauseCacheKey, $this->permissionsClauseCacheValue);
		$cachedValue = $this->permissionCache->getPermissionsClause($this->permissionsClauseCacheKey);
		$this->assertEquals($this->permissionsClauseCacheValue, $cachedValue);
	}

	/**
	 * @test
	 */
	public function previouslySetCacheValueIsReturnedBySecondLevelCache(): void {

		$this->initializePermissionCacheMock(array('initializeRequiredClasses'));

		/** @var \JBartels\BeAcl\Cache\TimestampUtility|\PHPUnit_Framework_MockObject_MockObject $timestampUtility */
		$timestampUtility = $this->getMock('JBartels\\BeAcl\\Cache\\TimestampUtility', array('permissionTimestampIsValid'));
		$timestampUtility->expects($this->once())->method('permissionTimestampIsValid')->will($this->returnValue(TRUE));
		$this->permissionCache->setTimestampUtility($timestampUtility);

		$this->permissionCache->disableFirstLevelCache();
		$this->permissionCache->setPermissionsClause($this->permissionsClauseCacheKey, $this->permissionsClauseCacheValue);
		$cachedValue = $this->permissionCache->getPermissionsClause($this->permissionsClauseCacheKey);
		$this->assertEquals($this->permissionsClauseCacheValue, $cachedValue);
	}

	/**
	 * @param array $mockedMethods
	 */
	protected function initializePermissionCacheMock($mockedMethods) {

		/** @var \JBartels\BeAcl\Cache\PermissionCache $permissionCache */
		$permissionCache = $this->getMock('JBartels\\BeAcl\\Cache\\PermissionCache', $mockedMethods);
		$permissionCache->setBackendUser($this->backendUser);

		$cacheBackend = new TransientMemoryBackend('Testing');
		$cacheFrontend = new VariableFrontend('tx_be_acl_permissions', $cacheBackend);

		$permissionCache->setPermissionCache($cacheFrontend);

		$this->permissionCache = $permissionCache;
	}
}