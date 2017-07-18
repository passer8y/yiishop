<?= \yii\bootstrap\Html::a('添加文章分类',['article-category/add']) ?>
<table class="table table-bordered table-condensed">
    <tr>
        <th>ID</th>
        <th>分类名称</th>
        <th>简介</th>
        <th>排序</th>
        <th>状态</th>
        <th>操作</th>
    </tr>
    <?php foreach($models as $model): ?>
        <tr>
            <td><?= $model->id ?></td>
            <td><?= $model->name ?></td>
            <td><?= $model->intro ?></td>
            <td><?= $model->sort ?></td>
            <td><?= \backend\models\ArticleCategory::getIndexStatus($model->status) ?></td>
            <td>
                <?= \yii\helpers\Html::a('修改',['article-category/edit','id'=>$model->id]) ?>
                <?= \yii\helpers\Html::a('删除',['article-category/del','id'=>$model->id]) ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
<div class="text-right">
    <?= \yii\widgets\LinkPager::widget(['pagination'=>$pager,'nextPageLabel'=>'下一页','prevPageLabel'=>'上一页','firstPageLabel'=>'首页']); ?>
</div>
