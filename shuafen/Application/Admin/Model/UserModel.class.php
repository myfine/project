<?php
namespace Admin\Model;
use Think\Model;
class UserModel extends Model 
{
	protected $insertFields = array('name','phone');
	protected $updateFields = array('id','name','phone');
	protected $_validate = array(
		array('name', 'require', '姓名不能为空！', 1, 'regex', 3),
		array('name', '1,10', '姓名的值最长不能超过 10 个字符！', 1, 'length', 3),
		array('phone', 'require', '电话不能为空！', 1, 'regex', 3),
		array('phone', '1,15', '电话的值最长不能超过 15 个字符！', 1, 'length', 3),
	);
	public function search($pageSize = 20)
	{
		/**************************************** 搜索 ****************************************/
		$where = array();
		if($username = I('get.name'))
			$where['name'] = array('like', "%$name%");
		if($password = I('get.phone'))
			$where['phone'] = array('like', "%$phone%");
		/************************************* 翻页 ****************************************/
		$count = $this->alias('a')->where($where)->count();
		$page = new \Think\Page($count, $pageSize);
		// 配置翻页的样式
		$page->setConfig('prev', '上一页');
		$page->setConfig('next', '下一页');
		$data['page'] = $page->show();
		/************************************** 取数据 ******************************************/
		$data['data'] = $this->alias('a')->where($where)->group('a.id')->limit($page->firstRow.','.$page->listRows)->select();
		return $data;
	}
	// 添加前
	protected function _before_insert(&$data, $option)
	{
	}
	// 修改前
	protected function _before_update(&$data, $option)
	{
	}
	// 删除前
	protected function _before_delete($option)
	{
		if(is_array($option['where']['id']))
		{
			$this->error = '不支持批量删除';
			return FALSE;
		}
	}
	/************************************ 其他方法 ********************************************/
}