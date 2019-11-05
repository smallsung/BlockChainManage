<?php
/**
 * Created by PhpStorm.
 * User: smallsung
 * Date: 2019-10-28
 * Time: 04:12
 */

namespace SmallSung\BlockChainManage\BlockChain\Interfaces;


use SmallSung\BlockChainManage\BlockChain\StdClasses\Address as AddressInterface;
use SmallSung\BlockChainManage\BlockChain\StdClasses\Params\Address as AddressParam;
use SmallSung\BlockChainManage\BlockChain\StdClasses\Params\Collect as CollectParam;
use \SmallSung\BlockChainManage\BlockChain\StdClasses\Results\Address as AddressResult;
use SmallSung\BlockChainManage\BlockChain\StdClasses\Results\AllBalance;
use SmallSung\BlockChainManage\BlockChain\StdClasses\Results\Collect as CollectResult;
use SmallSung\BlockChainManage\BlockChain\StdClasses\Results\Tx;
use SmallSung\BlockChainManage\BlockChain\StdClasses\Results\Txs;

/**
 * 交易所需要
 * Interface ExInterface
 * @package SmallSung\BlockChainManage\BlockChain\Interfaces
 */
interface ExInterface
{

    /**
     * 生成新地址
     * @param AddressParam $addressParam
     * @return AddressResult
     */
    public function getNewAddressApi(AddressParam $addressParam) : AddressResult;


    /**
     * 校验地址规范
     * @param AddressInterface $address
     * @return bool
     */
    public function validAddress(AddressInterface $address) : bool ;


    /**
     * 返回最新区块高度
     * @return int
     */
    public function getBlockHeightApi() : int ;


    /**
     * 返回所给[$from, $to]内，节点所有地址接收数据
     * @param int $from
     * @param int $to
     * @return Txs
     */
    public function getTxByBlocksApi(int $from, int $to) : Txs ;


    /**
     * @param AddressParam $from
     * @param AddressParam $to
     * @param string $value
     * @return Tx
     */
    public function transferApi(AddressParam $from, AddressParam $to, string $value) : Tx ;
    public function beforeTransferApi(AddressParam $from, AddressParam $to, string $value) : void ;
    public function runTransferApi(AddressParam $from, AddressParam $to, string $value) : Tx;
    public function afterTransferApi(Tx $tx) : void ;

    /**
     * @param Tx $tx
     * @return bool
     */
    public function validTxHash(Tx $tx) : bool ;


    /**
     * 返回节点内所有地址余额
     * @return AllBalance
     */
    public function balanceApi() : AllBalance;

    /**
     * @param string $minQuantity   最小值，(minQuantity, ++]
     * @param AddressParam $to
     * @param CollectParam|null $collectParam
     * @return CollectResult
     */
    public function collectApi(string $minQuantity, AddressParam $to, ?CollectParam $collectParam = null) : CollectResult ;
}
