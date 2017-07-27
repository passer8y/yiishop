<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'username');
echo $form->field($model,'password_hash')->passwordInput();
echo $form->field($model,'email');
echo $form->field($model,'roles',['inline'=>true])->checkboxList(\yii\helpers\ArrayHelper::map(Yii::$app->authManager->getRoles(),'name','description'));
echo $form->field($model,'status',['inline'=>true])->radioList([10=>'启用',0=>'禁用']);
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();
