<?php
namespace backend\models;
use yii\db\ActiveRecord;

class Article extends ActiveRecord{
    public function getArticleCategory()
    {
        return $this->hasOne(ArticleCategory::className(),['id'=>'article_category_id']);
    }
    
    public function rules()
    {
        return [
            [['name','intro','article_category_id','sort','status'],'required'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'name'=>'文章标题',
            'intro'=>'简介',
            'article_category_id'=>'文章分类',
            'sort'=>'排序',
            'status'=>'状态',
        ];
    }

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
}