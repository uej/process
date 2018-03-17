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
    
    
    public function tx() {
        $this->display();
    }
    
    /**
     * 我的申请
     * 
     * @access public
     */
    public function index() {
        $a['撒打算'] = 'asda';
        var_dump($a);
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
     * 添加流程页
     * 
     * @access public
     */
    public function addflow() {
        $this->assign('flowtype', \example\model\Flowtype::select('*'));
        $this->display();
    }
    
    
    /**
     * 执行添加流程
     * 
     * @access public
     */
    public function addflowgo() {
        $workflow   = new \process\Workflow();
//        var_dump($_POST);die;
        
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
                [
                    'fieldtypeid'   => 1,
                    'FieldName'     => 'Content1',
                    'FieldTitle'    => '原因',
                    'Placeholder'   => '请输入请假原因',
                    'Must'          => 1,
                ],
                [
                    'fieldtypeid'   => 8,
                    'FieldName'     => 'TimeBetween1',
                    'FieldTitle'    => '起止时间',
                    'Timetype'      => 3,
                    'Must'          => 1,
                ]
            ]
        ];
        
        $res    = $workflow->createFlow($flow);
        
        var_dump($res);
        if ($res['code'] == 1) {
            
//            die(json_encode(['code' => 1, 'msg' => '添加流程成功']));
        } else {
//            die(json_encode($res));
        }
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
        $res        = $workflow->startNew(1, 2, [
//            'Content1'  => '阿斯顿加啊卡机双打刻录机打开asdada按时大大',
//            'TimeBetween1Start' => "2018-03-14 09:00",
//            'TimeBetween1End'   => '2018-03-15 18:00',
//            'TimeBetween1Total' => 2,
        ]);
        
        dump($res);
    }
    
    public function doflow() {
        $workflow   = new \process\Workflow();
        $res        = $workflow->doFlow(1, 5, [
            'Content'   => '通过',
            'Pass'      => 1,
        ]);
        
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
