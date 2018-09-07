<?php
namespace Admin\Model;
use Think\Model;
class JiangzuoModel extends Model 
{
	protected $insertFields = array('title','content','startDate','picture','address','ctime');
	protected $updateFields = array('id','title','content','startDate','picture','address','ctime');
	protected $_validate = array(
		array('title', 'require', '标题不能为空！', 1, 'regex', 3),
		array('title', '1,100', '标题的值最长不能超过 100 个字符！', 1, 'length', 3),
		array('startDate', 'require', '开始时间不能为空！', 1, 'regex', 3),
		array('startDate', '1,20', '开始时间的值最长不能超过 20 个字符！', 1, 'length', 3),
		array('address', 'require', '地址不能为空！', 1, 'regex', 3),
		array('address', '1,50', '地址的值最长不能超过 50 个字符！', 1, 'length', 3),
	);
	public function search($pageSize = 20)
	{
		$count = $this->alias('a')->where($where)->count();
		$page = new \Think\Page($count, $pageSize);
		// 配置翻页的样式
		$page->setConfig('prev', '上一页');
		$page->setConfig('next', '下一页');
		$data['page'] = $page->show();
		$data['data'] = $this->alias('a')->where($where)->group('a.id')->limit($page->firstRow.','.$page->listRows)->select();
		return $data;
	}	
	// 添加前
	protected function _before_insert(&$data, $option)
	{
		if(isset($_FILES['picture']) && $_FILES['picture']['error'] == 0)
		{
			$ret = uploadOne('picture', 'Admin', array(
				array(640, 640, 2),
			));
			if($ret['ok'] == 1)
			{
				$data['picture'] = $ret['images'][0];
				$data['sm_picture'] = $ret['images'][1];
			}
			else 
			{
				$this->error = $ret['error'];
				return FALSE;
			}
		}
		$data['ctime']=date('Y-m-d H:i:s');

	}
	// 修改前
	protected function _before_update(&$data, $option)
	{
		if(isset($_FILES['picture']) && $_FILES['picture']['error'] == 0)
		{
			$ret = uploadOne('picture', 'Admin', array(
				array(640, 640, 2),
			));
			if($ret['ok'] == 1)
			{
				$data['picture'] = $ret['images'][0];
				$data['sm_picture'] = $ret['images'][1];
			}
			else 
			{
				$this->error = $ret['error'];
				return FALSE;
			}
			deleteImage(array(
				I('post.old_picture'),
				I('post.old_sm_picture'),
	
			));
		}
		$data['ctime']=date('Y-m-d H:i:s');
	}
	// 删除前
	protected function _before_delete($option)
	{
		if(is_array($option['where']['id']))
		{
			$this->error = '不支持批量删除';
			return FALSE;
		}
		$images = $this->field('picture,sm_picture')->find($option['where']['id']);
		deleteImage($images);
	}
}