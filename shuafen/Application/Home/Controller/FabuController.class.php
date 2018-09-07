<?php
namespace Home\Controller;
use Think\Controller;
class FabuController extends CommonController {
    public function index(){
        $this->is_login();
        $this -> display();
    }

    //发布方法
    public function doFabu(){
        $redis = $this->redis_connect();
        $typeid = $_POST['typeid'];
        $title = $_POST['title'];
        $info = $_POST['info'];
        $username = session('username');
        $time = time();

        $postid = incr("global:postid");
        $redis->set("poster:postid:".$postid.":typeid",$typeid);
        $redis->set("poster:postid:".$postid.":title",$title);
        $redis->set("poster:postid:".$postid.":info",$info);
        $redis->set("poster:postid:".$postid.":username",$username);
        $redis->set("poster:postid:".$postid.":createtime",$time);

        $this_title =  $redis->get("poster:postid:".$postid.":title");
        if($this_title){
            $this->tishi(1,'');
        }else{
            $this->tishi(2,'失败啦');
        }
    }



}