<?php
namespace backend\filters;
use yii\base\ActionFilter;
use yii\web\NotFoundHttpException;

class RbacFilter extends ActionFilter{
    public function beforeAction($action)
    {
        //判断用户是否登录
        if(\Yii::$app->user->isGuest){
            return $action->controller->redirect(\Yii::$app->user->loginUrl);
        }else{
            if(\Yii::$app->user->can($action->uniqueId)){
                return true;
            }else{
                throw new NotFoundHttpException('对不起，你没有权限');
//            return false;
            }
        }


    }
}
