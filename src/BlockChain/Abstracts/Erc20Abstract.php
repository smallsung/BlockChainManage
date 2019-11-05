<?php
/**
 * Created by PhpStorm.
 * User: smallsung
 * Date: 2019-10-28
 * Time: 08:15
 */

namespace SmallSung\BlockChainManage\BlockChain\Abstracts;


use SmallSung\BlockChainManage\BlockChain\StdClasses\Address as AddressInterface;
use SmallSung\BlockChainManage\BlockChain\StdClasses\Config\EthContractEventTransferConfig;
use SmallSung\BlockChainManage\BlockChain\StdClasses\Config\EthContractFunctionTransferConfig;
use SmallSung\BlockChainManage\BlockChain\StdClasses\Params\Address as AddressParam;
use SmallSung\BlockChainManage\BlockChain\StdClasses\Params\Collect as CollectParam;
use SmallSung\BlockChainManage\BlockChain\StdClasses\Results\Address as AddressResult;
use SmallSung\BlockChainManage\BlockChain\StdClasses\Results\AllBalance;
use SmallSung\BlockChainManage\BlockChain\StdClasses\Results\Collect as CollectResult;
use SmallSung\BlockChainManage\BlockChain\StdClasses\Results\Tx;
use SmallSung\BlockChainManage\BlockChain\StdClasses\Results\Txs;
use SmallSung\BlockChainManage\Constant;
use SmallSung\BlockChainManage\Exceptions\InterfaceNotImplementedException;
use SmallSung\BlockChainManage\Exceptions\ParamException;
use SmallSung\BlockChainManage\Exceptions\UnknownException;
use SmallSung\BlockChainManage\Manage;
use SmallSung\BlockChainManage\Utility\Math;
use SmallSung\BlockChainManage\BlockChain\StdClasses\ApiClients\ApiClientInterface;
use SmallSung\BlockChainManage\BlockChain\StdClasses\ApiClients\JsonRpc2 as JsonRpc2Client;
use SmallSung\BlockChainManage\BlockChain\StdClasses\ApiParser\ApiParserInterface;
use SmallSung\BlockChainManage\BlockChain\StdClasses\ApiParser\JsonRpc2 as JsonRpc2Parser;
use SmallSung\BlockChainManage\Exceptions\InsufficientFundsException;
use Web3\Contracts\Ethabi;
use Web3\Utils;

abstract class Erc20Abstract extends BaseAbstract
{

    /**
     * 合约地址
     * @return string
     */
    abstract public function getContractAddress() : string ;

    /**
     * 合约ABi
     * @return string
     */
    abstract public function getContractAbi() : string ;

    /**
     * 合约事件配置-转账
     * @return EthContractEventTransferConfig
     */
    abstract public function getContractEventTransfer() : EthContractEventTransferConfig ;

    /**
     * 合约方法配置-转账
     * @return EthContractFunctionTransferConfig
     */
    abstract public function getContractFunctionTransfer() : EthContractFunctionTransferConfig ;

    /**
     * 合约方法-余额
     * @return string
     */
    abstract public function getContractFunctionBalanceOf() : string ;

    /**
     * 转账所需手续费数量
     * @return int
     */
    abstract public function getTransferGas() : int ;


    /**
     * 最小单位到最大单位
     * @param string $quantity
     * @return string
     */
    protected function fromWeiToEth(string $quantity) : string
    {
        return Math::smallUnit2BigUnit($quantity, $this->getDecimals());
    }

    /**
     * 最大单位到最小单位
     * @param string $quantity
     * @return string
     */
    protected function fromEthToWei(string $quantity) : string
    {
        return explode('.', Math::bigUnit2SmallUnit($quantity, $this->getDecimals()))[0];
    }


    public function getApiHost(): string
    {
        return Constant::ETH_HOST;
    }

    public function getApiPort(): int
    {
        return Constant::ETH_PORT;
    }

    public function isApiAuth(): bool
    {
        return false;
    }

