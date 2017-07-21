<?php
namespace backend\controllers;
use backend\models\Article;
use backend\models\ArticleCategory;
use backend\models\ArticleDetail;
use kucha\ueditor\UEditorAction;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\Request;

class ArticleController extends Controller{
    public function actionIndex()
    {
        //分页
        $query = Article::find()->where('status>=0');
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
        //实例化
        $category = ArticleCategory::find()->where('status>=0')->all();
        $model = new Article();
        $detail = new ArticleDetail();
        $request = new Request();
        if($request->isPost){
            $model->load($request->post());
            $detail->load($request->post());
            if($model->validate() && $detail->validate()){
                $model->create_time = time();
                $model->save();
                $detail->article_id = $model->id;
                $detail->save();
                return $this->redirect(['article/index']);
            }else{
                var_dump($model->getErrors());
                var_dump($detail->getErrors());exit;
            }
        }
        return $this->render('add',['model'=>$model,'detail'=>$detail,'category'=>$category]);
    }
    public function actionEdit($id)
    {
        //实例化
        $category = ArticleCategory::find()->where('status>=0')->all();
        $model = Article::findOne(['id'=>$id]);
        $detail = ArticleDetail::findOne(['article_id'=>$id]);
        $request = new Request();
        if($request->isPost){
            $model->load($request->post());
            $detail->load($request->post());
            if($model->validate() && $detail->validate()){
                $model->save();
                $detail->save();
                return $this->redirect(['article/index']);
            }else{
                var_dump($model->getErrors());
                var_dump($detail->getErrors());exit;
            }
        }
        return $this->render('add',['model'=>$model,'detail'=>$detail,'category'=>$category]);
    }

    //删除
    public function actionDel($id)
    {
        //获取数据
        $model = Article::findOne(['id'=>$id]);
        //将状态值改为-1
        $model->status = -1;
        //保存数据
        $model->save();
        //跳转
        return $this->redirect(['article/index']);
    }
    //Ueditor
    public function actions()
    {
        return[
            'upload' => [
                'class' => UEditorAction::className()
            ]
        ];
    }
}