<?php

namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\PermissionForm;
use backend\models\RoleForm;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class RbacController extends Controller
{
    //权限 增删改查
    //权限列表
    public function actionPermissionIndex()
    {
        $authManager = \Yii::$app->authManager;
        $models = $authManager->getPermissions();
        return $this->render('permission-index',['models'=>$models]);
    }
    //添加权限
    public function actionPermissionAdd()
    {
        $model = new PermissionForm();
        $model->scenario = PermissionForm::SCENARIO_ADD;
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            //实例化组件
            $authManager = \Yii::$app->authManager;
            //创建
            $permission = $authManager->createPermission($model->name);
            $permission->description = $model->description;
            //保存到数据表
            $authManager->add($permission);
            //提示信息
            \Yii::$app->session->setFlash('success','权限添加成功');
            return $this->redirect(['rbac/permission-index']);
        }
        return $this->render('permission-add',['model'=>$model]);
    }
    //修改权限
    public function actionPermissionEdit($name)
    {
        //检测权限是否存在
        $authManager = \Yii::$app->authManager;
        $permission = $authManager->getPermission($name);
        if($permission == null){
            throw new NotFoundHttpException('权限不存在');
        }
        //实例化
        $model = new PermissionForm();
        if(\Yii::$app->request->isPost){
            if($model->load(\Yii::$app->request->post()) && $model->validate()){
                //将表单数据赋值给$permission
                $permission->name = $model->name;
                $permission->description = $model->description;
                //更新
                $authManager->update($name,$permission);
                //提示信息
                \Yii::$app->session->setFlash('success','权限修改成功');
                //跳转
                return $this->redirect(['rbac/permission-index']);
            }
        }else{
            //回显数据(将值赋值给$model)
            $model->name = $permission->name;
            $model->description = $permission->description;
        }
        return $this->render('permission-add',['model'=>$model]);
    }
    //删除权限
    public function actionPermissionDel($name)
    {
        $authManager = \Yii::$app->authManager;
        $permission = $authManager->getPermission($name);
        $authManager->remove($permission);
        \Yii::$app->session->setFlash('success','权限删除成功');
        return $this->redirect(['rbac/permission-index']);
    }
    //角色增删改查
    //角色列表
    public function actionRoleIndex()
    {
        $authManager = \Yii::$app->authManager;
        $models = $authManager->getRoles();
        return $this->render('role-index',['models'=>$models]);
    }
    //添加角色
    public function actionRoleAdd()
    {
        $model = new RoleForm();
        $model->scenario = RoleForm::SCENARIO_ADD;
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            //创建和保存角色
            $authManager = \Yii::$app->authManager;
            $role = $authManager->createRole($model->name);
            $role->description = $model->description;
            $authManager->add($role);
            //给角色赋予权限
            if(is_array($model->permissions)){
                foreach($model->permissions as $permissionName){
                    $permission = $authManager->getPermission($permissionName);
                    if($permission){
                        $authManager->addChild($role,$permission);
                    }
                }
            }
            \Yii::$app->session->setFlash('success','角色添加成功');
            return $this->redirect(['role-index']);
        }
        return $this->render('role-add',['model'=>$model]);
    }
    //修改角色
    public function actionRoleEdit($name)
    {
        //检测角色是否存在
        $authManager = \Yii::$app->authManager;
        $role = $authManager->getRole($name);
        if($role == null){
            throw new NotFoundHttpException('角色不存在');
        }
        $model = new RoleForm();
        if(\Yii::$app->request->isPost){
            if($model->load(\Yii::$app->request->post()) && $model->validate()){
                //取消角色和权限的关联
                $authManager->removeChildren($role);
                //给角色赋值并更新
                $role->name = $model->name;
                $role->description = $model->description;
                $authManager->update($name,$role);
                //给角色重新赋予权限
                if(is_array($model->permissions)){
                    foreach($model->permissions as $permissionName){
                        $permission = $authManager->getPermission($permissionName);
                        if($permission){
                            $authManager->addChild($role,$permission);
                        }
                    }
                }
                //提示信息
                \Yii::$app->session->setFlash('success','角色修改成功');
                //跳转
                return $this->redirect(['rbac/role-index']);
            }
        }else{
            //表单数据回显
            $model->name = $role->name;
            $model->description = $role->description;
            //获取该角色的权限
            $permissions = $authManager->getPermissionsByRole($name);
            $model->permissions = ArrayHelper::map($permissions,'name','name');

        }
        return $this->render('role-add',['model'=>$model]);
    }
    //删除角色
    public function actionRoleDel($name)
    {
        $authManager = \Yii::$app->authManager;
        $role = $authManager->getRole($name);
        $authManager->removeChildren($role);
        $authManager->remove($role);
        \Yii::$app->session->setFlash('success','角色删除成功');
        return $this->redirect(['rbac/role-index']);
    }

    public function behaviors()
    {
        return [
            'rbac'=>[
                'class'=>RbacFilter::className()
            ]
        ];
    }
}
