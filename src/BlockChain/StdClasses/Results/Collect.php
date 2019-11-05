<?php
/**
 * Created by PhpStorm.
 * User: smallsung
 * Date: 2019-10-31
 * Time: 05:32
 */

namespace SmallSung\BlockChainManage\BlockChain\StdClasses\Results;


class Collect
{
    private $collectDetails = [];

    /**
     * @return array
     */
    public function getCollectDetails(): array
    {
        return $this->collectDetails;
    }

    /**
     * @param \SmallSung\BlockChainManage\BlockChain\StdClasses\Params\Address $address
     * @param Tx $tx
     * @return Collect
     */
    public function addCollectDetails(\SmallSung\BlockChainManage\BlockChain\StdClasses\Params\Address $address, Tx $tx): self
    {
        $this->collectDetails[] = [
            'address'=>$address,
            'tx'=>$tx
        ];
        return $this;
    }

    /**
     * @param callable|null $fn
     * @return string
     */
    public function toJson(?callable $fn = null) : string
    {
        if (is_callable($fn)){
            return $fn($this->collectDetails);
        }

        $array = [];
        foreach ($this->collectDetails as $row){
            $array[$row['address']->getAddress()] = $row['tx'];
        }

        return json_encode($array);
    }

}