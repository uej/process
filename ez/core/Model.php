<?php
namespace ez\core;

/**
 * 数据模型类
 * 
 * @author lxj
 */
class Model
{
    /**
     * 真实表名，带前缀
     */
    public $trueTableName;
    
    /**
     * 错误信息
     */
    public $error = "操作失败";
    
    /**
     * 字段验证规则
     */
    public $fieldCheckRule;
    
    /**
     * 表前缀
     */
    public $tablePrefix;
    
    
    
    /**
     * 构造函数
     * 
     * @access public
     */
    public function __construct($table = '')
    {
        $this->table = empty($table) ? static::$tableName : $table;
        if (empty($this->table)) {
            throw new \Exception("no tableName");
        }
        
        $this->tablePrefix      = Ez::config('dbPrefix');
        $this->trueTableName    = $this->tablePrefix . $this->table;
    }
    
    /**
     * 创建medoo实例
     * 
     * @access public
     */
    public static function makeMedoo($type = 1)
    {
        static $medoo   = [];
        
        if ($type == 1) {
            if (!empty($medoo[0])) {
                return $medoo[0];
            } else {
                $master = Ez::config('dbMaster');
                $master = $master[array_rand($master)];
                $option = [
                    'database_type' => Ez::config('dbType'),
                    'database_name' => $master['dbName'],
                    'server'        => $master['dbHost'],
                    'username'      => $master['dbUser'],
                    'password'      => $master['dbPassword'],
                    'charset'       => Ez::config('dbCharset'),
                    'port'          => $master['dbPort'],
                    'prefix'        => Ez::config('dbPrefix'),
                ];
                
                $medoo[0]   = new Medoo($option);
                return $medoo[0];
            }
        } else {
            if (!empty($medoo[1])) {
                return $medoo[1];
            } else {
                $slave  = Ez::config('dbSlave');
                $slave  = $slave[array_rand($slave)];
                $option = [
                    'database_type' => Ez::config('dbType'),
                    'database_name' => $slave['dbName'],
                    'server'        => $slave['dbHost'],
                    'username'      => $slave['dbUser'],
                    'password'      => $slave['dbPassword'],
                    'charset'       => Ez::config('dbCharset'),
                    'port'          => $slave['dbPort'],
                    'prefix'        => Ez::config('dbPrefix'),
                ];
                
                $medoo[1]   = new Medoo($option);
                return $medoo[1];
            }
        }
    }
    
    /**
     * 魔术方法调用medoo
     * 
     * @param string $name 方法名
     * @param array $$arguments 参数数组
     */
    public function __call($name, $arguments)
    {
        if (in_array($name, [
                'id',
                'action',
                'quote',
                'debug',
            ])) {
            $medoo  = self::makeMedoo(1);
            return call_user_func_array([$medoo, $name], $arguments);
            
        } else if (in_array($name, [
                'error',
                'log',
                'last',
                'info',
            ])) {
            
            if (Ez::config('dbDistributede') === 0) {
                $medoo  = self::makeMedoo(1);
                return call_user_func_array([$medoo, $name], $arguments);
            } else {
                $medoo1 = self::makeMedoo(1);
                $medoo2 = self::makeMedoo(2);
                return [
                    0   => call_user_func_array([$medoo1, $name], $arguments),
                    1   => call_user_func_array([$medoo2, $name], $arguments),
                ];
            }
            
        } else if (in_array($name, [
                'delete',
                'insert',
                'replace',
                'update',
            ])) {
            $medoo  = self::makeMedoo(1);
            array_unshift($arguments, $this->table);
            return call_user_func_array([$medoo, $name], $arguments);
            
        } else if (in_array($name, [
                'get', 
                'max',
                'min',
                'avg',
                'has',
                'count',
                'select',
                'sum',
            ])) {
            if (Ez::config('dbDistributede') === 0) {
                $medoo = self::makeMedoo(1);
            } else {
                $medoo = self::makeMedoo(2);
            }
            array_unshift($arguments, $this->table);
            return call_user_func_array([$medoo, $name], $arguments);
            
        } else {
            throw new \Exception('Method not exists');
        }
    }
    
