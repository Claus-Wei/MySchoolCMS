<?php
namespace app\index\controller;
use think\Controller;
use think\Session;

class Base extends Controller
{
	//初始化，检测/获取用户登录信息
	protected function _initialize()
	{
		parent::_initialize();//继承父类中的初始化方法

		define('ADMIN_ID',Session::has('admin_id') ? Session::get('admin_id') : null);
	}
	//检测用户是否登录，防止绕过登录页面直接进入后台页面
	protected function isLogin()
	{
		if(is_null(ADMIN_ID)){
			$this->error('用户未登录，无权访问该页面',url('admin/login'));
		}
	}
	//alredyLogin方法，防止重复登录；
	protected function alreadyLogin()
	{
		if(ADMIN_ID){
			$this->error('帐户已经登录，请勿重复登录',url('index/index'));
		}
	}
}

?>