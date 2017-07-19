<?php
namespace backend\controllers;
use backend\models\Brand;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\Request;
use yii\web\UploadedFile;

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
            //实例化文件上传对象
            $model->logoFile = UploadedFile::getInstance($model,'logoFile');
            if($model->validate()){
                //处理文件
                if($model->logoFile){
                    //有上传文件
                    $d = \Yii::getAlias('@webroot').'/upload/'.date('Ymd');
                    if(!is_dir($d)){
                        mkdir($d);
                    }
                    $fileName = '/upload/'.date('Ymd').'/'.uniqid().'.'.$model->logoFile->extension;
                    $model->logoFile->saveAs(\Yii::getAlias('@webroot').$fileName,false);
                    $model->logo = $fileName;
                }
                //保存并跳转
                $model->save();
                return $this->redirect(['brand/index']);
            }else{
                //验证失败，打印错误信息
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
            //实例化文件上传对象
            $model->logoFile = UploadedFile::getInstance($model,'logoFile');
            if($model->validate()){
                //处理文件
                if($model->logoFile){
                    //有上传文件
                    $d = \Yii::getAlias('@webroot').'/upload/'.date('Ymd');
                    if(!is_dir($d)){
                        mkdir($d);
                    }
                    $fileName = '/upload/'.date('Ymd').'/'.uniqid().'.'.$model->logoFile->extension;
                    $model->logoFile->saveAs(\Yii::getAlias('@webroot').$fileName,false);
                    $model->logo = $fileName;
                }
                //保存并跳转
                $model->save();
                return $this->redirect(['brand/index']);
            }else{
                //验证失败，打印错误信息
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
}