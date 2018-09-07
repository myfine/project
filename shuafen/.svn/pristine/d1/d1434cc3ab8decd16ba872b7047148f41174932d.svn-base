<?php
namespace Home\Controller;
use Think\Controller;
class CallbackController extends Controller {
	
	/**
	 * qq登录后处理
	 */
	public function qqlogin(){
		$user = qqlogincallback();
		
		$id = $user['openid'];
		$nickname = $user['nickname'];//昵称
		$avatar = $user['figureurl_qq_2'];//头像
		$gender = $user['gender'];//性别
		

		$shejiao = 'QQ';

		$this -> commonlogin($id,$nickname,$avatar,$shejiao,$gender);
	}
	
	
	/**
	 * 微信登录后处理
	 */
	public function wxlogin(){
		if($_GET['state']!=$_SESSION["wx_state"]){
			exit("5001");
		}
		$AppID = "wx1bd278f18e270877";
		$AppSecret = "f9167484d81c5f7ac9d1399e4488c7db";
		$url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$AppID.'&secret='.$AppSecret.'&code='.$_GET['code'].'&grant_type=authorization_code';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_URL, $url);
		$json =  curl_exec($ch);
		curl_close($ch);
		$arr=json_decode($json,1);
		//得到 access_token 与 openid
		$url='https://api.weixin.qq.com/sns/userinfo?access_token='.$arr['access_token'].'&openid='.$arr['openid'].'&lang=zh_CN';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_URL, $url);
		$json =  curl_exec($ch);
		curl_close($ch);
		$user_obj=json_decode($json,1);
	
		//得到 用户资料
		$nickname = $user_obj['nickname'];
		$avatar = $user_obj['headimgurl'];
		$unionid = $user_obj['unionid'];
	
		$id = $unionid;
		$shejiao = "微信";
		$gender = "男";
		if($user_obj['sex'] == 1){
			$gender = 1;
		}else{
			$gender = 0;
		}
		$this -> commonlogin($id,$nickname,$avatar,$shejiao,$gender);
	
	}
	
	
	/**
	 * 第三方登录公共方法
	 */
	function commonlogin($id,$nickname,$avatar,$gender,$shejiao){
		if($id && $nickname && $avatar && $shejiao && $gender){
			$user = M("pwuser") -> where(array(
					"id" => $id
			)) -> find();
			if(!$user){
				$num = M('pwuser')->order('userid desc')->select();
				$ordernum = $num[0]['userid'];
				if($gender=='男'){
					$sex = 1;
				}else{
					$sex = 0;
				}
				$rs = M("pwuser") -> add(array(
						"id" => $id,
						"userid"=>$ordernum+1,
						"username" => $nickname,
						"logo" => $avatar,
						"sex" => $sex,
						"time" => time(),
				));
				if($rs){
					$user = M("pwuser") -> where(array(
							"id" => $id
					)) -> find();
					session("user",$user);
					$returnVal['error'] = "";
					$this -> redirect("Index/index");
				}else{
					$returnVal['error'] = "登录失败";
					$this -> redirect("Index/index",array("type" => 2),1, '登录失败,页面跳转中...');
				}
			}else{
				session("user",$user);
				$returnVal['error'] = "";
				$this -> redirect("Index/index");
			}
		}else{
			$returnVal['error'] = "缺少参数";
			$this -> redirect("Index/index",array("type" => 2),1, '缺少参数,页面跳转中...');
		}
	}
	

	/**
	 * fenbi支付宝支付回调地址
	 */
	public function alipay_notify_url(){
		$verify_result = alipay_notify_url();
		if($verify_result) {//验证成功
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//请在这里加上商户的业务逻辑程序代
		
			//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
		
			//获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
		
			//商户订单号自己
			$out_trade_no = $_POST['out_trade_no'];
		
			//支付宝交易号//支付宝
			$trade_no = $_POST['trade_no'];
		
			//交易状态
			$trade_status = $_POST['trade_status'];
		
			if($_POST['trade_status'] == 'TRADE_FINISHED') {
				//判断该笔订单是否在商户网站中已经做过处理
				//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
				//请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
				//如果有做过处理，不执行商户的业务程序
		
				//注意：
				//退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
		
				//调试用，写文本函数记录程序运行情况是否正常
				//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
			}else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
				//判断该笔订单是否在商户网站中已经做过处理
				//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
				//请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
				//如果有做过处理，不执行商户的业务程序
				if("2088802354941530" != $_POST['seller_id']){
		 			logx("seller_id错误，支付失败");
		 			echo "fail";
		 			die;
		 		}
		 		$data['userid'] = I("get.userid");
		 		if($data['userid'] && I("get.remarks")){
		 			//查询粉币
		 			if($_POST["total_fee"]){
		 				$total_fee = $_POST["total_fee"];
		 			}
		 			
		 			if($_POST["total_amount"]){
		 				$total_fee = $_POST["total_amount"];
		 			}
		 			
		 			if(I("get.remarks") == 2){
		 				//首充礼包
		 				$first_charge = M("sf_first_charge") -> where(array(
		 						"id" => 1,
		 				)) -> find();
		 				$fenbi = $first_charge['fenbi1'] + $first_charge['fenbi2'];
		 			}else{
		 				$fenbi = M("coin") -> where(array(
		 						"price" => $total_fee,
		 				)) -> getField("fenbi");
		 			}
		 			
		 			//查询积分记录
		 			$ir = M("integral_record") -> where(array(
		 				"out_trade_no" => $_POST['out_trade_no']
		 			)) -> find();
		 			
		 			if(!$ir){
		 				//首充
		 				if(I("get.remarks") == 2){
		 					//首充礼包
		 					M("first_charge_record") -> add(array(
		 						"userid" => I("get.userid")
		 					));
		 				}
		 				
		 				
		 				//添加积分记录
		 				$data['id'] = create_uuid();
		 				$data['Integralrecord'] = "+" + $fenbi;
		 				if(I("get.remarks") == 1){
		 					$data['remark'] = "购买粉币";
		 				}else{
		 					$data['remark'] = "首充礼包";
		 				}
		 				$data['userid'] = $data['userid'];
		 				$data['time'] = time();
		 				$data['out_trade_no'] = $_POST['out_trade_no'];
		 				$data['trade_no'] = $_POST['trade_no'];
		 				$data['trade_status'] = $_POST['trade_status'];
		 				M('integral_record') -> add($data);
		 				//增加用户积分
		 				M("user") -> where(array("id" => $data['userid'])) -> setInc("fenbi",$fenbi);
		 				//添加充值记录
		 				$nickname = M("user") -> where(array("id" => $data['userid'])) -> getField("nickname");
		 				M("recharge") -> add(array(
		 					"nickname" => $nickname,
		 					"fenbi" => $fenbi,
		 					"cdate" => date("Y-m-d"),
		 					"time" => time(),
		 				));
		 				
		 			}
		 			logx("支付成功");
		 			echo "success";
		 		}else{
		 			logx("缺少参数");
		 		}
		 

				//注意：
				//付款完成后，支付宝系统发送该交易状态通知
		
				//调试用，写文本函数记录程序运行情况是否正常
				//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
				
				//根据$out_trade_no查找订单，并修改订单状态
				
			}
		
			//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
		
			echo "success";		//请不要修改或删除
		
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		}else {
			//验证失败

			$data['status'] = 2;
			$data['out_trade_no'] = $out_trade_no;
        	$data['trade_status'] = $trade_status;
        	
        	M('order') -> where(array("out_trade_no" => $out_trade_no)) -> save($data);


			echo "fail";
		
			//调试用，写文本函数记录程序运行情况是否正常
			//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
		}
	}
	

	/**
	 * 微信支付回调地址
	 */
	public function wxpay_notify_url(){
		wxpayc($_GET['userid']);
	}
	public function fwxpay_notify_url(){
		fwxpayc($_GET['userid']);
	}
}