<?php
/**
 * Created by PhpStorm.
 * User: smallsung
 * Date: 2019-10-28
 * Time: 04:16
 */

namespace SmallSung\BlockChainManage\BlockChain\Abstracts;


use SmallSung\BlockChainManage\BlockChain\StdClasses\Address as AddressInterface;
use SmallSung\BlockChainManage\BlockChain\StdClasses\ApiClients\ApiClientInterface;
use SmallSung\BlockChainManage\BlockChain\StdClasses\ApiParser\ApiParserInterface;
use SmallSung\BlockChainManage\BlockChain\StdClasses\Params\Address as AddressParam;
use SmallSung\BlockChainManage\BlockChain\StdClasses\Params\Collect as CollectParam;
use SmallSung\BlockChainManage\BlockChain\StdClasses\Results\Collect as CollectResult;
use SmallSung\BlockChainManage\BlockChain\StdClasses\Results\Address as AddressResult;
use SmallSung\BlockChainManage\BlockChain\StdClasses\Results\AllBalance;
use SmallSung\BlockChainManage\BlockChain\StdClasses\Results\Tx;
use SmallSung\BlockChainManage\BlockChain\StdClasses\Results\Txs;
use SmallSung\BlockChainManage\Constant;
use SmallSung\BlockChainManage\BlockChain\StdClasses\ApiClients\JsonRpc2 as JsonRpc2Client;
use SmallSung\BlockChainManage\BlockChain\StdClasses\ApiParser\JsonRpc2 as JsonRpc2Parser;
use SmallSung\BlockChainManage\Exceptions\ParamException;
use SmallSung\BlockChainManage\Exceptions\UnknownException;
use SmallSung\BlockChainManage\Utility\Math;


abstract class OmniAbstrace extends BaseAbstract
{
    abstract public function getPropertyId() : int ;

    public function getDecimals(): int
    {
        return 8;
    }

    public function getApiHost(): string
    {
        return Constant::OMNI_HOST;
    }

    public function getApiPort(): int
    {
        return Constant::OMNI_PORT;
    }

    public function isApiAuth(): bool
    {
        return true;
    }

    public function getApiUser(): string
    {
        return Constant::OMNI_USER;
    }

    public function getApiPass(): string
    {
        return Constant::OMNI_PASS;
    }



    public function getNewAddressApi(AddressParam $addressParam): AddressResult
    {
//        if (empty($addressParam->getLabel())){
//            throw new ParamException('addressParam::Label');
//        }

        $client = $this->getApiClient();
        $ret = $client->jRpc('getnewaddress', [
            $addressParam->getLabel()
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

    public function getApiClient(): ApiClientInterface
    {
        if ($this->apiClient instanceof ApiClientInterface){
            return $this->apiClient;
        }

        $client = new JsonRpc2Client();

        $client->setRpcHost($this->getApiHost())
            ->setRpcPort($this->getApiPort())
            ->setIsRpcAuth($this->isApiAuth())
            ->setRpcUser($this->getApiUser())
            ->setRpcPass($this->getApiPass());

        return $client;
    }

    public function getApiParser(): ApiParserInterface
    {
        $parser = new JsonRpc2Parser();

        return $parser;
    }

    public function getBlockHeightApi(): int
    {
        $client = $this->getApiClient();
        $ret = $client->jRpc('getblockcount');

        $parser = $this->getApiParser();
        $height = $parser->parse($ret); //int

        if (!Math::isPositiveInteger($height)){
            throw new UnknownException();
        }
        return $height;
    }

    public function getTxByBlocksApi(int $from, int $to): Txs
    {
        $client = $this->getApiClient();
        $parser = $this->getApiParser();

        $propertyId = $this->getPropertyId();

        $height = $from;
        $txs = new Txs();
        for (; $height <= $to; $height++){
            $ret = $client->jRpc('omni_listblocktransactions', [$height]);
            $transactions = $parser->parse($ret);

            foreach ($transactions as $txid) {
                $ret = $client->jRpc('omni_gettransaction', [$txid]);
                $tx = $parser->parse($ret);

                if ($tx['ismine'] !== true) continue;
                if ($tx['valid'] !== true) continue;
                if ($tx['type_int'] !== 0) continue;
                if ($tx['propertyid'] !== $propertyId) continue;

                $txObj = new Tx();
                $txObj->setHash($tx['txid'])
                    ->setTo($tx['referenceaddress'])
                    ->setValue($tx['amount'])
                    ->setTimestamp($tx['blocktime'])
                    ->setBlockNumber($tx['block'])
                    ->setBlockHash($tx['blockhash'])
                    ->setBlockTime($tx['blocktime'])
                    ->setConfirm($tx['confirmations']);
                $txs->addTx($txObj);
            }
        }
        return $txs;
    }

    public function beforeTransferApi(AddressParam $from, AddressParam $to, string $value): void
    {
        if (!$this->validAddress($from)){
            throw new ParamException('from::Address');
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
        $client = $this->getApiClient();
        $client->setRpcTimeOut(30);
        $ret = $client->jRpc('omni_funded_send', [
            $from->getAddress(),
            $to->getAddress(),
            $this->getPropertyId(),
            $value,
            $from->getAddress(),
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

        $propertyId = $this->getPropertyId();
        $allBalance = new AllBalance();
        foreach ($addresses as $address) {
            $ret = $client->jRpc('omni_getbalance', [$address, $propertyId]);
            $balance = $parser->parse($ret);

            $addressParam = new AddressParam();
            $addressParam->setAddress($address);
            $allBalance->addBalanceDetails($addressParam, $balance['balance']);
        }
        return $allBalance;
    }


    public function collectApi(string $minQuantity, AddressParam $to, ?CollectParam $collectParam = null): CollectResult
    {
        if (!$this->validAddress($to)){
            throw new ParamException('to::Address');
        }

        $client = $this->getApiClient();
        $ret = $client->jRpc('getaddressesbyaccount', ['*']);

        $parser = $this->getApiParser();
        $addresses = $parser->parse($ret);

        $propertyId = $this->getPropertyId();

        $collectResult = new CollectResult();
        foreach ($addresses as $address) {
            try{
                $ret = $client->jRpc('omni_getbalance', [$address, $propertyId]);
                $balance = $parser->parse($ret);
                $balance = $balance['balance'];

                if (1 !== bccomp($balance, $minQuantity, $this->getDecimals())){
                    continue;
                }

                $ret = $client->jRpc('omni_funded_send', [
                    $address,
                    $to->getAddress(),
                    $propertyId, $balance,
                    $to->getAddress()
                ]);
                $hash = $parser->parse($ret);

                $addressParam = new AddressParam();
                $addressParam->setAddress($address);
                $txParam = new Tx();
                $txParam->setHash($hash);

                $collectResult->addCollectDetails($addressParam, $txParam);
            }catch (\Exception $e) {
                var_dump($e->getMessage());
                continue;
            }
        }
        return $collectResult;
    }


    /**
     * 仅通过正则校验常见地址，未深入校验
     * @param AddressInterface $address
     * @return bool
     */
    public function validAddress(AddressInterface $address): bool
    {
        return preg_match('@^[13][a-z0-9]{33}$@i', $address->getAddress()) > 0;
    }


    public function validTxHash(Tx $tx): bool
    {
        return preg_match('@^[a-f0-9]{64}$@', $tx->getHash()) > 0;
    }

}