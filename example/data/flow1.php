<?php
return [
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
            'type'          => 2,
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

