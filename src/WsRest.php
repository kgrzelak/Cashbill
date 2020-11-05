<?php namespace Kgrzelak\Cashbill;

class WsRest extends Components {

    const URL = 'https://pay.cashbill.pl/ws/rest/';
    const TESTURL = 'https://pay.cashbill.pl/testws/rest/';

    public $components;

    protected $channelId;
    protected $channelKey;

    public function __construct(string $channelId, string $channelKey, bool $test = false) {

        $this->channelId = $channelId;
        $this->channelKey = $channelKey;

        $this->components = new Components($test ? self::TESTURL : self::URL);

    }

    public function newPayment(
        string $title,
        float $amountValue,
        string $description = null,
        string $amountCurrency = 'PLN',
        string $additionalData = null,
        string $returnUrl = null,
        string $negativeReturnUrl = null,
        int $paymentChannel = null,
        string $languageCode = 'PL',
        array $personal = null,
        string $referer = null
    ) {
        
        $data = [
            'title' => $title,
            'amount.value' => $amountValue,
            'amount.currencyCode' => $amountCurrency,
            'description' => $description,
            'additionalData' => $additionalData,
            'returnUrl' => $returnUrl,
            'negativeReturnUrl' => $negativeReturnUrl,
            'paymentChannel' => $paymentChannel,
            'languageCode' => $languageCode,
            'referer' => $referer
        ];

        if (is_array($personal)) {
            foreach ($personal as $key => $value) {
                $data['personalData.' . $key] = $value;
            }
        }

        $data['sign'] = hash('sha1', implode('', $data) . $this->channelKey);

        return $this->components->request('POST', 'payment/' . $this->channelId, $data);

    }

    public function setRedirect(string $paymentId, string $returnUrl, string $negativeReturnUrl) {
        
        $data = [
            'returnUrl' => $returnUrl,
            'negativeReturnUrl' => $negativeReturnUrl
        ];

        $data['sign'] = hash('sha1', implode('', $data) . $this->channelKey);

        return $this->components->request('PUT', 'payment/' . $this->channelId . '/' . $paymentId, $data);

    }

    public function getChannels(string $lang = 'pl') {

        return $this->components->request('GET', 'paymentchannels/' . $this->channelId . '/' . $lang);

    }

    public function getPayment(string $paymentId) {

        $sign = hash('sha1', $paymentId . $this->channelKey);

        return $this->components->request('GET', 'payment/' . $this->channelId . '/' . $paymentId . '?sign=' . $sign);

    }

}