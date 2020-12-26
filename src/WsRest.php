<?php namespace Kgrzelak\Cashbill;

class WsRest extends WsRestAbstract {

    const URL = 'https://pay.cashbill.pl/ws/rest/';
    const TESTURL = 'https://pay.cashbill.pl/testws/rest/';

    public Components $components;

    protected string $channelId;
    protected string $channelKey;

    protected string $payment_title;
    protected float $payment_amount;
    protected ?string $payment_description = null;
    protected string $payment_amountCurrency = 'PLN';
    protected ?string $payment_additionalData = null;
    protected ?array $payment_personal = null;
    protected ?string $payment_returnUrl = null;
    protected ?string $payment_negativeReturnUrl = null;
    protected ?string $payment_paymentChannel = null;
    protected string $payment_languageCode = 'PLN';
    protected ?string $payment_referer= NULL;

    public function __construct(string $channelId, string $channelKey, bool $test = false) {

        $this->channelId = $channelId;
        $this->channelKey = $channelKey;

        $this->components = new Components($test ? self::TESTURL : self::URL);

    }

    public function makePayment() {

        $data = [
            'title' => $this->payment_title,
            'amount.value' => $this->payment_amount,
            'amount.currencyCode' => $this->payment_amountCurrency,
            'description' => $this->payment_description,
            'additionalData' => $this->payment_additionalData,
            'returnUrl' => $this->payment_returnUrl,
            'negativeReturnUrl' => $this->payment_negativeReturnUrl,
            'paymentChannel' => $this->payment_paymentChannel,
            'languageCode' => $this->payment_languageCode,
            'referer' => $this->payment_referer
        ];

        if (is_array($this->payment_personal)) {
            foreach ($this->payment_personal as $key => $value) {
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
