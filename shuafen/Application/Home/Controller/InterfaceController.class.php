<?php
namespace Home\Controller;
use Think\Controller;
class InterfaceController extends Controller {
	
	public function test(){
		$url='http://'.$_SERVER['HTTP_HOST'].'/aixuewen/index.php/Home/Interface/regist';
		$data=array(
			"username" => 18020225134,
			"password" => "123",
			"nickname" => "Ricky",
			"type" => 3
		);
		
		$data = json_encode($data);
		$my = https_request($url,$data);
		echo $my;
	}
	
	/**
	 * 登陆
	 */
    public function login(){
    	if($GLOBALS["HTTP_RAW_POST_DATA"]){
			$param = json_decode($GLOBALS["HTTP_RAW_POST_DATA"],true);
			$data['id'] = $param['id'];
			$data['nickname'] = $param['nickname'];
			$data['avatar'] = $param['avatar'];
			
			//测试数据获取
			
//			$data['nickname'] = I('nickname');
//			$data['avatar'] = I('avatar');
//			$data['id']= I('id');
			
			if(!$data['id']){
				$returnVal['error'] = "id不能为空";
				$this -> ajaxReturn($returnVal);
			}
			if(!$data['nickname']){
				$returnVal['error'] = "昵称不能为空";
				$this -> ajaxReturn($returnVal);
			}
			if(!$data['avatar']){
				$returnVal['error'] = "头像不能为空";
				$this -> ajaxReturn($returnVal);
			}
			
			
			
			$data['last_login_time'] = time();
	  		$data['last_login_ip'] = get_client_ip();

    		$existed = M('user') -> where(array("id" => $data['id'])) -> find();
    		if($existed){
  				M('user')->save($data);
  				$existed = M('user') -> where(array("id" => $data['id'])) -> field("id,nickname,avatar,identityid,fenbi") -> find();
				$returnVal['error'] = "";
				//返回信息
				$returnVal['userinfo'] = $existed;
    		}else{
    			while(1){			//防止随机数重复
    				$data['identityid'] = rand(10000,999999);//随机生成
    				$sjs = M('user') -> where(array("identityid" => $data['identityid'])) ->find();
    				if($sjs){
    					continue;
    				}else{
    					break;
    				}
    			}
    			$data['fenbi'] = 0;
    			M('user') -> data($data) -> add();
    			$existed = M('user') -> where(array("id" => $data['id'])) -> field("id,nickname,avatar,identityid,fenbi") -> find();
    			if($existed){
    				$returnVal['error'] = "";
					//返回信息
					$returnVal['userinfo'] = $existed;
    			}
    			else{
    				$returnVal['error'] = "注册数据录入失败";
    			}
    		}
		}else{
			$returnVal['error'] = "非法请求";
		}
    	
    	$this -> ajaxReturn($returnVal);
  	}



	public function uploadfile(){
    	$data = array();
    	$upload = new\Think\Upload();
    	//设置附件上传目录
    	$upload->rootPath  = './Public/upload/';
    	$upload->savePath  = ''; // 设置附件上传（子）目录
    	$upload->autoSub = true;
    	$upload->subName = array('date','Ymd');
    	$info = $upload -> upload();
    	if (!$info) {
    		//捕获上传异常
    		$data['error'] = $upload->getError();
    		return $data;
    	}else{
			//取得成功上传的文件信息
			foreach($info as $file){
				$data['savename'] = $file['savepath'].$file['savename'];
			}
			$data['error'] = "";
			return $data;
    	}	
    }
    
    
    /**
     * 返回产品列表【名称/图片/id】
     */
    public function products(){
    	$res = M('products') -> select();
    	foreach($res as $key => $val){
    		$res[$key]['pic'] = 'http://'.$_SERVER['HTTP_HOST']."/Public/upload/".$res[$key]['pic'];
    		$res[$key]['shuafen'] = M('pay') -> where(array(
				"proid" => $val['id']
			)) -> field('num_shua,num_fenbi') -> select();
    	}    	
    	if($res){
    		$returnVal['error'] = '';
    		$returnVal['productlist'] = $res;
    	}else{
    		$returnVal['error'] = '数据读取失败';
    	}
    	$this -> ajaxReturn($returnVal);
    }
    
