<?php declare(strict_types=1);

namespace OpenGSQ\Protocols;

use OpenGSQ\UdpClient;
use OpenGSQ\Responses\KillingFloor\Status;

use Exception;

/**
 * Class KillingFloor
 *
 * This class provides methods to interact with the Killing Floor Protocol.
 *
 * @package Protocols
 */
class KillingFloor extends Unreal2
{
    /**
     * @var string $fullname The full name of the protocol.
     */
    public string $fullname = "Killing Floor Protocol";

    /**
     * Constructs an KillingFloor instance.
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
     * @return Status The details of the server.
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
        $status->waveCurrent = $this->readUInt32($response);
        $status->waveTotal = $this->readUInt32($response);
        $status->ping = $this->readUInt32($response);
        $status->flags = $this->readUInt32($response);
        $status->skill = $this->readUnreal2String($response, $stripColor);

        return $status;
    }
}
