<?php

namespace backend\controllers;

use backend\models\Brand;
use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsDayCount;
use backend\models\GoodsIntro;
use flyok666\uploadifive\UploadAction;
use yii\data\Pagination;
use yii\web\Request;

class GoodsController extends \yii\web\Controller
{
    public $enableCsrfValidation = false;
    public function actionIndex()
    {

        $request = new Request();
        if($request->isPost){
            $sn = $_POST['search_sn'];
            $name = $_POST['search_name'];
        }
//        var_dump($sn);exit;
        if(!empty($sn)){
            $sn = " and sn like "."'%".$sn."%'";
        }else{
            $sn = '';
        }
        if(!empty($name)){
            $name = " and name like "."'%".$name."%'";
        }else{
            $name = '';
        }
        //分页
        $query = Goods::find()->where("status=1 $sn $name");
        //总条数
        $total = $query->count();
        //每页显示5条
        $perPage = 5;
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
        $model=new Goods();
        $intro = new GoodsIntro();
        //根据日期查看数量表
        $count = GoodsDayCount::findOne(['day'=>date('Ymd',time())]);
        $goods_category = new GoodsCategory(['parent_id'=>0]);
        //品牌分类
        $brand = Brand::find()->all();
        $categories = GoodsCategory::find()->select(['id','parent_id','name'])->asArray()->all();
        $request = new Request();
        if($request->isPost){
            $model->load($request->post());
            $intro->load($request->post());
            if($model->validate() &&$intro->validate()){
                //判断是否是今天添加的商品
                if(empty($count)){
                    //不是今天添加的，就初始化goods_day_count
                    $day_count = new GoodsDayCount();
                    $day_count ->day = date('Ymd',time());
                    $day_count -> count = 0001;
                    $day_count->save();
                }else{
                    //是今天添加的，count加1
                    $count -> count ++;
                    $count ->save();
                }
                $model->create_time = time();
                //sn商品货号
                if(empty($count)){
                    //商品数量表里没数据，则新增为1
                    $model->sn = date('Ymd',time()).str_pad('1',4,"0",STR_PAD_LEFT);
                }else{
                    //商品数量表里有数据，则使用表里面的数据
                    $model->sn = date('Ymd',time()).str_pad($count->count,4,"0",STR_PAD_LEFT);
                }
                $model->save();
                $intro->goods_id = $model->id;
                $intro->save();
                \Yii::$app->session->setFlash('success', '添加成功');
                return $this->redirect(['goods/index']);
            }else{
                var_dump($model->getErrors());exit;
            }
        }
        return $this->render('add',['model'=>$model,'goods_category'=>$goods_category,'brand'=>$brand,'categories'=>$categories,'intro'=>$intro]);
    }

    public function actionEdit($id)
    {
        $model= Goods::findOne(['id'=>$id]);
        $intro = GoodsIntro::findOne(['goods_id'=>$id]);
        $goods_category = GoodsCategory::findOne(['id'=>$model->goods_category_id]);
        $brand = Brand::find()->all();

        $request = new Request();
        if($request->isPost){
            $model->load($request->post());
//            var_dump($model);exit;
            if($model->validate()){
                $model->save();
                \Yii::$app->session->setFlash('success', '修改成功');
                return $this->redirect(['goods/index']);
            }else{
                var_dump($model->getErrors());exit;
            }
        }
        $categories = GoodsCategory::find()->select(['id','parent_id','name'])->asArray()->all();
        return $this->render('add',['model'=>$model,'goods_category'=>$goods_category,'brand'=>$brand,'categories'=>$categories,'intro'=>$intro]);
    }

    public function actionDel($id)
    {
        //获取数据
        $model = Goods::findOne(['id'=>$id]);
        //将状态值改为-1
        $model->status = -1;
        //保存数据
        $model->save();
        //跳转
        \Yii::$app->session->setFlash('success', '删除成功');
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
                    $action->output['fileUrl'] = $action->getWebUrl();//输出文件的相对路径
//                    $action->getFilename(); // "image/yyyymmddtimerand.jpg"
//                    $action->getWebUrl(); //  "baseUrl + filename, /upload/image/yyyymmddtimerand.jpg"
//                    $action->getSavePath(); // "/var/www/htdocs/upload/image/yyyymmddtimerand.jpg"

                },
            ],
        ];
    }
}
