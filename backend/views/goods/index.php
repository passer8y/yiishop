<h1>商品列表 \ <?=\yii\bootstrap\Html::a('添加商品',['goods/add'],['class'=>'btn btn-lg'])?></h1>
<form class="form-inline" method="post">
    <div class="form-group">
        <input type="text" class="form-control" name="search_sn" placeholder="货号">
    </div>
    <div class="form-group">
        <input type="text" class="form-control" name="search_name" placeholder="商品名称">
    </div>
    <button type="submit" class="btn glyphicon glyphicon-search"></button>
</form>

<!--//$form = \yii\bootstrap\ActiveForm::begin();-->
<!--//echo "<h1>商品列表 \ ". \yii\bootstrap\Html::a('添加商品',['goods/add'],['class'=>'btn btn-lg'])."</h1>";-->
<!--//echo $form->field($goods,'sn')->textInput(['class'=>'col-xs-2','placeholder'=>'按货号']);-->
<!--//echo $form->field($goods,'name')->textInput(['class'=>'col-xs-2','placeholder'=>'按商品名称']);-->
<!--//echo \yii\bootstrap\Html::submitButton('搜索',['goods/index']);-->
<!--//\yii\bootstrap\ActiveForm::end();-->

<table class="table table-bordered table-condensed" style="padding-top: 0">
    <tr>
        <th>ID</th>
        <th>货号</th>
        <th>商品名称</th>
        <th>价格</th>
        <th>库存</th>
        <th>LOGO</th>
        <th width="230px">操作</th>
    </tr>
    <?php foreach($models as $model): ?>
        <tr>
            <td><?= $model->id ?></td>
            <td><?= $model->sn ?></td>
            <td><?= $model->name ?></td>
            <td><?= $model->shop_price ?></td>
            <td><?= $model->stock ?></td>
            <td><?= \yii\bootstrap\Html::img($model->logo,['height'=>40]) ?></td>
            <td>
                <?= \yii\bootstrap\Html::a('修改',['goods/edit','id'=>$model->id],['class'=>'btn btn-info btn-warning btn-sm glyphicon glyphicon-edit']) ?>
                <?= \yii\bootstrap\Html::a('删除',['goods/del','id'=>$model->id],['class'=>'btn btn-info btn-danger btn-sm glyphicon glyphicon-trash']) ?>
                <?= \yii\bootstrap\Html::a('相册',['goods-gallery/index','id'=>$model->id],['class'=>'btn btn-info btn-info btn-sm glyphicon glyphicon-picture']) ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
<div class="text-right">
    <?= \yii\widgets\LinkPager::widget(['pagination'=>$pager,'nextPageLabel'=>'下一页','prevPageLabel'=>'上一页','firstPageLabel'=>'首页']); ?>
</div>