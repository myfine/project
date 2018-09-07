<?php
namespace Admin\Controller;
use Think\Controller;
class LoginController extends Controller 
{
	public function chkcode()
	{
		$Verify = new \Think\Verify(array(
		    'fontSize'    =>    30,    // 验证码字体大小
		    'length'      =>    2,     // 验证码位数
		    'useNoise'    =>    TRUE, // 关闭验证码杂点
		));
		$Verify->entry();
	}
   
   public function logout()
   {
	   	session_unset();
	   	$this -> redirect('Login/login');
   }
   
   
   public function loginHandle(){
   		$username = I("post.username");
   		$password = I("post.password");
   		if($username && $password){
   			$pwd = md5(md5($password));
   			$user = M("admin") -> where(array(
   				"username" => $username,
   				"password" => $pwd,
   			)) -> find();
   			if($user){
   				session("uid",$user['id']);
   				session("uname",$username);
   				$returnval['error'] = "";
   			}else{
   				$returnval['error'] = "用户名或者密码错误";
   			}
   		}else{
   			$returnval['error'] = "请完善信息";
   		}
   		$this->ajaxReturn($returnval,'JSON');
   }
   
}