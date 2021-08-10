<?php

namespace doupay\doupayphp\Utils;

class Http
{
    /**
     * post提交数据
     * @param $url
     * @param $data
     * @param int $timeout
     * @param array $header
     * @param array $cookie
     * @return mixed
     */
    public static function post($url, $data, $timeout = 20, array $header = array(), array $cookie = array())
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        if (!empty($header)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);// array('Content-Type:application/json;charset=UTF-8');
        }

        if (!empty($cookie)) {
            $cookie_str = array();
            foreach ($cookie as $key => $val) {
                $cookie_str[] = urlencode($key) . '=' . urlencode($val);
            }
            curl_setopt($ch, CURLOPT_COOKIE, implode(';', $cookie_str));
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        $result = curl_exec($ch);
        $msg = curl_error($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return array('code' => $code, 'res'=> $result, 'msg'=> $msg);
    }


    /**
     * GET方式网络请求
     * @param $url
     * @param array $data
     * @param int $timeout
     * @return mixed
     */
    public static function get($url, array $data = array(), $timeout = 20, array $header = array())
    {
        $ch = curl_init(self::makeUrl($url, $data));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if (!empty($header)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);// array('Content-Type:application/json;charset=UTF-8'));
        }
        $result = curl_exec($ch);
        $msg = curl_error($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return array('code' => $code, 'res'=> $result, 'msg'=> $msg);
    }

    public static function makeUrl($url, array $data)
    {
        $params = '';
        if (!empty($data)) {
            $params = http_build_query($data, '', '&');
        }
        if (strpos($url, '?') === false) {
            $url = $url . '?' . $params;
        } else {
            if (!empty($params)) {
                $url = $url . '&' . $params;
            }
        }
        return $url;
    }
}