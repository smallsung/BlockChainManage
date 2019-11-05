<?php
/**
 * Created by PhpStorm.
 * User: smallsung
 * Date: 2019-10-28
 * Time: 03:15
 */

namespace SmallSung\BlockChainManage;


use SmallSung\BlockChainManage\BlockChain\Abstracts\BaseAbstract;
use SmallSung\BlockChainManage\Component\Singleton;
use SmallSung\BlockChainManage\Exceptions\FileNotFoundException;
use SmallSung\BlockChainManage\Exceptions\InvalidChainNameException;
use SmallSung\BlockChainManage\Exceptions\InvalidSymbolException;

class Manage
{
    use Singleton;

    private $blockChainInstance = [];

    private function __construct(string $constantFile)
    {
        if (!file_exists($constantFile)){
            throw new FileNotFoundException($constantFile);
        }
        include_once $constantFile;
    }

    public function getBlockChainInstance(string $symbol, string $chainName = 'base') :BaseAbstract
    {
        $symbolWithChain = sprintf('chain:%s symbol:%s', $symbol, $chainName);
        $symbolWithChainHash = md5($symbolWithChain);
        if (isset($this->blockChainInstance[$symbolWithChainHash])){
            return $this->blockChainInstance[$symbolWithChainHash];
        }

        if (!preg_match('@^[a-z0-9]+$@', $chainName)){
            throw new InvalidChainNameException($chainName);
        }

        if (!preg_match('@^[a-z0-9]+$@', $symbol)){
            throw new InvalidSymbolException($symbol);
        }

        $filePath = sprintf('%s/BlockChain/Instances/%s/%s.php', __DIR__, ucwords($chainName), ucwords($symbol));
        if (!file_exists($filePath)){
            throw new FileNotFoundException($filePath);
        }

        $className = sprintf("\\SmallSung\\BlockChainManage\\BlockChain\\Instances\\%s\\%s", ucwords($chainName), ucwords($symbol));
        $this->blockChainInstance[$symbolWithChainHash] = call_user_func([$className, 'getInstance']);
        return $this->blockChainInstance[$symbolWithChainHash];
    }
}