    public function getApiUser(): string
    {
        throw new InterfaceNotImplementedException(__METHOD__);
    }

    public function getApiPass(): string
    {
        throw new InterfaceNotImplementedException(__METHOD__);
    }

    public function getApiClient(): ApiClientInterface
    {
        if ($this->apiClient instanceof ApiClientInterface){
            return $this->apiClient;
        }

        $client = new JsonRpc2Client();
        $client->setRpcHost($this->getApiHost())
        ->setRpcPort($this->getApiPort())
        ->setIsRpcAuth($this->isApiAuth());
        return $client;
    }

    public function getApiParser(): ApiParserInterface
    {
        $parser = new JsonRpc2Parser();

        return $parser;
    }

    public function getNewAddressApi(AddressParam $addressParam): AddressResult
    {
        if (empty($addressParam->getPassword())){
            throw new ParamException('addressParam::Password');
        }

        $client = $this->getApiClient();
        $ret = $client->jRpc('personal_newAccount', [
            $addressParam->getPassword()
        ]);

        $parser = $this->getApiParser();
        $address = $parser->parse($ret);

        $addressResult = new AddressResult();
        $addressResult->setAddress($address);
        if (!$this->validAddress($addressResult)){
            throw new UnknownException();
        }
        return $addressResult;
    }


    public function getTxByBlocksApi(int $from, int $to): Txs
    {
        $client = $this->getApiClient();
        $ret = $client->jRpc('eth_accounts', []);

        $parser = $this->getApiParser();
        $addresses = $parser->parse($ret);

        $ethAbi = new Ethabi([
            'address' => new \Web3\Contracts\Types\Address(),
            'uint' => new \Web3\Contracts\Types\Uinteger()
        ]);
        $addresses = array_map(function ($address) use ($ethAbi){
            return $ethAbi->encodeParameter('address', $address);
        }, $addresses);

        $event = $this->getContractEventTransfer();

        $ret = $client->jRpc('eth_getLogs', [
            [
                'fromBlock'=>Utils::toHex($from, true),
                'toBlock'=>Utils::toHex($to, true),
                'address'=>$this->getContractAddress(),
                'topics'=>[
                    $ethAbi->encodeEventSignature($event->getEventName()),
                    null,
                    $addresses
                ]
            ]
        ]);

        $logs = $parser->parse($ret);
        $txs = new Txs();
        foreach ($logs as $log) {
            //  不确定用途
            // if ($log['removed'])

            $value = $ethAbi->decodeParameter('uint256', $log['topics'][$event->getValueIndex()]);
            if (bccomp($value, 0, 0) !== 1){
                continue;
            }

            $ret = $client->jRpc('eth_getTransactionReceipt', [$log['transactionHash']]);
            $receipt = $parser->parse($ret);

            if (!isset($receipt['status'])){
                continue;
            }
            if ($receipt['status'] !== '0x1'){
                continue;
            }

            $txObj = new Tx();
            $txObj->setHash($log['transactionHash'])
            ->setFrom($ethAbi->decodeParameter('address', $log['topics'][$event->getFromIndex()]))
            ->setTo($ethAbi->decodeParameter('address', $log['topics'][$event->getToIndex()]))
            ->setValue($this->fromWeiToEth($value))
            ->setBlockNumber(Math::hex2Dec($log['blockNumber']))
            ->setBlockHash($log['blockHash']);
            $txs->addTx($txObj);
        }

        return $txs;
    }


    public function beforeTransferApi(AddressParam $from, AddressParam $to, string $value): void
    {
        if (!$this->validAddress($from)){
            throw new ParamException('from::Address');
        }
        if (empty($from->getPassword())){
            throw new ParamException('from::Password');
        }
        if (!$this->validAddress($to)){
            throw new ParamException('to::Address');
        }
        if (!Math::isPositiveNumber($value)){
            throw new ParamException('value');
        }
    }

