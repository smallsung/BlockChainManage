<?php
/**
 * Created by PhpStorm.
 * User: smallsung
 * Date: 2019-10-28
 * Time: 05:43
 */

namespace SmallSung\BlockChainManage\Exceptions;


class JsonRpc2ParseException extends BaseExceptionAbstract
{

    private $originStr = '';


    public function __construct(string $originStr)
    {
        $this->originStr = $originStr;
        $message = 'jsonRpc2解析失败';
        parent::__construct($message, 107);
    }

    /**
     * @return string
     */
    public function getOriginStr(): string
    {
        return $this->originStr;
    }


}