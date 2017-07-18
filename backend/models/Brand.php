<?php
namespace backend\models;
use yii\db\ActiveRecord;

class Brand extends ActiveRecord{
    public $logoFile;  //logo对象

    public static function getStatus($hidden_del=true)
    {
        $options = [
            '-1' => '删除',
            '0' => '隐藏',
            '1' => '正常',
        ];

        return $options[$hidden_del];

    }
    public function rules()
    {
        return [
            [['name','intro','sort','status'],'required','message'=>'{attribute}不能为空'],
            ['logoFile','file','extensions'=>['jpg','png','gif']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name'=>'名称',
            'intro'=>'简介',
            'sort'=>'排序',
            'status'=>'状态',
            'logoFile'=>'LOGO',
        ];
    }
}