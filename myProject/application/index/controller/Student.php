<?php
namespace app\index\controller;
use app\index\controller\Base;
use app\index\model\Student as StudentModel;
use think\Session;
use think\Request;

class Student extends Base
{	
	//渲染学生列表模板
	public function studentList()
	{	
		//获取数据总条数
		$count = studentModel::count();
		//获取数据
		$student = studentModel::all();

		if(!empty($student)){
			//遍历数组，插入外联表内容
			foreach ($student as $value) {
				$data = [
					'id'=>$value->id,
					'name'=>$value->name,
					'sex'=>$value->sex,
					'age'=>$value->age,
					'mobile'=>$value->mobile,
					'email'=>$value->email,
					'city'=>$value->city,
					'status'=>$value->status,
					//教授课程
					'grade'=>isset($value->grade->name)? $value->grade->name : '<span style="color:red;">未分配<span>'
				];
				//将遍历数组保存到一个数组中
				 $list[] = $data;		
			}
			
		}else{
			$list = '';
		}
		//dump($list);
		$this -> assign('list',$list);
		$this -> assign('count',$count);
		

		return $this->fetch('student_list');
	}

	//设置状态
	public function setStatus(Request $request)
	{
		//获取当前编辑id
		$student_id = $request->param('id');
		//从服务器获取当前id的信息
		$result = StudentModel::get($student_id);
		//设置状态
		if($result->getData('status')==1){

			StudentModel::update(['status'=>0],['id'=>$student_id]);

		}else{

			StudentModel::update(['status'=>1],['id'=>$student_id]);
		}
	}
	//渲染编辑模板
	public function studentEdit($id)
	{
		//从服务器获取当前id的信息
		$data = StudentModel::get($id);
		//将获取的信息赋给student_info
		$this->assign('student_info',$data);
		//将班级表中所有数据赋值给当前模板
        $this->assign('list',\app\index\model\Grade::all());

		//渲染模板
		return $this->fetch('student_edit');
	}
	

	//执行编辑操作
	public function doEdit(Request $request)
	{
		
		//获取当前表单信息
		$data = $request-> param();
		//获取当前id
		$id = $data['id'];
		
		if($result==true){
			$status=1;
		}else{
			$sattus=0;
		}
		
		return ['status'=>$status,'result'=>$result,'data'=>$status];
	}

	//渲染添加学生模板
	public function studentAdd()
	{

		return $this->fetch('student_add');
	}

	//执行添加操作
	public function doAdd(Request $request)
	{
		if($request()->isPost()){
			$data = [
				'name'=>input('name'),
				'sex'=>input('sex'),
				'mobile'=>input('mobile'),
				'email'=>input('email'),
				'city'=>input('city'),
				'remark'=>input('remark')
			];
			$rules = [
				'name|用户名'=>'require|unique|max:10',
	        	'password|密码'=>'require|length:8,16',
	        	'mobile'=>'require|number',
	        	'email'=>'email',
	        	'remark'=>'max:100'
			];
			$validate = new Validate($rules);
			$check = $validate ->validate($data,$rules);
			if($check===true){
				$result = StudentModel::create($data);
				if($result){
					$status = 1;
				}else{
					$status = 0;
				}
				return ['status'=>$status];
			}else{
				return $this->error($validate->getError());
			}
		}
	}
	//渲染学生信息表
	public function studentShow($id)
	{
		$data = StudentModel::get($id);

		$this->assign('student_info',$data);

		return $this->fetch('student_show');
	}
	
	//删除操作
	public function delete($id)
	{
		StudentModel::destroy($id);
	}

	//渲染已删除的用户列表
	public function alreadyDeleteList()
	{	
		//仅查询软删除的数据
		$list = StudentModel::onlyTrashed()->select();
		$this ->assign('list',$list);
		//获取软删除的数据总条数
		$count = StudentModel::onlyTrashed()->count();
		$this ->assign('count',$count);

		return $this ->fetch('student_del');
	}

	//恢复删除
	public function unDelete(Request $request)
	{	
		$id = $request -> param('id');

		StudentModel::update(['delete_time'=>NULL],['id'=>$id]);
	}

	public function allDel()
	{	
		dump($_GET['id']);
		$name = $this->getActionName();
		$model = D($name);//获取当期模块的操作对象
		$id = $_GET['id']; 
		if(is_array($id)){
			$where = 'id in('.implode(',',$id).')';
		 }else{
		  $where = 'id='.$id;
		 }
		 
		 StudentModel::destroy($id);
	}


	//彻底删除
	public function pack(Request $request)
	{
		$id = $request -> param('id');
		StudentModel::destroy($id,true);
	}


}