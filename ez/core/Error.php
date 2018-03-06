<?php
namespace ez\core;

/**
 * 框架错误异常显示类
 * 
 * @author lxj
 */
class Error
{
    /**
     * 错误显示
     * 
     * @access public
     */
    public static function showError($error)
    {
        header("Content-type:text/html;charset=utf-8");
        die("<pre>$error</pre>");
    }
    
    /**
     * 错误不显示
     * 
     * @access public
     */
    public static function showErrorPage()
    {
        $errorPage  = Ez::config('errorPage');
        if (!$errorPage && is_file(__DIR__ . '/../template/error.php')) {
            include __DIR__ . '/../template/error.php';
            die;
        }
        if (is_file($errorPage)) {
            include $errorPage;
            die;
        }
        die('<h1>施工现场，<a href="'.Route::createUrl().'">返回首页</a></h1>');
    }
    
    /**
     * 自定义错误处理函数
     * 
     * @param integer $errno 错误的级别
     * @param string $errstr 错误的信息
     * @param string $errfile 发生错误的文件名
     * @param integer $errline 错误发生的行号
     * @param array $errcontext 错误发生时活动符号表的array。包含错误触发处作用域内所有变量的数组
     * 
     * @access public
     */
    public static function errorHandler($errno, $errstr, $errfile = '', $errline = 0, $errcontext = [])
    {
        $errType = self::getErrorLevel($errno);
        $msg = "ErrorLevel:$errno, $errType: $errstr in $errfile at line $errline";
        Log::addLog($msg);
        
        switch ($errno) {
            case E_NOTICE:
            case E_WARNING:
            case E_USER_WARNING:
            case E_USER_NOTICE:
            case E_STRICT:
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                break;
            default:
                if (Ez::config('debug')) {
                    self::showError($msg);
                } else {
                    self::showErrorPage();
                }
        }
    }
    
    /**
     * 自定义异常处理
     * 
     * @param object $ex 异常类
     * 
     * @access public
     */
    public static function exceptionHandler($ex)
    {
        $msg = $ex->__toString();
        Log::addLog($msg);
        
        if (Ez::config('debug')) {
            self::showError($msg);
        } else {
            self::showErrorPage();
        }
    }
    
    /**
     * 获取错误常量字面量
     * 
     * @access public
     */
    public static function getErrorLevel($errno)
    {
        $arr = [
            1       => 'E_ERROR',
            2       => 'E_WARNING',
            4       => 'E_PARSE',
            8       => 'E_NOTICE',
            16      => 'E_CORE_ERROR',
            32      => 'E_CORE_WARNING',
            64      => 'E_COMPILE_ERROR',
            128     => 'E_COMPILE_WARNING',
            256     => 'E_USER_ERROR',
            512     => 'E_USER_WARNING',
            1024    => 'E_USER_NOTICE',
            2048    => 'E_STRICT',
            4096    => 'E_RECOVERABLE_ERROR',
            8192    => 'E_DEPRECATED',
            16384   => 'E_USER_DEPRECATED',
            30719   => 'E_ALL',
        ];
        
        return $arr[$errno];
    }
}

