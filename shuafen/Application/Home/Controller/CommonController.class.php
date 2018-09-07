<?php
namespace Home\Controller;
use Think\Controller;
use Think\Cache\Driver\Redis;
class CommonController extends Controller {
    public function redis_connect(){
        $redis = new \Redis();
        $redis->connect('127.0.0.1',6379);
        $redis->auth('123456');
        return $redis;
    }

    public function tishi($status,$info){
            if(!$info){
                $info = '';
            }
            $arr = array(
                'status'=>$status,
                'info'=>$info,
            );
            echo json_encode($arr);
            die;
    }

    public function is_login(){
        if(!session('username')){
            header('Location: /index.php/Login/login');
        }
    }

}