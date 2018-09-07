<?php
namespace Admin\Model;
use Think\Model;
class TypeModel extends Model 
{
	protected $insertFields = array('tname','name','mmid','status');
	protected $updateFields = array('id','tname','name','mmid','status');
	protected $_validate = array(
		array('tname', 'require', '顶部名称不能为空！', 1, 'regex', 3),
		array('tname', '1,20', '顶部名称的值最长不能超过 20 个字符！', 1, 'length', 3),
		array('name', 'require', '名称不能为空！', 1, 'regex', 3),
		array('name', '1,20', '名称的值最长不能超过 20 个字符！', 1, 'length', 3),
		array('mmid', 'require', 'id命名不能为空！', 1, 'regex', 3),
		array('mmid', '1,20', 'id命名的值最长不能超过 20 个字符！', 1, 'length', 3),
		array('status', 'number', '状态必须是一个整数！', 2, 'regex', 3),
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