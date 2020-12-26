<?php namespace Kgrzelak\Cashbill;

abstract class WsRestAbstract {

    public function setTitle(string $title): WsRestAbstract {
        $this->payment_title = $title;
        return $this;
    }

    public function setAmount(float $amount): WsRestAbstract {
        $this->payment_amount = $amount;
        return $this;
    }

    public function setDescription(string $description): WsRestAbstract {
        $this->payment_description = $description;
        return $this;
    }

    public function setCurrency(string $amountCurrency): WsRestAbstract {
        $this->payment_amountCurrency = $amountCurrency;
        return $this;
    }

    public function setAdditionalData(string $additionalData): WsRestAbstract {
        $this->payment_additionalData = $additionalData;
        return $this;
    }

    public function setPersonal(array $personal): WsRestAbstract {
        $this->payment_personal = $personal;
        return $this;
    }

    public function setReturnUrl(string $returnUrl): WsRestAbstract {
        $this->payment_returnUrl = $returnUrl;
        return $this;
    }

    public function setNegativeReturnUrl(string $negativeReturnUrl): WsRestAbstract {
        $this->payment_negativeReturnUrl = $negativeReturnUrl;
        return $this;
    }

    public function setPaymentChannel(string $paymentChannel): WsRestAbstract {
        $this->payment_paymentChannel = $paymentChannel;
        return $this;
    }

    public function setLanguageCode(string $languageCode = 'PL'): WsRestAbstract {
        $this->payment_languageCode = $languageCode;
        return $this;
    }

    public function setReferer(string $referer): WsRestAbstract {
        $this->payment_referer = $referer;
        return $this;
    }

}
