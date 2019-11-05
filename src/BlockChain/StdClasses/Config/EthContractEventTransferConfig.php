<?php
/**
 * Created by PhpStorm.
 * User: smallsung
 * Date: 2019-10-29
 * Time: 04:33
 */

namespace SmallSung\BlockChainManage\BlockChain\StdClasses\Config;


class EthContractEventTransferConfig
{
    private $eventName = '';
    private $fromIndex = -1;
    private $toIndex = -1;
    private $valueIndex = -1;

    /**
     * @return string
     */
    public function getEventName(): string
    {
        return $this->eventName;
    }

    /**
     * @param string $eventName
     */
    public function setEventName(string $eventName): void
    {
        $this->eventName = $eventName;
    }

    /**
     * @return int
     */
    public function getFromIndex(): int
    {
        return $this->fromIndex;
    }

    /**
     * @param int $fromIndex
     */
    public function setFromIndex(int $fromIndex): void
    {
        $this->fromIndex = $fromIndex;
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