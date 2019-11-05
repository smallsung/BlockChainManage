<?php
/**
 * Created by PhpStorm.
 * User: smallsung
 * Date: 2019-10-29
 * Time: 17:25
 */

namespace SmallSung\BlockChainManage\BlockChain\StdClasses\Params;


/**
 * Class CollectApiKwParam
 * @package SmallSung\BlockChainManage\BlockChain\StdClasses\Params
 * 归集额外需要参数
 */
class Collect
{
    /**
     * @var float
     * ETH 链转账手续费 gasPrice
     */
    private $collectEthGasPriceUnitGwei = 1.0;
    /**
     * @var null
     * ETH 归集Erc20，从该地址支出手续费
     */
    private $collectErc20GasAddress = null;
    /**
     * @var null
     * ETH 归集，用户地址密码，用户地址必须统一密码
     */
    private $collectErc20UserAddressPassword = null;


    /**
     * @return float
     */
    public function getCollectEthGasPriceUnitGwei(): float
    {
        return $this->collectEthGasPriceUnitGwei;
    }

    /**
     * @param float $collectEthGasPriceUnitGwei
     * @return Collect
     */
    public function setCollectEthGasPriceUnitGwei(float $collectEthGasPriceUnitGwei): self
    {
        $this->collectEthGasPriceUnitGwei = $collectEthGasPriceUnitGwei;
        return $this;
    }

    /**
     * @return Address
     */
    public function getCollectErc20GasAddress() : Address
    {
        return $this->collectErc20GasAddress;
    }

    /**
     * @param Address $collectErc20GasAddress
     * @return Collect
     */
    public function setCollectErc20GasAddress(Address $collectErc20GasAddress): self
    {
        $this->collectErc20GasAddress = $collectErc20GasAddress;
        return $this;
    }

    /**
     * @return Address
     */
    public function getCollectErc20UserAddressPassword() :Address
    {
        return $this->collectErc20UserAddressPassword;
    }

    /**
     * @param Address $collectErc20UserAddressPassword
     * @return Collect
     */
    public function setCollectErc20UserAddressPassword(Address $collectErc20UserAddressPassword): self
    {
        $this->collectErc20UserAddressPassword = $collectErc20UserAddressPassword;
        return $this;
    }







}