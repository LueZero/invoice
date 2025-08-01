<?php

namespace Zero;

use Zero\InvoiceObject;
use Zero\Invoices\ECPay\ECB2CInvoice;

class InvoiceClient
{
    /**
     * @var string
     */
    public $invoiceName;

    /**
     * @var string
     */
    public $invoiceTypeName;

    /**
     * @param string invoiceName
     * @param string invoiceTypeName
     */
    public function __construct($invoiceName, $invoiceTypeName)
    {
        $this->invoiceName = $invoiceName;
        $this->invoiceTypeName = $invoiceTypeName;            
    }

    /**
     * 設定 EC 發票模組
     * @throws \Exception
     */
    public function createECInvoice()
    {
        $ecInvoice = null;

        if($this->invoiceName === InvoiceObject::INVOICE_NAME_EC)
        {
            switch ($this->invoiceTypeName) {
                case InvoiceObject::B2C:
                    $ecInvoice = new ECB2CInvoice();
                    break;
            }
        }

        if (is_null($ecInvoice))
            throw new \Exception('Zero\Invoice::[No ec invoice class]');

        return $ecInvoice;
    }
}
