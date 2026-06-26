<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);

require './vendor/autoload.php';

use Zero\InvoiceClient as InvoiceClient;
use Zero\InvoiceObject as InvoiceObject;
use Zero\Invoices\ECPay\Requests\Parameters\Issue;
use Zero\Invoices\ECPay\Requests\Parameters\Invalid;
use Zero\Invoices\ECPay\Requests\Parameters\AllowanceInvalid;
use Zero\Invoices\ECPay\Requests\Parameters\Allowance;
use Zero\Invoices\ECPay\Requests\Parameters\AllowanceByCollegiate;
use Zero\Invoices\ECPay\Requests\Parameters\Barcode;
use Zero\Invoices\ECPay\Requests\Parameters\LoveCode;
use Zero\Invoices\ECPay\Requests\Parameters\Company;

// 綠界 發票範例 - 開立發票
$relateNumber = 'ECInvoice' . date('YmdHis') . rand(1000, 0);
$invoiceClient = new InvoiceClient(InvoiceObject::INVOICE_NAME_EC, InvoiceObject::B2C);
$issue = new Issue();
$issue->MerchantID = '2000132';
$issue->RqHeader = ['Timestamp' => time()];
$issue->Data->MerchantID = '2000132';
$issue->Data->RelateNumber = $relateNumber;
$issue->Data->CustomerID = '';
$issue->Data->CustomerIdentifier = '';
$issue->Data->CustomerName = 'xxx有限公司';
$issue->Data->CustomerAddr = '106 台北市南港區發票一街 1 號 1 樓';
$issue->Data->CustomerPhone = '';
$issue->Data->CustomerEmail = 'test@ecpay.com.tw';
$issue->Data->ClearanceMark = 1;
$issue->Data->Print = '1';
$issue->Data->Donation = '0';
$issue->Data->LoveCode = '';
$issue->Data->CarrierType = '';
$issue->Data->CarrierNum = '';
$issue->Data->TaxType = '1';
$issue->Data->SpecialTaxType = 1;
$issue->Data->SalesAmount = 100;
$issue->Data->InvoiceRemark = '發票備註';
$issue->Data->InvType = '07';
$issue->Data->Vat = '1';
$issue->Data->Items = [
    [
        'ItemSeq' => 1,
        'ItemName' => 'item01',
        'ItemCount' => 1,
        'ItemWord' => '件',
        'ItemPrice' => 100,
        'ItemTaxType' => '1',
        'ItemAmount' => 100,
        'ItemRemark' => 'item01_desc'
    ]
];
$ecInvoice = $invoiceClient->createECInvoice();
// echo $ecInvoice->createIssue($issue);
// return;

// 綠界 發票範例 - 作廢發票
$invoiceClient = new InvoiceClient(InvoiceObject::INVOICE_NAME_EC, InvoiceObject::B2C);
$invalid = new Invalid();
$invalid->MerchantID = '2000132';
$invalid->RqHeader = ['Timestamp' => time()];
$invalid->Data->MerchantID = '2000132';
$invalid->Data->InvoiceNo = 'AQ90003550';
$invalid->Data->InvoiceDate = date('Y-m-d');
$invalid->Data->Reason = 'test';
$ecInvoice = $invoiceClient->createECInvoice();
// echo $ecInvoice->createInvalid($invalid);
// return;

// 綠界 發票範例 - 作廢折讓
$invoiceClient = new InvoiceClient(InvoiceObject::INVOICE_NAME_EC, InvoiceObject::B2C);
$allowanceInvalid = new AllowanceInvalid();
$allowanceInvalid->MerchantID = '2000132';
$allowanceInvalid->RqHeader = ['Timestamp' => time()];
$allowanceInvalid->Data->MerchantID = '2000132';
$allowanceInvalid->Data->InvoiceNo = 'AQ90003550';
$allowanceInvalid->Data->AllowanceNo = '2016022615195209';
$allowanceInvalid->Data->Reason = 'test';
$ecInvoice = $invoiceClient->createECInvoice();
// echo $ecInvoice->createAllowanceInvalid($allowanceInvalid);
// return;


