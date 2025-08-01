<?php

namespace Zero\Invoices\ECPay;

use Zero\Invoices\ECPay\ECInvoice;
use Zero\Invoices\ECPay\Requests\Parameters\Base;
use WpOrg\Requests\Requests;

class ECB2CInvoice extends ECInvoice
{
    /**
     * @var string  
     * issue URL
     */
    private $issueURL;

    /**
     * @var string  
     * invalid URL
     */
    private $invalidURL;

    /**
     * @var string  
     * allowanceInvalid URL
     */
    private $allowanceInvalidURL;

    /**
     * @var string  
     * allowance URL
     */
    private $allowanceURL;

    /**
     * @var string  
     * allowanceByCollegiate URL
     */
    private $allowanceByCollegiateURL;

    /**
     * @param array configs 
     */
    public function __construct()
    {
        $this->requireConfig('ec');
        $this->issueURL = $this->configs['B2C']['invoiceURLs']['issue'];
        $this->invalidURL = $this->configs['B2C']['invoiceURLs']['invalid'];
        $this->allowanceInvalidURL = $this->configs['B2C']['invoiceURLs']['allowanceInvalid'];
        $this->allowanceURL = $this->configs['B2C']['invoiceURLs']['allowance'];
        $this->allowanceByCollegiateURL = $this->configs['B2C']['invoiceURLs']['allowanceByCollegiate'];
    }

    /**
     * @param Issue issue 
     * @return string
     */
    public function createIssue(Base $issue)
    {
        $sendData = array_filter((array) $issue);
        $sendData['Data'] = $this->encrypt($sendData['Data']);
        $response = Requests::post($this->baseURL . $this->issueURL, [
            'Content-Type: application/json',
        ], json_encode($sendData));

        $result = json_decode($response->body, true);
        $result['Data'] = $this->decrypt($result['Data']);

        return json_encode($result, true);
    }

    /**
     * @param Issue invalid 
     * @return string
     */
    public function createInvalid(Base $invalid)
    {
        $sendData = array_filter((array) $invalid);
        $sendData['Data'] = $this->encrypt($sendData['Data']);
        $response = Requests::post($this->baseURL . $invalid, [
            'Content-Type: application/json',
        ], json_encode($sendData));

        $result = json_decode($response->body, true);
        $result['Data'] = $this->decrypt($result['Data']);

        return json_encode($result, true);
    }

    /**
     * @param AllowanceInvalid allowanceInvalid 
     * @return string
     */
    public function createAllowanceInvalid(Base $allowanceInvalid)
    {
        $sendData = array_filter((array) $allowanceInvalid);
        $sendData['Data'] = $this->encrypt($sendData['Data']);
        $response = Requests::post($this->baseURL . $this->allowanceInvalidURL, [
            'Content-Type: application/json',
        ], json_encode($sendData));

        $result = json_decode($response->body, true);
        $result['Data'] = $this->decrypt($result['Data']);

        return json_encode($result, true);
    }

    /**
     * @param Allowance allowance 
     * @return string
     */
    public function createAllowance(Base $allowance)
    {
        $sendData = array_filter((array) $allowance);
        $sendData['Data'] = $this->encrypt($sendData['Data']);
        $response = Requests::post($this->baseURL . $this->allowanceURL, [
            'Content-Type: application/json',
        ], json_encode($sendData));

        $result = json_decode($response->body, true);
        $result['Data'] = $this->decrypt($result['Data']);

        return json_encode($result, true);
    }

    /**
     * @param AllowanceByCollegiate allowanceByCollegiate 
     * @return string
     */
    public function createAllowanceByCollegiate(Base $allowanceByCollegiate)
    {
        $sendData = array_filter((array) $allowanceByCollegiate);
        $sendData['Data'] = $this->encrypt($sendData['Data']);
        $response = Requests::post($this->baseURL . $this->allowanceByCollegiateURL, [
            'Content-Type: application/json',
        ], json_encode($sendData));

        $result = json_decode($response->body, true);
        $result['Data'] = $this->decrypt($result['Data']);

        return json_encode($result, true);
    }

    /**
     * 加密
     * @param string data
     * @throws \Exception
     */
    public function encrypt($data)
    {
        if (openssl_cipher_iv_length('aes-128-cbc') !== strlen($this->hashIv)) {
            throw new \LogicException('Hash iv is not valid');
        }

        return openssl_encrypt($this->addPadding(urlencode(json_encode($data))), 'aes-128-cbc', $this->hashKey, OPENSSL_ZERO_PADDING, $this->hashIv);
    }

    /**
     * 解密
     * @param string encrypted
     * @throws \Exception
     */
    public function decrypt($encrypted)
    {
        if (openssl_cipher_iv_length('aes-128-cbc') !== strlen($this->hashIv)) {
            throw new \LogicException('Hash iv is not valid');
        }

        return json_decode(urldecode($this->stripPadding(openssl_decrypt($encrypted, 'aes-128-cbc', $this->hashKey, OPENSSL_ZERO_PADDING, $this->hashIv))), true);
    }

    /**
     * 強度 128/8 = 16bytes
     * @param string str
     * @param int size
     * @return string
     */
    protected function addPadding(string $str, int $size = 16)
    {
        $len = strlen($str);
        $pad = $size - ($len % $size);
        $str .= str_repeat(chr($pad), $pad);
        return $str;
    }

    /**
     * @param string string
     * @return string
     * @throws \Exception
     */
    protected function stripPadding($string)
    {
        if(strlen($string) === 0)
            throw new \Exception("The stripPadding method parameter is empty.");

        $sLast = ord(substr($string, -1));
        $sLastC = chr($sLast);
        $pCheck = substr($string, -$sLast);

        if (preg_match("/$sLastC{" . $sLast . "}/", $string)) 
        {
            $string = substr($string, 0, strlen($string) - $sLast);
            return $string;
        }

        throw new \Exception("Bad hashed string $string");
    }
}
