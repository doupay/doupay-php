<?php

namespace doupay\doupayphp\Utils;


class Lib
{
    /**
     * 结果数据
     *
     * @param Integer $code 状态码
     * @param mixed $data 数据
     * @param String $msg 数据信息Tag
     *
     */
    public static function result($code, $msg = 'null', $data = array()){
        $dataString = json_encode($data);
        $res = array(
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
            'md5' => md5($dataString)
        );
        return json_encode($res);
    }


    /**
     * 获取13位时间戳
     */
    public static function timestamp(){
        list($t1, $t2) = explode(' ', microtime());
        $timestamp = (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
        return $timestamp;
    }
}