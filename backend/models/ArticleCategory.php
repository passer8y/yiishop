<?php
namespace backend\models;
use yii\db\ActiveRecord;

class ArticleCategory extends ActiveRecord{

    public static function getStatus($del=true)
    {
        $options = [-1=>'删除',0=>'隐藏',1=>'正常'];
        if($del){
            unset($options[-1]);
        }
        return $options;
    }
    public static function getIndexStatus($status)
    {
        $options = [-1=>'删除',0=>'隐藏',1=>'正常'];

        return $options[$status];
    }
    public function rules()
    {
        return [
            [['name','intro','sort','status'],'required','message'=>'{attribute}不能为空'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name'=>'名称',
            'intro'=>'简介',
            'sort'=>'排序',
            'status'=>'状态',
        ];
    }
}