<?php declare(strict_types=1);

namespace OpenGSQ\Responses\Vcmp;

/**
 * Class Status
 *
 * Represents the status of a game server.
 *
 * @package OpenGSQ\Responses\Vcmp
 */
class Status
{
    /**
     * @var string The version of the server.
     */
    public string $version;

    /**
     * @var bool Indicates whether a password is required to connect to the server.
     */
    public bool $password;

    /**
     * @var int The number of players currently connected to the server.
     */
    public int $numPlayers;

    /**
     * @var int The maximum number of players that can connect to the server.
     */
    public int $maxPlayers;

    /**
     * @var string The name of the server.
     */
    public string $serverName;

    /**
     * @var string The type of game being played on the server.
     */
    public string $gameType;

    /**
     * @var string The language of the server.
     */
    public string $language;
}
