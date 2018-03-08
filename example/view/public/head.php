<?php 
use ez\core\Route;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>php工作流引擎</title>
    <link rel="stylesheet" href="/layui/css/layui.css">
    <link rel="stylesheet" href="/css/class.css">
</head>
<body>
    <div class="layui-layout layui-layout-admin">
        <div class="layui-header">
            <div class="layui-logo">php工作流引擎</div>
            <ul class="layui-nav layui-layout-left">
                <li class="layui-nav-item <?php if(CONTROLLER_NAME == 'index') { echo 'layui-this'; } ?>" ><a href="">控制台</a></li>
                <li class="layui-nav-item <?php if(CONTROLLER_NAME == 'document') { echo 'layui-this'; } ?>"><a href="">文档</a></li>
            </ul>
        </div>
  
        <div class="layui-side layui-bg-black">
            <div class="layui-side-scroll">
                <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
                <ul class="layui-nav layui-nav-tree"  lay-filter="test">
                    <li class="layui-nav-item layui-nav-itemed">
                        <a class="" href="javascript:;">流程管理</a>
                        <dl class="layui-nav-child">
                            <dd><a href="<?=Route::createUrl('index/type')?>" <?php if(ACTION_NAME == 'type') { ?>class="layui-this"<?php } ?>>类型</a></dd>
                            <dd><a href="<?=Route::createUrl('index/flowlist')?>" <?php if(ACTION_NAME == 'flowlist') { ?>class="layui-this"<?php } ?>>流程</a></dd>
                        </dl>
                    </li>
                    <li class="layui-nav-item"><a href="<?=Route::createUrl('index/index')?>" <?php if(ACTION_NAME == 'index') { ?>class="layui-this"<?php } ?>>我的申请</a></li>
                    <li class="layui-nav-item"><a href="<?=Route::createUrl('index/mycheck')?>" <?php if(ACTION_NAME == 'mycheck') { ?>class="layui-this"<?php } ?>>我的审批</a></li>
                    <li class="layui-nav-item"><a href="<?=Route::createUrl('index/myget')?>" <?php if(ACTION_NAME == 'myget') { ?>class="layui-this"<?php } ?>>抄送给我的</a></li>
                </ul>
            </div>
        </div>
