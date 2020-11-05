<?php namespace Kgrzelak\Cashbill;

class Html extends Components {

    const URL = 'https://pay.cashbill.pl/form/pay.php';

    public $components;

    protected $channelId;
    protected $channelKey;

    public function __construct(string $channelId, string $channelKey, bool $test = false) {

        $this->channelId = $channelId;
        $this->channelKey = $channelKey;

        $this->components = new Components(self::URL);

    }

    public function newPayment(
        string $title,
        float $amount,
        string $desc = null,
        string $currency = 'PLN',
        string $userdata = null,
        string $lang = 'PL',
        string $referer = null,
        array $personal = null,
        bool $getHtml = true
    ) {

        $data = [
            'service' => $this->channelId,
            'amount' => $amount,
            'currency' => $currency,
            'desc' => $desc,
            'lang' => $lang,
            'userdata' => $userdata,
            'ref' => $referer
        ];

        if (is_array($personal)) {
            foreach ($personal as $key => $value) {
                $data[$key] = $value;
            }
        }

        $data['sign'] = hash('md5', implode('|', $data) . '|' . $this->channelKey);

        if ($getHtml) {

            $html = '<form method="post" action="' . self::URL . '" id="cashbill_form">';
            foreach ($data as $key => $value) {
                $html .= '<input type="hidden" name="' . $key . '" value="' . $value . '">';
            }

            $html .= '<button type="submit">Przejdź do płatności</button>';

            $html .= '<script>document.getElementById("cashbill_form").submit();</script>';

            return $html;

        } else {
            return $data;
        }

    }

    public function parseResponse(array $data) {

        if (!isset($data['service'], $data['orderid'], $data['amount'], $data['userdata'], $data['status'], $data['sign'])) {
            $this->components->error = 'Missing required params';
            return false;
        }

        if (hash('md5', $data['service'] . $data['orderid'] . $data['amount'] . $data['userdata'] . $data['status'] . $this->channelKey) != $data['sign']) {
            $this->components->error = 'Bad sign';
            return false;
        }

        if ($data['service'] != $this->channelId) {
            $this->components->error = 'Bad service id';
            return false;
        }

        if (strtoupper($data['status']) != "OK") {
            $this->components->error = 'Bad transaction status';
            return false;
        }

        return $data;

    }

}