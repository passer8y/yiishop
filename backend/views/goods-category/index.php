<?= \yii\bootstrap\Html::a('添加商品分类',['goods-category/add'],['class'=>'btn btn-info btn-sm']) ?>
<table class="table table-bordered table-condensed">
    <tr>
        <th>ID</th>
        <th>名称</th>
        <th>简介</th>
        <th>层级</th>
        <th>操作</th>
    </tr>
    <?php foreach($models as $model): ?>
    <tr>
        <td><?= $model->id ?></td>
        <td><?= str_repeat('—',$model['depth']).$model['name'] ?></td>
        <td><?= $model->intro ?></td>
        <td><?= $model->depth ?></td>
        <td>
            <?= \yii\bootstrap\Html::a('修改',['goods-category/edit','id'=>$model->id],['class'=>'btn btn-warning btn-xs']) ?>
            <?= \yii\bootstrap\Html::a('删除',['goods-category/del','id'=>$model->id],['class'=>'btn btn-danger btn-xs']) ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<div class="text-right">
    <?= \yii\widgets\LinkPager::widget(['pagination'=>$pager,'nextPageLabel'=>'下一页','prevPageLabel'=>'上一页','firstPageLabel'=>'首页']); ?>
</div>

