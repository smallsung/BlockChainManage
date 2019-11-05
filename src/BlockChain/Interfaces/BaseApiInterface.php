<?php
/**
 * Created by PhpStorm.
 * User: smallsung
 * Date: 2019-10-28
 * Time: 03:50
 */

namespace SmallSung\BlockChainManage\BlockChain\Interfaces;


use SmallSung\BlockChainManage\BlockChain\StdClasses\ApiClients\ApiClientInterface;
use SmallSung\BlockChainManage\BlockChain\StdClasses\ApiParser\ApiParserInterface;

interface BaseApiInterface
{

    /**
     * 返回节点Host
     * @return string
     */
    public function getApiHost() : string ;

    /**
     * 返回节点端口
     * @return int
     */
    public function getApiPort() : int ;

    /**
     * 节点是否需要验证
     * @return bool
     */
    public function isApiAuth() : bool ;

    /**
     * 节点验证用户名
     * @return string
     */
    public function getApiUser() : string ;

    /**
     * 节点验证密码
     * @return string
     */
    public function getApiPass() : string ;

    /**
     * 返回节点请求客户端 如JsonRpc
     * @return ApiClientInterface
     */
    public function getApiClient() : ApiClientInterface;

    /**
     * 返回节点相应数据解析器
     * @return ApiParserInterface
     */
    public function getApiParser() : ApiParserInterface;


}