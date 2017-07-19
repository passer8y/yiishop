<?php
namespace backend\controllers;
use backend\models\ArticleCategory;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\Request;

class ArticleCategoryController extends Controller{
    public function actionIndex()
    {
        //分页
        $query = ArticleCategory::find()->where('status>=0');
        //总条数
        $total = $query->count();
        //每页显示3条
        $perPage = 3;
        //分页工具类
        $pager = new Pagination([
            'totalCount'=>$total,
            'defaultPageSize'=>$perPage,
        ]);
        $models = $query->orderBy('sort')->limit($pager->limit)->offset($pager->offset)->all();
        //调用视图
        return $this->render('index',['models'=>$models,'pager'=>$pager]);
    }

    public function actionAdd()
    {
        //实例化对象
        $model = new ArticleCategory();
        $request = new Request();
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $model->save();
                return $this->redirect(['article-category/index']);
            }else{
                var_dump($model->getErrors());exit;
            }
        }
        //调用视图
        return $this->render('add',['model'=>$model]);
    }
    //修改
    public function actionEdit($id)
    {
        //实例化对象
        $model = ArticleCategory::findOne(['id'=>$id]);
        $request = new Request();
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $model->save();
                return $this->redirect(['article-category/index']);
            }else{
                var_dump($model->getErrors());exit;
            }
        }
        //调用视图
        return $this->render('add',['model'=>$model]);
    }
    public function actionDel($id)
    {
        //获取数据
        $model = ArticleCategory::findOne(['id'=>$id]);
        $model->status = -1;
        $model->save();
        //跳转到列表
        return $this->redirect(['article-category/index']);
    }
}