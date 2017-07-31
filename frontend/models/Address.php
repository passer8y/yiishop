<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "address".
 *
 * @property integer $id
 * @property string $name
 * @property string $area
 * @property string $address
 * @property string $tel
 * @property integer $status
 */
class Address extends \yii\db\ActiveRecord
{
    public $province;
    public $city;
    public $areas;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'address';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','tel','address','province','city','areas'],'required','message'=>'{attribute}不能为空'],
            [['status'], 'safe'],
            [['name'], 'string', 'max' => 30],
            [['area', 'address'], 'string', 'max' => 255],
            [['tel'], 'string', 'max' => 11],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '收货人',
            'area' => '所在地区',
            'address' => '详细地址',
            'tel' => '手机号码',
            'status' => '默认地址 1:默认,0:正常',
        ];
    }
}
