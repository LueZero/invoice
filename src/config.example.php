<?php

return [
    'ec' => [
        'invoiceParameters' => [
            'MerchantID' => 2000132,
            'HashKey' => 'ejCk326UnaZWKisg',
            'HashIV' => 'q9jcZX8Ib9LM8wYk'
        ],
        'B2B' => [
            'invoiceURLs' => [
                'baseURL' => 'https://einvoice-stage.ecpay.com.tw/B2BInvoice'
            ]
        ],
        'B2C' => [
            'invoiceURLs' => [
                'baseURL' => 'https://einvoice-stage.ecpay.com.tw/B2CInvoice',
                'issue' => '/Issue',
                'invalid' => '/Invalid',
                'allowanceInvalid' => '/AllowanceInvalid',
                'allowanceInvalidByCollegiate' => '/AllowanceInvalidByCollegiate',
                'allowance' => '/Allowance',
                'allowanceByCollegiate' => '/AllowanceByCollegiate',
                'getIssue' => '/GetIssue',
                'getAllowanceList' => '/GetAllowanceList',
                'getInvalid' => '/GetInvalid',
                'getAllowanceInvalid' => '/GetAllowanceInvalid',
                'getInvoiceWordSetting' => '/GetInvoiceWordSetting',
                'invoiceNotify' => '/InvoiceNotify',
                'invoicePrint' => '/InvoicePrint',
                'checkBarcode' => '/CheckBarcode',
                'checkLoveCode' => '/CheckLoveCode',
                'getCompanyNameByTaxID' => '/GetCompanyNameByTaxID'
            ]
        ]
    ]
];
