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
 * This class is used to interact with the Unreal 2 Protocol.
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
     * @param string $host The server host.
     * @param int $port The server port.
     * @param int $timeout The timeout for server communication.
     */
    public function __construct(string $host, int $port, int $timeout = 5)
    {
        parent::__construct($host, $port, $timeout);
    }

    /**
     * Retrieves the server details.
     *
     * @param bool $stripColor Whether to strip color codes from the server details.
     * @return Status The server status.
     * @throws Exception If an invalid packet header is received.
     */
    public function getDetails(bool $stripColor = false): Status
    {
        $response = UdpClient::communicate($this, implode('', array_map('chr', [0x79, 0x00, 0x00, 0x00, self::DETAILS])));

        $header = ord(substr($response, 4, 5));
        if ($header !== self::DETAILS) {
            throw new Exception('Invalid packet header');
        }

        $response = substr($response, 5); // Remove the first 5 bytes

        $status = new Status();
        $status->serverId = $this->readUInt32($response);
        $status->serverIP = $this->readString($response);
        $status->gamePort = $this->readUInt32($response);
        $status->queryPort = $this->readUInt32($response);
        $status->serverName = $this->readString($response, $stripColor);
        $status->mapName = $this->readString($response);
        $status->gameType = $this->readString($response);
        $status->numPlayers = $this->readUInt32($response);
        $status->maxPlayers = $this->readUInt32($response);
        $status->ping = $this->readUInt32($response);
        $status->flags = $this->readUInt32($response);
        $status->skill = $this->readString($response);

        return $status;
    }

    /**
     * Retrieves the server rules.
     *
     * @return Rules The server rules.
     * @throws Exception If an invalid packet header is received.
     */
    public function getRules(): Rules
    {
        $response = UdpClient::communicate($this, implode('', array_map('chr', [0x79, 0x00, 0x00, 0x00, self::RULES])));

        $header = ord($response[4]);
        if ($header !== self::RULES) {
            throw new Exception('Invalid packet header');
        }

        $response = substr($response, 5); // Remove the first 5 bytes

        $rules = new Rules();

        while (strlen($response) > 0) {
            $key = $this->readString($response);
            $val = $this->readString($response);

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
     * @return array<Player> The server players.
     * @throws Exception If an invalid packet header is received.
     */
    public function getPlayers(): array
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
            $player->name = $this->readString($response);
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
     * @param bool $stripColor Optional parameter indicating whether to strip color codes from the response.
     * @return string The decoded string. If $stripColor is true, the returned string will have color codes removed.
     */
    protected function readString(string &$response, bool $stripColor = false): string
    {
        $length = ord($response[0]);
        $response = substr($response, 1);

        if ($length >= 128) {
            $length = ($length & 0x7f) * 2;
            $bytes = substr($response, 0, $length);
            $result = mb_convert_encoding($bytes, 'UTF-16LE', 'UTF-8');
        } else {
            $bytes = substr($response, 0, $length);
            $result = $bytes;
        }

        $response = substr($response, $length);

        if (strlen($response) > 0 && ord(substr($response, 0, 1)) === 0) {
            $response = substr($response, 1);
        }

        return $stripColor ? self::stripColor($result) : trim($result);
    }
}
