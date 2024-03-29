<?php

namespace doupay\doupayphp\Api;
use doupay\doupayphp\Utils\Http;
use doupay\doupayphp\Utils\Lib;

class Paymentinfo
{
    public $basrUrl = '';
	public $language = 'zh_TW';
    public $Version = 'v1.0';
    public $expireTime = 1800;

    public function __construct($baseUrl, $language, $Version, $appid, $secret, $publicKey, $privateKey, $expireTime)
    {
        if(empty($baseUrl)){
            return "baseUrl error.";
        }
        $this->basrUrl = $baseUrl;
        $this->language = $language;
        $this->Version = $Version;
		
        $this->appid = $appid;
		$this->secret = $secret;
		$this->publicKey = $publicKey;
		$this->privateKey = $privateKey;
        $this->expireTime = $expireTime;
    }

    /**
     * 获取币种列表
     *
     */
    public function getCoinList() {
        $uri = '/trade/getCoinList';
        $data = array();
        $signature = $this->signature($data);
        $header = array(
            'X-Merchant-sign:'.base64_encode($signature),
            'Content-Type:application/json;charset=UTF-8',
            'X-Language:'.$this->language,
            'X-Version:'.$this->Version
        );
        $res = Http::post($this->basrUrl.$uri, array(), $this->expireTime, $header);
        if($res['code'] != 200){
            return Lib::result(999, $res['code'].$res['msg']);
        }
		return $res['res'];
    }

    /**
     * 获取法币列表
     */
    public function getCurrencyList()
    {
        $uri = '/trade/getCurrencyList';
        $data = array();
        $signature = $this->signature($data);
        $header = array(
            'X-Merchant-sign:'.base64_encode($signature),
            'Content-Type:application/json;charset=UTF-8',
            'X-Language:'.$this->language,
            'X-Version:'.$this->Version
        );
        $res = Http::post($this->basrUrl.$uri, array(), $this->expireTime, $header);
        if($res['code'] != 200){
            return Lib::result(999, $res['code'].$res['msg']);
        }
		return $res['res'];
    }

    /**
     * 获取订单信息
     * @param orderCode 订单号【长度20到50】
     */
    public function getOrderInfo($orderCode)
    {
        if (empty($orderCode)) {
            return Lib::result(401, 'orderCode参数不能为空');
        }
        $uri = '/trade/getOrderInfo';
        $data = array(
            'orderCode' => $orderCode,
        );
        $signature = $this->signature($data);
        $header = array(
            'X-Merchant-sign:'.base64_encode($signature),
            'Content-Type:application/json;charset=UTF-8',
            'X-Language:'.$this->language,
            'X-Version:'.$this->Version
        );
        $res = Http::post($this->basrUrl.$uri, json_encode($data), $this->expireTime, $header);
        if($res['code'] != 200){
            return Lib::result(999, $res['code'].$res['msg']);
        }
		return $res['res'];
    }

    /**
     * 获取支付信息
     * @param coinName  币种名称
     * @param chainCoinCode 链币种代码【长度4】,非必传
     * @param orderCode 订单号【长度10到30】
     */
    public function getPaymentInfo($coinName, $chainCoinCode, $orderCode)
    {
        if(empty($coinName) || empty($chainCoinCode) || empty($orderCode)){
            return Lib::result(401, '参数不能为空');
        }
        $uri = '/trade/getPaymentInfo';
        $data = array(
            'coinName' => $coinName,
            'chainCoinCode' => $chainCoinCode,
            'orderCode' => $orderCode
        );
        $signature = $this->signature($data);
        $header = array(
            'X-Merchant-sign:'.base64_encode($signature),
            'Content-Type:application/json;charset=UTF-8',
            'X-Language:'.$this->language,
            'X-Version:'.$this->Version
        );
        $res = Http::post($this->basrUrl.$uri, json_encode($data), $this->expireTime, $header);
        if($res['code'] != 200){
            return Lib::result(999, $res['code'].$res['msg']);
        }
		return $res['res'];
    }

