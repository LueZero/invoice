<?php

namespace Zero\Invoices\EcPay\Requests\Parameters;

use Zero\Invoices\EcPay\Requests\Parameters\Base;
use Zero\Invoices\EcPay\Requests\Parameters\BarcodeData;

class Barcode extends Base
{
    /**
     * @var class 
     * 加密資料
     */
    public BarcodeData $Data;
    
    public function __construct()
    {
        $this->Data = new BarcodeData();
    }
}
