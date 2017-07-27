<?php

namespace backend\controllers;

use backend\models\LoginForm;
use backend\models\PasswordForm;
use backend\models\User;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Request;

class UserController extends Controller
{
    public function actionIndex()
    {
        $models = User::find()->all();
        return $this->render('index',['models'=>$models]);
    }

    public function actionAdd()
    {
        $model = new User();
        $request = new Request();
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
//                var_dump($model);exit;
                $model->password_hash = \Yii::$app->security->generatePasswordHash($model->password_hash);
                $model->save();
                //给用户赋予角色
                $authManager = \Yii::$app->authManager;
                if(is_array($model->roles)){
                    foreach($model->roles as $roleName){
                        $role = $authManager->getRole($roleName);
                        if($role){
                            $authManager->assign($role,$model->id);
                        }
                    }
                }
                return $this->redirect(['user/index']);
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    public function actionEdit($id)
    {
        $model = User::findOne(['id'=>$id]);
        $password = $model->password_hash;
        $request = new Request();
        $authManager = \Yii::$app->authManager;
        $roles = $authManager->getRolesByUser($id);
        $model->roles = ArrayHelper::map($roles,'name','name');
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
//                var_dump($model);exit;
                if($model->password_hash == $password){
                    $model->password_hash = $password;
                }else{
                    $model->password_hash = \Yii::$app->security->generatePasswordHash($model->password_hash);
                }
                $model->save();
                //取消用户和角色的关联
                $authManager->revokeAll($id);
                //给用户重新赋予角色
                if(is_array($model->roles)){
                    foreach($model->roles as $roleName){
                        $role = $authManager->getRole($roleName);
                        if($role){
                            $authManager->assign($role,$model->id);
                        }
                    }
                }
                return $this->redirect(['user/index']);
            }
        }
        return $this->render('add',['model'=>$model]);
    }

    public function actionDel($id)
    {
        $model = User::findOne(['id'=>$id]);
        $model->delete();
        return $this->redirect(['user/index']);
    }

    public function actionLogin()
    {
        //验证
        $model = new LoginForm();
        $request = new Request();
        if($request->isPost){
            $model->load($request->post());
//            var_dump($model->auto);exit;
            if($model->validate() && $model->login()){
                //保存登录ip
                $user = User::findOne(['username'=>$model->username]);
                $user->last_login_time = time();
                $user->last_login_ip = \Yii::$app->request->userIP;
                $user->save();
                //登录成功
                \Yii::$app->session->setFlash('success','登录成功');
                //跳转
                return $this->redirect(['user/index']);
            }
        }
        return $this->render('login',['model'=>$model]);
    }
    public function actionLogout()
    {
        \Yii::$app->user->logout();
        return $this->redirect(['user/index']);
    }
    //修改自己的密码
    public function actionPassword()
    {
        if(!\Yii::$app->user->isGuest){
//            var_dump(\Yii::$app->user->identity->id);exit;
            $model = new PasswordForm();
            $request = new Request();
            $user = User::findOne(['id'=>\Yii::$app->user->identity->id]);
            if($request->isPost){
                $model->load($request->post());
                //验证旧密码是否正确
                if(\Yii::$app->security->validatePassword($model->old_password,$user->password_hash)){
                    //旧密码正确，验证旧密码和新密码是否一致
                    if($model->old_password != $model->new_password){
                        //不一致，验证新密码和确认密码是否一致
                        if($model->new_password == $model->re_password){
                            $user->password_hash = \Yii::$app->security->generatePasswordHash($model->new_password);
                            $user->save();
                            return $this->redirect(['user/index']);
                        }else{
                            $model->addError('re_password','新密码和确认密码不一致');
                        }
                    }else{
                        $model->addError('new_password','旧密码和新密码重复');
                    }
                }else{
                    $model->addError('old_password','旧密码不正确');
                }
            }
            return $this->render('password',['model'=>$model]);
        }else{
            return $this->redirect(['user/login']);
        }
    }
    
}
