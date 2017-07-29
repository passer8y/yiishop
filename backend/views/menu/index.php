<h1>菜单列表&nbsp; \<?= \yii\bootstrap\Html::a('添加',['menu/add'],['class'=>'btn btn-lg']) ?></h1>
<table class="table table-condensed table-bordered">
    <tr>
        <th>ID</th>
        <th>菜单名称</th>
        <th>地址/路由</th>
        <th>上级菜单</th>
        <th>排序</th>
        <th>操作</th>
    </tr>
    <?php foreach($models as $model): ?>
        <tr>
            <td><?= $model->id ?></td>
            <td><?= $model->name ?></td>
            <td><?= $model->url ?></td>
            <td><?= $model->parent_id ?></td>
            <td><?= $model->sort ?></td>
            <td>
                <?= \yii\bootstrap\Html::a('修改',['menu/edit','id'=>$model->id]) ?>
                <?= \yii\bootstrap\Html::a('删除',['menu/del','id'=>$model->id]) ?>

            </td>
        </tr>
    <?php endforeach; ?>
</table>
