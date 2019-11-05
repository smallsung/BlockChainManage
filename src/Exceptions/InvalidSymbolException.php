<?php
/**
 * Created by PhpStorm.
 * User: smallsung
 * Date: 2019-10-28
 * Time: 03:29
 */

namespace SmallSung\BlockChainManage\Exceptions;


class InvalidSymbolException extends BaseExceptionAbstract
{
    private $symbol = '';

    /**
     * InvalidSymbolException constructor.
     * @param string $symbol
     */
    public function __construct(string $symbol)
    {
        $this->symbol = $symbol;
        $message = "无效的简称：{$symbol}，简称规则应是\\[a-z0-9]+\\，如btc，eth。";
        parent::__construct($message, 102);
    }

    /**
     * @return string
     */
    public function getSymbol(): string
    {
        return $this->symbol;
    }
}