    /**
     * 签到
     */
    public function check(){
    	if($GLOBALS["HTTP_RAW_POST_DATA"]){
			$param = json_decode($GLOBALS["HTTP_RAW_POST_DATA"],true);
			$data['id'] = $param['id'];
//			$data['id'] = I(id);   //浏览器测试
			if(!$data['id']){
				$returnVal['error'] = "请登录";
			}else{
				$addfb = M('add_sub_rule') -> where(array("id" => 1)) ->getField('add_or_sub');//ID为1，表示签到
				
				$qd_today = M("user") -> where(array(
		    		"id" => $data['id'],
		    		"checktime" => date("Y-m-d"),
		    	)) -> find();
		    	
		    	$noqiandao = M("user") -> where(array(
		    		"id" => $data['id']
		    	)) -> find();
		    	
				if($qd_today){
					$returnVal['error'] = '今天你已经签过了哦！';
				}else{
					$data['checktime'] = date("Y-m-d");
					$data['fenbi'] = $noqiandao['fenbi'] + $addfb;
					$this -> add_Integral_record($data['id'],"+".$addfb,"签到返积分");
					$res = M('user') -> save($data);
					if($res){
						$returnVal['error'] = '';
					}else{
						$returnVal['eror'] = '签到失败，请重新签到';
					}
				}
			}	
		}else{
			$returnVal['error'] = "非法请求";
		}
    	$this -> ajaxReturn($returnVal);
    }
    
    /**
     * 添加积分记录
     */
    public function add_Integral_record($userid,$add_sub,$remark){
    	$data['id'] = create_uuid();
    	$data['Integralrecord'] = $add_sub;
    	$data['remark'] = $remark;
    	$data['userid'] = $userid;
    	$data['time'] = time();
    	M('integral_record') -> add($data);
    }
    
    /**
     * 关注公众号增加积分
     */
    public function attention(){
    	if($GLOBALS["HTTP_RAW_POST_DATA"]){
			$param = json_decode($GLOBALS["HTTP_RAW_POST_DATA"],true);
			$data['id'] = $param['id'];
//			$data['id'] = I(id);   //浏览器测试
			if(!$data['id']){
				$returnVal['error'] = "请登录";
			}else{
				$res = M('integral_record') -> where(array(
					"remark" => "关注公众号返积分",
					"userid" => $data['id']
				)) ->find();
				if($res){
					$returnVal['error'] = "你已关注过公众号,无法重复获得奖励";
				}else{
					$addfb = M('add_sub_rule') -> where(array("id" => 2)) ->getField('add_or_sub');//ID为2 表示公众号
					
					$getfenbi = M("user") -> where(array(
			    		"id" => $data['id']
			    	)) -> find();
			    	$data['fenbi'] = $getfenbi['fenbi'] + $addfb;
					$res2 = M('user') -> save($data);
					if($res2){
						$returnVal['error'] = '';
						$this -> add_Integral_record($data['id'],"+".$addfb,"关注公众号返积分");
					}else{
						$returnVal['error'] = '积分赠送失败，请重试';
					}
				}
			}

		}else{
			$returnVal['error'] = "非法请求";
		}
    	$this -> ajaxReturn($returnVal);
    	
    }
    
    
    
    /**
     * 获取积分记录
     */
    public function getintegralrecord(){
    	if($GLOBALS["HTTP_RAW_POST_DATA"]){
			$param = json_decode($GLOBALS["HTTP_RAW_POST_DATA"],true);
			
			$userid = $param['userid'];
			$pageNo = $param['pageNo'] ? $param['pageNo'] : 1;
			$pageSize = C("PAGE_SIZE");
			$firstIndex = ($pageNo - 1) * $pageSize;
			$limit = "{$firstIndex},{$pageSize}";
	    	
	    	$record = M('integral_record') ->where(array("userid" => $userid)) -> order("time desc") -> field("Integralrecord,remark,time")-> limit($limit) ->select();
			
			for($i=0;$i<count($record);$i++){
				$record[$i]['time'] = date("m-d H:i",intval($record[$i]['time']));
			}
			
			$returnVal['error']= '';
			$returnVal['list'] = $record ? $record : array();
		}else{
			$returnVal['error'] = "非法请求";
		}
		
		$this -> ajaxReturn($returnVal);
    }
	
	
	
