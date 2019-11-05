<?php
/**
 * Created by PhpStorm.
 * User: smallsung
 * Date: 2019-10-28
 * Time: 05:07
 */

namespace SmallSung\BlockChainManage\BlockChain\StdClasses\ApiClients;


interface ApiClientInterface
{

    public function jRpc(string $method, array $params = []) : string ;

    public function get(string $uri) : string ;
    public function post(string $uri, array $data) : string ;
}