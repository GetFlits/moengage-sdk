<?php

namespace Flits\MoEngage;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7;
use Flits\MoEngage\MoEngageException;

class MoEngageProvider {
    public $BASE_URL = "https://api-0<DATA_CENTER>.moengage.com/<VERSION>/";
    public $APP_ID;
    public $HEADERS;
    public $VERSION = 'v1';
    public $DATA_CENTER;
    public $EXTRA_CONFIG;
    public $client;

    function __construct($config) {
        $this->APP_ID = $config['app_id'] ?? ''; // APP_ID from the moengage
        $this->HEADERS = $config['headers'] ?? []; // extra headers if you want to pass it in request
        $this->AUTH = $config['authorization'] ?? [];  // Authorization username and password
        $this->VERSION = $config['version'] ?? 'v1'; // version of the request as per moenagage
        $this->DATA_CENTER = $config['data_center'] ?? 1; // version of the request as per moenagage
        $this->EXTRA_CONFIG = $config['EXTRA_CONFIG'] ?? []; // Extra Guzzle/client config for api call
        $this->setupBaseURL();
        $this->setupClient();
    }

    function setupClient() {
        $config = [
            'base_uri' => $this->BASE_URL,
            'timeout' => 2.0,
            'auth' => $this->AUTH,
            'curl' => [
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
            ],
            'headers' => $this->HEADERS,
        ];
        $config = array_merge($config, $this->EXTRA_CONFIG);
        $this->client = new Client($config);
    }

    function setupBaseURL() {
        $this->setAPIVersion();
        $this->setAPIDataCenter();
    }

    function setAPIVersion() {
        $this->BASE_URL = str_replace('<VERSION>', $this->VERSION, $this->BASE_URL);
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