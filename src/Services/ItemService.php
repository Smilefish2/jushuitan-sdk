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
class ItemService
{
    const SKU_QUERY = 'sku.query';

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
     *普通商品查询
     *
     * @param array $params
     * @return array|\JushuitanSdk\Http\Response|object|\Psr\Http\Message\ResponseInterface
     * @throws \JushuitanSdk\Exceptions\HttpException
     * @throws \JushuitanSdk\Exceptions\InvalidConfigException
     */
    public function skuQuery(array $params = []) {
        return $this->client->request(self::SKU_QUERY, $params);
    }

}