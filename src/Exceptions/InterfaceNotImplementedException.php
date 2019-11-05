<?php
/**
 * Created by PhpStorm.
 * User: smallsung
 * Date: 2019-10-28
 * Time: 05:10
 */

namespace SmallSung\BlockChainManage\Exceptions;


class InterfaceNotImplementedException extends BaseExceptionAbstract
{
    private $interfaceName = '';

    /**
     * InterfaceNotImplementedException constructor.
     * @param string $interfaceName
     */
    public function __construct(string $interfaceName)
    {
        $this->interfaceName = $interfaceName;
        $message = "{$interfaceName} 未实现";
        parent::__construct($message, 104);
    }

    /**
     * @return string
     */
    public function interfaceName(): string
    {
        return $this->interfaceName;
    }
}