	/**
	 * 提交订单
	 */
	 
	 public function submitorder(){
	 	
		if($GLOBALS["HTTP_RAW_POST_DATA"]){
			$param = json_decode($GLOBALS["HTTP_RAW_POST_DATA"],true);
			
			$data['userid'] = $param['userid'];
			$data['productid'] = $param['productid'];
			$data['shuafen_num'] = $param['shuafen_num'];
			$data['needfenbi'] = $param['needfenbi'];
			$data['content'] = $param['content'];
			
			
			$data['id'] = create_uuid();
			$data['time'] = time();
			
			if(!$data['userid']){
				$returnVal['error'] = "用户获取失败，请重试";
				$this -> ajaxReturn($returnVal);
			}elseif(!$data['productid']){
				$returnVal['error'] = "商品获取失败，请重试";
				$this -> ajaxReturn($returnVal);
			}elseif(!$data['shuafen_num']){
				$returnVal['error'] = "请选择要刷的次数";
				$this -> ajaxReturn($returnVal);
			}elseif(!$data['content']){
				$returnVal['error'] = "请输入内容后提交订单";
				$this -> ajaxReturn($returnVal);
			}
			$isuser = M('user') -> where(array("id" => $data['userid'])) ->find();
			if($isuser){
				if($isuser['fenbi'] - $param['needfenbi'] < 0){
					$returnVal['error'] = "余额不足，请充值";
					$this -> ajaxReturn($returnVal);
				}
				
				$res = M('order') -> data($data) ->add();
				if($res){
					$returnVal['error'] = "";
					//扣除用户粉币
					M("user") -> where(array("id" => $data['userid'])) -> setDec("fenbi",$data['needfenbi']);
					//增加粉币记录
					$product = M("products") -> where(array("id" => $param['productid'])) -> find();
					$this -> add_Integral_record($data['userid'],"-".$data['needfenbi'],"购买".$product['title']);
				}else{
					$returnVal['error'] = "订单创建失败";
				}
			}else{
				$returnVal['error'] = "没有此用户";
			}
		}else{
			$returnVal['error'] = "非法请求";
		}

		$this -> ajaxReturn($returnVal);
	 }
	 

