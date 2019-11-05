<?php
/**
 * Created by PhpStorm.
 * User: smallsung
 * Date: 2019-10-28
 * Time: 05:35
 */

namespace SmallSung\BlockChainManage\Exceptions;


class CurlResponseCodeException extends BaseExceptionAbstract
{

    private $responseCode = 0;


    public function __construct(int $responseCode)
    {
        $this->responseCode = $responseCode;
        $message = 'curl响应码:'.$responseCode;
        parent::__construct($message, 106);
    }

    /**
     * @return int
     */
    public function getResponseCode(): int
    {
        return $this->responseCode;
    }


}