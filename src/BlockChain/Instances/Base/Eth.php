<?php
/**
 * Created by PhpStorm.
 * User: smallsung
 * Date: 2019-10-30
 * Time: 04:00
 */

namespace SmallSung\BlockChainManage\BlockChain\Instances\Base;


use SmallSung\BlockChainManage\BlockChain\Abstracts\Erc20Abstract;
use SmallSung\BlockChainManage\BlockChain\StdClasses\Params\Address as AddressParam;
use SmallSung\BlockChainManage\BlockChain\StdClasses\EthContractFunctionTransferConfig;
use SmallSung\BlockChainManage\BlockChain\StdClasses\Params\Collect as CollectParam;
use SmallSung\BlockChainManage\BlockChain\StdClasses\Results\AllBalance;
use SmallSung\BlockChainManage\BlockChain\StdClasses\Results\Collect as CollectResult;
use SmallSung\BlockChainManage\BlockChain\StdClasses\Results\EthContractEventTransferConfig;
use SmallSung\BlockChainManage\BlockChain\StdClasses\Results\Tx;
use SmallSung\BlockChainManage\BlockChain\StdClasses\Results\Txs;
use SmallSung\BlockChainManage\Exceptions\InterfaceNotImplementedException;
use SmallSung\BlockChainManage\Exceptions\ParamException;
use SmallSung\BlockChainManage\Utility\Math;
use Web3\Utils;

class Eth extends Erc20Abstract
{

    public function getContractAddress(): string
    {
        throw new InterfaceNotImplementedException(__METHOD__);
    }

    public function getContractAbi(): string
    {
        throw new InterfaceNotImplementedException(__METHOD__);
    }

    public function getContractEventTransfer(): EthContractEventTransferConfig
    {
        throw new InterfaceNotImplementedException(__METHOD__);
    }

    public function getContractFunctionTransfer(): EthContractFunctionTransferConfig
    {
        throw new InterfaceNotImplementedException(__METHOD__);
    }

    public function getContractFunctionBalanceOf(): string
    {
        throw new InterfaceNotImplementedException(__METHOD__);
    }



    public function getDecimals(): int
    {
        return 18;
    }

    public function getTransferGas(): int
    {
        return 21000;
    }

    public function getTxByBlocksApi(int $from, int $to): Txs
    {

        $client = $this->getApiClient();
        $ret = $client->jRpc('eth_accounts', []);

        $parser = $this->getApiParser();
        $addresses = $parser->parse($ret);

        $height = $from;
        $txs = new Txs();

        for (; $height <= $to; $height++){
            $ret = $client->jRpc('eth_getBlockByNumber', [
                Utils::toHex($height, true),
                true
            ]);
            $block = $parser->parse($ret);

            foreach ($block['transactions'] as $tx){
                if (!in_array(strtolower($tx['to']), $addresses)){
                    continue;
                }
                $value = Math::hex2Dec($tx['value']);
                if (bccomp($value, 0, 0) !== 1){
                    continue;
                }

                $ret = $client->jRpc('eth_getTransactionReceipt', [$tx['hash']]);
                $receipt = $parser->parse($ret);

                if (!isset($receipt['status'])){
                    continue;
                }
                if ($receipt['status'] !== '0x1'){
                    continue;
                }

                $txObj = new Tx();
                $txObj->setHash($tx['hash'])
                ->setFrom($tx['from'])
                ->setTo($tx['to'])
                ->setValue($this->fromWeiToEth($value))
                ->setTimestamp($block['timestamp'])
                ->setBlockNumber(Math::hex2Dec($block['number']))
                ->setBlockHash($block['hash'])
                ->setBlockTime($block['timestamp']);
                $txs->addTx($txObj);
            }
        }
        return $txs;
    }

    public function runTransferApi(AddressParam $from, AddressParam $to, string $value): Tx
    {
        $client = $this->getApiClient();
        $ret = $client->jRpc('personal_sendTransaction', [
            [
                'from'=>$from->getAddress(),
                'to'=>$to->getAddress(),
                'value'=>Utils::toHex($this->fromEthToWei($value), true),
                'gas'=>Utils::toHex($this->getTransferGas(), true)
            ],
            $from->getPassword()
        ]);

        $parser = $this->getApiParser();
        $hash = $parser->parse($ret);

        $tx = new Tx();
        $tx->setHash($hash);

        return $tx;
    }

    public function balanceApi(): AllBalance
    {
        $client = $this->getApiClient();
        $ret = $client->jRpc('eth_accounts', []);

        $parser = $this->getApiParser();
        $addresses = $parser->parse($ret);

        $allBalance = new AllBalance();

        foreach ($addresses as $address){
            $ret = $client->jRpc('eth_getBalance', [$address, 'latest']);
            $balance = $parser->parse($ret);

            $addressParam = new AddressParam();
            $addressParam->setAddress($address);

            $allBalance->addBalanceDetails($addressParam, $this->fromWeiToEth(Math::hex2Dec($balance)));
        }

        return $allBalance;
    }

    public function collectApi(string $minQuantity, AddressParam $to, ?CollectParam $collectParam = null): CollectResult
    {
        if (!Math::isPositiveNumber($collectParam->getCollectEthGasPriceUnitGwei())){
            throw new ParamException('collectParam::CollectEthGasPriceUnitGwei');
        }
        if (empty($collectParam->getCollectErc20UserAddressPassword()->getPassword())){
            throw new ParamException('collectParam::CollectErc20UserAddressPassword::Password');
        }
        $minQuantityWei = $this->fromEthToWei($minQuantity);
        $gasPriceWei = explode('.', Math::bigUnit2SmallUnit($collectParam->getCollectEthGasPriceUnitGwei(), 9))[0];
        $gasLimitWei = bcmul($gasPriceWei, $this->getTransferGas(), 0);


        $client = $this->getApiClient();
        $ret = $client->jRpc('eth_accounts', []);

        $parser = $this->getApiParser();
        $addresses = $parser->parse($ret);

        $collectResult = new CollectResult();

        foreach ($addresses as $address){
            $ret = $client->jRpc('eth_getBalance', [
                $address,
                'latest'
            ]);
            $ethBalance = Math::hex2Dec($parser->parse($ret));

            if (1 !== bccomp($ethBalance, $minQuantityWei, 0)) {
                continue;
            }

            $ret = $client->jRpc('personal_sendTransaction', [
                [
                    'from'=>$address,
                    'to'=>$to->getPassword(),
                    'value'=>Utils::toHex(bcsub($ethBalance, $gasLimitWei, 0), true),
                    'gasPrice'=>Utils::toHex($gasPriceWei, true),
                    'gas'=>Utils::toHex($this->getTransferGas(), true)
                ],
                $collectParam->getCollectErc20UserAddressPassword()->getPassword(),
            ]);
            $hash = $parser->parse($ret);

            $addressParam = new AddressParam();
            $addressParam->setAddress($addressParam);
            $tx = new Tx();
            $tx->setHash($hash);
            $collectResult->addCollectDetails($addressParam, $tx);
        }

        return $collectResult;
    }


}