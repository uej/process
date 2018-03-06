<?php
namespace ez\driver;

/**
 * Session驱动抽象类，所有session驱动需继承此类
 * 
 * @author lxj
 */
abstract class Session
{
    /**
     * 构造函数
     * 
     * @access public
     */
    public function __construct()
    {
		if (\ez\core\Ez::config('sessionAutoStart')) {
			$this->init();
			session_set_save_handler(
				array($this, 'open'),
				array($this, 'close'),
				array($this, 'read'),
				array($this, 'write'),
				array($this, 'destroy'),
				array($this, 'gc')
			);
			$this->start();
			register_shutdown_function(array($this, 'close'));
		}
	}
    
    /* 初始 */ 
	abstract public function init();
    
	/* 开始 */
	abstract public function start();
    
	/* 打开 */ 
	abstract public function open($path, $name);
    
	/* 关闭 */ 
	abstract public function close();
    
	/* 删除 */ 
	abstract public function destroy($id);
    
	/* 回收 */ 
	abstract public function gc($maxLifetime);
    
	/* 写入 */ 
	abstract public function write($id, $data);
    
	/* 读取 */ 
	abstract public function read($id);
    
}

