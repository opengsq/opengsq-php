<?php declare(strict_types=1);

namespace OpenGSQ\Responses\KillingFloor;

/**
 * Class Status
 *
 * Represents the status of a game server.
 */
class Status extends \OpenGSQ\Responses\Unreal2\Status
{
    /**
     * @var int $waveCurrent The current wave number in a game.
     */
    public int $waveCurrent;

    /**
     * @var int $waveTotal The total number of waves in a game.
     */
    public int $waveTotal;
}