<?php

/*
 * This file is part of the jmj/jushuitan-sdk.
 *
 * (c) JMJones <yang.jmj@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace JushuitanSdk;

/**
 * Class Config.
 *
 * @author JMJones <yang.jmj@gmail.com>
 */
class Config
{

    /**
     * 店铺ID
     *
     * @var string
     */
    protected $shopId;

    /**
     * 是否使用沙箱模式（即true=测试环境，false=正式环境）
     *
     * @see https://open.jushuitan.com/document/2037.html
     * @var bool
     */
    protected $sandbox;

    /**
     * 合作伙伴ID
     *
     * @see https://open.jushuitan.com/document/2122.html
     * @var string
     */
    protected $partnerId;

    /**
     * 合作伙伴Key
     *
     * @see https://open.jushuitan.com/document/2122.html
     * @var string
     */
    protected $partnerKey;

    /**
     * API认证Token（注意有时效性，请定时刷新，token值不变，但需要刷新）
     *
     * @see https://open.jushuitan.com/document/2135.html
     * @var string
     */
    protected $token;

    /**
     * 淘宝Key，作用未知(奇门接口？)，暂未使用
     *
     * @var string
     */
    protected $taobaoAppKey;

    /**
     * 淘宝Secret，作用未知(奇门接口？)，暂未使用
     *
     * @var string
     */
    protected $taobaoAppSecret;

    /**
     * 完整配置项示例
     *
     * @var array
     */
    protected $options = [
        /**
         * API接口基本信息
         *
         * @see https://open.jushuitan.com/document/2037.html
         */
        'shop_id' => 0, // 店铺ID，一般用于单一店铺操作
        'sandbox' => true, // 测试环境还是正式环境
        'partner_id' => 'ywv5jGT8ge6Pvlq3FZSPol345asd',
        'partner_key' => 'ywv5jGT8ge6Pvlq3FZSPol2323',
        'token' => '181ee8952a88f5a57db52587472c3798',
        'taobao_app_key' => '',
        'taobao_app_secret' => '',

        /**
         * 指定 API 调用返回结果的类型：array(default)/object/raw
         */
        'response_type' => 'object',

        /**
         * 接口请求相关配置，超时时间等，具体可用参数请参考：
         * https://docs.guzzlephp.org/en/stable/request-options.html
         *
         * - timeout: 请求超时（单位：s）。
         * - connect_timeout: 连接超时（单位：s）
         */
        'http' => [
            'timeout'         => 3.14,
            'connect_timeout' => 3.14,
        ]
    ];

    /**
     * Config constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = array_merge($this->options, $options);

        // init
        $this->shopId = $this->getOption('shop_id', 0);
        $this->sandbox = $this->getOption('sandbox', false);
        $this->partnerId = $this->getOption('partner_id', '');
        $this->partnerKey = $this->getOption('partner_key', '');
        $this->token = $this->getOption('token', '');
        $this->taobaoAppKey = $this->getOption('taobao_app_key', '');
        $this->taobaoAppSecret = $this->getOption('taobao_app_secret', '');
    }

    /**
     * 获取聚水谭API地址
     *
     * @return string
     */
    public function getBaseUri(): string
    {
        // 沙箱模式
        if($this->isSandBox()){
            return 'https://c.jushuitan.com/api/open/query.aspx';
        }

        return  'https://open.erp321.com/api/open/query.aspx';
    }

    /**
     * @return int
     */
    public function getShopId(): int
    {
        return $this->shopId;
    }

    /**
     * @return bool
     */
    public function isSandbox(): bool
    {
        return $this->sandbox;
    }

    /**
     * @return string
     */
    public function getPartnerId(): string
    {
        return $this->partnerId;
    }

    /**
     * @return string
     */
    public function getPartnerKey(): string
    {
        return $this->partnerKey;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function getTaobaoAppKey(): string
    {
        return $this->taobaoAppKey;
    }

    /**
     * @return string
     */
    public function getTaobaoAppSecret(): string
    {
        return $this->taobaoAppSecret;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->options;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return Config
     */
    public function setOption(string $key, $value): Config
    {
        $this->options[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getOption(string $key, $default = null)
    {
        return $this->options[$key] ?? $default;
    }

    /**
     * @param array $options
     *
     * @return Config
     */
    public function mergeOptions(array $options): Config
    {
        $this->options = array_merge($this->options, $options);

        return $this;
    }

    /**
     * @param array $options
     *
     * @return Config
     */
    public function setOptions(array $options): Config
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
