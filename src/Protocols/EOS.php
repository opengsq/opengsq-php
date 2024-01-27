<?php declare(strict_types=1);

namespace OpenGSQ\Protocols;

use OpenGSQ\ProtocolBase;
use OpenGSQ\Responses\EOS\Matchmaking;

/**
 * Class EOS
 *
 * This class is used to interact with the Epic Online Services (EOS) platform.
 *
 * @package Protocols
 */
class EOS extends ProtocolBase
{
    /**
     * @var string $fullname The full name of the protocol.
     */
    public string $fullname = 'Epic Online Services (EOS) Protocol';

    /**
     * The base URL for the Epic Games API.
     */
    private static string $apiUrl = 'https://api.epicgames.dev';

    /**
     * The deployment ID for the application.
     */
    private string $deploymentId;

    /**
     * The access token for the application.
     */
    private string $accessToken;

    /**
     * EOS constructor.
     * @param string $host The host name of the server.
     * @param int $port The port number of the server.
     * @param string $deploymentId The deployment ID for the application.
     * @param string $accessToken The access token for the application.
     * @param int $timeout The timeout value for the connection, in seconds. Default is 5.
     * @throws \Exception Thrown when either deploymentId or accessToken is null.
     */
    public function __construct(string $host, int $port, string $deploymentId, string $accessToken, int $timeout = 5)
    {
        parent::__construct($host, $port, $timeout);
        $this->deploymentId = $deploymentId;
        $this->accessToken = $accessToken;
    }

    /**
     * Gets an access token.
     * @param string $clientId The client ID for the application.
     * @param string $clientSecret The client secret for the application.
     * @param string $deploymentId The deployment ID for the application.
     * @param string $grantType The type of grant being requested.
     * @param string $externalAuthType The type of external authentication being used.
     * @param string $externalAuthToken The token for the external authentication.
     * @return string The access token.
     * @throws \Exception Thrown when the access token cannot be retrieved.
     */
    public static function getAccessToken(string $clientId, string $clientSecret, string $deploymentId, string $grantType, string $externalAuthType, string $externalAuthToken): string
    {
        $url = self::$apiUrl . '/auth/v1/oauth/token';

        $values = array(
            'grant_type' => $grantType,
            'external_auth_type' => $externalAuthType,
            'external_auth_token' => $externalAuthToken,
            'nonce' => 'opengsq',
            'deployment_id' => $deploymentId,
            'display_name' => 'User',
        );

        $queryString = http_build_query($values);

        $authInfo = base64_encode($clientId . ':' . $clientSecret);

        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\nAuthorization: Basic {$authInfo}",
                'method' => 'POST',
                'content' => $queryString,
            ),
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        if ($result === FALSE) {
            throw new \Exception("Failed to get access token from {$url}");
        }

        $data = json_decode($result, true);

        if (!isset($data['access_token'])) {
            throw new \Exception("Failed to get access token from {$url}");
        }

        return $data['access_token'];
    }

    /**
     * Retrieves an external authentication token.
     * @param string $clientId The client ID for the application.
     * @param string $clientSecret The client secret for the application.
     * @param string $externalAuthType The type of external authentication being used.
     * @return string The access token.
     * @throws \Exception Thrown when either clientId or clientSecret is null.
     * @throws \Exception Thrown when the provided externalAuthType hasn't been implemented yet.
     */
    public static function getExternalAuthToken(string $clientId, string $clientSecret, string $externalAuthType): string
    {
        if ($externalAuthType == 'deviceid_access_token') {
            $url = self::$apiUrl . '/auth/v1/accounts/deviceid';

            $values = array(
                'deviceModel' => 'PC',
            );

            $queryString = http_build_query($values);

            $authInfo = base64_encode($clientId . ':' . $clientSecret);

            $options = array(
                'http' => array(
                    'header' => "Content-type: application/x-www-form-urlencoded\r\nAuthorization: Basic {$authInfo}",
                    'method' => 'POST',
                    'content' => $queryString,
                ),
            );
            $context = stream_context_create($options);
            $result = file_get_contents($url, false, $context);

            if ($result === FALSE) {
                throw new \Exception("Failed to get access token from {$url}");
            }

            $data = json_decode($result, true);

            if (!isset($data['access_token'])) {
                throw new \Exception("Failed to get access token from {$url}");
            }

            return $data['access_token'];
        }

        throw new \Exception("The external authentication type '{$externalAuthType}' is not supported. Please provide a supported authentication type.");
    }

    /**
     * Retrieves matchmaking data.
     *
     * @param string $deploymentId The deployment ID for the application.
     * @param string $accessToken The access token.
     * @param array $filter An optional filter for the matchmaking data.
     *
     * @return Matchmaking The matchmaking data.
     *
     * @throws \Exception Thrown when the matchmaking data cannot be retrieved.
     */
    public static function getMatchmaking(string $deploymentId, string $accessToken, array $filter = array("" => "")): Matchmaking
    {
        $url = self::$apiUrl . "/matchmaking/v1/{$deploymentId}/filter";

        $options = array(
            'http' => array(
                'header' => "Content-type: application/json\r\nAuthorization: Bearer {$accessToken}",
                'method' => 'POST',
                'content' => json_encode($filter),
            ),
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        if ($result === FALSE) {
            throw new \Exception("Failed to load data from {$url}");
        }

        $responseData = json_decode($result, true);
        $responseData ?? throw new \Exception("Failed to load data from {$url}");

        $matchmaking = new Matchmaking();
        $matchmaking->sessions = $responseData['sessions'];
        $matchmaking->count = $responseData['count'];

        return $matchmaking;
    }

    /**
     * Retrieves the information about the game server.
     *
     * @return array<string, mixed> An array that contains the server information.
     *
     * @throws \Exception Thrown when the server is not found.
     * @throws \Exception Thrown when there is a failure in getting the access token.
     */
    public function getInfo(): array
    {
        $address = $this->getIPAddress();
        $addressBoundPort = ":" . $this->port;

        $data = self::getMatchmaking(
            $this->deploymentId,
            $this->accessToken,
            array(
                'criteria' => array(
                    array(
                        'key' => 'attributes.ADDRESS_s',
                        'op' => 'EQUAL',
                        'value' => $address
                    ),
                    array(
                        'key' => 'attributes.ADDRESSBOUND_s',
                        'op' => 'CONTAINS',
                        'value' => $addressBoundPort
                    ),
                )
            )
        );

        if ($data->count <= 0) {
            throw new \Exception("Server with address {$address} and port {$this->port} was not found.");
        }

        return reset($data->sessions);
    }
}
