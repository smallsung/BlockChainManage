<?php
/**
 * Created by PhpStorm.
 * User: smallsung
 * Date: 2019-10-31
 * Time: 05:22
 */

namespace SmallSung\BlockChainManage\BlockChain\StdClasses\Results;


class AllBalance
{

    private $allBalance = [];


    public function getAllBalanceDetails(): array
    {
        return $this->allBalance;
    }

    public function getAllBalance() : string
    {

    }

    public function addBalanceDetails(\SmallSung\BlockChainManage\BlockChain\StdClasses\Params\Address $address, string $value) : self
    {
        $this->allBalance[] = [
            'address'=>$address,
            'value'=>$value
        ];
        return $this;
    }

    public function toJson(?callable $fn = null) : string
    {
        if (is_callable($fn)){
            return $fn($this->allBalance);
        }

        $array = [];
        foreach ($this->allBalance as $row){
            $array[$row['address']->getAddress()] = $row['value'];
        }

        return json_encode($array);
    }
}