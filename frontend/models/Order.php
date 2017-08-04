<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "order".
 *
 * @property integer $id
 * @property integer $member_id
 * @property string $name
 * @property string $province
 * @property string $address
 * @property string $tel
 * @property integer $delivery_id
 * @property string $delivery_name
 * @property double $delivery_price
 * @property integer $payment_id
 * @property string $payment_name
 * @property string $total
 * @property integer $status
 * @property string $trade_no
 * @property integer $create_time
 */
class Order extends \yii\db\ActiveRecord
{
    //定义收货方式
    public static $deliveries=[
        1=>['id'=>1,'name'=>'普通快递送货上门','price'=>'10.00','intro'=>'速度一般，服务一般'],
        2=>['id'=>2,'name'=>'特快专递','price'=>'40.00','intro'=>'速度快，服务好'],
        3=>['id'=>3,'name'=>'加急快递送货上门','price'=>'40.00','intro'=>'速度超快，服务好'],
        4=>['id'=>4,'name'=>'平邮','price'=>'10.00','intro'=>'速度一般，服务一般']
    ];
    public static $payments = [
        1=>['id'=>1,'name'=>'货到付款','intro'=>'送货上门后再收款，支持现金、POS机刷卡、支票支付'],
        2=>['id'=>2,'name'=>'在线支付','intro'=>'即时到帐，支持绝大数银行借记卡及部分银行信用卡'],
        3=>['id'=>3,'name'=>'上门自提','intro'=>'自提时付款，支持现金、POS刷卡、支票支付'],
        4=>['id'=>4,'name'=>'邮局汇款','intro'=>'通过快钱平台收款 汇款后1-3个工作日到账']
    ];
    public $address_id;
    public $deliveries_id;
    public $pay_id;
    public $total_price;
    //定义支付方式
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['address_id','deliveries_id','pay_id','total_price'],'required'],
            [['total_price', 'trade_no'],'safe'],
            [['member_id', 'delivery_id', 'payment_id', 'status', 'create_time'], 'integer'],
            [['delivery_price', 'total'], 'number'],
            [['name'], 'string', 'max' => 50],
            [['province'], 'string', 'max' => 255],
            [['address', 'delivery_name', 'payment_name'], 'string', 'max' => 255],
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
            'member_id' => '用户id',
            'name' => '收货人',
            'province' => '地址',
            'address' => '详细地址',
            'tel' => '电话号码',
            'delivery_id' => '配送方式id',
            'delivery_name' => '配送方式名称',
            'delivery_price' => '配送方式价格',
            'payment_id' => '支付方式id',
            'payment_name' => '支付方式名称',
            'total' => '订单金额',
            'status' => '订单状态(0已取消1待付款2待发货3待收货4完成)',
            'trade_no' => '第三方支付交易号',
            'create_time' => '创建时间',
        ];
    }
}
