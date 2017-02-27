<?php
namespace Model;
class AdminUserRole extends Model
{
    public function __construct($pkVal = '')
    {
        parent::__construct('admin_user_role');
        if ($pkVal)
            $this->initArData($pkVal);
    }
    public function setItem($id, $data)
    {
        $self = new self();
        $self->initArData($id);
        if ($self->arActive()) {
            foreach($data as $field => $val)
            {
                $self->$field = $val;
            }
            return $self->save();
        } else {
            $data['role_id'] = $id;
            return  $self->add($data);
        }
    }
}