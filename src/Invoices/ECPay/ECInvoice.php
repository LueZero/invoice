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
        $result = $this->post($this->configs['B2C']['invoiceURLs']['checkBarcode'], $barcode);

        return $result['Data']['IsExist'] === 'Y';
    }

    /**
     * @param 
     * @return bool
     */
    public function isLoveCode(Base $loveCode)
    {
        $result = $this->post($this->configs['B2C']['invoiceURLs']['checkLoveCode'], $loveCode);

        return $result['Data']['IsExist'] === 'Y';
    }

    /**
     * @param Company company
     * @return bool
     */
    public function isCompanyNameByTaxId(Base $company)
    {
        $result = $this->post($this->configs['B2C']['invoiceURLs']['getCompanyNameByTaxID'], $company);

        return $result['Data']['RtnCode'] == '1';
    }

    /**
     * Send an encrypted ECPay JSON request and decrypt the response data.
     *
     * @param string $path
     * @param Base $base
     * @return array
     */
    protected function post($path, Base $base)
    {
        $payload = $this->buildPayload($base);
        $response = Requests::post($this->baseURL . $path, [
            'Content-Type' => 'application/json',
        ], json_encode($payload));

        return $this->decodeResponse($response->body);
    }

    /**
     * @param Base $base
     * @return array
     */
    protected function buildPayload(Base $base)
    {
        $payload = $this->toArray($base);
        $payload['MerchantID'] = empty($payload['MerchantID']) ? $this->merchantID : $payload['MerchantID'];
        $payload['RqHeader'] = empty($payload['RqHeader']) ? ['Timestamp' => time()] : $payload['RqHeader'];

        if (!array_key_exists('Data', $payload)) {
            $payload['Data'] = [];
        }

        if (!is_array($payload['Data'])) {
            throw new \InvalidArgumentException('ECPay request Data must be an array or object.');
        }

        $payload['Data']['MerchantID'] = empty($payload['Data']['MerchantID']) ? $payload['MerchantID'] : $payload['Data']['MerchantID'];
        $payload['Data'] = $this->encrypt($payload['Data']);

        return $this->filterNullValues($payload);
    }

    /**
     * @param string $body
     * @return array
     */
    protected function decodeResponse($body)
    {
        $result = json_decode($body, true);

        if (!is_array($result)) {
            throw new \UnexpectedValueException('ECPay response is not valid JSON.');
        }

        if (!empty($result['Data'])) {
            $result['Data'] = $this->decrypt($result['Data']);
        }

        return $result;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    protected function toArray($value)
    {
        if (is_object($value)) {
            $value = get_object_vars($value);
        }

        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = $this->toArray($item);
            }
        }

        return $this->filterNullValues($value);
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    protected function filterNullValues($value)
    {
        if (!is_array($value)) {
            return $value;
        }

        foreach ($value as $key => $item) {
            if ($item === null) {
                unset($value[$key]);
            }
        }

        return $value;
    }

    public abstract function createIssue(Base $base);

    public abstract function createInvalid(Base $base);

    public abstract function createAllowanceInvalid(Base $base);

    public abstract function createAllowance(Base $base);

    public abstract function createAllowanceByCollegiate(Base $base);

    public abstract function createAllowanceInvalidByCollegiate(Base $base);

    public abstract function getIssue(Base $base);

    public abstract function getInvalid(Base $base);

    public abstract function getAllowanceList(Base $base);

    public abstract function getAllowanceInvalid(Base $base);

    public abstract function getInvoiceWordSetting(Base $base);
}