    public function runTransferApi(AddressParam $from, AddressParam $to, string $value): Tx
    {

        $ethAbi = new Ethabi([
            'address' => new \Web3\Contracts\Types\Address(),
            'uint' => new \Web3\Contracts\Types\Uinteger()
        ]);


        $client = $this->getApiClient();
        $ret = $client->jRpc('eth_call', [
            [
                'from'=>$from->getAddress(),
                'to'=>$this->getContractAddress(),
                'data'=>$ethAbi->encodeFunctionSignature($this->getContractFunctionBalanceOf())
                    .Utils::stripZero($ethAbi->encodeParameter('address', $from->getAddress()))
            ],
            'latest'
        ]);

        $parser = $this->getApiParser();
        $balance = $parser->parse($ret);

        $balance = Math::hex2Dec($balance);
        if (-1 === bccomp($balance, $this->fromEthToWei($value), 0)){
            throw new InsufficientFundsException();
        }

        $function = $this->getContractFunctionTransfer();
        $type = [];
        $type[$function->getToIndex()] = 'address';
        $type[$function->getValueIndex()] = 'uint256';
        $param = [];
        $param[$function->getToIndex()] = $to->getAddress();
        $param[$function->getValueIndex()] = $value;

        $ret = $client->jRpc('personal_sendTransaction', [
            [
                'from'=>$from->getAddress(),
                'to'=>$this->getContractAddress(),
                'gas'=>Utils::toHex($this->getTransferGas(), true),
                'data'=>$ethAbi->encodeFunctionSignature($function->getFunctionName())
                    .Utils::stripZero($ethAbi->encodeParameters($type, $param)),

            ],
            $from->getPassword()
        ]);
        $hash = $parser->parse($ret);

        $tx = new Tx();
        $tx->setHash($hash);
        return $hash;
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
        $ret = $client->jRpc('eth_accounts');

        $parser = $this->getApiParser();
        $addresses = $parser->parse($ret);

        $ethAbi = new Ethabi([
            'address' => new \Web3\Contracts\Types\Address(),
        ]);

        $allBalance = new AllBalance();
        foreach ($addresses as $address){
            $ret = $client->jRpc('eth_call', [
                [
                    'from'=>$address,
                    'to'=>$this->getContractAddress(),
                    'data'=>$ethAbi->encodeFunctionSignature($this->getContractFunctionBalanceOf())
                        .Utils::stripZero($ethAbi->encodeParameter('address', $address))
                ],
                'latest'
            ]);
            $balance = $parser->parse($ret);

            $addressParam = new AddressParam();
            $addressParam->setAddress($addressParam);
            $allBalance->addBalanceDetails($addressParam, $this->fromWeiToEth(Math::hex2Dec($balance)));
        }

        return $allBalance;
    }

    public function getBlockHeightApi(): int
    {
        $client = $this->getApiClient();
        $ret = $client->jRpc('eth_blockNumber', []);

        $parser = $this->getApiParser();
        $height = $parser->parse($ret);
        $height = Math::hex2Dec($height);

        if (!Math::isPositiveInteger($height)){
            throw new UnknownException();
        }

        return $height;
    }

