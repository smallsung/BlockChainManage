<?php
/**
 * Created by PhpStorm.
 * User: smallsung
 * Date: 2019-10-28
 * Time: 03:47
 */

namespace SmallSung\BlockChainManage\BlockChain\Interfaces;


interface BaseInterface
{

    /**
     * 返回区块链链名称，如 erc20
     * @return string
     */
    public function getChainName() : string ;
    /**
     * 返回区块链简称，如 btc
     * @return string
     */
    public function getSymbol() : string ;

    /**
     * 返回区块链精度，如 btc return 8，eth return 18
     * @return int
     */
    public function getDecimals() : int ;
}