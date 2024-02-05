<?php declare(strict_types=1);

use OpenGSQ\Protocols\KillingFloor;
use OpenGSQ\Responses\Unreal2\Player;
use OpenGSQ\Responses\Unreal2\Rules;
use OpenGSQ\Responses\KillingFloor\Status;
use PHPUnit\Framework\TestCase;

/**
 * Class KillingFloorTest
 *
 * This class contains unit tests for the KillingFloor class.
 */
final class KillingFloorTest extends TestCase
{
    use PhpDocs;

    /**
     * @var KillingFloor An instance of the KillingFloor class.
     */
    public KillingFloor $killingFloor;

    /**
     * Sets up the test environment before each test.
     */
    protected function setUp(): void
    {
        $this->killingFloor = new KillingFloor('normal.ws-gaming.eu', 7708);
    }

    /**
     * Tests the getDetails method of the KillingFloor class.
     */
    public function testGetDetails(): void
    {
        $status = $this->killingFloor->getDetails();

        $this->assertInstanceOf(Status::class, $status);
        $this->updateTestResult(__FILE__, __METHOD__, $status);
    }

    /**
     * Tests the getPlayers method of the KillingFloor class.
     */
    public function testGetPlayers(): void
    {
        $players = $this->killingFloor->getPlayers();

        $this->assertIsArray($players);

        foreach ($players as $player) {
            $this->assertInstanceOf(Player::class, $player);
        }

        $this->updateTestResult(__FILE__, __METHOD__, $players);
    }

    /**
     * Tests the getRules method of the KillingFloor class.
     */
    public function testGetRules(): void
    {
        $rules = $this->killingFloor->getRules();

        $this->assertInstanceOf(Rules::class, $rules);
        $this->updateTestResult(__FILE__, __METHOD__, $rules);
    }
}
