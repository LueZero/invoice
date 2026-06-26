<?php

namespace Zero;

use Zero\Invoices\ECPay\ECB2CInvoice;

class InvoiceClient
{
    /**
     * @var array
     */
    protected static $invoiceClasses = [
        InvoiceObject::INVOICE_NAME_EC => [
            InvoiceObject::B2C => ECB2CInvoice::class,
        ],
    ];

    /**
     * @var string
     */
    public $invoiceName;

    /**
     * @var string
     */
    public $invoiceTypeName;

    /**
     * @param string $invoiceName
     * @param string $invoiceTypeName
     */
    public function __construct($invoiceName, $invoiceTypeName)
    {
        $this->invoiceName = $invoiceName;
        $this->invoiceTypeName = $invoiceTypeName;
    }

    /**
     * Register a provider/type invoice implementation.
     *
     * @param string $invoiceName
     * @param string $invoiceTypeName
     * @param string $className
     * @return void
     */
    public static function registerInvoice($invoiceName, $invoiceTypeName, $className)
    {
        static::$invoiceClasses[$invoiceName][$invoiceTypeName] = $className;
    }

    /**
     * Create an invoice implementation by provider and invoice type.
     *
     * @throws \Exception
     */
    public function createInvoice()
    {
        if (empty(static::$invoiceClasses[$this->invoiceName][$this->invoiceTypeName])) {
            throw new \Exception('Zero\Invoice::[No invoice class]');
        }

        $className = static::$invoiceClasses[$this->invoiceName][$this->invoiceTypeName];

        if (!class_exists($className)) {
            throw new \Exception('Zero\Invoice::[Invoice class does not exist]');
        }

        return new $className();
    }

    /**
     * Backward compatible ECPay factory method.
     *
     * @throws \Exception
     */
    public function createECInvoice()
    {
        return $this->createInvoice();
    }
}
