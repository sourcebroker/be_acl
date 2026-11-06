<?php

declare(strict_types=1);

namespace JBartels\BeAcl\Tests\Unit\Cache;

use JBartels\BeAcl\Cache\TimestampUtility;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Cache\Backend\TransientMemoryBackend;
use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Unit tests for the timestamp utility.
 */
final class TimestampUtilityTest extends UnitTestCase
{
    // The cache frontend and backend create the LogManager singleton.
    protected bool $resetSingletonInstances = true;

    protected TimestampUtility $timestampUtility;

    protected function setUp(): void
    {
        parent::setUp();
        $this->timestampUtility = new TimestampUtility();
        $this->timestampUtility->setTimestampCache(
            new VariableFrontend('tx_be_acl_timestamp', new TransientMemoryBackend())
        );
    }

    #[Test]
    public function timestampNewerThanTheLastPermissionChangeIsValid(): void
    {
        $this->timestampUtility->updateTimestamp();

        self::assertTrue($this->timestampUtility->permissionTimestampIsValid(time() + 100));
    }

    #[Test]
    public function timestampOlderThanTheLastPermissionChangeIsInvalid(): void
    {
        $this->timestampUtility->updateTimestamp();

        self::assertFalse($this->timestampUtility->permissionTimestampIsValid(time() - 100));
    }
}
