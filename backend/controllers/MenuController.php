<?php

namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\Menu;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class MenuController extends Controller
{
    public function actionIndex()
    {
        $models = Menu::find()->where(['>','parent_id','0'])->all();
        return $this->render('index',['models'=>$models]);
    }
    public function actionAdd()
    {
        $model = new Menu();
        $models = Menu::find()->where(['<=','parent_id','1'])->all();
        if($model->load(\Yii::$app->request->post()) &&$model->validate()){
            //保存
            $model->save();
            //提示信息
            \Yii::$app->session->setFlash('success','菜单添加成功');
            //跳转
            return $this->redirect(['menu/index']);
        }
        return $this->render('add',['model'=>$model,'models'=>$models]);
    }
    public function actionEdit($id)
    {
        $model = Menu::findOne(['id'=>$id]);
        $models = Menu::find()->where(['<=','parent_id','1'])->all();
        if($model->load(\Yii::$app->request->post()) &&$model->validate()){
            if(!empty(Menu::findOne(['parent_id'=>$id])) && $model->parent_id!=1){
                \Yii::$app->session->setFlash('error','该菜单下面有子菜单，不能修改到其他菜单下面');
                return $this->redirect(['menu/edit','id'=>$id]);
            }else{
                //保存
                $model->save();
                //提示信息
                \Yii::$app->session->setFlash('success','菜单修改成功');
                //跳转
                return $this->redirect(['menu/index']);
            }
        }
        return $this->render('add',['model'=>$model,'models'=>$models]);
    }

    public function actionDel($id)
    {
        $model = Menu::findOne(['id'=>$id]);
        if(empty(Menu::findOne(['parent_id'=>$id]))){
            $model->delete();
            \Yii::$app->session->setFlash('success','删除成功');
            return $this->redirect(['menu/index']);
        }else{
            \Yii::$app->session->setFlash('error','该菜单下面有子菜单，不能删除');
            return $this->redirect(['menu/index']);
        }
    }

    public function behaviors()
    {
        return [
            'rbac'=>[
                'class'=>RbacFilter::className()
            ]
        ];
    }
}
