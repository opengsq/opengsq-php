<?php declare(strict_types=1);

namespace OpenGSQ\Protocols;

use OpenGSQ\Responses\Vcmp\Status;
use OpenGSQ\Responses\Vcmp\Player;
use OpenGSQ\UdpClient;
use OpenGSQ\ProtocolBase;
use OpenGSQ\Exceptions\InvalidPacketException;

/**
 * Class Vcmp
 *
 * The Vcmp class extends the ProtocolBase class and implements the Vice City Multiplayer Protocol.
 *
 * @package Protocols
 */
class Vcmp extends ProtocolBase
{
    /**
     * @var string $fullname The full name of the protocol.
     */
    public string $fullname = 'Vice City Multiplayer Protocol';

    /**
     * @var string $requestHeader The request header for the protocol.
     */
    protected string $requestHeader = "VCMP";

    /**
     * @var string $responseHeader The response header for the protocol.
     */
    protected string $responseHeader = "MP04";

    /**
     * Vcmp constructor.
     *
     * @param string $host The host of the server.
     * @param int $port The port of the server.
     * @param int $timeout The timeout for the request.
     */
    public function __construct(string $host, int $port, int $timeout = 5)
    {
        parent::__construct($host, $port, $timeout);
    }

    /**
     * Get the status of the server.
     *
     * @return Status The status of the server.
     */
    public function getStatus(): Status
    {
        // Get the response from the server
        $response = $this->getResponse('i');

        // Define the offset for reading the response
        $offset = 17;

        // Create a new Status object
        $status = new Status();
        // Parse the response and set the properties of the Status object
        $status->version = trim(substr($response, 0, 12));
        $status->password = ord($response[12]) == 1;
        $status->numPlayers = unpack("s", substr($response, 13, 2))[1];
        $status->maxPlayers = unpack("s", substr($response, 15, 2))[1];
        $status->serverName = $this->readString($response, $offset, 4);
        $status->gameType = $this->readString($response, $offset, 4);
        $status->language = $this->readString($response, $offset, 4);

        // Return the Status object
        return $status;
    }

    /**
     * Get the players on the server.
     *
     * @return array<Player> An array of Player objects representing the players on the server.
     */
    public function getPlayers(): array
    {
        // Get the response from the server
        $response = $this->getResponse('c');

        // Get the number of players from the response
        $numplayers = unpack("s", substr($response, 0, 2))[1];

        // Initialize an array to hold the Player objects
        /** @var array<Player> */
        $players = array();

        // Define the offset for reading the response
        $offset = 2;

        // Loop through the response and create a Player object for each player
        for ($i = 0; $i < $numplayers; $i++) {
            $player = new Player();
            $player->name = $this->readString($response, $offset);
            $players[] = $player;
        }

        // Return the array of Player objects
        return $players;
    }

    /**
     * Send a request to the server and get the response.
     *
     * @param string $data The data to send with the request.
     * @return string The response from the server.
     * @throws InvalidPacketException If the response is not valid.
     */
    protected function getResponse(string $data): string
    {
        // Format the address
        $host = gethostbyname($this->host);
        $hostArray = array_merge(array_map('intval', explode('.', $host)), [$this->port]);
        $packetHeader = pack('CCCCn', ...$hostArray) . $data;
        $request = $this->requestHeader . $packetHeader;

        // Send the request and get the response
        $response = UdpClient::communicate($this, $request);
        // Get the header from the response
        $header = substr($response, 0, strlen($this->responseHeader));
        // Throw an exception if the header is not valid
        InvalidPacketException::throwIfNotEqual($header, $this->responseHeader);

        // Trim the header from the response
        $response = substr($response, strlen($this->responseHeader) + strlen($packetHeader));

        // Return the response
        return $response;
    }

    /**
     * Read a string from the response.
     *
     * @param string $response The response to read from.
     * @param int $offset The offset to start reading from.
     * @param int $readOffset The number of bytes to read for the length of the string.
     * @return string The string read from the response.
     */
    protected function readString(string $response, int &$offset, int $readOffset = 1): string
    {
        // Define the format for unpacking the length of the string
        $format = $readOffset == 4 ? "L" : "C";

        // Get the length of the string
        $length = unpack($format, substr($response, $offset, $readOffset))[1];
        $offset += $readOffset;

        // Get the string from the response
        $string = substr($response, $offset, $length);
        $offset += $length;

        // Return the string
        return $string;
    }
}
