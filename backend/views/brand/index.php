<?= \yii\bootstrap\Html::a('添加品牌',['brand/add']) ?>
<table class="table table-bordered table-condensed">
    <tr>
        <th>ID</th>
        <th>品牌名称</th>
        <th>简介</th>
        <th>LOGO</th>
        <th>排序</th>
        <th>状态</th>
        <th>操作</th>
    </tr>
    <?php foreach($models as $model): ?>
    <tr>
        <td><?= $model->id ?></td>
        <td><?= $model->name ?></td>
        <td><?= $model->intro ?></td>
        <td><?= \yii\helpers\Html::img([$model->logo],['height'=>50]) ?></td>
        <td><?= $model->sort ?></td>
        <td><?= \backend\models\Brand::getIndexStatus($model->status) ?></td>
        <td>
            <?= \yii\helpers\Html::a('修改',['brand/edit','id'=>$model->id]) ?>
            <?= \yii\helpers\Html::a('删除',['brand/del','id'=>$model->id]) ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<div class="text-right">
    <?= \yii\widgets\LinkPager::widget(['pagination'=>$pager,'nextPageLabel'=>'下一页','prevPageLabel'=>'上一页','firstPageLabel'=>'首页']); ?>
</div>