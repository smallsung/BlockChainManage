<?php
/**
 * Created by PhpStorm.
 * User: smallsung
 * Date: 2019-10-28
 * Time: 06:08
 */

namespace SmallSung\BlockChainManage\Exceptions;


class UnknownException extends BaseExceptionAbstract
{
    public function __construct(string $desc = '')
    {
        $message = 'Unknown Exception:'.$desc;
        parent::__construct($message, 999);
    }
}