    /**
     * 静态调用medoo （同上）,不可直接通过Model调用（如Model::get()），仅可用子类定义表名后调用
     * 
     * @param string $name 方法名
     * @param array $$arguments 参数数组
     */
    public function __callStatic($name, $arguments) {
        if (in_array($name, [
                'id',
                'action',
                'quote',
                'debug',
            ])) {
            $medoo  = self::makeMedoo(1);
            return call_user_func_array([$medoo, $name], $arguments);
            
        } else if (in_array($name, [
                'error',
                'log',
                'last',
                'info',
            ])) {
            
            if (Ez::config('dbDistributede') === 0) {
                $medoo  = self::makeMedoo(1);
                return call_user_func_array([$medoo, $name], $arguments);
            } else {
                $medoo1 = self::makeMedoo(1);
                $medoo2 = self::makeMedoo(2);
                return [
                    0   => call_user_func_array([$medoo1, $name], $arguments),
                    1   => call_user_func_array([$medoo2, $name], $arguments),
                ];
            }
            
        } else if (in_array($name, [
                'delete',
                'insert',
                'replace',
                'update',
            ])) {
            $medoo  = self::makeMedoo(1);
            array_unshift($arguments, static::$tableName);
            return call_user_func_array([$medoo, $name], $arguments);
            
        } else if (in_array($name, [
                'get', 
                'max',
                'min',
                'avg',
                'has',
                'count',
                'select',
                'sum',
            ])) {
            if (Ez::config('dbDistributede') === 0) {
                $medoo = self::makeMedoo(1);
            } else {
                $medoo = self::makeMedoo(2);
            }
            array_unshift($arguments, static::$tableName);
            return call_user_func_array([$medoo, $name], $arguments);
            
        } else {
            throw new \Exception('Method not exists');
        }
    }
    
    /**
     * 执行一条原生sql
     * 
     * @param string $sql
     * @return boolean
     */
    public static function query($sql, $map = [])
    {
        $medoo  = self::makeMedoo(1);
        $medoo->query($sql, $map);
        if ($medoo->statement->errorCode() === '00000') {
            return $medoo->statement;
        } else {
            return FALSE;
        }
    }
    
    /**
     * 分页查找
     * 
     * @param int $page 每页展示条数
     * @param int $max 最多展示页数
     * @param mixed $columns 查询字段
     * @param array $where 查询条件
     * @param mixed $join 连表查询设置
     * @return array 数据结果  [ 'data'=>数据数组, 'pages'=>总页数, 'count'=>数据总条数, 'html'=>分页html代码 ]
     */
    public function findPage($page = 10, $max = 9, $columns = '*', $where = null, $join = null)
    {
        /* 总数，页数计算 */
        $p      = !empty($_GET['p']) ? intval(filter_input(INPUT_GET, 'p')) : 1;
        $cwhere = $where;
        unset($cwhere['ORDER']);
        unset($cwhere['GROUP']);
        if (empty($join)) {
            $count = $this->count($cwhere);
        } else {
            $count = $this->count($join, '*', $cwhere);
        }
        if ($count == 0) {
            return [
                'data'      => [],
                'pages'     => 1,
                'count'     => 0,
                'html'      => '',
            ];
        }
        $pages = ceil($count/$page);
        if ( $max > $pages ) {
            $max = $pages;
        }
        $p > $pages && $p = $pages;
        
        if ( empty($p) || $p < 0 ) {
            $p     = 1;
            $start = 0;
        } else if ( $p > $pages ) { 
            $start = ($pages-1) * $page;
        } else {
            $start = (intval($p) - 1) * $page;
        }
        is_array($where) ? $where = array_merge( $where, [ 'LIMIT' =>  [$start, $page] ] ) : $where = [ 'LIMIT' =>  [$start, $page] ];
        
        /* 数据 */
        if (empty($join)) {
            $data = $this->select($columns, $where);
        } else {
            $data = $this->select($join, $columns, $where);
        }
        if (!$data) {
            return FALSE;
        }
        
        /* get参数 */
        $parameter      = filter_input_array(INPUT_GET);
        
        /* 分页html生成 */
        if ($pages > 1) {
            $html  = '<span class="total">共'.$count.'条，'.$pages.'页</span>';
            if (empty($p) || $p == 1) {
                $html .= '<span class="disabled">上一页</span>';
            } else {
                $params = $parameter;
                $params['p'] = $p-1;
                $html .= '<a href="'.Route::createUrl(ACTION_NAME, $params).'">上一页</a>';
            }
            if ($p > ceil($max/2)) {
                $i = $p - floor($max/2);
            }
            if (isset($i)) {
                $showMax = $p + floor($max/2);
                $max % 2 == 0 && $showMax--;
            } else {
                $showMax = $max;
                $i       = 1;
            }
            if ($i > $pages-($max-1)) {
                $i = $pages-($max-1);
            }
            if ($showMax > $pages) {
                $showMax = $pages;
            }
            for (; $i<=$showMax; $i++) {
                if( $i != $p ) {
                    $params = $parameter;
                    $params['p'] = $i;
                    $html .= '<a href="'.Route::createUrl(ACTION_NAME, $params).'">'.$i.'</a>';
                } else {
                    $html .= '<span class="nowpage">'.$i.'</span>';
                }
            }
            if ($p == $pages) {
                $html .= '<span class="disabled">下一页</span>';
            } else {
                $params = $parameter;
                $params['p'] = $p+1;
                $html .= '<a href="'.Route::createUrl(ACTION_NAME, $params).'">下一页</a>';
            }
            $params = $parameter;
            unset($params['p']);
            $url = Route::createUrl(ACTION_NAME, $params);
            if (strpos($url, '?') === FALSE) {
                $url .= '?';
            } else {
                $url .= '&';
            }
            
            $html .= '<span class="turnto">转到</span>
<input id="jump_page" class="textInput" value="" style="width:30px;" maxlength="10" type="text">
<span class="turnto">页</span>
<a href="javascript:void(0)" onclick="jumppage()">GO</a>
<script>
    function jumppage() {
        var hrefPageNo = document.getElementById("jump_page");
        var hrefPageNoValue = hrefPageNo.value;
        var pattern = /^\d+$/;
        if(pattern.test(hrefPageNoValue) && hrefPageNoValue>0 && hrefPageNoValue<='.$pages.') {
            window.location.href="'.$url.'p="+hrefPageNoValue;
        } else {
            alert("页数输入不合法");
            hrefPageNo.focus();
        }
    }
</script>';
        }
        
        return [
            'data'      => $data,
            'pages'     => $pages,
            'count'     => $count,
            'html'      => $html,
        ];
    }
    
