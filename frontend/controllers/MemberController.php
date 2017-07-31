<?php

namespace frontend\controllers;

use frontend\models\Address;
use frontend\models\Locations;
use frontend\models\Member;
use yii\web\Controller;

class MemberController extends Controller
{
    public $layout = false;
    public $enableCsrfValidation = false;
    //注册
    public function actionRegister()
    {
        $model = new Member();
        $model->scenario = Member::SCENARIO_REGISTER;
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
//            var_dump($model);exit;
            //密码加密
            $model->password_hash = \Yii::$app->security->generatePasswordHash($model->password_hash);
            $model->status = 1;
            $model->created_at = time();
            $model->auth_key = \Yii::$app->security->generateRandomString();
            $model->save();
            //跳转
            return $this->redirect(['member/login']);
        }
        return $this->render('register',['model'=>$model]);
    }

    public function actionLogin()
    {
        $model = new Member();
        if($model->load(\Yii::$app->request->post()) &&$model->validate()){
            if($model->login()){
//                var_dump($model->auto);exit;
                //保存登录ip
                $member = Member::findOne(['username'=>$model->username]);
                $member->last_login_time = time();
                $member->last_login_ip = ip2long(\Yii::$app->request->userIP) ;

                $member->save();
                //登录成功
                \Yii::$app->session->setFlash('success','登录成功');
                //跳转
                return $this->redirect(['site/index']);
            }
        }
        return $this->render('login',['model'=>$model]);
    }

    public function actionAddress()
    {
        $model = new Address();
        $address = Address::find()->all();
        if($model->load(\Yii::$app->request->post()) &&$model->validate() ){
            //保存地区
            $model->area = $model->province.$model->city.$model->areas;
            if($model->status){
                $model->status = 1;
            }else{
                $model->status = 0;
            }
            $model->save();
        }
        return $this->render('address',['model'=>$model,'address'=>$address]);
    }

}