	 /**
	  * 支付回调地址(支付宝)
	  */
	 public function alipay_notify_url(){
	 	if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
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
	 	}else{
	 		logx("支付宝，支付失败".$_POST['trade_status']);
	 		echo "fail";
	 	}
	 }
	 
	 /**
	  * 微信充值
	  */
	 public function recharge_wxpay(){
	 
	 	$data['userid'] = I("get.userid");
	 	logx("userid：".$data['userid']);
	 	logx("remarks：".I("get.remarks"));
	 	if($data['userid'] && I("get.remarks")){
	 		$result = xml_to_data($GLOBALS['HTTP_RAW_POST_DATA']);
	 		
	 		logx("进入支付".json_encode($result));
	 		
	 		if( !empty($result['result_code']) && !empty($result['err_code']) ){
	 			$err_msg = error_code( $result['err_code'] );
	 			$data['return_code'] = 'FAIL';
	 			$xml = data_to_xml( $data );
	 			logx("支付失败".$xml);
	 			die();
	 		}
	 		
	 		//验证appid
	 		if($result['appid'] != "wxd72f5f5f1b177aae"){
	 			replyNotify();
	 			logx("支付失败".$result['appid']);
	 			die;
	 		}
	 		//验证商户id
	 		if($result['mch_id'] != "1491117732"){
	 			replyNotify();
	 			logx("支付失败".$result['mch_id']);
	 			die;
	 		}
	 		
	 		if($data['userid'] && I("get.remarks")){
	 			//查询粉币
	 			if(I("get.remarks") == 2){
	 				//首充礼包
	 				$first_charge = M("sf_first_charge") -> where(array(
	 						"id" => 1,
	 				)) -> find();
	 				$fenbi = $first_charge['fenbi1'] + $first_charge['fenbi2'];
	 			}else{
	 				$total_fee = $result["total_fee"] / 100;
	 				$fenbi = M("coin") -> where(array(
	 						"price" => $total_fee,
	 				)) -> getField("fenbi");
	 			}
	 			
	 			//添加积分记录
	 			$ir = M("integral_record") -> where(array(
	 					"wx_out_trade_no" => $result["out_trade_no"]
	 			)) -> find();
	 				
	 			if(!$ir){
	 				if(I("get.remarks") == 2){
	 					//首充礼包
	 					M("first_charge_record") -> add(array(
	 					"userid" => I("get.userid")
	 					));
	 				}
	 				//添加记录
	 				$data['id'] = create_uuid();
	 				$data['Integralrecord'] = "+" + $fenbi;
	 				if(I("get.remarks") == 1){
	 					$data['remark'] = "购买粉币";
	 				}else{
	 					$data['remark'] = "首充礼包";
	 				}
	 				$data['time'] = time();
	 				$data['wx_out_trade_no'] = $result["out_trade_no"];
	 				$data['result_code'] = $result["result_code"];
	 				$data['total_fee'] = $total_fee;
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
	 				logx("支付成功");
	 				replyNotify();
	 			}
	 		}else{
	 			logx("缺少参数");
	 		}
	 	}else{
	 		logx("wypay_app:缺少参数");
	 	}
	 }
	 
	 public function recharge_apple(){
	 	if($GLOBALS["HTTP_RAW_POST_DATA"]){
	 		$param = json_decode($GLOBALS["HTTP_RAW_POST_DATA"],true);
	 		$receipt_data = $param['apple_receipt'];//苹果内购的验证收据
	 		$userid = $param['userid'];
	 		$fenbi = $param['fenbi'];
	 		$remarks = $param['remarks'];
	 		if($receipt_data){
	 			// 验证支付状态
	 			$result=validate_apple_pay($receipt_data);
	 			if($result['status']){
	 				// 验证通过 此处可以是修改数据库订单状态等操作
	 				if($userid && $fenbi && $remarks){
	 					//添加积分记录
 						$data['id'] = create_uuid();
 						$data['Integralrecord'] = "+" + $fenbi;
 						$data['remark'] = $remarks;
 						$data['userid'] = $userid;
 						$data['time'] = time();
 						M('integral_record') -> add($data);
 						//增加用户积分
 						M("user") -> where(array("id" => $userid)) -> setInc("fenbi",$fenbi);
 						//添加充值记录
 						$nickname = M("user") -> where(array("id" => $userid)) -> getField("nickname");
 						M("recharge") -> add(array(
	 						"nickname" => $nickname,
	 						"fenbi" => $fenbi,
	 						"cdate" => date("Y-m-d"),
	 						"time" => time(),
 						));
	 					$returnVal['error'] = "充值失败";
	 				}else{
	 					$returnVal['error'] = "充值失败";
	 				}
	 				
	 			}else{
	 				// 验证不通过
	 				$returnVal['error'] = "充值失败";
	 			}
	 		}else{
	 			$returnVal['error'] = "apple_receipt为空";
	 		}
	 	}else{
	 		$returnVal['error'] = "非法请求";
	 	}
	 	$this -> ajaxReturn($returnVal);
	 }
	 
	 
	 /**
	  * 粉币
	  */
	 public function coin(){
	 	$list = M("coin")-> field("fenbi,price") -> select();
	 	$returnVal['list'] = $list ? $list : array();
	 	$returnVal['error'] = "";
	 	$this -> ajaxReturn($returnVal);
	 }
	 
	 /**
	  * 订单记录
	  */
	 public function orders(){
	 	if($GLOBALS["HTTP_RAW_POST_DATA"]){
	 		$param = json_decode($GLOBALS["HTTP_RAW_POST_DATA"],true);
	 		$pageNo = $param['pageNo'] ? $param['pageNo'] : 1;
	 		$userid = $param['userid'];
	 		$pageSize = C("PAGE_SIZE");
	 		$firstIndex = ($pageNo - 1) * $pageSize;
	 		$limit = "{$firstIndex},{$pageSize}";
	 		 
 			$list = M("order") -> where(array(
 			 	"userid" => $userid
 			)) -> limit($limit) -> field("id,shuafen_num,needfenbi,status,content,productid,time") -> select();
	 		for($i = 0;$i < count($list);$i++){
	 			$product = M("products") -> where(array("id" => $list[$i]['productid'])) -> find();
	 			$list[$i]['title'] = $product['title'];
	 			$list[$i]['date'] = date("Y-m-d H:i",$list[$i]['time']);
// 	 			$list[$i]['pic'] = 'http://'.$_SERVER['HTTP_HOST']."/Public/upload/".$product['pic'];
	 		}
	 		$returnVal['list'] = $list ? $list : array();
	 		$returnVal['error'] = "";
	 	}else{
	 		$returnVal['error'] = "非法请求";
		}
		$this -> ajaxReturn($returnVal);
		
	 }
	 
	 /**
	  * 首次充值
	  */
	 public function firstRecharge(){
	 	if($GLOBALS["HTTP_RAW_POST_DATA"]){
	 		$param = json_decode($GLOBALS["HTTP_RAW_POST_DATA"],true);
	 		
	 		$userid = $param['userid'];
	 		if($userid){
	 			$charge_record = M("first_charge_record") -> where(array("userid" => $userid)) -> find();
	 			if($charge_record){
	 				$returnVal['status'] = "1";
	 			}else{
	 				$returnVal['status'] = "0";
	 			}
	 		}else{
	 			$returnVal['status'] = "0";
	 		}
	 		 
	 		$data = M("first_charge") -> field("fenbi1,fenbi2,price") -> where("id = 1") -> find();
	 		$returnVal['fenbi1'] = $data['fenbi1'];
	 		$returnVal['fenbi2'] = $data['fenbi2'];
	 		$returnVal['price'] = $data['price'];
	 		$returnVal['error'] = "";
	 		$this -> ajaxReturn($returnVal);
	 	}else{
	 		$returnVal['error'] = "非法请求";
	 	}
	 	$this -> ajaxReturn($returnVal);
	 }
	 
	 /**
	  * 最新成交订单
	  */
	 public function newestorders(){
	 	$list = M("recharge") -> field("nickname,fenbi") -> limit("0,50") -> order("time DESC") -> select();
	 	$returnVal['list'] = $list;
	 	$returnVal['error'] = "";
	 	$this -> ajaxReturn($returnVal);
	 }
	 
	 /**
	  * 公告
	  */
	 public function notice(){
	 	$content = M("notice") -> where("id = 1") -> getField("content");
	 	$returnVal['content'] = $content;
	 	$returnVal['error'] = "";
	 	$this -> ajaxReturn($returnVal);
	 }
	 
	 /**
	  * 获取用户积分
	  */
	 public function getUserCoin(){
	 	if($GLOBALS["HTTP_RAW_POST_DATA"]){
	 		$param = json_decode($GLOBALS["HTTP_RAW_POST_DATA"],true);
	 	
	 		$userid = $param['userid'];
	 		if(!$userid){
	 			$returnVal['error'] = "缺少参数";
	 			$this -> ajaxReturn($returnVal);
	 		}
	 		$fenbi = M("user") -> where(array("id" => $userid)) -> getField("fenbi");
	 		$returnVal['fenbi'] = $fenbi ? $fenbi : "0";
	 		$returnVal['error'] = "";
	 	}else{
	 		$returnVal['error'] = "非法请求";
	 	}
	 	$this -> ajaxReturn($returnVal);
	 }
	 
	 /**
	  * 是否显示支付宝，微信，qq支付
	  */
	 public function isshowpay(){
	 	$returnVal['status'] = "1";//0不显示，1显示
	 	$this -> ajaxReturn($returnVal);
	 }
	 
	 /**
	  * 粉币和钱对应关系
	  */
	 public function relative_fenbi(){
	 	$list = M('chongzhi')->field('money,fenbi')->select();
	 	$this->ajaxReturn($list);
	 }
	 
}