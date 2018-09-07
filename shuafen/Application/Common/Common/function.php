<?php
function create_uuid($prefix = "shuafen"){    //可以指定前缀
	$str = md5(uniqid(mt_rand(), true));
	$uuid  = substr($str,0,8) . '_';
	$uuid .= substr($str,8,4) . '_';
	$uuid .= substr($str,12,4) . '_';
	$uuid .= substr($str,16,4) . '_';
	$uuid .= substr($str,20,12);
	return $prefix . $uuid;
}


function https_request($url,$data = null){
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	if (!empty($data)){
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	}
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$output = curl_exec($curl);
	curl_close($curl);
	return $output;
}

function logx($content){
	require_once './log/log.php';
	//初始化日志
	$logHandler= new CLogFileHandler("./log/alipay/".date('Y-m-d').'.log');
	$log = Logx::Init($logHandler, 15);
	Logx::DEBUG($content);
}

/**
 * 验证AppStore内付
 * @param  string $receipt_data 付款后凭证
 * @return array                验证是否成功
 */
function validate_apple_pay($receipt_data){
	/**
	 * 21000 App Store不能读取你提供的JSON对象
	 * 21002 receipt-data域的数据有问题
	 * 21003 receipt无法通过验证
	 * 21004 提供的shared secret不匹配你账号中的shared secret
	 * 21005 receipt服务器当前不可用
	 * 21006 receipt合法，但是订阅已过期。服务器接收到这个状态码时，receipt数据仍然会解码并一起发送
	 * 21007 receipt是Sandbox receipt，但却发送至生产系统的验证服务
	 * 21008 receipt是生产receipt，但却发送至Sandbox环境的验证服务
	 */
	function acurl($receipt_data, $sandbox=0){
		//小票信息
		$POSTFIELDS = array("receipt-data" => $receipt_data);
		$POSTFIELDS = json_encode($POSTFIELDS);

		//正式购买地址 沙盒购买地址
		$url_buy     = "https://buy.itunes.apple.com/verifyReceipt";
		$url_sandbox = "https://sandbox.itunes.apple.com/verifyReceipt";
		$url = $sandbox ? $url_sandbox : $url_buy;

		//简单的curl
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $POSTFIELDS);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
	// 验证参数
	if (strlen($receipt_data)<20){
		$result=array(
				'status'=>false,
				'message'=>'非法参数'
		);
		return $result;
	}
	// 请求验证
	$html = acurl($receipt_data);
	$data = json_decode($html,true);

	// 如果是沙盒数据 则验证沙盒模式
	if($data['status']=='21007'){
		// 请求验证
		$html = acurl($receipt_data, 1);
		$data = json_decode($html,true);
		$data['sandbox'] = '1';
	}

	if (isset($_GET['debug'])) {
		exit(json_encode($data));
	}

	// 判断是否购买成功
	if(intval($data['status'])===0){
		$result=array(
				'status'=>true,
				'message'=>'购买成功'
		);
	}else{
		$result=array(
				'status'=>false,
				'message'=>'购买失败 status:'.$data['status']
		);
	}
	return $result;
}

/**
 * 输出xml字符
 * @param $params 参数名称
 * return string 返回组装的xml
 **/
function data_to_xml2($params){
	if(!is_array($params)|| count($params) <= 0)
	{
		return false;
	}
	$xml = "<xml>";
	foreach ($params as $key=>$val)
	{
		if (is_numeric($val)){
			$xml.="<".$key.">".$val."</".$key.">";
		}else{
			$xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
		}
	}
	$xml.="</xml>";
	return $xml;
}

/**
 * 将xml转为array
 * @param string $xml
 * return array
 */
function xml_to_data($xml){
	if(!$xml){
		return false;
	}
	//将XML转为array
	//禁止引用外部xml实体
	libxml_disable_entity_loader(true);
	$data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
	return $data;
}

/**
 * 微信错误代码
 * @param $code 服务器输出的错误代码
 * return string
 */
function error_code( $code ){
	$errList = array(
			'NOAUTH' => '商户未开通此接口权限',
			'NOTENOUGH' => '用户帐号余额不足',
			'ORDERNOTEXIST' => '订单号不存在',
			'ORDERPAID' => '商户订单已支付，无需重复操作',
			'ORDERCLOSED' => '当前订单已关闭，无法支付',
			'SYSTEMERROR' => '系统错误!系统超时',
			'APPID_NOT_EXIST' => '参数中缺少APPID',
			'MCHID_NOT_EXIST' => '参数中缺少MCHID',
			'APPID_MCHID_NOT_MATCH' => 'appid和mch_id不匹配',
			'LACK_PARAMS' => '缺少必要的请求参数',
			'OUT_TRADE_NO_USED' => '同一笔交易不能多次提交',
			'SIGNERROR' => '参数签名结果不正确',
			'XML_FORMAT_ERROR' => 'XML格式错误',
			'REQUIRE_POST_METHOD' => '未使用post传递参数 ',
			'POST_DATA_EMPTY' => 'post数据不能为空',
			'NOT_UTF8' => '未使用指定编码格式',
	);
	if( array_key_exists( $code , $errList ) ){
		return $errList[$code];
	}
}

/**
 * 接收通知成功后应答输出XML数据
 * @param string $xml
 */
function replyNotify(){
	$data['return_code'] = 'SUCCESS';
	$data['return_msg'] = 'OK';
	$xml = data_to_xml2( $data );
	echo $xml;
	die();
}