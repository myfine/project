<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> 
<meta http-equiv="Pragma" content="no-cache"> 
<meta http-equiv="Cache-Control" content="no-cache"> 
<meta http-equiv="Expires" content="0"> 
<title>后台登录</title> 
<link href="/Public/Admin/styles/login.css" type="text/css" rel="stylesheet">
<script type="text/javascript" src="/Public/Admin/js/jquery-1.8.2.min.js"></script>
<script type="text/javascript" src="/Public/Admin/js/jquery.form.js"></script> 
</head> 
<body> 

<div class="login">
    <div class="message">管理登录</div>
    <div id="darkbannerwrap"></div>
    
    <form id="form" action="/index.php/Admin/Login/loginHandle" method="post">
		<input name="action" value="login" type="hidden">
		<input id="username" name="username" placeholder="用户名" required="" type="text">
		<hr class="hr15">
		<input id="password" name="password" placeholder="密码" required="" type="password">
		<hr class="hr15">
		<input value="登录" style="width:100%;" type="submit">
		<hr class="hr20">
	</form>
	
</div>
</body>
</html>

<script>

var loading=0;
$(function(){
  if(loading){
    return false;
  }
  loading=1;
   $('#form').ajaxForm({
        beforeSubmit:  checkForm,// pre-submit callback
        success:       complete,// post-submit callback
        dataType: 'json'
   });
   
    function checkForm(){
      if(!$.trim ( $('#username').val() )){
        alert('请输入用户名');
        return false;
      }     

       if(!$.trim ( $('#password').val() )){
        alert('请输入密码');
        return false;
      }

    }
    
    function complete(d){
        if (!d.error){
        	window.location.href="/index.php/Admin";
        }else{
          	alert(d.error);
          	loading=0;
        }
    }

});
</script>