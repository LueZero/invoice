<?php

namespace Zero\Invoices;

abstract class Invoice
{
    /**
     * @var array
     * 配置
     */
    protected $configs;

    /**
     * 呼叫配置檔案
     * @param string paymentName
     * @throws \Exception
     */
    public function requireConfig($paymentName)
    {
        $configs = require(dirname(__DIR__).'/config.php');

        if (empty($configs[$paymentName]))
            throw new \Exception('Zero\Invoice::[Invoice config is empty]');

        $this->setConfig($configs[$paymentName]);
    }

    /**
     * @var array configs
     */
    public function setConfig(array $configs)
    {
        $this->$configs = $configs;
    }

    /**
     * 設定配置
     * @return array
     */
    public function getConfigs()
    {
        return $this->configs;
    }
}
