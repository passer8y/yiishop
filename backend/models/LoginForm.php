<?php
namespace backend\models;
use yii\base\Model;

class LoginForm extends Model{
    public $auto;
    public $username;
    public $password;

    public function rules()
    {
        return [
            [['username','password'],'required'],
            ['auto','safe']
        ];
    }

    public function attributeLabels()
    {
        return[
            'username'=>'用户名',
            'password'=>'密码',
            'auto'=>'自动登录'

        ];
    }

    public function login()
    {
        //通过用户名查找用户
        $user_log = User::findOne(['username'=>$this->username]);
//        var_dump($user_log);exit;
        if($user_log){
            if(\Yii::$app->security->validatePassword($this->password,$user_log->password_hash)){
                \Yii::$app->user->login($user_log,$this->auto ? 3600*24 : 0);
                return true;
            }else{
                $this->addError('password','密码错误');
            }
        }else{
            $this->addError('username','用户名不存在');
        }
        return false;
    }
}