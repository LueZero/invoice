<?php

namespace Zero\Invoices\EcPay\Requests\Parameters;

class BarcodeData
{
    /**
     * @var string 
     * 特店編號
     */
    public $MerchantID;

    /**
     * @var string 
     * 手機條碼
     */
    public $BarCode;
}
