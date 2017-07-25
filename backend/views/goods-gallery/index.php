<?php
use yii\web\JsExpression;
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($goods,'name')->textInput(['disabled'=>'disabled']);
//echo $form->field($model,'path[]')->hiddenInput();

//外部TAG
echo \yii\bootstrap\Html::fileInput('test', NULL, ['id' => 'test']);
echo \flyok666\uploadifive\Uploadifive::widget([
    'url' => yii\helpers\Url::to(['goods-gallery/s-upload']),
    'id' => 'test',
    'csrf' => true,
    'renderTag' => false,
    'jsOptions' => [
        'formData'=>['someKey' => 'someValue'],
        'width' => 120,
        'height' => 40,
        'onError' => new JsExpression(<<<EOF
function(file, errorCode, errorMsg, errorString) {
    console.log('The file ' + file.name + ' could not be uploaded: ' + errorString + errorCode + errorMsg);
}
EOF
        ),
        'onUploadComplete' => new JsExpression(<<<EOF
function(file, data, response) {
    data = JSON.parse(data);
    //console.log(data);
    if (data.error) {
        console.log(data.msg);
    } else {
        console.log(data.fileUrl);
        //将图片的地址赋值给logo字段
        $("#goodsgallery-path").val(data.fileUrl);
        //将上传成功的图片回显
        //$("#img").attr('src',data.fileUrl);
        //生成一个隐藏的输入框
        var html = '<input type="hidden" name="path[]" value="'+data.fileUrl+'">';
        $("#submit_btn").before($(html));
    }
}
EOF
        ),
    ]
]);
//logo回显
foreach($models as $paths){
    echo \yii\bootstrap\Html::img($paths->path?$paths->path:false,['id'=>'img','height'=>50]);
    echo \yii\helpers\Html::a('删除',['goods-gallery/del','id'=>$paths->id],['class'=>'btn btn-xs btn-danger'])."<br />";
}

echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info','id'=>'submit_btn']);
\yii\bootstrap\ActiveForm::end();


