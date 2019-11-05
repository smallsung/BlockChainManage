<?php
/**
 * Created by PhpStorm.
 * User: smallsung
 * Date: 2019-10-28
 * Time: 03:23
 */

namespace SmallSung\BlockChainManage\BlockChain\Abstracts;


use SmallSung\BlockChainManage\BlockChain\Interfaces\BaseApiInterface;
use SmallSung\BlockChainManage\BlockChain\Interfaces\BaseInterface;
use SmallSung\BlockChainManage\BlockChain\Interfaces\ExInterface;
use SmallSung\BlockChainManage\BlockChain\StdClasses\Params\Address as AddressParam;
use SmallSung\BlockChainManage\BlockChain\StdClasses\Params\BlockChainInstanceConstruct;
use SmallSung\BlockChainManage\BlockChain\StdClasses\Results\Tx;
use SmallSung\BlockChainManage\Component\Singleton;

abstract class BaseAbstract implements BaseInterface, BaseApiInterface, ExInterface
{
    use Singleton;

    protected $apiClient = null;    //部分节点可能需要设置特定参数，如omni的timeout

    protected function __construct(?BlockChainInstanceConstruct $param = null)
    {
        if ($param instanceof BlockChainInstanceConstruct){
            $this->apiClient = $param->getApiClient();
        }
    }


    public function getChainName(): string
    {
        return strtolower(explode("\\", static::class)[-2]);
    }

    public function getSymbol(): string
    {
        return strtolower(explode("\\", static::class)[-1]);
    }

    public function transferApi(AddressParam $from, AddressParam $to, string $value): Tx
    {
        $this->beforeTransferApi($from, $to, $value);
        $tx = $this->runTransferApi($from, $to, $value);
        $this->afterTransferApi($tx);
        return $tx;
    }


}