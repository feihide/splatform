<?php

use Phalcon\Mvc\Model\Validator\Email as EmailValidator;
use Phalcon\Mvc\Model\Validator\Uniqueness as UniquenessValidator;

class Apply extends Phalcon\Mvc\Model
{
    public function initialize()
    {
        //Skips fields/columns on both INSERT/UPDATE operations
        //$this->skipAttributes(array('year', 'price'));

        //Skips only when inserting
        $this->skipAttributesOnCreate(array('check_id','check_name','check_time','publish_id','publish_name','publish_time'));

        //Skips only when updating
        //$this->skipAttributesOnUpdate(array('check_id','check_name','check_time','publish_id','publish_name','publish_time'));
    }

    //设置对应版本已提交状态
    public function beforeCreate()
    {
        //Set the creation date
        //$this->repo = date('Y-m-d H:i:s');
    }

    public function getStatus(){
        $list = array('申请中','测试通过','测试失败','已上线');
        return $list[$this->status];
    }

}
