<h1>管理员列表&nbsp;&nbsp;\ <?= \yii\bootstrap\Html::a('添加管理员',['user/add'],['class'=>'btn btn-lg']) ?> </h1>

<table class="table table-condensed table-bordered">
    <tr>
        <th>ID</th>
        <th>用户名</th>
        <th>邮箱</th>
        <th>状态</th>
        <th>最后登录时间</th>
        <th>最后登录IP</th>
        <th>操作</th>
    </tr>
    <?php foreach($models as $model): ?>
        <tr>
            <td><?= $model->id ?></td>
            <td><?= $model->username ?></td>
            <td><?= $model->email ?></td>
            <td><?= ($model->status==10)?'启用':'禁用' ?></td>
            <td><?= $model->last_login_time?date('Y-m-d H:i:d',$model->last_login_time):'' ?></td>
            <td><?= $model->last_login_ip ?></td>
            <td>
                <?= \yii\bootstrap\Html::a('修改',['user/edit','id'=>$model->id],['class'=>'btn btn-warning btn-sm']) ?>
                <?= \yii\bootstrap\Html::a('删除',['user/del','id'=>$model->id],['class'=>'btn btn-danger btn-sm']) ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>