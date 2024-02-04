<?php declare(strict_types=1);

namespace OpenGSQ\Responses\Unreal2;

/**
 * Class Status
 *
 * Represents the status of a game server.
 */
class Status
{
    /**
     * @var int The server ID.
     */
    public int $serverId;

    /**
     * @var string The IP address of the server.
     */
    public string $serverIP;

    /**
     * @var int The game port of the server.
     */
    public int $gamePort;

    /**
     * @var int The query port of the server.
     */
    public int $queryPort;

    /**
     * @var string The name of the server.
     */
    public string $serverName;

    /**
     * @var string The name of the map.
     */
    public string $mapName;

    /**
     * @var string The type of the game.
     */
    public string $gameType;

    /**
     * @var int The number of players.
     */
    public int $numPlayers;

    /**
     * @var int The maximum number of players.
     */
    public int $maxPlayers;

    /**
     * @var int The ping.
     */
    public int $ping;

    /**
     * @var int The flags.
     */
    public int $flags;

    /**
     * @var string The skill level.
     */
    public string $skill;
}