<?php
/**
 * Created by PhpStorm.
 * User: smallsung
 * Date: 2019-10-31
 * Time: 05:04
 */

namespace SmallSung\BlockChainManage\BlockChain\StdClasses\Results;


class Txs
{

    private $txs = [];

    /**
     * @return array
     */
    public function getTx(): array
    {
        return $this->txs;
    }


    /**
     * @param Tx $tx
     * @return Txs
     */
    public function addTx(Tx $tx) : self
    {
        $this->txs[] = $tx;
        return $this;
    }
}