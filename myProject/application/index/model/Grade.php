<?php 
namespace app\index\model;
use think\Model;
use traits\model\SoftDelete;
class Grade extends Model
{	
	//导入软删除方法集
    use SoftDelete;
    //设置软删除字段
    //只有该字段为NULL,该字段才会显示出来
    protected $deleteTime = 'delete_time';

    // 保存自动完成列表
    protected $auto = [
        'delete_time' => NULL,
        'is_delete' => 1, //1:允许删除 
    ];
    // 新增自动完成列表
    protected $insert = [
        //新增时登录时间应该为NULL,因为刚创建
        'login_time'=> NULL, 
        //原因同上,刚创建肯定没有登录过
        'login_count' => 0, 
    ];
    // 更新自动完成列表
    protected $update = [];
    
    // 设置创建时间字段
    protected $createTime = 'create_time';
    // 设置更新时间字段
    protected $updateTime = 'update_time';
    // 时间字段取出后的默认时间格式
    protected $dateFormat = 'Y-m-d H:i:s';

	public function getStatusAttr($value)
	{
		$status=[
			0=>'已停课',
			1=>'开课',
		];
		return $status[$value];
	} 
	public function getRoleAttr($value)
	{
		$role=[
			0=>'管理员',
			1=>'超级管理员',
		];
		return $role[$value];
	} 
    //多对多关联
    public function student()
    {
        return $this->hasMany('Student');
    }
    //1对多关联
    public function teacher()
    {
        return $this ->hasOne('teacher');
    }
}

?>