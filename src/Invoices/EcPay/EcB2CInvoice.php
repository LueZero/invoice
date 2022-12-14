<?php

namespace Zero\Invoices\EcPay;

use Zero\Invoices\EcPay\EcInvoice;
use Zero\Invoices\EcPay\Requests\Parameters\Base;
use WpOrg\Requests\Requests;

class EcB2CInvoice extends EcInvoice
{
    /**
     * @param array configs 
     */
    public function __construct()
    {
        $this->requireConfig('ec');
    }

    /**
     * @param Issue issue 
     * @return string
     */
    public function createIssue(Base $issue)
    {
        $sendData = array_filter((array) $issue);
        $sendData['Data'] = $this->encrypt($sendData['Data']);
        $response = Requests::post($this->configs['B2C']['invoiceURLs']['baseURL'] . $this->configs['B2C']['invoiceURLs']['issue'], [
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
        $response = Requests::post($this->configs['B2C']['invoiceURLs']['baseURL'] . $this->configs['B2C']['invoiceURLs']['invalid'], [
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
        $response = Requests::post($this->configs['B2C']['invoiceURLs']['baseURL'] . $this->configs['B2C']['invoiceURLs']['allowanceInvalid'], [
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
        $response = Requests::post($this->configs['B2C']['invoiceURLs']['baseURL'] . $this->configs['B2C']['invoiceURLs']['allowance'], [
            'Content-Type: application/json',
        ], json_encode($sendData));

        $result = json_decode($response->body, true);
        $result['Data'] = $this->decrypt($result['Data']);

        return json_encode($result, true);
    }

    /**
     * ??????
     * @param string data
     * @throws \Exception
     */
    public function encrypt($data)
    {
        if (openssl_cipher_iv_length('aes-128-cbc') !== strlen($this->hashIv)) {
            throw new \LogicException('hash iv is not valid');
        }

        return openssl_encrypt($this->addPadding(urlencode(json_encode($data))), 'aes-128-cbc', $this->hashKey, OPENSSL_ZERO_PADDING, $this->hashIv);
    }

    /**
     * ??????
     * @param string encrypted
     * @throws \Exception
     */
    public function decrypt($encrypted)
    {
        if (openssl_cipher_iv_length('aes-128-cbc') !== strlen($this->hashIv)) {
            throw new \LogicException('hash iv is not valid');
        }

        return json_decode(urldecode($this->stripPadding(openssl_decrypt($encrypted, 'aes-128-cbc', $this->hashKey, OPENSSL_ZERO_PADDING, $this->hashIv))), true);
    }

    /**
     * ?????? 128/8 = 16bytes
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

        throw new \Exception("bad hashed string $string");
    }
}
