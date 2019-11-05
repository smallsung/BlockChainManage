<?php
/**
 * Created by PhpStorm.
 * User: smallsung
 * Date: 2019-10-31
 * Time: 05:49
 */

namespace SmallSung\BlockChainManage\Exceptions;


class ParamException extends BaseExceptionAbstract
{


    public function __construct(string $paramName)
    {

        $message = 'Param Exception：'.$paramName;

        parent::__construct($message, 108);
    }
}