<?php
namespace Home\Controller;
use Think\Controller;
class WordController extends Controller {
    public function index(){
    	$res = M('word') -> where(array("id" => 1)) -> find();
    	$this -> res = $res;
    	$this -> display();
    }

}