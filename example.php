<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Zero\InvoiceClient;
use Zero\InvoiceObject;
use Zero\Invoices\ECPay\Requests\Parameters\Allowance;
use Zero\Invoices\ECPay\Requests\Parameters\Barcode;
use Zero\Invoices\ECPay\Requests\Parameters\Company;
use Zero\Invoices\ECPay\Requests\Parameters\Invalid;
use Zero\Invoices\ECPay\Requests\Parameters\Issue;
use Zero\Invoices\ECPay\Requests\Parameters\LoveCode;
use Zero\Invoices\ECPay\Requests\Parameters\Query;

$client = new InvoiceClient(InvoiceObject::INVOICE_NAME_EC, InvoiceObject::B2C);
$invoice = $client->createInvoice();

$issue = new Issue();
$issue->Data->RelateNumber = 'EC' . date('YmdHis');
$issue->Data->CustomerID = '';
$issue->Data->CustomerIdentifier = '';
$issue->Data->CustomerName = 'Test Customer';
$issue->Data->CustomerAddr = 'No. 1, Test Rd., Taipei City';
$issue->Data->CustomerPhone = '';
$issue->Data->CustomerEmail = 'test@example.com';
$issue->Data->ClearanceMark = '';
$issue->Data->Print = '1';
$issue->Data->Donation = '0';
$issue->Data->LoveCode = '';
$issue->Data->CarrierType = '';
$issue->Data->CarrierNum = '';
$issue->Data->TaxType = '1';
$issue->Data->SpecialTaxType = '';
$issue->Data->SalesAmount = 100;
$issue->Data->InvoiceRemark = 'Example invoice';
$issue->Data->InvType = '07';
$issue->Data->Vat = '1';
$issue->Data->Items = [
    [
        'ItemSeq' => 1,
        'ItemName' => 'Example item',
        'ItemCount' => 1,
        'ItemWord' => 'pcs',
        'ItemPrice' => 100,
        'ItemTaxType' => '1',
        'ItemAmount' => 100,
        'ItemRemark' => '',
    ],
];

$invalid = new Invalid();
$invalid->Data->InvoiceNo = 'AA00000000';
$invalid->Data->InvoiceDate = date('Y-m-d');
$invalid->Data->Reason = 'Example invalid reason';

$allowance = new Allowance();
$allowance->Data->InvoiceNo = 'AA00000000';
$allowance->Data->InvoiceDate = date('Y/m/d');
$allowance->Data->AllowanceNotify = 'E';
$allowance->Data->CustomerName = 'Test Customer';
$allowance->Data->NotifyMail = 'test@example.com';
$allowance->Data->NotifyPhone = '0912345678';
$allowance->Data->AllowanceAmount = 50;
$allowance->Data->Items = [
    [
        'ItemSeq' => 1,
        'ItemName' => 'Example item',
        'ItemCount' => 1,
        'ItemWord' => 'pcs',
        'ItemPrice' => 50,
        'ItemTaxType' => '1',
        'ItemAmount' => 50,
    ],
];

$barcode = new Barcode();
$barcode->Data->BarCode = '/FRXEKUH';

$loveCode = new LoveCode();
$loveCode->Data->LoveCode = '17527';

$company = new Company();
$company->Data->UnifiedBusinessNo = '24549210';

$query = new Query([
    'RelateNumber' => $issue->Data->RelateNumber,
]);

// Uncomment one call at a time after src/config.php has been configured.
// $result = $invoice->createIssue($issue);
// $result = $invoice->createInvalid($invalid);
// $result = $invoice->createAllowance($allowance);
// $result = $invoice->getIssue($query);
// $result = $invoice->isBarcode($barcode);
// $result = $invoice->isLoveCode($loveCode);
// $result = $invoice->isCompanyNameByTaxId($company);
// print_r($result);
