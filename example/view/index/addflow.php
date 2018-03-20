<?php 
use ez\core\Route;
include(__DIR__ . '/../public/head.php');
?>
    <div class="layui-body">
        <div style="padding: 15px;">
            <a href="<?=Route::createUrl('flowlist')?>" class="layui-btn">返回</a>
            <form class="layui-form" action="<?= \ez\core\Route::createUrl('addmember')?>" method="post">
                <div class="layui-form-item">
                    <label class="layui-form-label">流程类型</label>
                    <div class="layui-input-inline">
                        <select id="TypeID" name="TypeID" lay-verify="required" lay-filter="city">
                            <option value="">请选择流程类型</option>
                            <?php foreach($flowtype as $val) { ?>
                            <option value="<?=$val['ID']?>"><?=$val['Type']?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">流程名称</label>
                    <div class="layui-input-block" style="width: 50%;">
                        <input type="text" name="Name" lay-verify="required" autocomplete="off" placeholder="请输入流程名称" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item layui-form-text">
                    <label class="layui-form-label">流程说明</label>
                    <div class="layui-input-block" style="width: 50%;">
                        <textarea placeholder="请输入流程说明" name="Introduce" class="layui-textarea"></textarea>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">编号规则</label>
                    <div class="layui-input-block" style="width: 50%;">
                        <input type="text" name="Name" lay-verify="required" autocomplete="off" placeholder="请输入流程名称" class="layui-input">
                    </div>
                </div>
                
                
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn" lay-submit="" lay-filter="demo1">立即提交</button>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="layui-footer">
            © 多彩贵州印象网络传媒股份有限公司
        </div>
    </div>

    

    <script src="/js/jquery-3.2.1.min.js" type="text/javascript"></script>
    <script src="/layui/layui.all.js"></script>
    <script>
        
        
    </script>
</body>
</html>
