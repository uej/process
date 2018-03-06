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
     * 流程进行
     * 
     * @param type $programID
     * @param type $workflowID
     * @param type $nowflow
     */
    public static function doFlow($programID, $workflowID)
    {
        $medoo  = self::connect();
        
        /* 执行前校验 */
        
        
    }
    
    public static function startNew()
    {
        $medoo  = self::connect();
        $post   = filter_input_array(INPUT_POST);
        
        /* 获取流程表单字段 */
        $workflowID = intval($_POST['WorkflowID']);
        
    }
    
    /**
     * 获取medoo数据库操作类实例
     * 
     * @staticvar object $medoo
     * @return object|\process\tool\Medoo medoo实例
     */
    public static function connect()
    {
        static $medoo = '';
        
        if (!empty($medoo)) {
            return $medoo;
        } else {
            $config = include(__DIR__ . '/config.php');
            $medoo  = new tool\Medoo([
                'database_type' => $config['dbType'],
                'database_name' => $config['dbName'],
                'server'        => $config['dbHost'],
                'username'      => $config['dbUser'],
                'password'      => $config['dbPassword'],
                'charset'       => $config['dbCharset'],
                'port'          => $config['dbPort'],
                'prefix'        => $config['dbPrefix'],
            ]);
            
            return $medoo;
        }
    }
    
}
