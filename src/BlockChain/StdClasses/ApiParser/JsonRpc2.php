<?php
/**
 * Created by PhpStorm.
 * User: smallsung
 * Date: 2019-10-28
 * Time: 05:42
 */

namespace SmallSung\BlockChainManage\BlockChain\StdClasses\ApiParser;


use SmallSung\BlockChainManage\Exceptions\ApiErrorException;
use SmallSung\BlockChainManage\Exceptions\JsonRpc2ParseException;

class JsonRpc2 implements ApiParserInterface
{
    public function parse(string $str)
    {
        $data = json_decode($str, true);
        if (!is_array($data)){
            throw new JsonRpc2ParseException($str);
        }
        if (isset($data['error']) && !empty($data['error'])){
            throw new ApiErrorException($data['error']['code'], $data['error']['message']);
        }

        if (!isset($data['result'])){
            throw new JsonRpc2ParseException($str);
        }

        return $data['result'];
    }

}