<?php
namespace Admin\Model;
use Think\Model;
class OrderModel extends Model 
{
	protected $insertFields = array('order_num','yudinghao','price','user_id','jiangzuo_id','seat','ctime');
	protected $updateFields = array('id','order_num','yudinghao','price','user_id','jiangzuo_id','seat','ctime');
	protected $_validate = array(
		array('order_num', 'require', '订单数量不能为空！', 1, 'regex', 3),
		array('order_num', '1,50', '订单数量的值最长不能超过 50 个字符！', 1, 'length', 3),
		array('yudinghao', 'require', '预定号不能为空！', 1, 'regex', 3),
		array('yudinghao', '1,20', '预定号的值最长不能超过 20 个字符！', 1, 'length', 3),
		array('price', 'require', '价格不能为空！', 1, 'regex', 3),
		array('price', 'currency', '价格必须是货币格式！', 1, 'regex', 3),
		array('user_id', 'require', '用户id不能为空！', 1, 'regex', 3),
		array('user_id', 'number', '用户id必须是一个整数！', 1, 'regex', 3),
		array('jiangzuo_id', 'require', '讲座id不能为空！', 1, 'regex', 3),
		array('jiangzuo_id', 'number', '讲座id必须是一个整数！', 1, 'regex', 3),
		array('seat', 'require', '座位不能为空！', 1, 'regex', 3),
		array('seat', '1,200', '座位的值最长不能超过 200 个字符！', 1, 'length', 3),
	);
	public function search($pageSize = 20,$where)
	{
// 		$where = array();
// 		if($yudinghao = I('get.yudinghao'))
// 			$where['a.yudinghao'] = array('like', "%$yudinghao%");
// 		if($phone = I('get.phone'))
// 			$where['a.tel'] = array('like', "%$phone%");		
// 		if($name = I('get.name'))
// 			$where['a.name'] = array('like', "%$name%");
		$count = $this->alias('a')->where($where)->count();
		$page = new \Think\Page($count, $pageSize);
		// 配置翻页的样式
		$page->setConfig('prev', '上一页');
		$page->setConfig('next', '下一页');
		$data['page'] = $page->show();
		$data['data'] = $this->alias('a')->where($where)->group('a.id')->limit($page->firstRow.','.$page->listRows)->order('a.ctime DESC')->select();
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