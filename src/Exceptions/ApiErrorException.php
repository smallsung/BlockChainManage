<?php
/**
 * Created by PhpStorm.
 * User: smallsung
 * Date: 2019-10-28
 * Time: 05:49
 */

namespace SmallSung\BlockChainManage\Exceptions;


class ApiErrorException extends BaseExceptionAbstract
{

    private $errno = 0;
    private $error = '';

    public function __construct(int $errno, string $error)
    {
        $this->errno = $errno;
        $this->error = $error;
        $message = "ApiError:{$errno}\t{$error}";
        parent::__construct($message, 110);
    }

    /**
     * @return int
     */
    public function getErrno(): int
    {
        return $this->errno;
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }


}