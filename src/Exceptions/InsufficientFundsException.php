<?php
/**
 * Created by PhpStorm.
 * User: smallsung
 * Date: 2019-10-29
 * Time: 05:21
 */

namespace SmallSung\BlockChainManage\Exceptions;


class InsufficientFundsException extends BaseExceptionAbstract
{

    public function __construct()
    {
        $message = 'Insufficient funds';
        parent::__construct($message, 119);
    }
}