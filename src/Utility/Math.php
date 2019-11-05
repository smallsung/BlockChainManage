<?php
/**
 * Created by PhpStorm.
 * User: smallsung
 * Date: 2019-10-29
 * Time: 02:26
 */

namespace SmallSung\BlockChainManage\Utility;


class Math
{

    /**
     * 16=>10
     * @param string $hex
     * @return string
     */
    static public function hex2Dec(string $hex) : string
    {
        if (preg_match('@^0x@i', $hex)){
            $hex = substr($hex, 2);
        }

        if(strlen($hex) == 1) {
            $dec = hexdec($hex);
        } else {
            $remain = substr($hex, 0, -1);
            $last = substr($hex, -1);
            $dec = bcadd(bcmul(16, static::hex2Dec($remain)), hexdec($last));
        }
        return (string)$dec;
    }

    /**
     * 10=>16
     * @param string $dec
     * @param bool $isPrefix    是否需要前缀0x
     * @return bool|string
     */
    static public function dec2Hex(string $dec, bool $isPrefix=false)
    {

        $last = bcmod($dec, 16);
        $remain = bcdiv(bcsub($dec, $last), 16);
        if($remain == 0) {
            $hex = dechex($last);
        } else {
            $hex = static::dec2Hex($remain);
            if ($isPrefix){
                if (preg_match('@^0x@i', $hex)){
                    $hex = substr($hex, 2);
                }
            }
            $hex = $hex.dechex($last);
        }
        $hex = (string)$hex;
        if ($isPrefix){
            $hex = '0x'.$hex;
        }
        return $hex;
    }


    /**
     * 大单位转换小单位
     * @param string $quantity
     * @param int $decimals
     * @return string
     */
    static public function bigUnit2SmallUnit(string $quantity, int $decimals) : string
    {
        return bcmul($quantity, bcpow(10, $decimals, $decimals), $decimals);
    }

    /**
     * 小单位转换大单位
     * @param string $quantity
     * @param int $decimals
     * @return string
     */
    static public function smallUnit2BigUnit(string $quantity, int $decimals) : string
    {
        return bcdiv($quantity, bcpow(10, $decimals, $decimals), $decimals);
    }

    /**
     * 校验是否正数
     * @param string $number
     * @return bool
     */
    static public function isPositiveNumber(string $number) : bool
    {
        return (bool)preg_match('@^[+]{0,1}(\d+)$|^[+]{0,1}(\d+\.\d+)$@', $number);
    }

    /**
     * 校验是否正整数
     * @param string $number
     * @return bool
     */
    static public function isPositiveInteger(string $number) : bool
    {
        return (bool)preg_match('@^[+]{0,1}(\d+)$@', $number);
    }
}
