<?php

namespace Zero\Invoices\ECPay;

use Zero\Invoices\ECPay\ECInvoice;
use Zero\Invoices\ECPay\Requests\Parameters\Base;

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
     * allowanceInvalidByCollegiate URL
     */
    private $allowanceInvalidByCollegiateURL;

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
     * @var string
     * getIssue URL
     */
    private $getIssueURL;

    /**
     * @var string
     * getInvalid URL
     */
    private $getInvalidURL;

    /**
     * @var string
     * getAllowanceList URL
     */
    private $getAllowanceListURL;

    /**
     * @var string
     * getAllowanceInvalid URL
     */
    private $getAllowanceInvalidURL;

    /**
     * @var string
     * getInvoiceWordSetting URL
     */
    private $getInvoiceWordSettingURL;

    /**
     * @param array configs 
     */
    public function __construct()
    {
        $this->requireConfig('ec');
        $this->issueURL = $this->configs['B2C']['invoiceURLs']['issue'];
        $this->invalidURL = $this->configs['B2C']['invoiceURLs']['invalid'];
        $this->allowanceInvalidURL = $this->configs['B2C']['invoiceURLs']['allowanceInvalid'];
        $this->allowanceInvalidByCollegiateURL = $this->configs['B2C']['invoiceURLs']['allowanceInvalidByCollegiate'];
        $this->allowanceURL = $this->configs['B2C']['invoiceURLs']['allowance'];
        $this->allowanceByCollegiateURL = $this->configs['B2C']['invoiceURLs']['allowanceByCollegiate'];
        $this->getIssueURL = $this->configs['B2C']['invoiceURLs']['getIssue'];
        $this->getInvalidURL = $this->configs['B2C']['invoiceURLs']['getInvalid'];
        $this->getAllowanceListURL = $this->configs['B2C']['invoiceURLs']['getAllowanceList'];
        $this->getAllowanceInvalidURL = $this->configs['B2C']['invoiceURLs']['getAllowanceInvalid'];
        $this->getInvoiceWordSettingURL = $this->configs['B2C']['invoiceURLs']['getInvoiceWordSetting'];
    }

    /**
     * @param Issue issue 
     * @return string
     */
    public function createIssue(Base $issue)
    {
        return $this->post($this->issueURL, $issue);
    }

    /**
     * @param Issue invalid 
     * @return string
     */
    public function createInvalid(Base $invalid)
    {
        return $this->post($this->invalidURL, $invalid);
    }

    /**
     * @param AllowanceInvalid allowanceInvalid 
     * @return string
     */
    public function createAllowanceInvalid(Base $allowanceInvalid)
    {
        return $this->post($this->allowanceInvalidURL, $allowanceInvalid);
    }

    /**
     * @param Allowance allowance 
     * @return string
     */
    public function createAllowance(Base $allowance)
    {
        return $this->post($this->allowanceURL, $allowance);
    }

    /**
     * @param AllowanceByCollegiate allowanceByCollegiate 
     * @return string
     */
    public function createAllowanceByCollegiate(Base $allowanceByCollegiate)
    {
        return $this->post($this->allowanceByCollegiateURL, $allowanceByCollegiate);
    }

    /**
     * @param AllowanceInvalid allowanceInvalidByCollegiate
     * @return array
     */
    public function createAllowanceInvalidByCollegiate(Base $allowanceInvalidByCollegiate)
    {
        return $this->post($this->allowanceInvalidByCollegiateURL, $allowanceInvalidByCollegiate);
    }

    /**
     * @param Base $issue
     * @return array
     */
    public function getIssue(Base $issue)
    {
        return $this->post($this->getIssueURL, $issue);
    }

    /**
     * @param Base $invalid
     * @return array
     */
    public function getInvalid(Base $invalid)
    {
        return $this->post($this->getInvalidURL, $invalid);
    }

    /**
     * @param Base $allowance
     * @return array
     */
    public function getAllowanceList(Base $allowance)
    {
        return $this->post($this->getAllowanceListURL, $allowance);
    }

    /**
     * @param Base $allowanceInvalid
     * @return array
     */
    public function getAllowanceInvalid(Base $allowanceInvalid)
    {
        return $this->post($this->getAllowanceInvalidURL, $allowanceInvalid);
    }

    /**
     * @param Base $wordSetting
     * @return array
     */
    public function getInvoiceWordSetting(Base $wordSetting)
    {
        return $this->post($this->getInvoiceWordSettingURL, $wordSetting);
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
