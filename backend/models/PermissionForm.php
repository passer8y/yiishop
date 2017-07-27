<?php
namespace backend\models;
use yii\base\Model;

class PermissionForm extends Model{
    const SCENARIO_ADD='add';
    public $name;
    public $description;

    public function rules()
    {
        return [
            [['name','description'],'required','message'=>'{attribute}不能为空'],
            ['name','validateName','on'=>self::SCENARIO_ADD]
        ];
    }

    public function attributeLabels()
    {
        return [
            'name'=>'权限名称',
            'description'=>'权限描述',
        ];
    }

    public function validateName()
    {
        $authManager = \Yii::$app->authManager;
        if($authManager->getPermission($this->name) ){
            //权限名称已存在
            $this->addError('name','权限名称已存在');
        }
    }
}