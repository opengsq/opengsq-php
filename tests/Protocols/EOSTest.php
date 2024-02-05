<?php declare(strict_types=1);

use OpenGSQ\Protocols\EOS;
use OpenGSQ\Responses\EOS\Matchmaking;
use PHPUnit\Framework\TestCase;

/**
 * Class EOSTest
 *
 * This class contains test cases for the EOS protocol.
 */
final class EOSTest extends TestCase
{
    use PhpDocs;

    /**
     * Test case for getting matchmaking information.
     *
     * This test case verifies the functionality of the getMatchmaking method of the EOS class.
     * It asserts that the returned matchmaking object is an instance of the Matchmaking class.
     */
    public function testGetMatchmaking(): void
    {
        // Palworld
        $clientId = 'xyza78916PZ5DF0fAahu4tnrKKyFpqRE';
        $clientSecret = 'j0NapLEPm3R3EOrlQiM8cRLKq3Rt02ZVVwT0SkZstSg';
        $deploymentId = '0a18471f93d448e2a1f60e47e03d3413';
        $grantType = 'external_auth';
        $externalAuthType = 'deviceid_access_token';
        $externalAuthToken = EOS::getExternalAuthToken($clientId, $clientSecret, $externalAuthType);
        $accessToken = EOS::getAccessToken($clientId, $clientSecret, $deploymentId, $grantType, $externalAuthType, $externalAuthToken);

        $matchmaking = EOS::getMatchmaking($deploymentId, $accessToken);
        $this->assertInstanceOf(Matchmaking::class, $matchmaking);
        $this->updateTestResult(__FILE__, __METHOD__, $matchmaking);
    }

    /**
     * Test case for getting server information.
     *
     * This test case verifies the functionality of the getInfo method of the EOS class.
     * It asserts that the returned information is an array and contains the key 'deployment'.
     * It also checks that the 'deployment' key's value matches the expected deployment ID.
     */
    public function testGetInfo(): void
    {
        // Ark: Survival Ascended
        $clientId = 'xyza7891muomRmynIIHaJB9COBKkwj6n';
        $clientSecret = 'PP5UGxysEieNfSrEicaD1N2Bb3TdXuD7xHYcsdUHZ7s';
        $deploymentId = 'ad9a8feffb3b4b2ca315546f038c3ae2';
        $grantType = 'client_credentials';
        $externalAuthType = '';
        $externalAuthToken = '';
        $accessToken = EOS::getAccessToken($clientId, $clientSecret, $deploymentId, $grantType, $externalAuthType, $externalAuthToken);

        $eos = new EOS('5.62.115.46', 7783, $deploymentId, $accessToken, 5000);
        $info = $eos->getInfo();

        $this->assertIsArray($info);
        $this->assertArrayHasKey('deployment', $info);
        $this->assertEquals($deploymentId, $info['deployment']);
        $this->updateTestResult(__FILE__, __METHOD__, $info);
    }

    public function testGetInfoPalWorld(): void
    {
        // Palworld
        $clientId = 'xyza78916PZ5DF0fAahu4tnrKKyFpqRE';
        $clientSecret = 'j0NapLEPm3R3EOrlQiM8cRLKq3Rt02ZVVwT0SkZstSg';
        $deploymentId = '0a18471f93d448e2a1f60e47e03d3413';
        $grantType = 'external_auth';
        $externalAuthType = 'deviceid_access_token';
        $externalAuthToken = EOS::getExternalAuthToken($clientId, $clientSecret, $externalAuthType);
        $accessToken = EOS::getAccessToken($clientId, $clientSecret, $deploymentId, $grantType, $externalAuthType, $externalAuthToken);

        $eos = new EOS('34.79.63.74', 30010, $deploymentId, $accessToken, 5000);
        $info = $eos->getInfo();

        $this->assertIsArray($info);
        $this->assertArrayHasKey('deployment', $info);
        $this->assertEquals($deploymentId, $info['deployment']);
        $this->updateTestResult(__FILE__, __METHOD__, $info);
    }
}