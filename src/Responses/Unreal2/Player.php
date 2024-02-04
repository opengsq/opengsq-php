<?php declare(strict_types=1);

namespace OpenGSQ\Responses\Unreal2;

/**
 * Class Player
 */
class Player
{
    /**
     * @var int The ID of the player.
     */
    public int $id;

    /**
     * @var string The name of the player.
     */
    public string $name = '';

    /**
     * @var int The ping of the player.
     */
    public int $ping;

    /**
     * @var int The score of the player.
     */
    public int $score;

    /**
     * @var int The stats ID of the player.
     */
    public int $statsId;
}