    /**
     * 创建数据
     * 
     * @param array $data 传入数据，只有数据库中有该字才会最终生成
     * @return mixed 生成数据
     * @access public
     */
    public function create($data = [])
    {
        if (empty($data)) {
            $data = filter_input_array(INPUT_POST);
        }
        
        $arr = [];
        
        $this->query("SHOW COLUMNS FROM `$this->trueTableName`");
        $columns = $this->statement->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($columns as $val) {
            $keys[] = $val['Field'];
        }
        foreach ($data as $key => $val) {
            if (in_array($key, $keys)) {
                $arr[$key] = htmlspecialchars(trim($val));      // 全局转换html元素
            }
        }
        
        $res = $this->checkColumns($arr);
        if ($res) {
            return $res;
        } else {
            return FALSE;
        }
    }
    
    /**
     * 字段验证
     * 
     * @param mixed $arr 待验证字段数组
     * @return boolen 验证成功返回true，否则返回false
     * @access public
     */
    public function checkColumns($arr) {
        if (empty($arr)) {
            $this->error = "数据为空";
            return FALSE;
        }
        
        if (empty($this->fieldCheckRule)) {
            return $arr;
        }
        
        if (!is_array($this->fieldCheckRule)) {
            $this->error = "Model::fieldCheckRule must be array";
            return FALSE;
        }
        
        /**
         * $this->fieldCheckRule对应字段的验证规则
         *      type        => 三种验证类型：function（函数返回等价于true通过验证）、pattern（正则匹配）、handle（操作改变数据值）
         *      method      => type in [function, handle]时填写
         *      pattern     => type in [pattern]时填写
         *      match       => type in [pattern]时填写，匹配成功通过验证为true，(默认)匹配失败通过验证为false 
         *      must        => 等价于true时必须验证，否则在值不为空时才验证
         *      errorMsg    => 不通过验证的错误消息
         */
        foreach ($arr as $key => $val) {
            if (isset($this->fieldCheckRule[$key])) {
                switch ($this->fieldCheckRule[$key]['type']) {
                    case 'function':
                        if (!empty($val) || $this->fieldCheckRule[$key]['must']) {
                            if (empty($this->fieldCheckRule[$key]['method'])) {
                                if (empty($val)) {
                                    $this->error = $this->fieldCheckRule[$key]['errorMsg'];
                                    return FALSE;
                                }
                            } else {
                                if (!call_user_func($this->fieldCheckRule[$key]['method'], $val)) {
                                    $this->error = $this->fieldCheckRule[$key]['errorMsg'];
                                    return FALSE;
                                }
                            }
                        }
                        break;
                    case 'pattern':
                        if (!empty($val) || $this->fieldCheckRule[$key]['must']) {
                            if (empty($this->fieldCheckRule[$key]['match'])) {
                                if (preg_match($this->fieldCheckRule[$key]['pattern'], $val)) {
                                    $this->error = $this->fieldCheckRule[$key]['errorMsg'];
                                    return FALSE;
                                }
                            } else {
                                if (!preg_match($this->fieldCheckRule[$key]['pattern'], $val)) {
                                    $this->error = $this->fieldCheckRule[$key]['errorMsg'];
                                    return FALSE;
                                }
                            }
                        }
                        break;
                    case 'handle':
                        if (!empty($val) || $this->fieldCheckRule[$key]['must']) {
                            $val = call_user_func($this->fieldCheckRule[$key]['method'], $val);
                            if (!$val) {
                                $this->error = $this->fieldCheckRule[$key]['errorMsg'];
                                return FALSE;
                            }
                            $arr[$key] = $val;
                        }
                        break;
                }
            }
        }
        
        return $arr;
    }
    
}
