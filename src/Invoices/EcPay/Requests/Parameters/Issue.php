<?php

namespace Zero\Invoices\EcPay\Requests\Parameters;

use Zero\Invoices\EcPay\Requests\Parameters\Base;
use Zero\Invoices\EcPay\Requests\Parameters\IssueData;

class Issue extends Base
{
    /**
     * @var class 
     * 加密資料
     */
    public IssueData $Data;

    public function __construct()
    {
        $this->Data = new IssueData();
    }
}
