<?php
/**
 * Created by PhpStorm.
 * User: smallsung
 * Date: 2019-10-28
 * Time: 05:08
 */

namespace SmallSung\BlockChainManage\BlockChain\StdClasses\ApiClients;


use SmallSung\BlockChainManage\Exceptions\CurlException;
use SmallSung\BlockChainManage\Exceptions\CurlResponseCodeException;
use SmallSung\BlockChainManage\Exceptions\InterfaceNotImplementedException;
use SmallSung\BlockChainManage\Utility\SnowFlake;

class JsonRpc2 implements ApiClientInterface
{

    private $rpcHost = '';
    private $rpcPort = -1;

    private $rpcConnectTimeOut = 3;
    private $rpcTimeOut = 3;

    private $isRpcAuth = false;
    private $rpcUser = '';
    private $rpcPass = '';

    private $requestHeaders = [
        'Content-Type: application/json',
    ];

    private $isRpcDebug = false;

    public function jRpc(string $method, array $params = []): string
    {
        $postData = [];
        $postData['jsonrpc'] = '2.0';
        $postData['id'] = SnowFlake::make();
        $postData['method'] = $method;
        $postData['params'] = $params;

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL=>$this->getRpcHost(),
            CURLOPT_PORT=>$this->getRpcPort(),
            CURLOPT_RETURNTRANSFER=>true,
            CURLOPT_HTTPHEADER=>$this->getRequestHeaders(),
            CURLOPT_POST=>true,
            CURLOPT_POSTFIELDS=>json_encode($postData),
            CURLOPT_CONNECTTIMEOUT=>$this->getRpcConnectTimeOut(),
            CURLOPT_TIMEOUT=>$this->getRpcTimeOut(),
        ]);

        if ($this->isRpcAuth()){
            curl_setopt_array($ch, [
                CURLOPT_HTTPAUTH=>CURLAUTH_BASIC,
                CURLOPT_USERPWD=>sprintf('%s:%s', $this->getRpcUser(), $this->getRpcPass())
            ]);
        }

        if ($this->isRpcDebug()){
            curl_setopt_array($ch, [
                CURLOPT_PROXYPORT=>8888,
                CURLOPT_PROXY=>'127.0.0.1',
            ]);
        }

        $ret = curl_exec($ch);
        $curlError = curl_error($ch);
        $curlErrno = curl_errno($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($curlErrno !== 0){
            throw new CurlException($curlErrno, $curlError);
        }

        //jRpc  部分节点jRpc 错误状态码是500  部分是200
        if (!in_array($responseCode, [200, 500])){
            throw new CurlResponseCodeException($responseCode);
        }

        return $ret;
    }


    public function addRequestHeaders(string $header) :self
    {
        $this->requestHeaders[] = $header;
        return $this;
    }

    public function setRequestHeaders(array $headers) : self
    {
        $this->requestHeaders = $headers;
        return $this;
    }

    /**
     * @return array
     */
    public function getRequestHeaders(): array
    {
        return $this->requestHeaders;
    }

    /**
     * @return string
     */
    public function getRpcHost(): string
    {
        return $this->rpcHost;
    }

    public function setRpcHost(string $rpcHost): self
    {
        $this->rpcHost = $rpcHost;
        return $this;
    }

    /**
     * @return int
     */
    public function getRpcPort(): int
    {
        return $this->rpcPort;
    }

    public function setRpcPort(int $rpcPort): self
    {
        $this->rpcPort = $rpcPort;
        return $this;
    }

    /**
     * @return int
     */
    public function getRpcConnectTimeOut(): int
    {
        return $this->rpcConnectTimeOut;
    }

    public function setRpcConnectTimeOut(int $rpcConnectTimeOut): self
    {
        $this->rpcConnectTimeOut = $rpcConnectTimeOut;
        return $this;
    }

    /**
     * @return int
     */
    public function getRpcTimeOut(): int
    {
        return $this->rpcTimeOut;
    }

    public function setRpcTimeOut(int $rpcTimeOut): self
    {
        $this->rpcTimeOut = $rpcTimeOut;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRpcAuth(): bool
    {
        return $this->isRpcAuth;
    }

    public function setIsRpcAuth(bool $isRpcAuth): self
    {
        $this->isRpcAuth = $isRpcAuth;
        return $this;
    }


    /**
     * @return string
     */
    public function getRpcUser(): string
    {
        return $this->rpcUser;
    }

    public function setRpcUser(string $rpcUser): self
    {
        $this->rpcUser = $rpcUser;
        return $this;
    }

    /**
     * @return string
     */
    public function getRpcPass(): string
    {
        return $this->rpcPass;
    }

    public function setRpcPass(string $rpcPass): self
    {
        $this->rpcPass = $rpcPass;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRpcDebug(): bool
    {
        return $this->isRpcDebug;
    }

    public function setIsRpcDebug(bool $isRpcDebug): self
    {
        $this->isRpcDebug = $isRpcDebug;
        return $this;
    }


    public function get(string $uri): string
    {
        throw new InterfaceNotImplementedException(__METHOD__);
    }

    public function post(string $uri = '', array $data = []): string
    {
        throw new InterfaceNotImplementedException(__METHOD__);
    }
}