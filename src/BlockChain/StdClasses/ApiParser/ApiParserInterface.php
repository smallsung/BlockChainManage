<?php
/**
 * Created by PhpStorm.
 * User: smallsung
 * Date: 2019-10-28
 * Time: 05:39
 */

namespace SmallSung\BlockChainManage\BlockChain\StdClasses\ApiParser;


interface ApiParserInterface
{
    public function parse(string $str);
}