<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'intro')->textarea();
echo $form->field($model,'sort')->textInput(['type'=>'number']);
echo $form->field($model,'status',['inline'=>true])->radioList(backend\models\ArticleCategory::getStatus(empty($model->status)?true:false));
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();
