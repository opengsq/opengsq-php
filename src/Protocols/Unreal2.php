<?php declare(strict_types=1);

namespace OpenGSQ\Protocols;

use OpenGSQ\UdpClient;
use OpenGSQ\ProtocolBase;
use OpenGSQ\Responses\Unreal2\Player;
use OpenGSQ\Responses\Unreal2\Rules;
use OpenGSQ\Responses\Unreal2\Status;

use Exception;

/**
 * Class Unreal2
 *
 * This class provides methods to interact with the Unreal 2 Protocol.
 *
 * @package Protocols
 */
class Unreal2 extends ProtocolBase
{
    /**
     * @var string $fullname The full name of the protocol.
     */
    public string $fullname = 'Unreal 2 Protocol';

    /**
     * Represents the byte value for details.
     */
    protected const DETAILS = 0x00;

    /**
     * Represents the byte value for rules.
     */
    protected const RULES = 0x01;

    /**
     * Represents the byte value for players.
     */
    protected const PLAYERS = 0x02;

    /**
     * Constructs an Unreal2 instance.
     *
     * @param string $host The IP address or hostname of the server.
     * @param int $port The port number of the server.
     * @param int $timeout The timeout duration (in seconds) for server communication.
     */
    public function __construct(string $host, int $port, int $timeout = 5)
    {
        parent::__construct($host, $port, $timeout);
    }

    /**
     * Retrieves the server details.
     *
     * @param bool $stripColor Determines whether to remove color codes from the server details.
     * @return Status An object containing the server status.
     * @throws Exception If the received packet header is invalid.
     */
    public function getDetails(bool $stripColor = true): Status
    {
        $response = UdpClient::communicate($this, implode('', array_map('chr', [0x79, 0x00, 0x00, 0x00, self::DETAILS])));

        $header = ord(substr($response, 4, 5));
        if ($header !== self::DETAILS) {
            throw new Exception('Invalid packet header');
        }

        $response = substr($response, 5); // Remove the first 5 bytes

        $status = new Status();
        $status->serverId = $this->readUInt32($response);
        $status->serverIP = $this->readUnreal2String($response, $stripColor);
        $status->gamePort = $this->readUInt32($response);
        $status->queryPort = $this->readUInt32($response);
        $status->serverName = $this->readUnreal2String($response, $stripColor, false);
        $status->mapName = $this->readUnreal2String($response, $stripColor, false);
        $status->gameType = $this->readUnreal2String($response, $stripColor);
        $status->numPlayers = $this->readUInt32($response);
        $status->maxPlayers = $this->readUInt32($response);
        $status->ping = $this->readUInt32($response);
        $status->flags = $this->readUInt32($response);
        $status->skill = $this->readUnreal2String($response, $stripColor);

        return $status;
    }

    /**
     * Retrieves the server rules.
     *
     * @param bool $stripColor Determines whether to remove color codes from the server rules.
     * @return Rules An object containing the server rules.
     * @throws Exception If the received packet header is invalid.
     */
    public function getRules(bool $stripColor = true): Rules
    {
        $response = UdpClient::communicate($this, implode('', array_map('chr', [0x79, 0x00, 0x00, 0x00, self::RULES])));

        $header = ord($response[4]);
        if ($header !== self::RULES) {
            throw new Exception('Invalid packet header');
        }

        $response = substr($response, 5); // Remove the first 5 bytes

        $rules = new Rules();

        while (strlen($response) > 0) {
            $key = $this->readUnreal2String($response, $stripColor);
            $val = $this->readUnreal2String($response, $stripColor);

            if (strtolower($key) === 'mutator') {
                $rules->mutators[] = $val;
            } else {
                $rules[$key] = $val;
            }
        }

        return $rules;
    }

    /**
     * Retrieves the server players.
     *
     * @param bool $stripColor Determines whether to remove color codes from the players.
     * @return array<Player> An array of Player objects representing the server players.
     * @throws Exception If the received packet header is invalid.
     */
    public function getPlayers(bool $stripColor = true): array
    {
        $response = UdpClient::communicate($this, implode('', array_map('chr', [0x79, 0x00, 0x00, 0x00, self::PLAYERS])));

        $header = ord($response[4]);
        if ($header !== self::PLAYERS) {
            throw new Exception('Invalid packet header');
        }

        $response = substr($response, 5); // Remove the first 5 bytes

        $players = [];

        while (strlen($response) > 0) {
            $player = new Player();
            $player->id = $this->readUInt32($response);
            $player->name = $this->readUnreal2String($response, $stripColor);
            $player->ping = $this->readUInt32($response);
            $player->score = $this->readUInt32($response);
            $player->statsId = $this->readUInt32($response);
            $players[] = $player;
        }

        return $players;
    }

    /**
     * Strips color codes from the input text.
     *
     * @param string $text The input text potentially containing color codes.
     * @return string The input text with color codes removed.
     */
    public static function stripColor(string $text): string
    {
        return preg_replace('/\x1b...|[\x00-\x1a]/', '', $text);
    }

    /**
     * Reads a string from a BinaryReader, decodes it, and optionally strips color codes.
     *
     * @param string $response The response from the BinaryReader.
     * @param bool $stripColor Determines whether to remove color codes from the response.
     * @return string The decoded string. If $stripColor is true, the returned string will have color codes removed.
     */
    protected function readUnreal2String(string &$response, bool $stripColor, bool $pascal = true): string
    {
        $length = ord($response[0]);
        $response = substr($response, 1);

        if ($pascal) {
            if ($length >= 128) {
                $length = ($length & 0x7f) * 2;
                $bytes = substr($response, 0, $length);
                $result = mb_convert_encoding($bytes, 'UTF-16LE', 'UTF-8');
            } else {
                $bytes = substr($response, 0, $length);
                $result = $bytes;
            }

            $response = substr($response, $length);
        } else {
            $result = $this->readNullString($response);
        }

        return $stripColor ? self::stripColor($result) : trim($result);
    }
}
