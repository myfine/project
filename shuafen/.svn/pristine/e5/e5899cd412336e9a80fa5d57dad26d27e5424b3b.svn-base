<?php
namespace Admin\Controller;
use Think\Controller;
class RechargeController extends Controller 
{
	public function lst()
	{
		$list = M('chongzhi')->select();
		$this->assign('list',$list);
		$this->display();
	}

	public function add(){
		$data = $_POST;
		$res = M('chongzhi')->add($data);
		if($res){
			$temp['error'] = '';
		}else{
			$temp['error'] = '操作失败';
		}
		$this->ajaxReturn($temp);
	}

	public function myinfo(){
		$info = M('chongzhi')->where(array('id'=>$_GET['id']))->find();
		$this->assign('info',$info);
		$this->display();
	}



	public function toSave(){
		$data = $_POST;
		$res = M('chongzhi')->where(array('id'=>I('id')))->save($data);
		if($res){
			$temp['error'] = '';
		}else{
			$temp['error'] = '操作失败';
		}
		$this->ajaxReturn($temp);
	}

	public function del(){
		$res = M('chongzhi')->delete(I('id'));
		if($res){
			$temp['error'] = '';
		}else{
			$temp['error'] = '操作失败';
		}
		$this->ajaxReturn($temp);
	}
		
}