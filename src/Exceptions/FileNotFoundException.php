<?php
/**
 * Created by PhpStorm.
 * User: smallsung
 * Date: 2019-10-28
 * Time: 03:38
 */

namespace SmallSung\BlockChainManage\Exceptions;


class FileNotFoundException extends BaseExceptionAbstract
{

    private $filePath = '';

    /**
     * FileNotFoundException constructor.
     * @param string $filePath
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
        $message = "{$filePath} Not Found.";
        parent::__construct($message, 103);
    }

    /**
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->filePath;
    }

}