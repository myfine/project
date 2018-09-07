<?php
namespace Admin\Controller;
use Think\Controller;
use Org\Util;
class IndexController extends Controller 
{
	
	public function __construct()
	{
		parent::__construct();
		if(!$_SESSION['uid']){
			$this -> redirect("Login/login");
		}
	}
	
	/**
	 * 修改密码
	 */
	public function alterPasswordHandle(){
		M("admin") -> where(array("id" => $_SESSION['uid'])) -> save(array("password" => md5(md5(I("password")))));
		$returnval['error'] = "";
		$this->ajaxReturn($returnval,'JSON');
	}

    public function index()
    {
    	$this -> redirect("Index/orders");
    }
    
    public function word(){
    	$res = M('word') -> where(array("id" => 1)) -> find();
    	
    	$this -> res = $res;
    	
    	$this -> display();
    }
    
    public function addword(){
    	$data['content'] = I('content');
    	$data['id'] = I('id');
    	
    	$res = M('word')-> save($data);
    	if($res){
    		$returnval['error'] = '';
    	}else{
    		$returnval['error'] = '保存失败';
    	}
    	$this->ajaxReturn($returnval);
    }
    
    public function webup(){
    	$uploadData = $this -> uploadfile();
    	$this->ajaxReturn($uploadData,'JSON');
    }
    
    
    public function uploadfile(){
    
    	$data = array();
    	$upload = new \Think\Upload();
    	//设置附件上传目录
    	$upload->rootPath  = './Public/upload/';
    	$upload->savePath  = ''; // 设置附件上传（子）目录
    	$upload->autoSub = true;
    	$upload->subName = array('date','Ymd');
    	$info = $upload->upload();
    	if (!$info) {
    		//捕获上传异常
    		$data['error'] = $upload->getError();
    		return $data;
    	} else {
    		//取得成功上传的文件信息
    		foreach($info as $file){
    			$data['savename'] = $file['savepath'].$file['savename'];
    		}
    		$data['error'] = "";
    		return $data;
    	}
    
    }
    
    /**
     * 显示商品
     */
    public function products(){
    	$count = M("products") -> count();
		$Page = new\Think\Page($count);
		$show = $Page->show();// 分页显示输出
		$limit = $Page->firstRow.','.$Page->listRows;
		$this -> page = $show;
		$this -> list = M("products") -> limit($limit) -> select();
		$this -> display();
    }
    
    /**
     * 删除商品
     */
    public function deleteproduct(){
		$id = I("post.id");
		if($id){
			$rs = M("products") -> where(array(
					"id" => $id
			)) -> delete();
			if($rs){
				$returnValue['error'] = "";
			}else{
				$returnValue['error'] = "删除失败";
			}
		}else{
			$returnValue['error'] = "删除失败";
		}
		$this->ajaxReturn($returnValue);
	}
	
	/**
	 * 编辑商品信息
	 */
	public function editpro(){
		$protype = M('type') -> select();
    	$this -> protype = $protype;
    	
		$data[id] = I('id');
		$proinfo = M('products') -> where(array("id" => $data['id'])) -> find();
		$this -> proinfo = $proinfo;
		
		$this -> display();
	}
	
	/**
	 * 修改商品信息
	 */
	public function edit_product(){
    	$data = $_POST;
    	$data['relation_jf'] = $data['jfgz'];
    	
    	$res = M('products')-> where(array("id" => $data['id'])) ->getField('pic');
    	
    	if($data['pic'] == '' && $data['path'] == ''){
    		$data['pic'] = $res;
    	}else{
    		$data['pic'] = $data['path'];
    	}
    	
    	
    	$rs = M("products") -> save($data);
    	if($rs){
			$returnValue['error'] = "";
		}else{
			$returnValue['error'] = "保存失败";
		}
		$this->ajaxReturn($returnValue);
    	
    }
    
    /**
     * 添加商品
     */
    public function addpro(){
    	
    	$protype = M('type') -> select();
    	$this -> protype = $protype;
    	
    	$this ->display();
    }
    
    public function add_product(){
    	$data = $_POST;
    	$data['id'] = create_uuid();
    	$data['relation_jf'] = $data['jfgz'];
    	$data['pic'] = $data['path'];
    	
    	$res = M('products') -> where(array("title" => $data['title'])) -> find();
    	
    	if($res){
    		$returnValue['error'] = "该商品已存在，请勿重复添加";
    	}else{
    		$rs = M("products") -> add($data);
	    	if($rs){
				$returnValue['error'] = "";
			}else{
				$returnValue['error'] = "添加失败";
			}
    	}
		$this->ajaxReturn($returnValue);
    }
    
    /**
     * 积分增加扣除设置     integral
     */
    
    public function add_sub_integral(){
    	$rule = M('add_sub_rule') -> select();
    	$this -> rule = $rule;
    	
    	$this -> display();
    }
    //更改积分增加扣除设置
    public function change_rule(){
    	
    	$data['id'] = I('id');
    	$data['add_or_sub'] = I('integral');
    	
    	$res = M('add_sub_rule') -> save($data);
    	if($res){
    		$returnval['error'] = '';
    	}else{
    		$returnval['error'] = '数据库存入失败，请重试';
    	}
    	
    	$this -> ajaxReturn($returnval);
    }
    
    /**
     * 修改刷粉次数页面显示
     */
    public function shuafencishu(){
    	
    	
    	$id = I('id');
    	
    	$proname = M("products") -> where(array("id" => $id)) -> getfield('title');
    	
    	$numlist = M("pay") -> where(array("proid" => $id))-> select();
//  	dump($numlist);die;
    	$this -> numlist = $numlist;
    	$this -> proname = $proname;
    	$this -> proid = $id;
    	$this -> display();
    }
    
