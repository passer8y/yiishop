<?php

namespace backend\controllers;

use backend\models\GoodsCategory;
use yii\data\Pagination;
use yii\web\HttpException;
use yii\web\Request;

class GoodsCategoryController extends \yii\web\Controller
{
    public function actionIndex()
    {
        //分页
        $query = GoodsCategory::find();
        //总条数
        $total = $query->count();
        //每页显示3条
        $perPage = 5;
        //分页工具类
        $pager = new Pagination([
            'totalCount'=>$total,
            'defaultPageSize'=>$perPage,
        ]);
        $models = $query->orderBy('depth')->limit($pager->limit)->offset($pager->offset)->all();
        //调用视图
        return $this->render('index',['models'=>$models,'pager'=>$pager]);
    }

    public function actionAdd()
    {
        $model = new GoodsCategory(['parent_id'=>0]);
        $request = new Request();
        if ($request->isPost) {
            $model->load($request->post());
            if ($model->validate()) {
                //判断是否是添加一级分类
                if ($model->parent_id) {
                    //非一级分类
                    $category = GoodsCategory::findOne(['id' => $model->parent_id]);
                    if ($category) {
                        $model->prependTo($category);
                    } else {
                        throw new HttpException(404, '上级分类不存在');
                    }
                } else {
                    //是一级分类
                    $model->makeRoot();
                }
                $model->save();
                \Yii::$app->session->setFlash('success', '添加分类成功');
                return $this->redirect(['goods-category/index']);
            } else {
                var_dump($model->getErrors());
                exit;
            }
        }
        //获取分类数据
        $categories = GoodsCategory::find()->select(['id','parent_id','name'])->asArray()->all();
        return $this->render('add',['model'=>$model,'categories'=>$categories]);
    }

    //修改
    public function actionEdit($id)
    {
        $model = GoodsCategory::findOne(['id'=>$id]);
        $request = new Request();
        if ($request->isPost) {
            $model->load($request->post());
            if ($model->validate()) {
                //判断是否是添加一级分类
                if ($model->parent_id) {
                    //非一级分类
                    $category = GoodsCategory::findOne(['id' => $model->parent_id]);
                    if ($category) {
                        $model->prependTo($category);
                    } else {
                        throw new HttpException(404, '上级分类不存在');
                    }
                } else {
                    //是一级分类
                    $model->makeRoot();
                }
                $model->save();
                \Yii::$app->session->setFlash('success', '修改分类成功');
                return $this->redirect(['goods-category/index']);
            } else {
                var_dump($model->getErrors());
                exit;
            }
        }
        //获取分类数据
        $categories = GoodsCategory::find()->select(['id','parent_id','name'])->asArray()->all();
        return $this->render('add',['model'=>$model,'categories'=>$categories]);
    }

    public function actionDel($id)
    {
        $model = GoodsCategory::findOne(['id'=>$id]);
        $child = GoodsCategory::findOne(['parent_id'=>$model->id]);
        if(empty($child)){
            $model->delete();
            \Yii::$app->session->setFlash('success', '删除成功!!');
        }else{
            \Yii::$app->session->setFlash('error', '下面有分类,不能删除!!');
        }
        //跳转
        return $this->redirect(['goods-category/index']);
    }
}
