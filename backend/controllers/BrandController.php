<?php
namespace backend\controllers;
use backend\filters\RbacFilter;
use backend\models\Brand;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\Request;

use flyok666\uploadifive\UploadAction;
use flyok666\qiniu\Qiniu;

class BrandController extends Controller{

    public function actionIndex()
    {
        //分页
        $query = Brand::find()->where('status>=0');
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
        $model = new Brand();
        $request = new Request();
        //判断提交
        if($request->isPost){
            //加载数据
            $model->load($request->post());
            if($model->validate()){
                $model->save();
                return $this->redirect(['brand/index']);
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
        $model = Brand::findOne(['id'=>$id]);
        $request = new Request();
        //判断提交
        if($request->isPost){
            //加载数据
            $model->load($request->post());
//            var_dump($model);exit;
            if($model->validate()){
                $model->save();
                return $this->redirect(['brand/index']);
            }else{
                var_dump($model->getErrors());exit;
            }
        }
        //调用视图
        return $this->render('add',['model'=>$model]);
    }

    //删除
    public function actionDel($id)
    {
        //获取数据
        $model = Brand::findOne(['id'=>$id]);
        $model->status = -1;
        $model->save();
        //跳转到列表
        return $this->redirect(['brand/index']);
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
                    $p1 = substr($filehash, 0, 2);
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
                    //$action->output['fileUrl'] = $action->getWebUrl();//输出文件的相对路径
//                    $action->getFilename(); // "image/yyyymmddtimerand.jpg"
//                    $action->getWebUrl(); //  "baseUrl + filename, /upload/image/yyyymmddtimerand.jpg"
//                    $action->getSavePath(); // "/var/www/htdocs/upload/image/yyyymmddtimerand.jpg"
                    //将图片上传到七牛云
                    $qiniu = new Qiniu(\Yii::$app->params['qiniu']);
                    $qiniu->uploadFile(
                        $action->getSavePath(), $action->getWebUrl()
                    );
                    $url = $qiniu->getLink($action->getWebUrl());
                    $action->output['fileUrl']  = $url;
                },
            ],
        ];
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