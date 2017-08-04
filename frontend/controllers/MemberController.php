<?php

namespace frontend\controllers;

use frontend\models\Address;
use frontend\models\Cart;
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
            $redis = \Yii::$app->redis;
            $tel = $redis->get('tel');
            $code = $redis->get('code');
            if($tel == $model->tel && $code == $model->captcha){
//            var_dump($model);exit;
                //密码加密
                $model->password_hash = \Yii::$app->security->generatePasswordHash($model->password_hash);
                $model->status = 1;
                $model->created_at = time();
                $model->auth_key = \Yii::$app->security->generateRandomString();
                $model->save(false);
                //跳转
                return $this->redirect(['member/login']);
            }else{
                $model->addError('captcha','验证码错误');
            }


        }
        return $this->render('register',['model'=>$model]);
    }

    public function actionLogin()
    {
        $model = new Member();
        if($model->load(\Yii::$app->request->post()) &&$model->validate()){
            if($model->login()){
                //登录成功，查看cookie中有没有cart
                $cookies = \Yii::$app->request->cookies;
                $cart = $cookies->get('cart');
                $member_id = \Yii::$app->user->id;
                if($cart != null){
                    $carts = unserialize($cart->value);
                    foreach($carts as $key=>$values){
                        $cartModel = Cart::findOne(['goods_id'=>$key,'member_id'=>$member_id]);
                        if($cartModel){
                            $cartModel->amount += $values;
                            $cartModel->save();
                        }else{
                            $cartMo = new Cart();
                            $cartMo->goods_id = $key;
                            $cartMo->amount = $values;
                            $cartMo->member_id = $member_id;
                            $cartMo->save();
                        }
                    }
                    //清除cart的cookie
                    \Yii::$app->response->cookies->remove('cart');
                }
                //保存登录ip
                $member = Member::findOne(['username'=>$model->username]);
                $member->last_login_time = time();
                $member->last_login_ip = ip2long(\Yii::$app->request->userIP) ;

                $member->save(false);
                //登录成功
                \Yii::$app->session->setFlash('success','登录成功');
                //跳转
                return $this->redirect(['index/index']);
            }
        }
        return $this->render('login',['model'=>$model]);
    }
    public function actionLogout()
    {
        \Yii::$app->user->logout();
        return $this->redirect(['member/login']);
    }

    public function actionAddress()
    {
        if(\Yii::$app->user->isGuest){
            return $this->redirect(['member/login']);
        }
        $model = new Address();
        $address = Address::find()->where(['member_id'=>\Yii::$app->user->id])->orderBy('status DESC')->all();
        if($model->load(\Yii::$app->request->post()) &&$model->validate() ){

            //保存地区
            $model->area = $model->province.$model->city.$model->areas;
            //保存登录用户id
            $model->member_id = \Yii::$app->user->id;
            $model->save();
            return $this->redirect(['member/address']);
        }
        return $this->render('address',['model'=>$model,'address'=>$address]);
    }
    //修改收货地址
    public function actionAddressEdit($id)
    {
        $model = Address::findOne(['id'=>$id]);
        $address = Address::find()->all();
        if($model->load(\Yii::$app->request->post()) &&$model->validate() ){
            //保存地区
//            var_dump($model);exit;
            if($model->province!='请选择省份' && $model->city!='请选择城市' && $model->areas!='请选择区县'){
                $model->area = $model->province.$model->city.$model->areas;
            }else
            $model->save();
            return $this->redirect(['member/address']);
        }
        return $this->render('address',['model'=>$model,'address'=>$address]);
    }
    //删除收货地址
    public function actionAddressDel($id)
    {
        $model = Address::findOne(['id'=>$id]);
        $model->delete();
        //跳转
        return $this->redirect(['member/address']);
    }
    //设置默认收货地址
    public function actionAddressRe($id)
    {
        $model = Address::findOne(['id'=>$id]);
//        var_dump($model->status);exit;
        $model->status = 1;
        $model->save();
        //跳转
        return $this->redirect(['member/address']);
    }
    //发手机短信验证码
    public function actionTest($tel)
    {
        $code = rand(1000,9999);
        $redis = \Yii::$app->redis;
        $redis->set('tel',$tel);
        $redis->set('code',$code);
        $res = \Yii::$app->sms->setPhoneNumbers($tel)->setTemplateParam(['code'=>$code])->send();
        return json_encode($res);
    }
    //测试redis
    public function actionRedis()
    {
        $redis = \Yii::$app->redis;
//        $redis->set('name','zhangsan');
//        $re = $redis->get('name');
        $tel = $redis->get('tel');
        $code = $redis->get('code');
        var_dump($tel);
        var_dump($code);
    }
}
