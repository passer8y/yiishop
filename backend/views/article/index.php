<?= \yii\bootstrap\Html::a('添加文章',['article/add'],['class'=>'btn btn-info btn-sm']) ?>
<table class="table table-bordered table-condensed">
    <tr class="text-center">
        <th>ID</th>
        <th>标题</th>
        <th>简介</th>
        <th>文章分类</th>
        <th>排序</th>
        <th>状态</th>
        <th>创建时间</th>
        <th>操作</th>
    </tr>
    <?php foreach($models as $model): ?>
        <tr>
            <td><?= $model->id ?></td>
            <td><?= $model->name ?></td>
            <td><?= $model->intro ?></td>
            <td><?= $model->articleCategory->name ?></td>
            <td><?= $model->sort ?></td>
            <td><?= \backend\models\Article::getIndexStatus($model->status) ?></td>
            <td><?= date('Y-m-d H:i:s',$model->create_time) ?></td>
            <td>
                <?= \yii\bootstrap\Html::a('修改',['article/edit','id'=>$model->id],['class'=>'btn btn-xs btn-warning']) ?>
                <?= \yii\bootstrap\Html::a('删除',['article/del','id'=>$model->id],['class'=>'btn btn-xs btn-danger']) ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
<div class="text-right">
    <?= \yii\widgets\LinkPager::widget(['pagination'=>$pager,'nextPageLabel'=>'下一页','prevPageLabel'=>'上一页','firstPageLabel'=>'首页']); ?>
</div>
