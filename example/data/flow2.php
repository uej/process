<?php
return [
    'Name'      => '合同审批',
    'Introduce' => '合同审批',
    'FlowNodes' => [
        [
            'type'          => 1,
            'role'          => '2,5',
            'self'          => 0,
            'need'          => 1,
        ],
        [
            'type'          => 2,
            'role'          => 4,
            'copy'          => 4,
            'need'          => [
                [
                    'field' => 'Decimal1',
                    'type'  => 1,
                    'value' => 100000,
                ],
                [
                    'field' => 'Title2',
                    'type'  => 2,
                    'value' => 'XXXX有限公司',
                ]
            ],
            'needtype'      => 2,
        ]
    ],
    'DepartmentID'  => NULL,
    'RoleID'        => 1,
    'TypeID'        => 4,
    'UserID'        => NULL,
    'OrderRule'     => [
        [
            'type'  => 1,
            'value' => 'HT'
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
            'fieldtypeid'   => 0,
            'FieldName'     => 'Title1',
            'FieldTitle'    => '合同标题',
            'Placeholder'   => '请输入合同标题',
            'Must'          => 1,
        ],
        [
            'fieldtypeid'   => 10,
            'FieldName'     => 'Decimal1',
            'FieldTitle'    => '合同金额',
            'Placeholder'   => '请输入合同金额',
            'Must'          => 1,
        ],
        [
            'fieldtypeid'   => 7,
            'FieldName'     => 'Time1',
            'FieldTitle'    => '生效时间',
            'Placeholder'   => '请输入合同生效时间',
            'Timetype'      => 3,                   // 1：年月 2：年月日 3：年月日时 4：年与日时分
            'Must'          => 1,
        ],
        [
            'fieldtypeid'   => 7,
            'FieldName'     => 'Time2',
            'FieldTitle'    => '失效时间',
            'Placeholder'   => '请输入合同失效时间',
            'Timetype'      => 3,                   // 1：年月 2：年月日 3：年月日时 4：年与日时分
            'Must'          => 1,
        ],
        [
            'fieldtypeid'   => 3,
            'FieldName'     => 'Num1',
            'FieldTitle'    => '持续月数',
            'Placeholder'   => '请输入合同持续月数',
            'Timetype'      => 3,                   // 1：年月 2：年月日 3：年月日时 4：年与日时分
            'Must'          => 1,
        ],
        [
            'fieldtypeid'   => 0,
            'FieldName'     => 'Title2',
            'FieldTitle'    => '单位名称',
            'Placeholder'   => '请输入与其签订合同的单位名称',
            'Must'          => 1,
        ],
        [
            'fieldtypeid'   => 1,
            'FieldName'     => 'Content1',
            'FieldTitle'    => '合同简介',
            'Placeholder'   => '请输入合同简介',
            'Must'          => 1,
        ],
        [
            'fieldtypeid'   => 9,
            'FieldName'     => 'FileName1',
            'FieldTitle'    => '合同',
            'Placeholder'   => '请上传合同',
            'Must'          => 1,
        ]
    ]
];

