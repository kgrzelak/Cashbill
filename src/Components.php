<?php namespace Kgrzelak\Cashbill;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Utils;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\TooManyRedirectsException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Exception\InvalidArgumentException;
use GuzzleHttp\Exception\BadResponseException;

class Components {

    protected $error;
    protected $errorCode;

    protected $url;

    public function __construct($url) {

        $this->url = $url;

        $this->client = new Client();
    }

    public function request(string $method, string $uri, array $body = null) {

        $this->client = new Client([
            'base_uri' => $this->url,
            'timeout'  => 3.0
        ]);

        try {

            $response = $this->client->request($method, $uri, [
                'form_params' => $body,
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF8'
                ]
            ]);

            $object = Utils::jsonDecode($response->getBody()->getContents());
            if ($object) {
                return $object;
            } else {
                return false;
            }

        //} catch(RequestException | ClientException | ConnectException | ServerException | TooManyRedirectsException | TransferException | BadResponseException $e) {
        } catch(ConnectException $e) {

            $this->error = Psr7\str($e->getRequest());
            $this->errorCode = $e->getCode();

            return false;

        } catch(GuzzleException $e) {

            if ($e->hasResponse()) {
                $this->error = Psr7\str($e->getResponse());
                $this->errorCode = $e->getCode();
            } else {
                $this->error = Psr7\str($e->getRequest());
                $this->errorCode = $e->getCode();
            }

            return false;

        }

    }

    public function getError() {
        return $this->error;
    }

    public function getErrorCode() {
        return $this->errorCode;
    }

}