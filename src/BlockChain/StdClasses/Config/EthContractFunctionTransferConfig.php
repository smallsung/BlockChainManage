<?php
/**
 * Created by PhpStorm.
 * User: smallsung
 * Date: 2019-10-29
 * Time: 04:57
 */

namespace SmallSung\BlockChainManage\BlockChain\StdClasses\Config;


class EthContractFunctionTransferConfig
{
    private $functionName = '';
    private $toIndex = -1;
    private $valueIndex = -1;

    /**
     * @return string
     */
    public function getFunctionName(): string
    {
        return $this->functionName;
    }

    /**
     * @param string $functionName
     */
    public function setFunctionName(string $functionName): void
    {
        $this->functionName = $functionName;
    }

    /**
     * @return int
     */
    public function getToIndex(): int
    {
        return $this->toIndex;
    }

    /**
     * @param int $toIndex
     */
    public function setToIndex(int $toIndex): void
    {
        $this->toIndex = $toIndex;
    }

    /**
     * @return int
     */
    public function getValueIndex(): int
    {
        return $this->valueIndex;
    }

    /**
     * @param int $valueIndex
     */
    public function setValueIndex(int $valueIndex): void
    {
        $this->valueIndex = $valueIndex;
    }


}