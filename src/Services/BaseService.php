<?php

/*
 * This file is part of the jmj/jushuitan-sdk.
 *
 * (c) JMJones <yang.jmj@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace JushuitanSdk\Services;

use JushuitanSdk\Client;

/**
 * Class BaseService.
 *
 * @author JMJones <yang.jmj@gmail.com>
 */
class BaseService
{
    const SHOPS_QUERY = 'shops.query';
    const LOGISTICS_COMPANY_QUERY = 'logisticscompany.query';

    /**
     * API客户端
     *
     * @var Client
     */
    private $client;

    /**
     * BaseService constructor.
     */
    public function __construct(Client $client) {
        $this->client = $client;
    }

    /**
     * 查询所有店铺
     * @see https://open.jushuitan.com/document/14.html
     *
     * @param array $params
     * @return array|\JushuitanSdk\Http\Response|object|\Psr\Http\Message\ResponseInterface
     * @throws \JushuitanSdk\Exceptions\HttpException
     * @throws \JushuitanSdk\Exceptions\InvalidConfigException
     */
    public function shopsQuery(array $params = []) {
        return $this->client->request(self::SHOPS_QUERY, $params);
    }

    /**
     * 物流公司查询
     *
     * @param array $params
     * @return array|\JushuitanSdk\Http\Response|object|\Psr\Http\Message\ResponseInterface
     * @throws \JushuitanSdk\Exceptions\HttpException
     * @throws \JushuitanSdk\Exceptions\InvalidConfigException
     */
    public function logisticsCompanyQuery(array $params = []) {
        return $this->client->request(self::LOGISTICS_COMPANY_QUERY, $params);
    }
}