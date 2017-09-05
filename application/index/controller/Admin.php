<?php
namespace app\index\controller;
use app\index\controller\Base;
use think\Request;
use app\index\model\Admin as AdminModel;
use think\Session;

class Admin extends Base
{	
	public function login()
	{       
        //调用alredyLogin方法，防止重复登录；
        $this->alreadyLogin();

		return $this->fetch();
	}
        //登录验证
	public function checkLogin(Request $request)
	{
		//从当前方法返回三个参数
		//返回当前状态
		$status = 0; //验证失败标志
        $result = ''; //失败提示信息
        $data = $request -> param();

        //设置验证规则
        $rule =[
        	'name|用户名'=>'require',
        	'password|密码'=>'require',
        	'verify|验证码'=>'require|captcha',
        ];

        $result = $this-> validate($data,$rule);
        if($result===true){
        	//查询条件
        	$map = [
        		'name' => $data['name'],
        		'password' => md5($data['password']),
                'status' =>'1'
        	];
        	//数据表查询，获取用户数据
        	$admin = AdminModel::get($map);
        	if($admin === null){
        		$result = '该用户不存在';
        	}
            else{
        		$status = 1;
        		$result = '验证通过，点击确定进入后台';

                //创建session用来检测用户的登录信息和防止重复登录      
                Session::set('admin_id',$admin->id); //获取用户id
                Session::set('admin_info',$admin->getData()); //获取用户信息
        	}      
        }
		    //打包成JSON格式返回前端
		    return ['status'=>$status,'message'=>$result];

	}
        //注销登录
	public function logout()
	{       AdminModel::update(['login_time'=>time()],['id'=>Session::get('admin_id')]);
            Session::destroy('admin_id');
            Session::destroy('admin_info');
            $this->success('注销登录成功','admin/login');
	}
    public function adminList()
    {   
        $this -> assign('title', '管理员列表');
        $this -> assign('keywords', '华南理工大学教学管理系统');

        $count = AdminModel::count('name');
        $this -> assign('count',$count);

        //判断登录用户为admin用户，则显示全部用户信息，否则只显示本用户信息。
        $adminName = Session::get('admin_info.name');

        if($adminName=='admin'){

            $list = AdminModel::all();
            
        }else{
            
            $list = AdminModel::all(['name'=>$adminName]);
           
        }

        $this -> assign('list',$list);
    
        return $this->fetch('admin_list');
    }
    //管理员状态变更设置
    public function setStatus(Request $request)
    {
        $admin_id = $request ->param('id');
        $result = AdminModel::get($admin_id);

        if($result -> getData('status')==1)
        {
            AdminModel::update(['status'=>0],['id'=>$admin_id]);
        }else{
            AdminModel::update(['status'=>1],['id'=>$admin_id]);
        }
    }
    
    //渲染编辑管理员界面
    public function adminEdit($id)
    {
        $this->assign('title','编辑管理员信息');

        $result =AdminModel::get($id)->getData();

        $this->assign('admin_info',$result);
       
        return $this->fetch('admin_edit');
    }

    //编辑操作
    public function doEdit(Request $request)
    {
        //获取表单返回的数据
//        $data = $request -> param();
        $param = $request -> param();
    
        //去掉表单中为空的数据,即没有修改的内容
        foreach ($param as $key => $value ){
            if (!empty($value)){
                $data[$key] = $value;
            }
        }

        $condition = ['id'=>$data['id']] ;
        $result = AdminModel::update($data, $condition);

        //如果是admin用户,更新当前session中用户信息user_info中的角色role,供页面调用
        if (Session::get('admin_info.name') == 'admin') 
        {
            Session::set('admin_info.role', $data['role']);
        }

        if (true == $result) {
            return ['status'=>1, 'message'=>'更新成功'];
        } else {
            return ['status'=>0, 'message'=>'更新失败,请检查'];
        }
    }

    //渲染添加管理员模板
    public function adminAdd()
    {   
        return $this->fetch('admin_add');
    }
    //检测用户名是否可用
    public function checkAdminName(Request $request)
    {
        $userName = trim($request -> param('name'));
        $status = 1;
        $message = '用户名可用';
        if (UserModel::get(['name'=> $userName])) {
            //如果在表中查询到该用户名
            $status = 0;
            $message = '用户名重复,请重新输入~~';
        }
        return ['status'=>$status, 'message'=>$message];
    }

    //检测用户邮箱是否可用
    public function checkAdminEmail(Request $request)
    {
        $userEmail = trim($request -> param('email'));
        $status = 1;
        $message = '邮箱可用';
        if (UserModel::get(['email'=> $userEmail])) {
            //查询表中找到了该邮箱,修改返回值
            $status = 0;
            $message = '邮箱重复,请重新输入~~';
        }
        return ['status'=>$status, 'message'=>$message];
    }

    //添加操作
    public function addAdmin(Request $request)
    {
        $data = $request -> param();
        $status = 1;
        $message = '添加成功';

        $rule = [
            'name|用户名' => "require|min:3|max:10",
            'password|密码' => "require|min:3|max:10",
            'email|邮箱' => 'require|email'
        ];

        $result = $this -> validate($data, $rule);

        if ($result === true) {
            $user= UserModel::create($request->param());
            if ($user === null) {
                $status = 0;
                $message = '添加失败';
            }
        }


        return ['status'=>$status, 'message'=>$message];
    }

    //删除操作
    public function delete($id)
    {
        AdminModel::destroy($id);
    }

    //渲染已删除的用户列表
    public function alreadyDeleteList()
    {   
        //仅查询软删除的数据
        $list = AdminModel::onlyTrashed()->select();
        $this ->assign('list',$list);
        //获取软删除的数据总条数
        $count = AdminModel::onlyTrashed()->count();
        $this ->assign('count',$count);

        return $this ->fetch('admin_del');
    }
    //恢复删除
    public function unDelete($id)
    {   
        AdminModel::update(['delete_time'=>NULL],['id'=>$id]);
   }
   //彻底删除
    public function pack($id)
    {
        AdminModel::destroy($id,true);
    }
    
    
}
?>