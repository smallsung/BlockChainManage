<?php
/**
 * Created by PhpStorm.
 * User: smallsung
 * Date: 2019-10-31
 * Time: 05:42
 */

namespace SmallSung\BlockChainManage\BlockChain\StdClasses\Params;

use SmallSung\BlockChainManage\BlockChain\StdClasses\ApiClient\ApiClientInterface;

class BlockChainInstanceConstruct
{
    private $apiClient ;


    /**
     * @return ApiClientInterface
     */
    public function getApiClient() : ApiClientInterface
    {
        return $this->apiClient;
    }

    /**
     * @param ApiClientInterface $apiClient
     * @return BlockChainInstanceConstruct
     */
    public function setApiClient(ApiClientInterface $apiClient): self
    {
        $this->apiClient = $apiClient;
        return $this;
    }

}