    /**
     * 付款,当orderType为0001时,amount内容为金额,currency为必传,当orderType为0002时amount内容为数量,coinName为必传
     */
    public function pay($amount, $money, $coinName, $currency, $merchantUser, $orderNo, $subject, $body, $description, $expireTime, $orderType)
    {
        if(empty($orderNo) || empty($orderType)){
            return Lib::result(401, '参数不正确');
        }
        $uri = '/trade/pay';
        $data = array(
            'appId' => $this->appid,
            'orderNo' => $orderNo,
            'subject' => $subject,
            'orderType' => $orderType
        );
		
		if(!empty($merchantUser)) $data['merchantUser'] = $merchantUser;
		
        if($orderType == 'BY_AMOUNT'){
            if(empty($amount) || empty($coinName)){
                return Lib::result(402, '参数缺少');
            }
            $data['amount'] = $amount;
            $data['coinName'] = $coinName;

        }elseif($orderType == 'BY_MONEY'){
            if(empty($money) || empty($currency)){
                return Lib::result(403, '参数缺少');
            }
            $data['money'] = $money;
            $data['currency'] = $currency;
        }else{
            return Lib::result(404, 'error');
        }

        if(!empty($body)){
            $data['body'] = $body;
        }
        if(!empty($description)){
            $data['description'] = $description;
        }
        if(!empty($expireTime)){
            $data['expireTime'] = $expireTime;
        }
        $signature = $this->signature($data);
        $header = array(
            'X-Merchant-sign:'.base64_encode($signature),
            'Content-Type:application/json;charset=UTF-8',
            'X-Language:'.$this->language,
            'X-Version:'.$this->Version
        );
        $res = Http::post($this->basrUrl.$uri, json_encode($data), $this->expireTime, $header);

        if($res['code'] != 200){
            return Lib::result(999, $res['code'].$res['msg']);
        }
		return $res['res'];
    }

    /**
     * 补单
     * @param orderCode  orderCode
     * @return
     */
    public function maleUp($remark, $orderCode)
    {
        if(empty($remark) || empty($orderCode)){
            return Lib::result(401, '参数不正确');
        }
        $uri = '/trade/makeUpOrder';
        $timestamp = Lib::timestamp();
        $data = array(
            'appId' => $this->appid,
            'timeStamp' => $timestamp,
            'remark' => $remark,
            'orderCode' => $orderCode,
        );
        $secertSign = openssl_encrypt($this->appid.$timestamp, 'aes-256-ecb', $this->secret, OPENSSL_RAW_DATA);
        $data['secretSign'] = base64_encode($secertSign);
        $signature = $this->signature($data);
        $header = array(
            'X-Merchant-sign:'.base64_encode($signature),
            'Content-Type:application/json;charset=UTF-8',
            'X-Language:'.$this->language,
            'X-Version:'.$this->Version
        );
        $res = Http::post($this->basrUrl.$uri, json_encode($data), $this->expireTime, $header);
        if($res['code'] != 200){
            return Lib::result(999, $res['code'].$res['msg']);
        }
		return $res['res'];
    }
	
	 /**
     * 回调
     * @param orderCode  orderCode
	 * @param orderType  orderType
     * @return
     */
	public function callback($merchantSign, $bodyString)
	{
		if (!$this->verifySignature($merchantSign, $bodyString)) {
			return Lib::result(501, '签名不正确');
		}
		$body = json_decode($bodyString, true);
		// self logic
		switch ($body['orderType'])
		{
		case 'payment':
			// 商家支付回调逻辑
			break;
		case 'makeUp':
			// 商家补单回调逻辑
			break;
		case 'withdraw':
			// 商家提币回调逻辑
			break;
		}
		return Lib::result(200, 'OK');
	}

