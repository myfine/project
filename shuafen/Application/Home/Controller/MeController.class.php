<?php
namespace Home\Controller;
use Think\Controller;
class MeController extends Controller {
    public function index(){
        $this->display();
    }
    
    public function personal(){
    	$jifen = M('integral_record')->where(array('userid'=>$_SESSION['user']['id']))->select();
    	$order = M('order')->where(array('userid'=>$_SESSION['user']['id']))->select();
    	foreach($order as $key=>$value){
    		$pro = M('products')->where(array('id'=>$value['productid']))->find();
    		$order[$key]['pro'] = $pro;
    	}
        $clist = M('coin')->select();
        $this->assign('clist',$clist);
    	$this->assign('order',$order);
    	$this->assign('jifen',$jifen);
    	$this->display();
    }

    public function first(){
        $jifen = M('integral_record')->where(array('userid'=>$_SESSION['user']['id']))->select();
        //print_r($jifen);exit();
        $order = M('order')->where(array('userid'=>$_SESSION['user']['id']))->select();
        foreach($order as $key=>$value){
            $pro = M('products')->where(array('id'=>$value['productid']))->find();
            $order[$key]['pro'] = $pro;
        }
        $clist = M('chongzhi')->select();
        //首冲
        $first = M('first_charge')->where(array('id'=>1))->find();
        $this->assign('first',$first);
        $this->assign('clist',$clist);
        $this->assign('order',$order);
        $this->assign('jifen',$jifen);
        $this->display();
    }

       public function tousu(){
        $jifen = M('integral_record')->where(array('userid'=>$_SESSION['user']['id']))->select();
        //print_r($jifen);exit();
        $order = M('order')->where(array('userid'=>$_SESSION['user']['id']))->select();
        foreach($order as $key=>$value){
            $pro = M('products')->where(array('id'=>$value['productid']))->find();
            $order[$key]['pro'] = $pro;
        }
        $clist = M('chongzhi')->select();
        $this->assign('clist',$clist);
        $this->assign('order',$order);
        $this->assign('jifen',$jifen);
        $this->display();
    }
   

    //上传头像方法
    public function uploadThumbnailHandle(){
    $error = "";
    $msg = "";
    $fileElementName = 'thumbnail';
    if(!empty($_FILES[$fileElementName]['error']))
    {
      $error = uploadErrorInfo($_FILES[$fileElementName]['error']);
    }elseif(empty($_FILES[$fileElementName]['tmp_name']) || $_FILES['thumbnail']['tmp_name'] == 'none')
    {
      $error = 'No file was uploaded..';
    }else
    {
      $uploadData = $this -> uploadImage("./Public/Uploads/");
      $msgs = $uploadData['savename'];

      $msg = '/Public/Uploads/'.$msgs;
      

     //保存到数据库

      M('user')->where(array('id'=>$_SESSION['user']['id']))->setField('avatar',$msg);


      @unlink($_FILES[$fileElementName]);
    }
    echo "{";
    echo        "error: '" . $error . "',\n";
    echo        "msg: '" . $msg . "'\n";
    echo "}";
  }
  
  public function uploadImage($savePath){
      $data = array();
      $upload = new \Think\Upload();
      //设置附件上传目录
      $upload->rootPath  = './Public/Uploads/';
      $upload->exts      = array('jpg', 'gif', 'png', 'jpeg');
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



public function imagecropper($source_path, $target_width, $target_height)
{
    $source_info   = getimagesize($source_path);
    $source_width  = $source_info[0];
    $source_height = $source_info[1];
    $source_mime   = $source_info['mime'];
    $source_ratio  = $source_height / $source_width;
    $target_ratio  = $target_height / $target_width;

    // 源图过高
    if ($source_ratio > $target_ratio)
    {
        $cropped_width  = $source_width;
        $cropped_height = $source_width * $target_ratio;
        $source_x = 0;
        $source_y = ($source_height - $cropped_height) / 2;
    }
    // 源图过宽
    elseif ($source_ratio < $target_ratio)
    {
        $cropped_width  = $source_height / $target_ratio;
        $cropped_height = $source_height;
        $source_x = ($source_width - $cropped_width) / 2;
        $source_y = 0;
    }
    // 源图适中
    else
    {
        $cropped_width  = $source_width;
        $cropped_height = $source_height;
        $source_x = 0;
        $source_y = 0;
    }

    switch ($source_mime)
    {
        case 'image/gif':
            $source_image = imagecreatefromgif($source_path);
            break;

        case 'image/jpeg':
            $source_image = imagecreatefromjpeg($source_path);
            break;

        case 'image/png':
            $source_image = imagecreatefrompng($source_path);
            break;

        default:
            return false;
            break;
    }

    $target_image  = imagecreatetruecolor($target_width, $target_height);
    $cropped_image = imagecreatetruecolor($cropped_width, $cropped_height);

    // 裁剪
    imagecopy($cropped_image, $source_image, 0, 0, $source_x, $source_y, $cropped_width, $cropped_height);
    // 缩放
    imagecopyresampled($target_image, $cropped_image, 0, 0, 0, 0, $target_width, $target_height, $cropped_width, $cropped_height);

  //保存图片到本地(两者选一)
    //$randNumber = mt_rand(00000, 99999).  mt_rand(000, 999);
    //$fileName = substr(md5($randNumber), 8, 16) .".png";
    //imagepng($target_image,'./'.$fileName);
    //imagedestroy($target_image);
  
  //直接在浏览器输出图片(两者选一)
  header('Content-Type: image/jpeg');
  imagepng($target_image);
    imagedestroy($target_image);
    imagejpeg($target_image);
    imagedestroy($source_image);
    imagedestroy($target_image);
    imagedestroy($cropped_image);
}

    public function alipay(){
        $out_trade_no = createOrderNo();
        $order = array(
                "trade_no" => $out_trade_no,
                "subject"  => "粉币",
                "price"    => 0.01,
                "body"     => "",
                "userId"   => $_SESSION['user']['id'],
        );   
       $btn = alipay($order);
       $this->assign('btn', $btn);
    }
}