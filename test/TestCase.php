<?php
/**
 * Created by PhpStorm.
 * User: smallsung
 * Date: 2019-11-01
 * Time: 16:36
 */

namespace SmallSung\BlockChainManage\Test;

use \PHPUnit\Framework\TestCase as BaseTestCase;
use SmallSung\BlockChainManage\Manage;

class TestCase extends BaseTestCase
{
    protected $blockChainManage;

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        $constantFile = realpath(dirname(__FILE__).'/../SmallsungBlockChainManageConstant.php');
        $this->blockChainManage = Manage::getInstance($constantFile);
        parent::__construct($name, $data, $dataName);
    }
}