<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'username');
echo $form->field($model,'password')->passwordInput();
echo $form->field($model,'auto')->checkbox();
echo \yii\helpers\Html::submitButton('登录',['class'=>'btn btn-info']);

\yii\bootstrap\ActiveForm::end();