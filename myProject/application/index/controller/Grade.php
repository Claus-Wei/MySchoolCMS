<?php
namespace app\index\controller;
use app\index\controller\Base;
use app\index\model\Grade as GradeModel;
use think\Session;
use think\Request;

class Grade extends Base
{	
	//渲染班级列表模板
	public function gradeList()
	{	
		//获取数据总条数
		$count = GradeModel::count();
		$this ->assign('count',$count);
		//获取班级数据
		$list = GradeModel::all();
		$this -> assign('list',$list);

		return $this->fetch('grade_list');
	}
	//班级状态变更
	public function setStatus(Request $request)
	{
		//获取当前编辑id
		$grade_id = $request->param('id');
		//从服务器获取当前id的信息
		$result = GradeModel::get($grade_id);
		//设置状态
		if($result->getData('status')==1){

			GradeModel::update(['status'=>0],['id'=>$grade_id]);

		}else{

			GradeModel::update(['status'=>1],['id'=>$grade_id]);
		}
		
	}

	//渲染编辑操作模板
	public function gradeEdit($id)
	{
		//从服务器获取当前id的课程信息
		$data = GradeModel::get($id);
		//从服务器获取当前id的老师信息
		$teacherList = \app\index\model\Teacher::all();
		
		//将获取的信息赋给student_info
		$this->assign('grade_info',$data);
		$this->assign('teacherList',$teacherList);

		//渲染模板
		return $this->fetch('grade_edit');
	}

	//执行编辑操作
	public function doEdit(Request $request)
	{
		//从提交表单中排除关联字段teacher字段
		$data = $request -> except('teacher');
		//更新条件
		$condition = ['id'=>$data['id']];
		//执行更新
		$result=GradeModel::update($data,$condition);
		if($result==1){
			$status=1;
		}else{
			$status=0;
		}

		return ['status'=>$status];
	}

	//渲染添加班级模板
	public function gradeAdd()
	{
		//从服务器获取当前id的老师信息
		$teacherList = \app\index\model\Teacher::all();
		$this->assign('teacherList',$teacherList);
		return $this->fetch('grade_add');
	}

	//执行添加操作
	public function doAdd()
	{
		
	}
	
	//软删除操作
    public function delete($id)
    {	
        GradeModel::destroy($id);
    }

    //渲染已删除的用户列表
    public function alreadyDeleteList()
    {   
        //仅查询软删除的数据
        $list = GradeModel::onlyTrashed()->select();
        $this ->assign('list',$list);
        //获取软删除的数据总条数
        $count = GradeModel::onlyTrashed()->count();
        $this ->assign('count',$count);

        return $this ->fetch('grade_del');
    }
    //恢复删除
    public function unDelete($id)
    {   
        GradeModel::update(['delete_time'=>NULL],['id'=>$id]);
   }
   //彻底删除
    public function pack($id)
    {
        GradeModel::destroy($id,true);
    }
}