    /**
     * 查询回调
     * @param orderCode  orderCode
     * @return
     */
    public function getCallback($orderCode)
    {
        if(empty($orderCode)){
            return Lib::result(401, '订单号不能为空');
        }
        $uri = '/trade/getCallback';
        $data = array(
            'orderCode' => $orderCode,
        );
        $signature = $this->signature($data);
        $header = array(
            'X-Merchant-sign:'.base64_encode($signature),
            'Content-Type:application/json;charset=UTF-8',
            'X-Language:'.$this->language,
            'X-Version:'.$this->Version
        );
        $res = Http::post($this->basrUrl.$uri, json_encode($data), $this->expireTime, $header);
        if($res['code'] != 200){
            return Lib::result(999, $res['code'].$res['msg']);
        }
		return $res['res'];
    }

    /**
     * 取消订单
     * @param orderCode 订单号
     */
    public function cancleOrder($orderCode)
    {
        if(empty($orderCode)){
            return Lib::result(401, '订单号不能为空');
        }
        $uri = '/trade/cancel';
        $data = array(
            'orderCode' => $orderCode,
        );
        $signature = $this->signature($data);
        $header = array(
            'X-Merchant-sign:'.base64_encode($signature),
            'Content-Type:application/json;charset=UTF-8',
            'X-Language:'.$this->language,
            'X-Version:'.$this->Version
        );
        $res = Http::post($this->basrUrl.$uri, json_encode($data), $this->expireTime, $header);
        if($res['code'] != 200){
            return Lib::result(999, $res['code'].$res['msg']);
        }
		return $res['res'];

    }

    /**
     * 退款
     * @param address       退款地址【长度5到50】
     * @param amount        退款数量【长度1到50
     * @param orderCode     订单编号【长度5到50】
     * @param remark   退款描述【长度5到50】
     */
    public function refund($refundType, $address, $amount, $orderCode, $remark)
    {
        if(empty($refundType) || empty($amount) || empty($orderCode) || empty($remark)){
            return Lib::result(401, '参数不能为空');
        }
        $uri = '/trade/refund';
        $timestamp = Lib::timestamp();
        $data = array(
            'appId' => $this->appid,
            'timeStamp' => $timestamp,
            'refundType' => $refundType,
            'address' => $address,
            'amount' => $amount,
            'orderCode' => $orderCode,
            'remark' => $remark,
            'appSecret' => $this->secret
        );
        $secertSign = openssl_encrypt($this->appid.$timestamp, 'aes-256-ecb', $this->secret, OPENSSL_RAW_DATA);
        $data['secretSign'] = base64_encode($secertSign);
        $signature = $this->signature($data);
        $header = array(
            'X-Merchant-sign:'.base64_encode($signature),
            'Content-Type:application/json;charset=UTF-8',
            'X-Language:'.$this->language,
            'X-Version:'.$this->Version
        );
        $res = Http::post($this->basrUrl.$uri, json_encode($data), $this->expireTime, $header);
        if($res['code'] != 200){
            return Lib::result(999, $res['code'].$res['msg']);
        }
		return $res['res'];
    }

    /**
     * 获取退款信息
     * @param orderCode     订单编号【长度20到50】
     */
    public function getRefunds($orderCode)
    {
        if(empty($orderCode)){
            return Lib::result(401, '订单号不能为空');
        }
        $uri = '/trade/getRefunds';
        $data = array(
            'orderCode' => $orderCode,
        );
        $signature = $this->signature($data);
        $header = array(
            'X-Merchant-sign:'.base64_encode($signature),
            'Content-Type:application/json;charset=UTF-8',
            'X-Language:'.$this->language,
            'X-Version:'.$this->Version
        );
        $res = Http::post($this->basrUrl.$uri, json_encode($data), $this->expireTime, $header);
        if($res['code'] != 200){
            return Lib::result(999, $res['code'].$res['msg']);
        }
		return $res['res'];
    }

