<?php
namespace app\index\controller;

class Index extends Base
{
	public function index()
	{		
		//调用isLogin()方法，判断用户是否登录，如果没有登录则跳转到登录入口页面 
		$this->isLogin();
		//页面标题
		$this->assign([
			'title'=>'华南理工大学-后台管理',
			'keywords'=>'华南理工大学,教学管理',
			'description'=>'这是华南理工大学的教学管理系统后台页面'
			]);
		//渲染模板
		return $this->fetch() ;
	}
	
}

?>