// 綠界 發票範例 - 一般開立折讓（紙本開立）
$invoiceClient = new InvoiceClient(InvoiceObject::INVOICE_NAME_EC, InvoiceObject::B2C);
$allowance = new Allowance();
$allowance->MerchantID = '2000132';
$allowance->RqHeader = ['Timestamp' => time()];
$allowance->Data->MerchantID = '2000132';
$allowance->Data->InvoiceNo = 'AQ90003550';
$allowance->Data->InvoiceDate = '2019/09/17';
$allowance->Data->AllowanceNotify = 'E';
$allowance->Data->CustomerName = '綠界科技股份有限公司';
$allowance->Data->NotifyMail = 'test@ecpay.com.tw';
$allowance->Data->NotifyPhone = '0912345678';
$allowance->Data->AllowanceAmount = 50;
$allowance->Data->Items = [
    [
        'ItemSeq' => 1,
        'ItemName' => 'item01',
        'ItemCount' => 1,
        'ItemWord' => '件',
        'ItemPrice' => 100,
        'ItemTaxType' => '1',
        'ItemAmount' => 100,
        'ItemRemark' => 'item01_desc'
    ]
];
$ecInvoice = $invoiceClient->createECInvoice();
// echo $ecInvoice->createAllowance($allowance);
// return;

// 綠界 發票範例 - 線上開立折讓（通知開立）
$invoiceClient = new InvoiceClient(InvoiceObject::INVOICE_NAME_EC, InvoiceObject::B2C);
$allowanceByCollegiate = new AllowanceByCollegiate();
$allowanceByCollegiate->MerchantID = '2000132';
$allowanceByCollegiate->RqHeader = ['Timestamp' => time()];
$allowanceByCollegiate->Data->MerchantID = '2000132';
$allowanceByCollegiate->Data->InvoiceNo = 'AQ90003550';
$allowanceByCollegiate->Data->InvoiceDate = '2019/09/17';
$allowanceByCollegiate->Data->AllowanceNotify = 'E';
$allowanceByCollegiate->Data->CustomerName = '綠界科技股份有限公司';
$allowanceByCollegiate->Data->NotifyMail = 'test@ecpay.com.tw';
$allowanceByCollegiate->Data->NotifyPhone = '0912345678';
$allowanceByCollegiate->Data->AllowanceAmount = 50;
$allowanceByCollegiate->Data->Items = [
    [
        'ItemSeq' => 1,
        'ItemName' => 'item01',
        'ItemCount' => 1,
        'ItemWord' => '件',
        'ItemPrice' => 100,
        'ItemTaxType' => '1',
        'ItemAmount' => 100,
        'ItemRemark' => 'item01_desc'
    ]
];
$ecInvoice = $invoiceClient->createECInvoice();
// echo $ecInvoice->createAllowanceByCollegiate($allowance);
// return;

// 綠界 發票範例 - 手機條碼驗證
$invoiceClient = new InvoiceClient(InvoiceObject::INVOICE_NAME_EC, InvoiceObject::B2C);
$barcode = new Barcode();
$barcode->MerchantID = '2000132';
$barcode->RqHeader = ['Timestamp' => time()];
$barcode->Data->MerchantID = '2000132';
$barcode->Data->BarCode  = '/FRXEKUH';
$ecInvoice = $invoiceClient->createECInvoice();
// var_dump($ecInvoice->isBarcode($barcode));
// return;

// 綠界 發票範例 - 捐贈碼驗證
$invoiceClient = new InvoiceClient(InvoiceObject::INVOICE_NAME_EC, InvoiceObject::B2C);
$loveCode = new LoveCode();
$loveCode->MerchantID = '2000132';
$loveCode->RqHeader = ['Timestamp' => time()];
$loveCode->Data->MerchantID = '2000132';
$loveCode->Data->LoveCode  = '17527';
$ecInvoice = $invoiceClient->createECInvoice();
// var_dump($ecInvoice->isLoveCode($loveCode));
// return;

// 綠界 發票範例 - 統一編號驗證
$invoiceClient = new InvoiceClient(InvoiceObject::INVOICE_NAME_EC, InvoiceObject::B2C);
$company = new Company();
$company->MerchantID = '2000132';
$company->RqHeader = ['Timestamp' => time()];
$company->Data->MerchantID = '2000132';
$company->Data->UnifiedBusinessNo  = '97s025978';
$ecInvoice = $invoiceClient->createECInvoice();
// var_dump($ecInvoice->isCompanyNameByTaxId($company));
// return;