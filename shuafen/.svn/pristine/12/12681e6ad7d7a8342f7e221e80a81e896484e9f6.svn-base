<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
    	//测试保存session
    	$user = M('user')->where(array('id'=>'userid00001'))->find();
    	session('user',$user);
    	//测试结束

    	$list = M('products')->select();
    	$this->assign('list',$list);
        $this->display();
    }
    
    //首页购买粉币
    
      public function logout(){
         session('user',null);
         $this->redirect('Index/index');
      }

}