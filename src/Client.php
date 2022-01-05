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

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use JushuitanSdk\Exceptions\HttpException;
use JushuitanSdk\Exceptions\InvalidConfigException;
use JushuitanSdk\Http\Response;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\GuzzleException;
use Exception;

/**
 * Class Client.
 *
 * @author JMJones <yang.jmj@gmail.com>
 */
class Client
{
    /**
     * 配置
     *
     * @var Config
     */
    protected $config;

    /**
     * Base URI
     *
     * @var string
     */
    protected $baseUri;

    /**
     * GuzzleHttp Client
     *
     * @var ClientInterface
     */
    protected $httpClient;

    /**
     * 中间件
     *
     * @var array
     */
    protected $middlewares = [];

    /**
     * 处理器
     *
     * @var HandlerStack
     */
    protected $handlerStack;

    /**
     * Curl Config
     *
     * @var array
     */
    protected static $defaults = [
        'curl' => [
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        ],
    ];

    /**
     * Client constructor.
     *
     * @param Config|null $config
     */
    public function __construct(Config $config = null)
    {
        $this->config = $config ?? new Config();
        $this->baseUri = $this->config->getBaseUri();
    }

    /**
     * Request.
     *
     * @param string       $jstMethod
     * @param string|array $data
     *
     * @return array|object|ResponseInterface|Response
     *
     * @throws InvalidConfigException
     * @throws HttpException
     */
    public function request(string $jstMethod, array $data = [])
    {
        $query = $this->getQueryParameters($jstMethod, $data);
        return $this->_jstRequest(['query' => $query, 'json' => $data]);
    }

    /**
     * Real Request
     *
     * @param array $options
     *
     * @return array|object|ResponseInterface|Response
     *
     * @throws InvalidConfigException
     * @throws HttpException
     */
    private function _jstRequest(array $options = [])
    {
        $baseUri = $this->config->getBaseUri();
        if (property_exists($this, 'baseUri') && ! is_null($this->baseUri)) {
            $baseUri = $this->baseUri;
        }

        $method   = strtoupper('POST');

        $options  = array_merge(
            self::$defaults,
            $this->config->getOption('http', []),
            $options, [
                'handler' => $this->getHandlerStack()
            ]
        );

        try {
            $response = $this->getHttpClient()->request($method, $baseUri, $options);
        } catch (GuzzleException $exception){
            throw new HttpException($exception->getMessage());
        } catch (Exception $exception){
            throw new HttpException($exception->getMessage());
        }

        return $this->castResponseToType($response, $this->config->getOption('response_type'));
    }

    /**
     * Cast Response.
     *
     * @param ResponseInterface $response
     * @param string|null $type
     * @return array|Response|object
     * @throws InvalidConfigException
     */
    protected function castResponseToType(ResponseInterface $response, string $type = null)
    {
        $response = Response::buildFromPsrResponse($response);
        $response->getBody()->rewind();

        switch ($type ?? 'array') {
            case 'array':
                return $response->toArray();
            case 'object':
                return $response->toObject();
            case 'raw':
                return $response;
            default:
                throw new InvalidConfigException('Config key "response_type" value Allowable array,object,raw');
        }
    }

    /**
     *
     *
     * @param GuzzleClient $client
     *
     * @return Client
     */
    public function setHttpClient(GuzzleClient $client): Client
    {
        $this->httpClient = $client;

        return $this;
    }

    /**
     * Return GuzzleHttp\Client instance.
     *
     * @return GuzzleClient
     */
    public function getHttpClient(): GuzzleClient
    {
        if (! ($this->httpClient instanceof GuzzleClient)) {
            $this->httpClient = new GuzzleClient(
                $this->config->getOption('http', [])
            );
        }

        return $this->httpClient;
    }

    /**
     * Set guzzle default settings.
     *
     * @param array $defaults
     */
    public static function setDefaultOptions(array $defaults = [])
    {
        self::$defaults = $defaults;
    }

    /**
     * Return current guzzle default settings.
     *
     * @return array
     */
    public static function getDefaultOptions(): array
    {
        return self::$defaults;
    }

    /**
     * Add a middleware.
     *
     * @param callable    $middleware
     * @param null|string $name
     *
     * @return Client
     */
    public function pushMiddleware(callable $middleware, string $name = null): Client
    {
        if (! is_null($name)) {
            $this->middlewares[$name] = $middleware;
        } else {
            array_push($this->middlewares, $middleware);
        }

        return $this;
    }

