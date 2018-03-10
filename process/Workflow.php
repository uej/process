<?php
namespace process;

/**
 * 工作流引擎接口类
 * 
 * @author lxj
 */
class Workflow
{
    /**
     * @var array 配置
     */
    public $config;
    
    
    /**
     * 初始化
     * 
     * @access public
     */
    public function __construct() {
        $this->config   = include(__DIR__ . '/config.php');
    }
    
    /**
     * 流程进行
     * 
     * @param type $programID
     * @param type $workflowID
     * @param type $nowflow
     */
    public function doFlow($programID, $workflowID)
    {
        $medoo  = self::connectdb();
        
        /* 执行前校验 */
        
        
    }
    
    public function startNew()
    {
        $post   = filter_input_array(INPUT_POST);
        
        /* 获取流程表单字段 */
        
        
    }
    
    /**
     * 新建流程
     * 
     * @access public
     */
    public function createFlow()
    {
        return flow\FlowControl::createFlow();
    }
    
    /**
     * 获取medoo数据库操作类实例
     * 
     * @staticvar object $medoo
     * @return object|\process\tool\Medoo medoo实例
     */
    public static function connectdb()
    {
        static $db = '';
        
        if (!empty($db)) {
            return $db;
        } else {
            $config = include(__DIR__ . '/config.php');
            $db  = new tool\Medoo([
                'database_type' => $config['dbType'],
                'database_name' => $config['dbName'],
                'server'        => $config['dbHost'],
                'username'      => $config['dbUser'],
                'password'      => $config['dbPassword'],
                'charset'       => $config['dbCharset'],
                'port'          => $config['dbPort'],
                'prefix'        => $config['dbPrefix'],
            ]);
            
            return $db;
        }
    }
    
    /**
     * 获取流程表单所有可以设置的项
     * 
     * @access public
     */
    public function getAllFrom()
    {
        return flow\Form::$fields;
    }
    
    /**
     * 创建数据
     * 
     * @param array $data 传入数据，只有数据库中有该字才会最终生成
     * @return mixed 生成数据
     * @access public
     */
    public function create($tableName, $data = [])
    {
        if (empty($data)) {
            $data = filter_input_array(INPUT_POST);
        }
        if (empty($tableName)) {
            return FALSE;
        }
        
        $arr = [];
        
        $columns = self::connect()->query("SHOW COLUMNS FROM `{$this->config['dbPrefix']}$tableName`")->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($columns as $val) {
            $keys[] = $val['Field'];
        }
        foreach ($data as $key => $val) {
            if (in_array($key, $keys)) {
                $arr[$key] = htmlspecialchars(trim($val));      // 全局转换html元素
            }
        }
        
        if ($res) {
            return $res;
        } else {
            return FALSE;
        }
    }
    
}
