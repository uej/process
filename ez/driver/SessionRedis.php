<?php
namespace ez\driver;

/**
 * Redis session驱动
 * 
 * @author lxj
 */
class SessionRedis
{
    /**
     * redis
     */
    protected $redis;


    /**
     * 初始化连接redis
     * 
     * @access public
     */
    public function init()
    {
        static $redis = NULL;
        if (!empty($redis)) {
            $this->redis = $redis;
        } else {
            $redis = new \Redis();
            $redis->connect(\ez\core\Ez::config('redisHost'), \ez\core\Ez::config('redisPort'));
            if (\ez\core\Ez::config('redisPassword')) {
                $redis->auth(\ez\core\Ez::config('redisPassword'));
            }
            $this->redis = $redis;
        }
        
        $this->redis->select(\ez\core\Ez::config('redisSessiondb'));
    }
    
    /**
     * 开启session
     * 
     * @access public
     */
    public function start()
    {
        session_start();
    }
    
    /**
     * open
     * 
     * @access public
     */
    public function open($savePath, $sessionName)
    {
        return TRUE;
    }
    
    /**
     * 读取session
     * 
     * @access public
     */
    public function read($sessionId)
    {
        return $this->redis->get(\ez\core\Ez::config('redisSessionPrefix') . $sessionId);
    }
    
    /**
     * 写入session
     * 
     * @access public
     */
    public function write($sessionId, $data)
    {
        $expire = \ez\core\Ez::config('sessionExpire');
        if ($expire > 0) {
            return $this->redis->setex(\ez\core\Ez::config('redisSessionPrefix') . $sessionId, \ez\core\Ez::config('sessionExpire'), $data);
        } else {
            return $this->redis->set(\ez\core\Ez::config('redisSessionPrefix') . $sessionId, $data);
        }
    }
    
    /**
     * 关闭Session
     *
     * @access public
     */
    public function close()
    {
        if (session_id() != '') {
            session_write_close();        
        }
        return true;
    }
   
	/**
     * 删除Session
	 *
     * @access public
     * @param string $sessionId
     * @return bool|void
     */
    public function destroy($sessionId) 
    {
        return $this->redis->delete(\ez\core\Ez::config('redisSessionPrefix') . $sessionId);
    }
    
    /**
     * Session 垃圾回收
     * @access public
     * @param string $lifetime
     * @return bool
     */
    public function gc($lifetime) 
    {
        return true;
    }
}