    /**
     * Return all middlewares.
     *
     * @return array
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    /**
     * @param array $middlewares
     *
     * @return Client
     */
    public function setMiddlewares(array $middlewares): Client
    {
        $this->middlewares = $middlewares;

        return $this;
    }

    /**
     * @param HandlerStack $handlerStack
     *
     * @return $this
     */
    public function setHandlerStack(HandlerStack $handlerStack): Client
    {
        $this->handlerStack = $handlerStack;

        return $this;
    }

    /**
     * Build a handler stack.
     *
     * @return HandlerStack
     */
    public function getHandlerStack(): HandlerStack
    {
        if ($this->handlerStack) {
            return $this->handlerStack;
        }

        $this->handlerStack = HandlerStack::create();

        foreach ($this->middlewares as $name => $middleware) {
            $this->handlerStack->push($middleware, $name);
        }

        return $this->handlerStack;
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * @param Config $config
     *
     * @return Client
     */
    public function setConfig(Config $config): Client
    {
        $this->config = $config;

        return $this;
    }

    /**
     * 是否为奇门接口
     *
     * @param string $method 聚水潭接口名称如 shops.query
     * @return bool
     */
    public function isQimen(string $method): bool
    {
        if (strstr($method, 'jst')) {
            return true;
        }
        return false;
    }

    /**
     * 获取URL查询参数
     *
     * @param string $method
     * @param array $requestParameters
     * @return array
     */
    public function getQueryParameters(string $method, array $requestParameters = []): array
    {
        $urlParameters = $this->generateSignature($method, $requestParameters);

        if($this->isQimen($method)) {
            foreach($requestParameters as $key=>$value) {
                if(is_array($value)) {
                    $urlParameters[$key] = join(',',$value);
                    continue;
                }
                $urlParameters[$key]=$value;
            }
        }
        return $urlParameters;
    }

    /**
     * 生成签名参数
     *
     * @param string $method
     * @param array $requestParameters
     * @return array
     */
    private function generateSignature(string $method, array $requestParameters): array
    {

        $systemParams = $this->getSystemParameters($method);

        $signString = '';
        ksort($systemParams);
        // 奇门接口
        if($this->isQimen($systemParams['method'])) {

            $method = str_replace('jst.','',$systemParams['method']);
            $jstSign = $method . $this->config->getPartnerId() . "token" . $this->config->getToken() . "ts" . $systemParams['ts'] . $this->config->getPartnerKey();

            $systemParams['jstsign'] = md5($jstSign);

            // 如果有业务参数则合并
            if($requestParameters != null) {
                $systemParams = array_merge($systemParams,$requestParameters);
                ksort($systemParams);

                foreach($systemParams as $key => $value) {
                    if(is_array($value)) {
                        $signString .= $key . join(',',$value);
                        continue;
                    }
                    $signString .=$key . $value;
                }
            }
            $systemParams['sign'] = strtoupper(
                md5($this->config->getTaobaoAppSecret() . $signString . $this->config->getTaobaoAppSecret())
            );
        } else { //普通接口
            $no_exists_array = array('method','sign','partnerid','partnerkey');

            $signString = $systemParams['method'].$systemParams['partnerid'];
            foreach($systemParams as $key=>$value) {

                if(in_array($key,$no_exists_array)) {
                    continue;
                }
                $signString .= $key . $value;
            }

            $signString .= $this->config->getPartnerKey();
            $systemParams['sign'] = md5($signString);
        }

        return $systemParams;
    }

    /**
     * 获取聚水潭接口请求时必须的系统参数
     *
     * @param string $method 接口名称如shops.query
     * @return array
     */
    private function getSystemParameters(string $method): array
    {
        # 默认系统参数
        $systemParams = [
            'partnerid' => $this->config->getPartnerId(),
            'token' => $this->config->getToken(),
            'method' => $method,
            'ts' => time()
        ];

        //是否包含jst
        if ($this->isQimen($method)) {
            $systemParams['sign_method'] = 'md5';
            $systemParams['format'] = 'json';
            $systemParams['app_key'] = $this->config->getTaobaoAppKey();
            $systemParams['timestamp'] = date("Y-m-d H:i:s", $systemParams['ts']);
            $systemParams['target_app_key'] = '23060081';
        }

        return $systemParams;
    }

}
