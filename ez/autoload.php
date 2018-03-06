<?php
/**
 * 系统函数
 * 
 * @author lxj
 */

/**
 * 框架自动加载函数
 * 
 * @param string $classname 类名
 */
function ezAutoload($classname)
{
    if (false !== strpos($classname, '\\')) {
            
        /* 定位路径 */
        $filename = __DIR__ . '/../' . str_replace('\\', '/', $classname . '.php');

        /* 引入文件 */
        if (is_file($filename)) {
            include $filename;
        }
    } else {
        $filename = __DIR__ . '/core/' . $classname . '.php';
        if (is_file($filename)) {
            include $filename;
        }
    }
    
    return;
}

/* 注册自动加载函数 */
spl_autoload_register('ezAutoload', TRUE, TRUE);

