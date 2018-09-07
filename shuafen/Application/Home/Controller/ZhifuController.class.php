<?php
namespace Home\Controller;
use Think\Controller;
class ZhifuController extends Controller {
    public function pay(){
    	if(I("get.type") && I("get.fid")){
    		$this -> coin = M("coin") -> where(array("id" => I("get.fid"))) -> find();
    		$this -> display();
    	}
    }


    public function toPay(){
    	$out_trade_no = createOrderNos();
    	$coin = M("coin") -> where(array("id" => I("get.id"))) -> find();
		$order = array(
				"trade_no" => $out_trade_no,
				"subject"  => "快手刷粉",
				"price"    => $coin['price'],
				"body"     => "",
				"userId"   => $_SESSION['user']['id'],
		);
    	$btn = alipay($order,1);
    }

    public function firstPay(){
        $out_trade_no = createOrderNos();
        $order = array(
                "trade_no" => $out_trade_no,
                "subject"  => "快手刷粉",
                "price"    => $_GET['money'],
                "body"     => "",
                "userId"   => $_SESSION['user']['id'],
        );
        $btn = alipay($order,2);
    }

    public function wxpay(){
    	$wx_out_trade_no = createOrderNos();
		$title = "快手刷粉";
        $price = I('price');
        $userid = $_SESSION['user']['id'];
        //微信支付
        $url = wxpay($title,$price,$wx_out_trade_no,1,$userid);
        $this -> url = $url;
        $link = 'http://paysdk.weixin.qq.com/example/qrcode.php?data='.urlencode($url);
        $show['link'] = $link;
        $show['weixinorder'] = $wx_out_trade_no;
        $this->ajaxReturn($show);
    }

    public function firstWxpay(){
        $wx_out_trade_no = createOrderNos();
        $title = "快手刷粉";
        $price = I('price');
        $userid = $_SESSION['user']['id'];
        //微信支付
        $url = fwxpay($title,$price,$wx_out_trade_no,1,$userid);
        $this -> url = $url;
        $link = 'http://paysdk.weixin.qq.com/example/qrcode.php?data='.urlencode($url);
        $show['link'] = $link;
        $show['weixinorder'] = $wx_out_trade_no;
        $this->ajaxReturn($show);
    }

    public function queryOrder(){
    	$res = M('integral_record')->where(array('wx_out_trade_no'=>I('weixinorder')))->find();
    	if($res['status']==1){
    		$temp['order'] = 1;
    	}else{
    		$temp['order'] = 0;
    	}
    	$this->ajaxReturn($temp);
    }

}