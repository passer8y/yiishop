<?php

namespace frontend\controllers;

use frontend\models\Address;
use frontend\models\Cart;
use frontend\models\Goods;
use frontend\models\GoodsCategory;
use frontend\models\GoodsGallery;
use frontend\models\Order;
use frontend\models\OrderGoods;
use yii\db\Exception;
use yii\web\Cookie;

class IndexController extends \yii\web\Controller
{
    public $layout = false;
    public $enableCsrfValidation = false;
    //首页
    public function actionIndex()
    {
        $models = GoodsCategory::find()->where(['=','parent_id','0'])->all();
        return $this->render('index',['models'=>$models]);
    }
    //商品列表页
    public function actionList($id)
    {
        $models = Goods::find()->where(['=','goods_category_id',$id])->all();
        return $this->render('list',['models'=>$models]);
    }
    
    //商品详情页
    public function actionGoods($id)
    {
        //商品详情
        $model = Goods::findOne(['id'=>$id]);
        //商品相册
        $pictures = GoodsGallery::find()->where(['=','goods_id',$id])->all();
        return $this->render('goods',['model'=>$model,'pictures'=>$pictures]);
    }

    //添加到购物车并显示
    public function actionAddCart($goods_id,$amount)
    {
        //未登录,存放到cookie中
        if(\Yii::$app->user->isGuest){
            $cookies = \Yii::$app->request->cookies;
            //获取cookie中的购物车数据
            $cart = $cookies->get('cart');
            if($cart == null){
                //如果没有，就保存该商品数据
                $carts = [$goods_id=>$amount];
            }else{
                //如果有，再判断是否是该商品
                $carts = unserialize($cart->value);
                if(isset($carts[$goods_id])){
                    //如果是该商品，则增加数量
                    $carts[$goods_id] += $amount;
                }else{
                    //如果不是该商品，则增加该商品
                    $carts[$goods_id] = $amount;
                }
            }
            //将商品id和商品数量写入cookie
            $cookies = \Yii::$app->response->cookies;
            $cookie = new Cookie([
                'name'=>'cart',
                'value'=>serialize($carts),
                'expire'=>7*24*3600+time()
            ]);
            $cookies->add($cookie);
        }else{
            //已经登录
            $member_id = \Yii::$app->user->id;
            $model = Cart::findOne(['member_id'=>$member_id,'goods_id'=>$goods_id]);
            if($model){
                $model->amount +=$amount;
                $model->save();
            }else{
                $cartModel = new Cart();
                $cartModel->goods_id = $goods_id;
                $cartModel->amount = $amount;
                $cartModel->member_id = $member_id;
                $cartModel->save();
            }
        }
        return $this->render('cart1');
    }
    //购物车页面
    public function actionCart()
    {
        //1 用户未登录，购物车数据从cookie取出
        if(\Yii::$app->user->isGuest){
            $cookies = \Yii::$app->request->cookies;
            $cart = $cookies->get('cart');
            if($cart==null){
                $carts = [];
            }else{
                $carts = unserialize($cart->value);
            }
            //获取商品数据
            $models = Goods::find()->where(['in','id',array_keys($carts)])->asArray()->all();
        }else{
            //2 用户已登录，购物车数据从数据表取
            $member_id = \Yii::$app->user->id;
            $car = Cart::find()->select(['goods_id','amount'])->where(['=','member_id',$member_id])->asArray()->All();
            $carts = [];
            foreach($car as $cart){
                $carts[$cart['goods_id']] = $cart['amount'];
            }

            //获取商品数据
            $models = Goods::find()->where(['in','id',array_keys($carts)])->asArray()->all();
        }
        return $this->render('cart',['models'=>$models,'carts'=>$carts]);
    }

    //修改购物车数据
    public function actionAjaxCart()
    {
        $goods_id = \Yii::$app->request->post('goods_id');
        $amount = \Yii::$app->request->post('amount');
        if(\Yii::$app->user->isGuest){
            $cookies = \Yii::$app->request->cookies;
            //获取cookie中的购物车数据
            $cart = $cookies->get('cart');
            if($cart==null){
                $carts = [$goods_id=>$amount];
            }else{
                $carts = unserialize($cart->value);
                if(isset($carts[$goods_id])){
                    //购物车中已经有该商品，更新数量
                    $carts[$goods_id] = $amount;
                }else{
                    //购物车中没有该商品
                    $carts[$goods_id] = $amount;
                }
            }
            //将商品id和商品数量写入cookie
            $cookies = \Yii::$app->response->cookies;
            $cookie = new Cookie([
                'name'=>'cart',
                'value'=>serialize($carts),
                'expire'=>7*24*3600+time()
            ]);
            $cookies->add($cookie);
            return 'success';
        }else{
            $member_id = \Yii::$app->user->id;
            $model = Cart::findOne(['goods_id'=>$goods_id,'member_id'=>$member_id]);
            $model->amount = $amount;
            $model->save();
        }
    }

