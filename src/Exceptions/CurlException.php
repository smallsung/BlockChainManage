<?php
/**
 * Created by PhpStorm.
 * User: smallsung
 * Date: 2019-10-28
 * Time: 05:30
 */

namespace SmallSung\BlockChainManage\Exceptions;


class CurlException extends BaseExceptionAbstract
{

    private $curlError = '';
    private $curlErrno = 0;

    public function __construct(int $curlErrno, string $curlError)
    {
        $this->curlErrno = $curlErrno;
        $this->curlError = $curlError;
        $message = "curl错误:[Errno:{$curlErrno}] {$curlError}";
        parent::__construct($message, 105);
    }

    /**
     * @return string
     */
    public function getCurlError(): string
    {
        return $this->curlError;
    }

    /**
     * @return int
     */
    public function getCurlErrno(): int
    {
        return $this->curlErrno;
    }


}