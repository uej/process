<?php
namespace process\flow;

/**
 * 流程表单管理
 * 
 * @author lxj
 */
class Form
{
    public static $fields   = [
        0 => ['name' => '单行文本', 'type' => 'varchar', 'length' => 255, 'formtype' => 'input', 'inputtype' => 'text', 'config' => ['placeholder']],
        1 => ['name' => '多行文本', 'type' => 'text', 'formtype' => 'textarea', 'config' => ['placeholder']],
        2 => ['name' => '手机号', 'type' => 'char', 'length' => 11, 'formtype' => 'input', 'inputtype' => 'text', 'pattern' => '/^1(3|4|5|7|8|9)\d{9}$/', 'config' => ['placeholder']],
        3 => ['name' => '整数', 'type' => 'int', 'length' => 11, 'formtype' => 'input', 'inputtype' => 'number', 'config' => ['placeholder']],
        4 => ['name' => '单选框', 'type' => 'varchar', 'length' => 255, 'formtype' => 'input', 'inputtype' => 'radio', 'config' => ['textarea', 'direction']],
        5 => ['name' => '复选框', 'type' => 'varchar', 'length' => 255, 'formtype' => 'input', 'inputtype' => 'checkbox', 'config' => ['textarea', 'direction']],
        6 => ['name' => '下拉框', 'type' => 'varchar', 'length' => 255, 'formtype' => 'select', 'config' => ['textarea']],
        7 => ['name' => '时间', 'type' => 'int', 'formtype' => 'input', 'inputtype' => 'text', 'config' => ['timetype']],
        8 => ['name' => '时间区间', 'type' => 'int', 'formtype' => 'input', 'inputtype' => 'text', 'config' => ['timetype']],
    ];

    
    public static function createDbTable()
    {
        
    }
}

