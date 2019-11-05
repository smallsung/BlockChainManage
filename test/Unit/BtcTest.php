<?php
/**
 * Created by PhpStorm.
 * User: smallsung
 * Date: 2019-11-01
 * Time: 16:39
 */

namespace SmallSung\BlockChainManage\Test\Unit;


use SmallSung\BlockChainManage\BlockChain\StdClasses\Params\Address;
use SmallSung\BlockChainManage\Manage;
use SmallSung\BlockChainManage\Test\TestCase;

class BtcTest extends TestCase
{
    private $instance;

    public function __construct()
    {
        parent::__construct();
        $this->instance = $this->blockChainManage->getBlockChainInstance('btc');
    }

    public function newAddress() : void
    {
        $addressParam = new Address();
        $addressParam->setLabel('');

        $addressResult = $this->instance->getNewAddressApi($addressParam);

        var_dump($addressResult->getAddress());
    }

    public function validAddress()
    {
        $address = '13kXobDgLjPokmfsu4oULiVLzfyVB9G48x';
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
        $from->setLabel('sxf');

        $to = new Address();
        $to->setAddress('15fyE5Jp3wHF9XxMUWeXr3LR5e55meW3Tx');

        $value = 0.0001;

        $tx = $this->instance->transferApi($from, $to, $value);
        var_dump($tx->getHash());
    }
}
