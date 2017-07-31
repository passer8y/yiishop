<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "member".
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $email
 * @property string $tel
 * @property integer $last_login_time
 * @property integer $last_login_ip
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class Member extends ActiveRecord implements IdentityInterface
{
    const SCENARIO_REGISTER = 'register'; //注册
    const SCENARIO_LOGIN = 'login'; //登录
    public $re_password;  //确认密码
    public $code;  //验证码
    public $auto;  //自动登录
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'member';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username','password_hash'],'required','message'=>'{attribute}不能为空'],
            [['re_password','email','tel'],'required','message'=>'{attribute}不能为空','on'=>self::SCENARIO_REGISTER],
            [['last_login_time', 'last_login_ip', 'status', 'created_at', 'updated_at'], 'integer'],
            [['username'], 'string', 'max' => 50],
            ['auto','safe'],
            ['code','required','message'=>'{attribute}不能为空'],
            [['auth_key'], 'string', 'max' => 32,'on'=>self::SCENARIO_REGISTER],
            [['password_hash', 'email'], 'string', 'max' => 100],
            [['tel'], 'string', 'max' => 11,'on'=>self::SCENARIO_REGISTER],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '用户名',
            'auth_key' => 'Auth Key',
            'password_hash' => '密码',
            're_password' => '确认密码',
            'email' => '邮箱',
            'tel' => '电话',
            'last_login_time' => '最后登录时间',
            'last_login_ip' => '最后登录ip',
            'status' => '状态（1正常，0删除）',
            'created_at' => '添加时间',
            'updated_at' => '修改时间',
            'code' => '验证码',
        ];
    }

    public function login()
    {
        //通过用户名查找用户
        $user_log = Member::findOne(['username' => $this->username]);
        if ($user_log) {
            if (\Yii::$app->security->validatePassword($this->password_hash, $user_log->password_hash)) {
                \Yii::$app->user->login($user_log, $this->auto ? 3600 * 24 : 0);
                return true;
            } else {
                $this->addError('password_hash', '密码错误');
            }
        } else {
            $this->addError('username', '用户名不存在');
        }
        return false;
    }


    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        return self::findOne(['id'=>$id]);
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }
}
