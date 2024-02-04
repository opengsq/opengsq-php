<?php declare(strict_types=1);

use OpenGSQ\Protocols\Unreal2;
use OpenGSQ\Responses\Unreal2\Player;
use OpenGSQ\Responses\Unreal2\Rules;
use OpenGSQ\Responses\Unreal2\Status;
use PHPUnit\Framework\TestCase;

/**
 * Class Unreal2Test
 *
 * This class contains unit tests for the Unreal2 class.
 */
final class Unreal2Test extends TestCase
{
    use PhpDocs;

    /**
     * @var Unreal2 An instance of the Unreal2 class.
     */
    public Unreal2 $unreal2;

    /**
     * Sets up the test environment before each test.
     */
    protected function setUp(): void
    {
        $this->unreal2 = new Unreal2('51.195.117.236', 9981);
    }

    /**
     * Tests the getDetails method of the Unreal2 class.
     */
    public function testGetDetails(): void
    {
        $status = $this->unreal2->getDetails();

        $this->assertInstanceOf(Status::class, $status);
        $this->updateTestResult(__FILE__, __METHOD__, $status);
    }

    /**
     * Tests the getPlayers method of the Unreal2 class.
     */
    public function testGetPlayers(): void
    {
        $players = $this->unreal2->getPlayers();

        $this->assertIsArray($players);

        foreach ($players as $player) {
            $this->assertInstanceOf(Player::class, $player);
        }

        $this->updateTestResult(__FILE__, __METHOD__, $players);
    }

    /**
     * Tests the getRules method of the Unreal2 class.
     */
    public function testGetRules(): void
    {
        $rules = $this->unreal2->getRules();

        $this->assertInstanceOf(Rules::class, $rules);
        $this->updateTestResult(__FILE__, __METHOD__, $rules);
    }
}
