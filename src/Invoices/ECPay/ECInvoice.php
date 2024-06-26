<?php

namespace Zero\Invoices\ECPay;

use Zero\Invoices\Invoice;
use Zero\Invoices\ECPay\Requests\Parameters\Base;
use WpOrg\Requests\Requests;

abstract class ECInvoice extends Invoice
{
    /**
     * @var string  
     * base URL
     */
    protected $baseURL;

    /**
     * @var string 
     * 特店編號
     */
    public $merchantID;

    /**
     * @var string
     */
    public $hashKey;

    /**
     * @var string
     */
    public $hashIv;

    /**
     * @override 
     * @param array configs
     */
    public function setConfig($configs)
    {
        $this->configs = $configs;
        $this->merchantID = empty($this->configs['invoiceParameters']['MerchantID']) == true ? null : $this->configs['invoiceParameters']['MerchantID'];
        $this->hashKey = empty($this->configs['invoiceParameters']['HashKey']) == true ? null : $this->configs['invoiceParameters']['HashKey'];
        $this->hashIv = empty($this->configs['invoiceParameters']['HashIV']) == true ? null : $this->configs['invoiceParameters']['HashIV'];
        $this->baseURL = $this->configs['B2C']['invoiceURLs']['baseURL'];
    }

    /**
     * @param Barcode barcode 
     * @return bool
     */
    public function isBarcode(Base $barcode)
    {
        $sendData = array_filter((array) $barcode);
        $sendData['Data'] = $this->encrypt($sendData['Data']);
        $response = Requests::post($this->configs['B2C']['invoiceURLs']['baseURL'] . $this->configs['B2C']['invoiceURLs']['checkBarcode'], [
            'Content-Type: application/json',
        ], json_encode($sendData));

        $result = json_decode($response->body, true);
        $result['Data'] = $this->decrypt($result['Data']);

        return $result['Data']['IsExist'] === 'Y';
    }

    /**
     * @param 
     * @return bool
     */
    public function isLoveCode(Base $loveCode)
    {
        $sendData = array_filter((array) $loveCode);
        $sendData['Data'] = $this->encrypt($sendData['Data']);
        $response = Requests::post($this->configs['B2C']['invoiceURLs']['baseURL'] . $this->configs['B2C']['invoiceURLs']['checkLoveCode'], [
            'Content-Type: application/json',
        ], json_encode($sendData));

        $result = json_decode($response->body, true);
        $result['Data'] = $this->decrypt($result['Data']);

        return $result['Data']['IsExist'] === 'Y';
    }

    /**
     * @param Company company
     * @return bool
     */
    public function isCompanyNameByTaxId(Base $company)
    {
        $sendData = array_filter((array) $company);
        $sendData['Data'] = $this->encrypt($sendData['Data']);
        $response = Requests::post($this->configs['B2C']['invoiceURLs']['baseURL'] . $this->configs['B2C']['invoiceURLs']['getCompanyNameByTaxID'], [
            'Content-Type: application/json',
        ], json_encode($sendData));

        $result = json_decode($response->body, true);
       
        $result['Data'] = $this->decrypt($result['Data']);

        return $result['Data']['RtnCode'] == '1';
    }

    public abstract function createIssue(Base $base);

    public abstract function createInvalid(Base $base);

    public abstract function createAllowanceInvalid(Base $base);

    public abstract function createAllowance(Base $base);

    public abstract function createAllowanceByCollegiate(Base $base);
}
