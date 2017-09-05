<?php
namespace app\index\controller;
use app\index\controller\Base;
use app\index\model\Teacher as TeacherModel;
use think\Session;
use think\Request;

class Teacher extends Base
{	
	//渲染学生列表模板
	public function teacherList()
	{	
		//获取数据总条数
		$count = TeacherModel::count();
		$teacher = TeacherModel::all();
		if(!empty($teacher)){
			foreach ($teacher as $value) {
				$data = [
					'id'=>$value->id,
					'name'=>$value->name,
					'sex'=>$value->sex,
					'age'=>$value->age,
					'school'=>$value->school,
					'mobile'=>$value->mobile,
					'email'=>$value->email,
					'city'=>$value->city,
					'hiredate'=>$value->hiredate,
					'status'=>$value->status,
					'grade'=>isset($value->grade->name)? $value->grade->name : '<span style="color:red;">未分配<span>'
				];

			 $list[] = $data;		
			}
		}else{
			$list = '';
		}
		//dump($list);
		$this -> assign('list',$list);
		$this -> assign('count',$count);
		

		return $this->fetch('teacher_list');
	}
	//设置状态
	public function setStatus(Request $request)
	{
		//获取当前编辑id
		$id = $request->param('id');
		//从服务器获取当前id的信息
		$result = TeacherModel::get($id);
		//设置状态
		if($result->getData('status')==1){

			TeacherModel::update(['status'=>0],['id'=>$id]);

		}else{

			TeacherModel::update(['status'=>1],['id'=>$id]);
		}
	}
	//渲染编辑模板
	public function teacherEdit($id)
	{
		//从服务器获取当前id的信息
		$data = TeacherModel::get($id);
		$gradeList = \app\index\model\Grade::all();
		//将获取的信息赋给student_info
		$this->assign('teacher_info',$data);
		$this->assign('gradeList',$gradeList);
		//渲染模板
		return $this->fetch('teacher_edit');
	}
	

	//执行编辑操作
	public function doEdit(Request $request)
	{
		
		//获取当前表单信息
		$data = $request-> param();
		//获取当前id
		$id = $data['id'];
		

		//return ['status'=>$status,'result'=>$result,'data'=>$status];
	}

	//渲染添加教师模板
	public function teacherAdd()
	{

		return $this->fetch('teacher_add');
	}

	//执行添加操作
	public function doAdd(Request $request)
	{
		
		if($request->isPost()){
			$data = [
				'name'=>input('name'),
				'sex'=>input('sex'),
				'age'=>input('age'),
				'mobile'=>input('mobile'),
				'email'=>input('email'),
				'city'=>input('city'),
				
			];
			$rule = [
				'name|用户名'=>'require|unique|max:10',
	        	'password|密码'=>'require|length:8,16',
	        	'mobile'=>'require|number',
	        	'email'=>'email',
	        	
			];
			
			$check = $this ->validate($data,$rule);
			if($check==true){
				$result = TeacherModel::create($data);
				if($result==true){
					$this -> success('成功');
				}else{
					$this -> error('成功');
				}
				
			}
		}
	}
	//渲染学生信息表
	public function teacherShow($id)
	{
		$data = TeacherModel::get($id);

		$this->assign('teacher_info',$data);

		return $this->fetch('teacher_show');
	}
	
	//删除操作
	public function delete($id)
	{
		TeacherModel::destroy($id);
	}

	//渲染已删除的用户列表
	public function alreadyDeleteList()
	{	
		//仅查询软删除的数据
		$list = TeacherModel::onlyTrashed()->select();
		$this ->assign('list',$list);
		//获取软删除的数据总条数
		$count = TeacherModel::onlyTrashed()->count();
		$this ->assign('count',$count);

		return $this ->fetch('teacher_del');
	}

	//恢复删除
	public function unDelete(Request $request)
	{	
		$id = $request -> param('id');

		TeacherModel::update(['delete_time'=>NULL],['id'=>$id]);
	}

	//彻底删除
	public function pack($id)
	{
		TeacherModel::destroy($id,true);
	}

}