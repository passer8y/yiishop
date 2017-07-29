<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name');
echo $form->field($model,'url')->dropDownList(
    array_merge_recursive(['顶级菜单'=>'顶级地址'],\yii\helpers\ArrayHelper::map(Yii::$app->authManager->getPermissions(),'name','name'))
);
echo $form->field($model,'parent_id')->dropDownList(
    \yii\helpers\ArrayHelper::map($models,'id','name')
);
echo $form->field($model,'sort');
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();