    //删除购物车数据
    public function actionDelCart($goods_id)
    {
        if(\Yii::$app->user->isGuest){
            //未登录，删除cookie中的对应数据
            $cookies = \Yii::$app->response->cookies;
            $cart = $cookies->remove('cart',$goods_id);
            return $this->redirect(['index/cart']);
        }else{
            //登录，删除cart数据库中对应数据
            $model = Cart::findOne(['goods_id'=>$goods_id,'member_id'=>\Yii::$app->user->id]);
            $model->delete();
            return $this->redirect(['index/cart']);
        }
    }
    //订单
    public function actionOrder()
    {
        $model = new Order();
        //先判断是否登陆，登录就读取购物车数据表，没有登录就跳转到登录页面
        if (\yii::$app->user->isGuest) {
            return $this->redirect(['member/login']);
        } else {
            $transaction = \Yii::$app->db->beginTransaction();
            if ($model->load(\yii::$app->request->post()) && $model->validate()) {
                //开启事务
                try {
                    //处理数据
                    $deliveries = Order::$deliveries;
                    $payment = Order::$payments;
                    $model->member_id = \yii::$app->user->id;
                    $address = Address::findOne(['id' => $model->address_id]);
                    $model->name = $address->name;
                    $model->province = $address->area;
                    $model->address = $address->address;
                    $model->tel = $address->tel;
                    $model->delivery_name = $deliveries[$model->deliveries_id]['name'];
                    $model->delivery_price = $deliveries[$model->deliveries_id]['price'];
                    $model->delivery_id = $model->deliveries_id;
                    $model->payment_id = $model->pay_id;
                    $model->payment_name = $payment[$model->pay_id]['name'];
                    $model->trade_no = rand(10000, 99999);
                    $model->create_time = time();
                    $model->status = 1;
                    $model->total = $model->total_price;
//                    var_dump($model->member_id);exit;
                    $model->save();
//                    var_dump($model->getErrors());exit;
                    //操作order_goods表
                    //取出所有的数据，进行遍历
                    $cart = Cart::find()->where(['member_id' => \yii::$app->user->id])->all();
                    foreach ($cart as $v) {
                        $order = new OrderGoods();
                        $order->order_id = $model->id;
                        $order->goods_id = $v->goods_id;
//                        var_dump($order->goods_id);exit;
                        $order->goods_name = Goods::findOne(['id' => $v->goods_id])->name;
                        $order->logo = Goods::findOne(['id' => $v->goods_id])->logo;
                        $order->price = Goods::findOne(['id' => $v->goods_id])->shop_price;
                        //判断数据库商品数量，如果数量足够就减去对应的数量，如果没有就回滚
                        $goods = Goods::findOne(['id' => $v->goods_id]);
                        $order->amount = $v->amount;
                        $order->total = $v->amount * $order->price;
                        $order->save();
                        //判断大小
                        if ($v->amount < $goods->stock) {
                            $goods->stock = ($goods->stock) - ($v->amount);
                            $goods->save();
                        } elseif ($v->amount > ($goods->stock)) {
                            throw new Exception('商品库存不足，无法继续下单，请修改购物车商品数量');
                        }
                        //清空购物车数据
                        $v->delete();
                    }
                    $transaction->commit();
                    return $this->redirect('order-end');
                } catch (Exception $e) {
                    $transaction->rollBack();
                    return $this->redirect(['index/order']);
                }
            } else {
//                var_dump(111);exit;
                $address = Address::find()->where(['member_id' => \yii::$app->user->id])->all();
                $cars = Cart::find()->where(['member_id' => \yii::$app->user->id])->all();
                return $this->render('order', ['model' => $model, 'cars' => $cars, 'address' => $address]);
            }
        }
    }
    //提交订单完成页面
    public function actionOrderEnd()
    {
        return $this->render('flow3');
    }
    //我的订单页面
    public function actionOrderList()
    {
        if(\Yii::$app->user->isGuest){
            return $this->redirect(['member/login']);
        }
        $member_id = \Yii::$app->user->id;
        $orders = Order::find()->where(['member_id'=>$member_id])->all();
        return $this->render('order_list',['orders'=>$orders]);
    }
}
