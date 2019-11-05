<?php
/**
 * Created by PhpStorm.
 * User: smallsung
 * Date: 2019-10-31
 * Time: 05:45
 */

namespace SmallSung\BlockChainManage\BlockChain\Abstracts;


use SmallSung\BlockChainManage\BlockChain\StdClasses\Params\Address as AddressParam;
use SmallSung\BlockChainManage\BlockChain\StdClasses\Params\Collect as CollectParam;
use SmallSung\BlockChainManage\BlockChain\StdClasses\Results\AllBalance;
use SmallSung\BlockChainManage\BlockChain\StdClasses\Results\Collect as CollectResult;
use SmallSung\BlockChainManage\BlockChain\StdClasses\Results\Tx;
use SmallSung\BlockChainManage\BlockChain\StdClasses\Results\Txs;
use SmallSung\BlockChainManage\Exceptions\ApiErrorException;
use SmallSung\BlockChainManage\Exceptions\InterfaceNotImplementedException;
use SmallSung\BlockChainManage\Exceptions\ParamException;
use SmallSung\BlockChainManage\Exceptions\UnknownException;
use SmallSung\BlockChainManage\Utility\Math;

abstract class BtcAbstract extends OmniAbstrace
{
    public function getPropertyId(): int
    {
        throw new InterfaceNotImplementedException(__METHOD__);
    }

    public function getTxByBlocksApi(int $from, int $to): Txs
    {
        set_time_limit(180);

        $client = $this->getApiClient();
        $client->setRpcTimeOut(180);
        $parser = $this->getApiParser();

        $height = $from;
        $txs = new Txs();

        for (; $height <= $to; $height++){
            $ret = $client->jRpc('getblockhash', [$height]);
            $blockHash = $parser->parse($ret);

            $ret = $client->jRpc('getblock', [$blockHash]);
            $block = $parser->parse($ret);

            foreach ($block['tx'] as $txid) {

                try {
                    $ret = $client->jRpc('gettransaction', [$txid]);
                    $tx = $parser->parse($ret);
                }catch (ApiErrorException $apiErrorException) {
                    //  本地无格式化数据
                    if ($apiErrorException->getErrno() === -5) {
                        continue;
                    }
                }

                if (isset($tx['details'])) {
                    $is_only_receive = false;
                    foreach ($tx['details'] as $detail){
                        $is_only_receive = $detail['category'] === 'receive';
                    }

                    if ($is_only_receive) {
                        foreach ($tx['details'] as $detail) {
                            $txObj = new Tx();
                            $txObj->setHash($tx['txid'])
                            ->setTo($detail['address'])
                            ->setValue($detail['amount'])
                            ->setTimestamp($block['time'])
                            ->setBlockNumber($block['height'])
                            ->setBlockHash($block['hash'])
                            ->setBlockTime($block['time'])
                            ->setConfirm($block['confirmations']);
                            $txs->addTx($tx);
                        }
                    }
                }
            }
        }
        return $txs;
    }

    public function beforeTransferApi(AddressParam $from, AddressParam $to, string $value): void
    {
//        if (empty($from->getLabel())){
//            throw new ParamException('from::Label');
//        }
        if (!$this->validAddress($to)){
            throw new ParamException('to::Address');
        }
        if (!Math::isPositiveNumber($value)){
            throw new ParamException('value');
        }
    }

    public function runTransferApi(AddressParam $from, AddressParam $to, string $value): Tx
    {
        $client = $this->getApiClient();
        $client->setRpcTimeOut(30);
        $ret = $client->jRpc('sendfrom', [
            $from->getLabel(),
            $to->getAddress(),
            $value
        ]);

        $parser = $this->getApiParser();
        $hash = $parser->parse($ret);

        $tx = new Tx();
        $tx->setHash($hash);
        return $tx;
    }

    public function afterTransferApi(Tx $tx): void
    {
        if (!$this->validTxHash($tx)){
            throw new UnknownException();
        }
    }


    public function balanceApi(): AllBalance
    {
        $client = $this->getApiClient();
        $ret = $client->jRpc('getaddressesbyaccount', ['*']);

        $parser = $this->getApiParser();
        $addresses = $parser->parse($ret);

        $ret = $client->jRpc('listaddressgroupings');
        $listAddressGroupings = $parser->parse($ret);

        $addressXamount = [];
        foreach ($listAddressGroupings as $groups) {
            foreach ($groups as $row) {
                $addressXamount[$row[0]] = $row[1];
            }
        }

        $allBalance = new AllBalance();
        foreach ($addresses as $address) {
            if (isset($addressXamount[$address])){
                $addressParam = new AddressParam();
                $addressParam->setAddress($addressParam);
                $allBalance->addBalanceDetails($addressParam, $addressXamount[$address]);
            }
        }
        return $allBalance;
    }

    public function collectApi(string $minQuantity, AddressParam $to, ?CollectParam $collectParam = null): CollectResult
    {
        if (!$this->validAddress($to)){
            throw new ParamException('to::Address');
        }

        $client = $this->getApiClient();
        $ret = $client->jRpc('getbalance', ['*']);

        $parser = $this->getApiParser();
        $balance = $parser->parse($ret);

        $ret = $client->jRpc('sendtoaddress', [
            $to->getAddress(),
            $balance
        ]);

        $hash = $parser->parse($ret);

        $collectResult = new CollectResult();
        $addressParam = new AddressParam();
        $addressParam->setAddress('000000000000000000000000000000000');
        $collectResult->addCollectDetails($addressParam, $hash);

        return $collectResult;
    }


}