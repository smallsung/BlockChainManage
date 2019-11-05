# BlockChainManage


## Constant
```
<?php
class SmallsungBlockChainManageConstant //命名空间必须是根
{
    const OMNI_HOST = '127.0.0.1';
    const OMNI_PORT = 8332;
    const OMNI_USER = 'rpcuser';
    const OMNI_PASS = 'rpcpassword';

    const ETH_HOST = '127.0.0.1';
    const ETH_PORT = 8545;
}
```

## Demo
```
<?php

include_once 'vendor/autoload.php';

#BTC
$btcTest = new \SmallSung\BlockChainManage\Test\Unit\BtcTest();
#生成地址
$btcTest->newAddress();
#校验地址
$btcTest->validAddress();
#充币
$btcTest->transactions();
#提币
$btcTest->transfer();

#OMNI/BTC
$usdtOmniTest = new \SmallSung\BlockChainManage\Test\Unit\UsdtOmniTest();
#生成地址
$usdtOmniTest->newAddress();
#校验地址
$usdtOmniTest->validAddress();
#充币
$usdtOmniTest->transactions();
#提币
$usdtOmniTest->transfer();

#ETH
$ethTest = new \SmallSung\BlockChainManage\Test\Unit\EthTest();
#生成地址
$ethTest->newAddress();
#校验地址
$ethTest->validAddress();
#充币
$ethTest->transactions();
#提币
$ethTest->transfer();

#ERC20/USDT
$ethTest = new \SmallSung\BlockChainManage\Test\Unit\UsdtErc20Test();
#生成地址
$ethTest->newAddress();
#校验地址
$ethTest->validAddress();
#充币
$ethTest->transactions();
#提币
$ethTest->transfer();

```