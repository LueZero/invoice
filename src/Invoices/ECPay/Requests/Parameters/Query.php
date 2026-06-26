<?php

namespace Zero\Invoices\ECPay\Requests\Parameters;

class Query extends Base
{
    /**
     * @var array
     */
    public $Data;

    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->Data = $data;
    }
}
