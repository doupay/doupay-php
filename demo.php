<?php
    include_once './vendor/autoload.php';

    use doupay\doupayphp\Constants;
    header("content-type:application/json");
    //初始化参数
	$appid = '';
    $secret = '';
    $privateKey = '';
    $publicKey = '';
    $expireTime = '20';
    //初始化
    $obj = Constants::init($appid, $secret, $publicKey, $privateKey, $expireTime);

    $params = json_decode(file_get_contents("php://input"), true);
    $method = isset($_POST['method']) ? $_POST['method'] : $_GET['method'];
	switch ($method)
	{
	//获取币种列表
	case 'getCoinList':
		$res = $obj->getCoinList();
		break;
	case 'getCurrencyList':
		//获取法币列表
		$res = $obj->getCurrencyList();
		break;
	case 'getOrderInfo':
		//获取订单信息
		$res = $obj->getOrderInfo($params['orderCode']);
		break;
	case 'getPaymentInfo':
		//获取支付信息
		$res = $obj->getPaymentInfo($params['coinName'], $params['chainCoinCode'], $params['orderCode']);
		break;
	case 'pay':
		//付款
		$orderNo = 'DD' . date('YmdHis',time()). rand(pow(10,2), pow(10,3)-1);
		$subject = '测试商品名称';
		$res = $obj->pay($params['amount'], $params['money'], $params['coinName'], $params['currency'], $params['merchantUser'], $orderNo, $subject, $params['body'], $params['description'], $params['expireTime'], $params['orderType'], $params['hashId']);
		break;
	case 'maleUp':
		//补单
		$res = $obj->maleUp($params['remark'], $params['orderCode']);
		break;
	case 'cancleOrder':
		//取消订单
		$res = $obj->cancleOrder($params['orderCode']);
		break;
	case 'refund':
		//退款
		$res = $obj->refund($params['refundType'], $params['address'], $params['amount'],$params['orderCode'], $params['remark']);
		break;
	case 'getRefunds':
		//获取退款信息
		$res = $obj->getRefunds($params['orderCode']);
		break;
	case 'withdraw':
		//提现
		$res = $obj->withdraw($params['address'], $params['amount'], $params['coinName'],$params['merchantUser'], $params['orderNo'], $params['orderType']);
		break;
	case 'getCoinPrice':
		//获取汇率
		$res = $obj->getCurrencyCoinPrice($params['coinName'], $params['currency']);
		break;
	case 'getBillRecords':
		//获取账单
		$res = $obj->getBillRecords($params['startTime'], $params['endTime'], $params['pageSize'],$params['pageNo']);
		break;
	case 'getCallback':
		//查询回调
		$res = $obj->getCallback($params['orderCode']);
		break;
	case 'callback':
		//回调
		$res = $obj->callbakc($params['orderCode'], $params['orderType'], $params['coinName'], $params['protocolName'], $params['price'], $params['address'], $params['amountPaid'], $params['money'], $params['result'], $params['paymentStatus']);
		break;
	default:
		$res = json_encode(array('code'=>444, 'message'=>'error'));
    }
	
	$res_data = json_decode($res, true);
    echo json_encode(array('code'=>$res_data['code'], 'message'=>$res_data['message'], 'data'=>$res_data['data']));die;
