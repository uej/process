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
    public function __construct()
    {
        $this->config   = include(__DIR__ . '/config.php');
    }
    
    /**
     * 流程进行
     * 
     * @param integer $programID 申请项目id
     * @param integer $userID 审批人id
     * @param array $data 审批数据
     * @return type 处理结果
     */
    public function doFlow($programID, $userID, $data)
    {
        return flow\FlowControl::doflow($programID, $userID, $data);
    }
    
    /**
     * 新的申请
     * 
     * @param integer $flowID 流程id
     * @param integer $userID 申请用户id
     * @param array $data 申请表单数据
     * @return array 处理结果
     */
    public function startNew($flowID, $userID, $data)
    {
        return flow\FlowControl::startNew($flowID, $userID, $data);
    }
    
    /**
     * 编辑申请
     * 
     * @param integer $programID 申请id
     * @param integer $userID 申请人id
     * @param array $data 申请表单数据
     * @return array 编辑结果
     */
    public function editProgram($programID, $userID, $data)
    {
        return flow\FlowControl::edit($programID, $userID, $data);
    }
    
    /**
     * 新建流程
     * 
     * @param array $data 流程数据
     * @access public
     */
    public function createFlow($data)
    {
        return flow\FlowControl::createFlow($data);
    }
    
    /**
     * 撤回申请（仅当没有审核记录时能撤回）
     * 
     * @param integer $programID 申请项id
     * @param integer $userID 申请人id
     * @return array 处理结果
     */
    public function revoke($programID, $userID)
    {
        return flow\FlowControl::revoke($programID, $userID);
    }
    
    
    public function editFlow($flowID, $data)
    {
        
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
     * @param integer $flowID 流程id
     * @return mixed 生成数据
     * @access public
     */
    public static function create($flowID)
    {
        $data = filter_input_array(INPUT_POST);
        if (empty($flowID)) {
            return FALSE;
        }
        $tableName  = "formtable$flowID";
        
        $arr = [];
        
        $columns = self::connectdb()->query("SHOW COLUMNS FROM `{$this->config['dbPrefix']}$tableName`")->fetchAll(\PDO::FETCH_ASSOC);
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
