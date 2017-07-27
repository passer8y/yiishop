<?php
namespace backend\models;
use yii\base\Model;

class RoleForm extends Model{
    const SCENARIO_ADD = 'add';
    public $name;  //角色名称
    public $description;   //角色描述
    public $permissions=[];  //角色的权限

    public function rules()
    {
        return [
            [['name','description'],'required','message'=>'{attribute}不能为空'],
            ['permissions','safe'],
            ['name','validateName','on'=>self::SCENARIO_ADD]
        ];
    }

    public function attributeLabels()
    {
        return [
            'name'=>'角色名称',
            'description'=>'角色描述',
            'permissions'=>'权限设置'
        ];
    }
    public function validateName()
    {
        $authManager = \Yii::$app->authManager;
        if($authManager->getRole($this->name) ){
            //权限名称已存在
            $this->addError('name','角色名称已存在');
        }
    }
}