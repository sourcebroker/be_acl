<?php
namespace JBartels\BeAcl\Tests\Unit\Cache;

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Unit tests for the timestamp utility.
 */
class TimestampUtilityTest extends UnitTestCase {

	/**
	 * @var \JBartels\BeAcl\Cache\TimestampUtility
	 */
	protected $timestampUtility;

	/**
	 * Initializes the timestamp utility
	 */
	public function setUp() {
		$this->timestampUtility = $this->getMock('JBartels\\BeAcl\\Cache\\TimestampUtility', array('initializeCache'));
		$this->initializeTimestampCache();
	}

	/**
	 * @test
	 */
	public function newerTimestampThanInCacheIsInvalid(): void {
		$this->timestampUtility->updateTimestamp();
		$isValid = $this->timestampUtility->permissionTimestampIsValid(time() + 100);
		$this->assertTrue($isValid);
	}

	/**
	 * @test
	 */
	public function olderTimestampThanInCacheIsInvalid(): void {
		$this->timestampUtility->updateTimestamp();
		$isValid = $this->timestampUtility->permissionTimestampIsValid(time() - 100);
		$this->assertFalse($isValid);
	}

	/**
	 * Initializes the cache mock in the timestamp utility.
	 */
	protected function initializeTimestampCache() {
		/** @var \TYPO3\CMS\Core\Cache\Frontend\FrontendInterface $cacheMock */
		$cacheMock = $this->getMock('TYPO3\\CMS\\Core\\Cache\\Frontend\\FrontendInterface', array(), array(), '', FALSE);
		$this->timestampUtility->setTimestampCache($cacheMock);
	}
}