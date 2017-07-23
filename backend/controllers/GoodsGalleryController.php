<?php

namespace backend\controllers;

use flyok666\uploadifive\UploadAction;
use backend\models\Goods;
use backend\models\GoodsGallery;
use yii\web\Request;

class GoodsGalleryController extends \yii\web\Controller
{
    public function actionIndex($id)
    {

        $num = $id;
        $model = new GoodsGallery();
        $goods = Goods::findOne(['id'=>$num]);
        $models = GoodsGallery::find()->where("goods_id=$num")->all();
        $request = new Request();
        if($request->isPost){
            $model -> load($request->post());
            if($model->validate()){
                $model->goods_id = $num;
                $model->save();
                //跳转到当前页面
                return $this->redirect(['goods/index']);
            }
        }
        return $this->render('index',['model'=>$model,'goods'=>$goods,'models'=>$models]);
    }

    public function actionDel($id)
    {
        $model = GoodsGallery::findOne(['id'=>$id]);
        $model->delete();
        return $this->redirect(['goods/index']);
    }

    public function actions() {
        return [
            's-upload' => [
                'class' => UploadAction::className(),
                'basePath' => '@webroot/upload',
                'baseUrl' => '@web/upload',
                'enableCsrf' => true, // default
                'postFieldName' => 'Filedata', // default
                //BEGIN METHOD
                //'format' => [$this, 'methodName'],
                //END METHOD
                //BEGIN CLOSURE BY-HASH
                'overwriteIfExist' => true,//如果文件已存在，是否覆盖
                /* 'format' => function (UploadAction $action) {
                     $fileext = $action->uploadfile->getExtension();
                     $filename = sha1_file($action->uploadfile->tempName);
                     return "{$filename}.{$fileext}";
                 },*/
                //END CLOSURE BY-HASH
                //BEGIN CLOSURE BY TIME
                'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filehash = sha1(uniqid() . time());
//                    $p1 = substr($filehash, 0, 2);
                    $p1 = date("Ymd",time());
                    $p2 = substr($filehash, 2, 2);
                    return "{$p1}/{$p2}/{$filehash}.{$fileext}";
                },//文件的保存方式
                //END CLOSURE BY TIME
                'validateOptions' => [
                    'extensions' => ['jpg', 'png'],
                    'maxSize' => 1 * 1024 * 1024, //file size
                ],
                'beforeValidate' => function (UploadAction $action) {
                    //throw new Exception('test error');
                },
                'afterValidate' => function (UploadAction $action) {},
                'beforeSave' => function (UploadAction $action) {},
                'afterSave' => function (UploadAction $action) {
                    $action->output['fileUrl'] = $action->getWebUrl();//输出文件的相对路径
                },
            ],
        ];
    }
}

