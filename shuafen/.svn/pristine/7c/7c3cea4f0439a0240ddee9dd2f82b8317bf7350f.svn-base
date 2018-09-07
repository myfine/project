<?php
namespace Admin\Model;
use Think\Model;
class HuichangModel extends Model 
{
	protected $insertFields = array('hengpai','shupai','price','qu','qh','lou','seatstr','ctime','status','paichu');
	protected $updateFields = array('id','hengpai','shupai','price','qu','qh','lou','seatstr','ctime','status','paichu');
	protected $_validate = array(
		array('hengpai', 'require', '横排不能为空！', 1, 'regex', 3),
		array('hengpai', 'number', '横排必须是一个整数！', 1, 'regex', 3),
		array('shupai', 'require', '竖排不能为空！', 1, 'regex', 3),
		array('shupai', 'number', '竖排必须是一个整数！', 1, 'regex', 3),
		array('price', 'require', '价格不能为空！', 1, 'regex', 3),
		array('price', 'currency', '价格必须是货币格式！', 1, 'regex', 3),
		array('seatstr', 'require', '座位不能为空！', 1, 'regex', 3),
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
        $qh=implode(',',I('post.qh'));
        $data['qh']=$qh;
	}
	// 修改前
	protected function _before_update(&$data, $option)
	{
        $qh=implode(',',I('post.qh'));
        $data['qh']=$qh;
	}

}