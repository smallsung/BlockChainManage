<?php
/**
 * Created by PhpStorm.
 * User: smallsung
 * Date: 2019-11-05
 * Time: 19:47
 */

namespace SmallSung\BlockChainManage\Test\Unit;


use SmallSung\BlockChainManage\BlockChain\StdClasses\Params\Address;
use SmallSung\BlockChainManage\Manage;
use SmallSung\BlockChainManage\Test\TestCase;

class UsdtErc20Test extends TestCase
{
    private $instance;

    public function __construct()
    {
        parent::__construct();
        $this->instance = $this->blockChainManage->getBlockChainInstance('usdt', 'erc20');
    }

    public function newAddress() : void
    {
        $addressParam = new Address();
        $addressParam->setPassword('a123456');

        $addressResult = $this->instance->getNewAddressApi($addressParam);

        var_dump($addressResult->getAddress());
    }

    public function validAddress()
    {
        $address = '0x0000000000000000000000000000000000000000';
        $addressParam = new Address();
        $addressParam->setAddress($address);

        var_dump($this->instance->validAddress($addressParam));
    }

    public function transactions()
    {
        $to = $this->instance->getBlockHeightApi();

        $from = $to - 1;//  应为上一次的to+1，注意存储to

        $txs = $this->instance->getTxByBlocksApi($from, $to);
        foreach ($txs->getTx() as $tx){

        }
        var_dump($txs);
    }

    public function transfer()
    {
        $from = new Address();
        $from->setAddress('0x32437f1ce311276ba5c795e70ed75d42f6d3f0bc');
        $from->setPassword('a123456');

        $to = new Address();
        $to->setAddress('0x32437f1ce311276ba5c795e70ed75d42f6d3f0bc');

        $value = 0.0001;

        $tx = $this->instance->transferApi($from, $to, $value);
        var_dump($tx->getHash());
    }
}