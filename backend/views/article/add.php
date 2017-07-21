<?php
$form = \yii\bootstrap\ActiveForm::begin();

echo $form->field($model,'name');
echo $form->field($model,'article_category_id')->dropDownList(\yii\helpers\ArrayHelper::map($category,'id','name'));
echo $form->field($model,'intro')->textarea();

echo $form->field($detail,'content')->widget(kucha\ueditor\UEditor::className(),[
    'clientOptions' => [
        //编辑区域大小
        'initialFrameHeight' => '200',
        //设置语言
        'lang' =>'zh-cn', //中文为 zh-cn
        ]
]);
echo $form->field($model,'sort');
echo $form->field($model,'status',['inline'=>true])->radioList(\backend\models\Article::getStatus(empty($model->status)?true:false));
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();