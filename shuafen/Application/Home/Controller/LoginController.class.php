<?php
namespace Home\Controller;
use Think\Controller;
class LoginController extends CommonController {
	//登录
	public function login(){
		$this -> display();
	}
		
	//登录方法--接口
	public function doLogin(){
		 $redis = $this->redis_connect();
		 $username = $_POST['username'];
		 $password = $_POST['password'];
         $userid = $redis->get("user:username:".$username.":userid");
         if(!$userid){
             $this->tishi(2,'没有该用户');
         }
        $this_password = $redis->get("user:userid:".$userid.":password");
         if($this_password != md5($password)){
             $this->tishi(2,'密码错误，请重试');
         }else{
             session('username',$username);
             $this->tishi(1,'');
         }
	}



	//注册
	public function register(){
		$this -> display();
	}
	
	//注册方法--接口
	public function doRegist(){
        $redis = $this->redis_connect();
        $username = $_POST['username'];
        $password = md5($_POST['password']);
        $user = $redis->get("user:username:".$username.":userid");
        if($user){
            $this->tishi(2,'用户名已经注册啦');
        }

        $userid = $redis->incr("global:userid");
        $redis->set("user:userid:".$userid.":username",$username);
        $redis->set("user:userid:".$userid.":password",$password);
        $redis->set("user:username:".$username.":userid",$userid);
        if($redis->get("user:username:".$username.":userid")){
            session('username',$username);
            $this->tishi(1,'');
        }else{
            $this->tishi(2,'失败');
        }

	}




	

}