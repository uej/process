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
                    <div class="layui-inline">
                        <label class="layui-form-label">标签</label>
                        <div class="layui-input-inline">
                            <input type="text" name="" placeholder="请输入标签（如：BQ）" class="layui-input">
                        </div>
                        <a href="" class="layui-btn layui-btn-danger">删除</a>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">日期</label>
                        <div class="layui-input-inline">
                            <select id="TypeID" name="TypeID" lay-verify="required" lay-filter="city">
                                <option value="">请选择日期类型</option>
                                <option value="1"><?=date('Y')?></option>
                                <option value="2"><?=date('Ym')?></option>
                                <option value="3"><?=date('Ymd')?></option>
                            </select>
                        </div>
                        <a href="" class="layui-btn layui-btn-danger">删除</a>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">增长值</label>
                        <div class="layui-input-inline">
                            <input type="text" name="email" placeholder="请输入增长值位数" lay-verify="email" autocomplete="off" class="layui-input">
                        </div>
                        <a href="" class="layui-btn layui-btn-danger">删除</a>
                    </div>
                    <a href="" class="layui-btn">添加</a>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label"></label>
                        <div class="layui-input-inline">
                            <select id="TypeID" name="TypeID" lay-verify="required" lay-filter="city">
                                <option value="">请选择表单类型</option>
                                <?php foreach($formlist as $key => $val) { ?>
                                <option value="<?=$key?>"><?=$val['name']?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <a href="" class="layui-btn">添加</a>
                    </div>
                </div>
                <div id="formbox"></div>
                
                <div id="flowbox" style="padding-left: 100px; margin-bottom: 30px;  ">
                    <ul class="layui-timeline">
                        <li class="layui-timeline-item">
                            <i class="layui-icon layui-timeline-axis">&#xe63f;</i>
                            <div class="layui-timeline-content layui-text">
                                <h3 class="layui-timeline-title">第1级审批</h3>
                                <blockquote class="layui-elem-quote layui-quote-nm" style="cursor:pointer; width: 500px; position: absolute;" id="flow1">
                                    审批人：
                                    <br>抄送人：
                                </blockquote>
                                <div style="position: relative; top: 0px; left: 550px; width: 400px;height: 160px; border: 1px solid #cfcfcf;">
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">审批类型</label>
                                        <div class="layui-input-block">
                                            <input type="radio" name="type" value="1" title="会签">
                                            <input type="radio" name="type" value="2" title="或签" checked>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">审批人</label>
                                        <div class="layui-input-block">
                                            <a class="layui-btn" href="javascript:addcheckperson()">
                                                <i class="layui-icon">&#xe608;</i>添加
                                            </a>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <label class="layui-form-label">抄送人</label>
                                        <div class="layui-input-block">
                                            <a class="layui-btn">
                                                <i class="layui-icon">&#xe608;</i>添加
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <a href="" class="layui-btn layui-btn-danger">删除</a>
                            </div>
                            
                        </li>
                        
                    </ul>
                    <a href="javascript:addflow()" class="layui-btn">添加</a>
                </div>
                
                
                
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn" lay-submit="" lay-filter="demo1">立即提交</button>
                    </div>
                </div>
                
                <div id="role">
                    <div class="layui-form-item">
                        <label class="layui-form-label">角色</label>
                        <div class="layui-input-block">
                            <?php foreach(example\model\Role::select('*') as $val) { ?>
                            <input type="checkbox" name="like[write]" title="<?=$val['Role']?>">
                            <?php } ?>
                        </div>
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
        // 添加流程
        function addcheckperson() {
            var html = $('#role').html();
            layer.tab({
                area: ['600px', '600px'],
                tab: [{
                  title: '人', 
                  content: html
                }, {
                  title: '部门角色', 
                  content: html
                }]
            });
        }
        
    </script>
</body>
</html>