    /**
     *提现
     * @param address              地址
     * @param amount               数量【最小0.000001】
     * @param coinName             币种
     * @param merchantUser     	   商家用户【长度10到20之间】
     * @param orderNo              订单号【长度10到30】
     */
    public function withdraw($address, $amount, $coinName, $merchantUser, $orderNo, $orderType, $currency ='', $money='', $protocolName)
    {
        if(empty($address) || empty($merchantUser) || empty($orderNo) || empty($orderType)){
            return Lib::result(401, '参数不正确');
        }
        $uri = '/trade/withdrawal';
        $timeStamp = Lib::timestamp();
        $data = array(
            'appId' => $this->appid,
            'timeStamp' => $timeStamp,
            'orderNo' => $orderNo,
            'address' => $address,
            'merchantUser' => $merchantUser,
        );
        if($orderType == 'BY_AMOUNT'){
            if(!isset($amount) || empty($coinName)){
                return Lib::result(402, '参数缺少');
            }
            $data['amount'] = $amount;
            $data['coinName'] = $coinName;

        }elseif($orderType == 'BY_MONEY'){
            if(!isset($money) || empty($currency)){
                return Lib::result(403, '参数缺少');
            }
            $data['money'] = $money;
            $data['currency'] = $currency;
        }else{
            return Lib::result(404, 'error');
        }
		$data['protocolName'] = $protocolName;
		
		$secertSign = openssl_encrypt($this->appid.$timestamp, 'aes-256-ecb', $this->secret, OPENSSL_RAW_DATA);
        $data['secretSign'] = base64_encode($secertSign);
		
        $signature = $this->signature($data);
        $header = array(
            'X-Merchant-sign:'.base64_encode($signature),
            'Content-Type:application/json;charset=UTF-8',
            'X-Language:'.$this->language,
            'X-Version:'.$this->Version
        );
        $res = Http::post($this->basrUrl.$uri, json_encode($data), $this->expireTime, $header);
		
        if($res['code'] != 200){
            return Lib::result(999, $res['code'].$res['msg']);
        }

		return $res['res'];
    }
	
	 /**
     * 获取链列表
	 * @param appId appId
     * @param coinName coinName
     * @return
     */
	public function getChainCoins($appId = '', $coinName = '')
	{
		$uri = '/trade/getChainCoins';
		if(empty($appId) || empty($coinName)){
            return Lib::result(402, '参数错误');
        }
		$data = array(
			'appId' => $appId,
			'coinName' => $coinName
		);
		$signature = $this->signature($data);
		$header = array(
            'X-Merchant-sign:'.base64_encode($signature),
            'Content-Type:application/json;charset=UTF-8',
            'X-Language:'.$this->language,
            'X-Version:'.$this->Version
        );
		$res = Http::post($this->basrUrl.$uri, json_encode($data), $this->expireTime, $header);
		if($res['code'] != 200){
            return Lib::result(999, $res['code'].$res['msg']);
        }
		return $res['res'];
	}

    /**
     * 获取汇率
     * @param coinName coinName
     * @param currency currencyEnum
     * @return
     */
    public function getCurrencyCoinPrice($coinName='', $currency='')
    {
        $uri = '/trade/getCurrencyCoinPrice';
		
		if(empty($coinName) || empty($currency)){
            return Lib::result(402, '参数错误');
        }

		$data = array(
			'coinName' => $coinName,
			'currency' => $currency
		);

        $signature = $this->signature($data);
        $header = array(
            'X-Merchant-sign:'.base64_encode($signature),
            'Content-Type:application/json;charset=UTF-8',
            'X-Language:'.$this->language,
            'X-Version:'.$this->Version
        );

        $res = Http::post($this->basrUrl.$uri, json_encode($data), $this->expireTime, $header);

		if($res['code'] != 200){
            return Lib::result(999, $res['code'].$res['msg']);
        }
		return $res['res'];
    }


