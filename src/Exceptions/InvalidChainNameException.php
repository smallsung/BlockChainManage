<?php
/**
 * Created by PhpStorm.
 * User: smallsung
 * Date: 2019-10-28
 * Time: 03:29
 */

namespace SmallSung\BlockChainManage\Exceptions;


class InvalidChainNameException extends BaseExceptionAbstract
{
    private $chainName = '';


    /**
     * InvalidChainNameException constructor.
     * @param string $chainName
     */
    public function __construct(string $chainName)
    {
        $this->chainName = $chainName;
        $message = "无效的链名称：{chainName}，链名称规则应是\\[a-z0-9]+\\，如omni, erc20.";
        parent::__construct($message, 101);
    }

    /**
     * @return string
     */
    public function getChainName(): string
    {
        return $this->chainName;
    }




}