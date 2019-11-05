<?php
/**
 * Created by PhpStorm.
 * User: smallsung
 * Date: 2019-10-31
 * Time: 05:05
 */

namespace SmallSung\BlockChainManage\BlockChain\StdClasses\Results;


class Tx
{
    private $hash = '';
    private $from = '';
    private $to = '';
    private $value = '';
    private $timestamp = 0;
    private $blockNumber = 0;
    private $blockHash = '';
    private $blockTime = 0;
    private $confirm = '';

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }


    /**
     * @param string $hash
     * @return Tx
     */
    public function setHash(string $hash): self
    {
        $this->hash = $hash;
        return $this;
    }

    /**
     * @return string
     */
    public function getFrom(): string
    {
        return $this->from;
    }

    /**
     * @param string $from
     * @return Tx
     */
    public function setFrom(string $from): self
    {
        $this->from = $from;
        return $this;
    }

    /**
     * @return string
     */
    public function getTo(): string
    {
        return $this->to;
    }

    /**
     * @param string $to
     * @return Tx
     */
    public function setTo(string $to): self
    {
        $this->to = $to;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return Tx
     */
    public function setValue(string $value): self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return int
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @param int $timestamp
     * @return Tx
     */
    public function setTimestamp(int $timestamp): self
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    /**
     * @return int
     */
    public function getBlockNumber(): int
    {
        return $this->blockNumber;
    }

    /**
     * @param int $blockNumber
     * @return Tx
     */
    public function setBlockNumber(int $blockNumber): self
    {
        $this->blockNumber = $blockNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getBlockHash(): string
    {
        return $this->blockHash;
    }

    /**
     * @param string $blockHash
     * @return Tx
     */
    public function setBlockHash(string $blockHash): self
    {
        $this->blockHash = $blockHash;
        return $this;
    }

    /**
     * @return int
     */
    public function getBlockTime(): int
    {
        return $this->blockTime;
    }


    /**
     * @param int $blockTime
     * @return Tx
     */
    public function setBlockTime(int $blockTime): self
    {
        $this->blockTime = $blockTime;
        return $this;
    }

    /**
     * @return string
     */
    public function getConfirm(): string
    {
        return $this->confirm;
    }

    /**
     * @param string $confirm
     * @return Tx
     */
    public function setConfirm(string $confirm): self
    {
        $this->confirm = $confirm;
        return $this;
    }

}