    /**
     * 获取单价汇率
     * @param coinName coinName
     * @param currency currencyEnum
     * @return
     */
    public function getCoinPrice($orderTypeCodeEnum, $amount='', $money='', $coinName='', $currency='')
    {
        $uri = '/trade/getCoinPrice';
        if(empty($orderTypeCodeEnum) || !in_array($orderTypeCodeEnum, array('BY_AMOUNT', 'BY_MONEY'))){
            return Lib::result(401, '参数不能为空');
        }
        if($orderTypeCodeEnum == 'BY_AMOUNT'){
            if(!isset($amount) || empty($coinName)){
                return Lib::result(402, '参数错误');
            }
            $data = array(
                'amount' => $amount,
                'payType' => $orderTypeCodeEnum,
                'coinName' => $coinName
            );
        }elseif($orderTypeCodeEnum == 'BY_MONEY'){
            if(!isset($money) || empty($currency)){
                return Lib::result(402, '参数错误');
            }
            $data = array(
                'money' => $money,
                'payType' => $orderTypeCodeEnum,
                'currency' => $currency
            );
        }else{
            return Lib::result(412, '参数错误');
        }
        $signature = $this->signature($data);
        $header = array(
            'X-Merchant-sign:'.base64_encode($signature),
            'Content-Type:application/json;charset=UTF-8',
            'X-Language:'.$this->language,
            'X-Version:'.$this->Version
        );

        $res = Http::post($this->basrUrl.$uri, json_encode($data), $this->expireTime, $header);

		if($res['code'] != 200){
            return Lib::result(999, $res['code'].$res['msg']);
        }
		return $res['res'];
    }

    /**
     * 获取账单
     * @param startTime 开始时间
     * @param endTime 结束时间
     * @param pageSize 数量
     * @param pageNo 页数
     */
    public function getBillRecords($startTime, $endTime, $pageSize, $pageNo)
    {
        $uri = '/trade/getBill';
        $data = array(
            'startTime' => $startTime,
            'endTime' => $endTime,
            'pageSize' => $pageSize,
            'pageNo' => $pageNo,
        );
        $signature = $this->signature($data);
        $header = array(
            'X-Merchant-sign:'.base64_encode($signature),
            'Content-Type:application/json;charset=UTF-8',
            'X-Language:'.$this->language,
            'X-Version:'.$this->Version
        );
        $res = Http::post($this->basrUrl.$uri, json_encode($data), $this->expireTime, $header);
        if($res['code'] != 200){
            return Lib::result(999, $res['code'].$res['msg']);
        }
		return $res['res'];
    }

    //签名
    public function signature($data){
        $data_str = '';
        ksort($data);
        foreach ($data as $key => $val){
            $data_str .= $key.'='.$val.',';
        }
        $data_str = rtrim($data_str, ',');
        $privateKey = "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($this->privateKey, 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";
        $pi_key = openssl_get_privatekey($privateKey);
        openssl_sign($data_str, $signature, $pi_key, "SHA256");
        return $signature;
    }

	/**
	 * @param headerSignString header中的签名(X-Merchant-sign)
     * @param bodyString  body体内容
	 */
	public function verifySignature($headerSignString, $bodyString) {
		if (empty($headerSignString) || empty($bodyString)) {
			return Lib::result(401, '请传入签名头和body体');
		}
		// 处理boolean类型错误
		$bodyString = str_replace("false","\"false\"", str_replace("true","\"true\"", $bodyString));
		$signString = $this->generateClearTextSign($bodyString);
		// 组装公钥
		$publicKey = "-----BEGIN PUBLIC KEY-----\n" .
			wordwrap($this->publicKey, 64, "\n", true) .
			"\n-----END PUBLIC KEY-----";
		$pub_key = openssl_get_publickey($publicKey);
		// 验证签名
		$isRight = (bool)openssl_verify($signString, base64_decode($headerSignString), $pub_key ,"SHA256");
		return $isRight;
	}

    public function generateClearTextSign($bodyString){
        $data = json_decode($bodyString, true);
        $data_str = '';
        ksort($data);
        foreach ($data as $key => $val){
            $data_str .= $key.'='.$val.',';
        }
        $data_str = rtrim($data_str, ',');
        return $data_str;
    }

}