<?php
namespace example\controller;
use ez\core\Controller;
use ez\core\Route;

/**
 * 示例控制器
 * 
 * @author lxj
 */
class IndexController extends Controller {
    
    
    /**
     * 我的申请
     * 
     * @access public
     */
    public function index() {
        $this->display();
    }
    
    
    /**
     * 流程类型
     * 
     * @access public
     */
    public function type() {
        $workflowType   = new \example\model\Flowtype();
        $type   = $workflowType->findPage(15);
        $this->display($type);
    }
    
    
    /**
     * 审批流程列表
     * 
     * @access public
     */
    public function flowlist() {
        $workflow   = new \example\model\Workflow();
        $flowlist   = $workflow->findPage(15);
        $this->display($flowlist);
    }
    
    
    /**
     * 添加流程
     * 
     * @access public
     */
    public function addflow() {
        if (empty($_POST)) {
            $this->assign('flowtype', \example\model\Flowtype::select('*'));
            $this->assign('formlist', \process\flow\Form::$fields);
            $this->display();
        } else {
            
        }
    }
    
    
    /**
     * 执行添加流程
     * 
     * @access public
     */
    public function addflowgo() {
        $workflow   = new \process\Workflow();
        
        // 测试流程
//        $flow   = include(__DIR__ . '/../data/flow2.php');
        
        $res    = $workflow->createFlow($flow);
        
        dump($res);
    }
    
    
    /**
     * 发起申请页
     * 
     * @access public
     */
    public function apply() {
        
    }
    
    
    /**
     * 发起流程
     * 
     * @access public
     */
    public function doapply() {
        $workflow   = new \process\Workflow();
        $userID     = 2;
        $flowID     = 2;
        $data       = [
            'Title1'  => 'sadaasda阿斯达',
            'Decimal1' => '151151',
            'Time1'   => '2018-03-17 18:00',
            'Time2' => '2019-03-17 18:00',
            'Num1'  => 12,
            'Title2'    => '摩萨德公司',
            'Content1'  => '阿娇是宽大空间卡机双打卡机双打',
            'FileName1' => '/data/sdad.sdf',
        ];
        
        $res        = $workflow->startNew($flowID, $userID, $data);
        
        dump($res);
    }
    
    public function doflow() {
        $workflow   = new \process\Workflow();
        $programID  = 8;
        $userID     = 5;
        $data       = [
            'Content'   => '通过',
            'Pass'      => 1,
        ];
        
        $res        = $workflow->doFlow($programID, $userID, $data);
        
        dump($res);
    }
    
    public function editprogram() {
        $workflow   = new \process\Workflow();
        
        $programID  = 5;
        $userID     = 2;
        $data       = [
            'Content1'  => '阿斯顿加啊卡机双打刻录机打开asdada按时大大',
            'TimeBetween1Start' => '2018-03-14 09:00',
            'TimeBetween1End'   => '2018-03-17 17:00',
            'TimeBetween1Total' => 4,
        ];
        
        $res    = $workflow->editProgram($programID, $userID, $data);
        dump($res);
    }
    
    
    public function editflow() {
        $workflow   = new \process\Workflow();
        
        // 测试流程
        $flow   = [
            'Name'      => '事假',
            'Introduce' => '用户事假申请',
            'FlowNodes' => [
                [
                    'type'          => 2,
                    'role'          => 2,
                    'self'          => 1,
                    'need'          => 1,
                ],
                [
                    'type'          => 1,
                    'role'          => 4,
                    'copy'          => 4,
                    'need'          => [
                        [
                            'field' => 'TimeBetween1Total',
                            'type'  => 1,
                            'value' => 3,
                        ]
                    ],
                    'needtype'      => 1,
                ]
            ],
            'DepartmentID'  => NULL,
            'RoleID'        => 1,
            'TypeID'        => 1,
            'UserID'        => NULL,
            'OrderRule'     => [
                [
                    'type'  => 1,
                    'value' => 'SJ'
                ],
                [
                    'type'  => 2,
                    'datetype'  => 2
                ],
                [
                    'type'  => 3,
                    'length'=> 3,
                ]
            ],
            'Form'  => [
                'add'   => [
                    [
                        'fieldtypeid'   => 1,
                        'FieldName'     => 'Content2',
                        'FieldTitle'    => '原因',
                        'Placeholder'   => '请输入请假原因',
                        'Must'          => 1,
                    ],
                    [
                        'fieldtypeid'   => 0,
                        'FieldName'     => 'Title1',
                        'FieldTitle'    => '天数',
                        'Placeholder'   => '请输入请假天数',
                        'Must'          => 1,
                    ],
                    [
                        'fieldtypeid'   => 7,
                        'FieldName'     => 'Time1',
                        'FieldTitle'    => '开始时间',
                        'Placeholder'   => '请输入请假开始时间',
                        'Must'          => 1,
                    ],
                ],
                'delete'    => [
                    1,2,3,4
                ]
            ]
        ];
        
        $res    = $workflow->editFlow(1, $flow);
        
        dump($res);
    }
    
    
    
}