    public function collectApi(string $minQuantity, AddressParam $to, ?CollectParam $collectParam = null): CollectResult
    {
        if (!$this->validAddress($to)){
            throw new ParamException('to::Address');
        }
        if (!$this->validAddress($collectParam->getCollectErc20GasAddress())){
            throw new ParamException('collectParam::CollectErc20GasAddress');
        }
        if (empty($collectParam->getCollectErc20GasAddress()->getPassword())){
            throw new ParamException('collectParam::CollectErc20GasAddress::Password');
        }
        if (!Math::isPositiveNumber($collectParam->getCollectEthGasPriceUnitGwei())){
            throw new ParamException('collectParam::CollectEthGasPriceUnitGwei');
        }
        if (empty($collectParam->getCollectErc20UserAddressPassword()->getPassword())){
            throw new ParamException('collectParam::CollectErc20UserAddressPassword::Password');
        }

        $minQuantityWei = $this->fromEthToWei($minQuantity);
        $gasPriceWei = Math::bigUnit2SmallUnit($collectParam->getCollectEthGasPriceUnitGwei(), 9);
        $gasLimitWei = bcmul($gasPriceWei, $this->getTransferGas(), 0);
        $functionTransfer = $this->getContractFunctionTransfer();

        $ethTransferGas = $ethInstance = Manage::getInstance()->getBlockChainInstance('eth')
            ->getTransferGas();


        $ethAbi = new Ethabi([
            'address' => new \Web3\Contracts\Types\Address(),
        ]);

        $client = $this->getApiClient();
        $ret = $client->jRpc('eth_accounts', []);
        $parser = $this->getApiParser();
        $addresses = $parser->parse($ret);





        $collectResult = new CollectResult();
        foreach ($addresses as $address){
            //判断是否达到归集最小值
            $ret = $client->jRpc('eth_call', [
                [
                    'from'=>$address,
                    'to'=>$this->getContractAddress(),
                    'data'=>$ethAbi->encodeFunctionSignature($this->getContractFunctionBalanceOf())
                        .Utils::stripZero($ethAbi->encodeParameter('address', $address))
                ],
                'latest'
            ]);
            $erc20BalanceHex = $parser->parse($ret);
            $erc20BalanceDec = Math::hex2Dec($erc20BalanceHex);
            if (1 !== bccomp($erc20BalanceDec, $minQuantityWei, 0)) {
                continue;
            }

            // 判断是否有足够的手续费
            $ret = $client->jRpc('eth_getBalance', [
                $address, 'latest'
            ]);
            $ethBalanceHex = $parser->parse($ret);
            $ethBalanceDec = Math::hex2Dec($ethBalanceHex);

            //手续费不足转手续费
            if (-1 === (bccomp($ethBalanceDec, $gasLimitWei, 0))){
                $ret = $client->jRpc('personal_sendTransaction', [
                    [
                        'from'=>$collectParam->getCollectErc20GasAddress()->getAddress(),
                        'to'=>$address,
                        'value'=>Utils::toHex(bcsub($gasLimitWei, $ethBalanceDec, 0), true),
                        'gas'=>$ethTransferGas,
                    ],
                    $collectParam->getCollectErc20GasAddress()->getPassword(),
                ]);
                $hash = $parser->parse($ret);

                $addressParam = new AddressParam();
                $addressParam->setAddress($address);
                $addressParam->setSymbol('eth');
                $tx = new Tx();
                $tx->setHash($hash);
                $collectResult->addCollectDetails($addressParam, $tx);
                continue;
            }

            $type = [];
            $type[$functionTransfer->getToIndex()] = 'address';
            $type[$functionTransfer->getValueIndex()] = 'uint256';
            $param = [];
            $param[$functionTransfer->getToIndex()] = $to->getAddress();
            $param[$functionTransfer->getValueIndex()] = $erc20BalanceDec;

            $ret = $client->jRpc('personal_sendTransaction', [
                [
                    'from'=>$address,
                    'to'=>$this->getContractAddress(),
                    'gas'=>Utils::toHex($this->getTransferGas(), true),
                    'gasPrice'=>Utils::toHex($gasPriceWei, true),
                    'data'=>$ethAbi->encodeFunctionSignature($functionTransfer->getFunctionName())
                        .Utils::stripZero($ethAbi->encodeParameters($type, $param)),
                ],
                $collectParam->getCollectErc20UserAddressPassword()->getPassword(),
            ]);

            $hash = $parser->parse($ret);
            $addressParam = new AddressParam();
            $addressParam->setAddress($address);
            $addressParam->setSymbol('erc20');
            $tx = new Tx();
            $tx->setHash($hash);
            $collectResult->addCollectDetails($addressParam, $tx);
            continue;
        }

        return $collectResult;
    }


    public function validAddress(AddressInterface $address): bool
    {
        return Utils::isAddress($address->getAddress());
    }

    public function validTxHash(Tx $tx): bool
    {
        return preg_match('@^0x[a-f0-9]{64}$@i', $tx->getHash()) > 0;
    }

}