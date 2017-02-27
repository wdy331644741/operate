<?php
namespace Model;
class AdminUser extends Model
{
    const DEL_TRUE = 1;
    const DEL_FALSE = 0;

    public function __construct($pkVal = '')
    {
        parent::__construct('admin_user');
        if ($pkVal)
            $this->initArData($pkVal);
    }

    /**
     * 根据用户查询用户是否存在
     * @param $username
     * @return bool
     */
    public function getUserInfoByPhone($phone)
    {
        $userInfo=$this->where(array('phone'=>$phone))->get()->rowArr();
        if($userInfo){
            return $userInfo;
        }else{
            return false;
        }

    }
    /**
     * 根据用户查询用户是否存在
     * @param $username
     * @return bool
     */
    public function getUserIdByName($username)
    {
        $sql = "SELECT `id` FROM `{$this->tableName}` WHERE `name` = :name";
        $this->prepareSql($sql);
        $row = $this->prepareQuery(array(':name' => $username))->row();
        if ($row) {
            return $row->id;
        } else {
            return false;
        }

    }
    /**
     * 根据用户查询用户是否存在
     * @param $id
     * @return bool
     */
    public function getUserInfoById($id)
    {
        $userInfo=$this->where(array('id'=>$id))->get()->rowArr();
        if($userInfo){
            return $userInfo;
        }else{
            return false;
        }

    }

    /**
     * @param $name 用户名
     * @param $form 来源
     * @param return $role_id
     */
    public function getRoleIdByUserNameAndIsForm($name,$form)
    {
        $userInfo = $this->where(array('name'=>$name,'is_form'=>$form))->get()->row();
        if(!$userInfo)
        {
            $self = new self();
            $self->name = $name;
            $self->password= md5($name);
            $self->is_form = $form;
            $self->role_id = '2';
            $self->create_time = date('Y-m-d H:i:s');
            $self->is_del = self::DEL_FALSE;
            if($self->save())
                return $self->role_id;
            else
                return false;
        }
        return $userInfo->role_id;
    }

}
