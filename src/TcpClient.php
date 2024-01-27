<?php declare(strict_types=1);

namespace OpenGSQ;

/**
 * Class TcpClient
 *
 * This class provides a method to communicate with a server using TCP protocol.
 */
class TcpClient
{
    /**
     * Sends data to a server and returns the response.
     *
     * @param ProtocolBase $protocolBase The protocol information.
     * @param string $data The data to send.
     * @return string The server's response.
     *
     * @throws \Exception If the operation fails or times out.
     */
    public static function communicate(ProtocolBase $protocolBase, string $data)
    {
        try {
            $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            if ($socket === false) {
                throw new \Exception("Failed to create socket: " . socket_strerror(socket_last_error()));
            }

            socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array("sec" => $protocolBase->timeout, "usec" => 0));
            socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, array("sec" => $protocolBase->timeout, "usec" => 0));

            $result = socket_connect($socket, $protocolBase->host, $protocolBase->port);
            if ($result === false) {
                throw new \Exception("Failed to connect: " . socket_strerror(socket_last_error($socket)));
            }

            $result = socket_write($socket, $data, strlen($data));
            if ($result === false) {
                throw new \Exception("Failed to write data: " . socket_strerror(socket_last_error($socket)));
            }

            $response = socket_read($socket, 2048);
            if ($response === false) {
                throw new \Exception("Failed to read response: " . socket_strerror(socket_last_error($socket)));
            }
        } finally {
            socket_close($socket);
        }

        return $response;
    }
}
