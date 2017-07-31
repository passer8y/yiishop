<?php
	//引入DB类
	require 'DB.class.php';
	//实例化对象
	$db = DB::getInstance(['password'=>'root','dbname'=>'yiishop']);
	//接收数据
	$pid = $_GET['pid'];
	//拼凑sql语句
	$sql = "select * from locations where parent_id={$pid}";
	//执行sql
	$rows = $db->fetchAll($sql);
	//返回结果
	echo json_encode($rows);
	
	
	
	
	
?>