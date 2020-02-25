<?php


namespace App\Service;


use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class MediumService
{
    const ENDPOINT_GOOGLE_OAUTH_FLOW_START = 'google/oauth/flow';
    const ENDPOINT_GOOGLE_DRIVE_FILE_LIST = 'google/drive/file/list';
    const ENDPOINT_GOOGLE_DRIVE_FILE_GET = 'google/drive/file/get';
    const ENDPOINT_GOOGLE_SHARE_URI_CAPABILITIES = 'google/share/uri/capabilities';

    const HEADER_GOOGLE_REFRESH_TOKEN = 'Google-Refresh-Token';

    const SELF_ENDPOINT_GOOGLE_OAUTH_FLOW_ANSWER = 'medium/google/oauth/flow/answer';
    /**
     * @var string
     */
    private $igorBaseUrl;
    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var string
     */
    private $encryptionKey;
    /**
     * @var string
     */
    private $authToken;
    /**
     * @var int
     */
    private $userId;
    /**
     * @var HttpClientInterface
     */
    private $client;

    public function __construct(
        string $igorBaseUrl,
        string $baseUrl,
        int $userId,
        string $authToken,
        string $encryptionKey
    ) {
        $this->igorBaseUrl = $igorBaseUrl;
        $this->baseUrl = $baseUrl;
        $this->userId = $userId;
        $this->authToken = $authToken;
        $this->encryptionKey = $encryptionKey;
        // for client options see https://github.com/symfony/symfony/blob/5.0/src/Symfony/Contracts/HttpClient/HttpClientInterface.php
        $this->client = HttpClient::createForBaseUri($this->baseUrl, [
            'headers' => [
                'Accept' => 'application/json',
                'X-Auth' => $this->authToken,
            ],
            'verify_peer' => false,  // see https://php.net/context.ssl for the following options
            'verify_host' => false,
            'resolve' => [
                'medium-dev.unipart.io' => 'medium-dev.unipart.io',
            ],
        ]);
    }

    public function encrypt(string $subject): string {
        for ($i = 0; $i < strlen($subject); $i++) {
            $subject[$i] = ($subject[$i] ^ $this->encryptionKey[$i % strlen($this->encryptionKey)]);
        }
        return base64_encode($subject);
    }

    public function decrypt(string $subject): string {
        $subject = base64_decode($subject);
        for ($i = 0; $i < strlen($subject); $i++) {
            $subject[$i] = ($subject[$i] ^ $this->encryptionKey[$i % strlen($this->encryptionKey)]);
        }
        return $subject;
    }

    public function getGoogleOauthFlowAnswerUri(array $answerParams = []): string {
        $queryString = '?' . http_build_query($answerParams);
        return sprintf('%s%s%s', $this->igorBaseUrl, self::SELF_ENDPOINT_GOOGLE_OAUTH_FLOW_ANSWER, $queryString == '?' ? '' : $queryString);
    }

    public function getGoogleOauthFlowStartUri(array $answerParams = []): string {
        $queryString = '?' . http_build_query([
            'uri' => $this->encrypt($this->getGoogleOauthFlowAnswerUri($answerParams)),
        ]);
        return sprintf('%s%s/%d%s', $this->baseUrl, self::ENDPOINT_GOOGLE_OAUTH_FLOW_START, $this->userId, $queryString);
    }

    public function getDriveFileList(string $refreshToken): ResponseInterface {
        $response =  $this->client->request('POST', self::ENDPOINT_GOOGLE_DRIVE_FILE_LIST, [
            'headers' => [
                self::HEADER_GOOGLE_REFRESH_TOKEN => $refreshToken,
            ],
            'json' => [
                'pageSize' => 1,
                'q' => "name contains 'myro'",
                'pageToken' => "~!!~AI9FV7QjSrnrdILwu0Kjd9jslgH1WU6LYGg-aKEcnNaS2woD4Voe5hAt8GO9Ire80ZiXLku9bpS5EjNLbSTqmxtAfLw8BiXx5RwiVjlVrxw_mL8vkS5D42qJm28C_lf-U-8FWgv7JsMI7N2r7eHv4vu9173VS9Cnb1QqpUUKFjb0kud0hlqmMRcUEzMUmU-S0K_OwJTCHe3EfpOKfAkfUNDkhKnG1af6XvyGf-YCB-dn0tcL384usrMSnQ2P_sRVsXH9JFm7Fuw4EG0zff22gteHPxrvq7Ub_Tjb3aM6uU47mDS8sGUkjtSYkuq-tKP7nfKqOuXFW6WIVtD7-6C1_1A6dCXmxFnSBjE9cgku3zBz_3nzrryQ_faIKeFVmjtgvHcrbkzlRqrxjxOwWmJJsPUIU3Q3LtUxnpeB2tAVtolU_VDXe8raHIoBxhr3Ew_r7Af2Fihok50XJDKi1roGjQtGMan2DvY_VzliADlBFzpXVP2rJL3pa9CvkX1yIScU5cgQdsXFteTClM4bBuIsW-ELN_CAnNaFrw==",
            ],
        ]);
        return $response;
    }

    public function getDriveFileMetadata(string $refreshToken, string $fileId): ResponseInterface {
        $response =  $this->client->request('POST', self::ENDPOINT_GOOGLE_DRIVE_FILE_GET . '/' . $fileId, [
            'headers' => [
                self::HEADER_GOOGLE_REFRESH_TOKEN => $refreshToken,
            ],
            'json' => [],
            'query' => [],

        ]);
        return $response;
    }

    public function getDriveFile(string $refreshToken, string $fileId, string $mimeType): ResponseInterface {
        $response =  $this->client->request('POST', self::ENDPOINT_GOOGLE_DRIVE_FILE_GET, [
            'headers' => [
                self::HEADER_GOOGLE_REFRESH_TOKEN => $refreshToken,
            ],
            'json' => [
                'fileId' => $fileId,
                'mimeType' => $mimeType,
            ],
        ]);
        return $response;
    }

    public function getGoogleShareUriCapabilities(string $uri): ResponseInterface {
        $response =  $this->client->request('POST', self::ENDPOINT_GOOGLE_SHARE_URI_CAPABILITIES, [
            'json' => [
                'uri' => $uri,
            ],
        ]);
        return $response;
    }
}