<?php declare(strict_types=1);

use OpenGSQ\Protocols\Vcmp;
use OpenGSQ\Responses\Vcmp\Player;
use OpenGSQ\Responses\Vcmp\Status;
use PHPUnit\Framework\TestCase;

/**
 * Class VcmpTest
 *
 * This class contains test cases for the Vcmp protocol.
 */
final class VcmpTest extends TestCase
{
    /**
     * @var Vcmp The Vcmp instance to be tested.
     */
    public Vcmp $vcmp;

    /**
     * Sets up the test environment before each test.
     *
     * This method is called before each test. It creates a new Vcmp instance
     * that connects to a specific server.
     */
    protected function setUp(): void
    {
        $this->vcmp = new Vcmp('51.178.65.136', 8114);
    }

    /**
     * Test case for getting server status.
     *
     * This test case verifies the functionality of the getStatus method of the Vcmp class.
     * It asserts that the returned status object is an instance of the Status class.
     */
    public function testGetStatus(): void
    {
        $status = $this->vcmp->getStatus();

        $this->assertInstanceOf(Status::class, $status);
    }

    /**
     * Test case for getting player information.
     *
     * This test case verifies the functionality of the getPlayers method of the Vcmp class.
     * It asserts that the returned players information is an array and each player in the array
     * is an instance of the Player class.
     */
    public function testGetPlayers(): void
    {
        $players = $this->vcmp->getPlayers();

        $this->assertIsArray($players);

        foreach ($players as $player) {
            $this->assertInstanceOf(Player::class, $player);
        }
    }
}