<?php declare(strict_types=1);

namespace OpenGSQ;

/**
 * Class ProtocolBase
 *
 * This abstract class serves as a base for all protocol classes. It provides basic properties and methods
 * that are common to all protocols.
 */
abstract class ProtocolBase
{
    /**
     * @var string The host to connect to.
     */
    public string $host;

    /**
     * @var int The port to connect to.
     */
    public int $port;

    /**
     * @var int The timeout for the connection in seconds.
     */
    public int $timeout;

    /**
     * Initializes a new instance of the ProtocolBase class.
     *
     * @param string $host The host to connect to.
     * @param int $port The port to connect to.
     * @param int $timeout The timeout for the connection in seconds. Default is 5 seconds.
     */
    public function __construct(string $host, int $port, int $timeout = 5)
    {
        $this->host = $host;
        $this->port = $port;
        $this->timeout = $timeout;
    }

    /**
     * Gets the Internet Protocol (IP) endpoint.
     *
     * @return string The IP address of the host.
     */
    protected function getIPAddress(): string
    {
        if (filter_var($this->host, FILTER_VALIDATE_IP)) {
            return $this->host;
        } else {
            return gethostbyname($this->host);
        }
    }
}