    //更改刷分次数和所需粉币
    public function change_sfcs(){
    	$proid = I("proid");
    	$jfgz = M("products") -> where(array("id" => $proid)) -> getfield('relation_jf');
    	$data['id'] = I('id');
    	$data['num_shua'] = I('shuafen_num');
    	$data['num_fenbi'] = $data['num_shua'] * $jfgz;
    	$res = M('pay') -> save($data);
    	if($res){
    		$returnval['error']='';
    	}else{
    		$returnval['error']='修改失败';
    	}
    	$this -> ajaxReturn($returnval);
    }
    
    //添加刷粉次数和所需粉币
    public function add_sfcs(){
    	
    	$proid = I("proid");
    	$jfgz = M("products") -> where(array("id" => $proid)) -> getfield('relation_jf');
    	
    	$data['proid'] = $proid;
    	$data['num_shua'] = I('shuafen_num');
    	$data['num_fenbi'] = $data['num_shua'] * $jfgz;
    	
    	
    	if(M('pay') -> create($data)){
			if(M('pay') -> add()){
				$returnval['error']='';
    			$returnval['sfcs'] = $data['num_shua'];
			}else{
				$returnval['error']='修改失败';
			}
		}
    	
    	
    	
    	
//  	$res = M('pay') -> save($data);
//  	if($res){
//  		$returnval['error']='';
//  		$returnval['sfcs'] = $data['num_shua'];
//  	}else{
//  		$returnval['error']='修改失败';
//  	}
    	$this -> ajaxReturn($returnval);
    }
    
    /**
     * 公告
     */
    public function notice(){
    	$this -> content = M("notice") -> where("id = 1") -> getField("content");
    	$this -> display();
    }
    
    /**
     * 保存公告
     */
    public function saveNoiceHandle(){
    	$data = array(
    		"id" => 1,
    		"content" => I("content"),
    	);
    	$rs = M("notice") -> save($data);
    	$returnval['error'] = "";
    	$this -> ajaxReturn($returnval);
    }
    
    /**
     * 首充礼包
     */
    public function firstRecharge(){
    	$this -> fc = M("first_charge") -> where("id = 1") -> find();
    	$this -> display();
    }
    
    /**
     * 保存礼包
     */
    public function saveLibaoHandle(){
    	if(I("fenbi1") && I("fenbi2") && I("price")){
    		$data = array(
    				"id" => 1,
    				"fenbi1" => I("fenbi1"),
    				"fenbi2" => I("fenbi2"),
    				"price" => I("price"),
    		);
    		M("first_charge") -> save($data);
    		$returnval['error'] = "";
    	}else{
    		$returnval['error'] = "请完善信息";
    	}
    	$this -> ajaxReturn($returnval);
    }
    
    /**
     * 用户列表
     */
    public function users(){
    	$count = M("user") -> count();
    	$Page = new \Think\Page($count,C("PAGE_SIZE"));
    	$show = $Page->show();// 分页显示输出
    	$limit = $Page->firstRow.','.$Page->listRows;
    	$this -> page = $show;
    	$this -> list = M("user") -> limit($limit) -> select();
    	$this -> display();
    }
    
    /**
     * 删除用户
     */
    public function deleteUser(){
    	$id = I("post.id");
    	if($id){
    		$rs = M("user") -> where(array(
    			"id" => $id
    		)) -> delete();
    		if($rs){
    			$returnval['error'] = "";
    		}else{
    			$returnval['error'] = "删除失败";
    		}
    	}else{
    		$returnval['error'] = "删除失败";
    	}
    	$this -> ajaxReturn($returnval);
    }
    
    
    /**
     * 订单记录
     */
    public function orders(){
    	$count = M("order") -> count();
    	$Page = new \Think\Page($count,C("PAGE_SIZE"));
    	$show = $Page->show();// 分页显示输出
    	$limit = $Page->firstRow.','.$Page->listRows;
    	$this -> page = $show;
    	
    	$list = M("order") -> limit($limit) -> field("id,shuafen_num,needfenbi,status,content,time,productid") ->order("time desc")-> select();
    	for($i = 0;$i < count($list);$i++){
    		$product = M("products") -> where(array("id" => $list[$i]['productid'])) -> find();
    		$list[$i]['title'] = $product['title'];
    	}
    	$this -> list = $list;
		$this -> display();    
    }
    
    /**
     * 删除订单
     */
    public function deleteOrder(){
    	$id = I("post.id");
    	if($id){
    		$rs = M("order") -> where(array(
    				"id" => $id
    		)) -> delete();
    		if($rs){
    			$returnval['error'] = "";
    		}else{
    			$returnval['error'] = "删除失败";
    		}
    	}else{
    		$returnval['error'] = "删除失败";
    	}
    	$this -> ajaxReturn($returnval);
    }
    
    /**
     * 取消订单
     */
    public function cancelOrder(){
    	$id = I("post.id");
    	if($id){
    		$order = M("order") -> where(array("id" => $id)) -> find();
    		$data = array(
    				"id" => $id,
    				"status" => 2,
    		);
    		$rs1 = M("order") -> save($data);
    		//退还积分
    		$rs2 = M("user") -> where(array("id" => $order['userid'])) -> setInc("fenbi",$order['needfenbi']);
    		if($rs1 && $rs2){
    			$returnval['error'] = "";
    		}else{
    			$returnval['error'] = "取消失败";
    		}
    	}else{
    		$returnval['error'] = "取消失败";
    	}
    	$this -> ajaxReturn($returnval);
    }
    
}