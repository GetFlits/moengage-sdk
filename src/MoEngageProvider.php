<?php

namespace Flits\MoEngage;

use GuzzleHttp\Client;
use App\Utility;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7;
use App\Classes\Integration\MoEngage\MoEngageException;

class MoEngageProvider {
    public $BASE_URL = "https://api-0<DATA_CENTER>.moengage.com";
    public $APP_ID;
    public $HEADERS;
    public $VERSION = 'v1';
    public $DATA_CENTER;

    public $client;

    function __construct($config) {
        $this->APP_ID = $config['app_id'] ?? ''; // APP_ID from the moengage
        $this->HEADERS = $config['headers'] ?? []; // extra headers if you want to pass it in request
        $this->AUTH = $config['authorization'] ?? [];  // Authorization username and password
        $this->VERSION = $config['version'] ?? 'v1'; // version of the request as per moenagage
        $this->DATA_CENTER = $config['data_center'] ?? 1; // version of the request as per moenagage
        $this->setupBaseURL();
        $this->client = new Client([
            'base_uri' => $this->BASE_URL,
            'timeout' => 2.0,
            'auth' => $this->AUTH,
            'curl' => [
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
            ],
            'headers' => $this->HEADERS,
            'debug' => true
        ]);
    }
    function setupBaseURL() {
        $this->setAPIVersion();
        $this->setAPIDataCenter();
    }

    function setAPIVersion() {
        $this->BASE_URL .= "/" . $this->VERSION . "/";
    }

    function setAPIDataCenter() {
        $this->BASE_URL = str_replace('<DATA_CENTER>', $this->DATA_CENTER, $this->BASE_URL);
    }

    function setupURL() {
        $this->URL = str_replace('<APP_ID>', $this->APP_ID, $this->URL);
    }

    function POST($payload) {
        try {
            $this->setupURL();
            $response = $this->client->request($this->METHOD, $this->URL, [
                'json' => $payload
            ]);
        } catch (RequestException $ex) {
            throw new MoEngageException($ex->getResponse()->getBody()->getContents(), $ex->getResponse()->getStatusCode());
        }
        if ($response->getStatusCode() != 200) {
            throw new MoEngageException($response->getBody()->getContents(), $response->getStatusCode());
        }
        return json_decode($response->getBody()->getContents());
    }
}