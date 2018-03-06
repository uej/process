<?php
/**
 * 应用配置
 * 
 * @author lxj
 */
return [
    /* 是否开启调试 */
    'debug'             => 1,
    
    /* 数据库配置 */
    'dbType'            => 'mysql',
    'dbDistributede'    => 0,                           // 0：单数据库、互为主从  1：主从读写分离
    'dbPrefix'          => 'cmf_',                      // 数据库表前缀
    'dbCharset'         => 'utf8',                      // 数据库链接字符集
    
    /* 主数据库 */
    'dbMaster'          => [
        [
            'dbHost'            => '127.0.0.1',
            'dbName'            => 'process',
            'dbUser'            => 'root',
            'dbPassword'        => 'root',
            'dbPort'            => 3306,
        ],
    ],
    
    /* 从数据库 */
    'dbSlave'           => [
        [
            'dbHost'        => '',
            'dbName'        => '',
            'dbUser'        => '',
            'dbPassword'    => '',
            'dbPort'        => 3306,
        ],
        [
            'dbHost'        => '',
            'dbName'        => '',
            'dbUser'        => '',
            'dbPassword'    => '',
            'dbPort'        => 3306,
        ],
    ],
    
    /* 应用配置 */
    'defaultController' => 'index',
    'defaultAction'     => 'index',
    
    'urlRewrite'        => TRUE,
    'urlSuffix'         => '.html',
    
    'sessionAutoStart'  => TRUE,
    'sessionSavePath'   => '',
    'sessionDriver'     => '',
    'sessionExpire'     => 3600,
    
    'redisHost'         => '127.0.0.1',
    'redisPort'         => 6379,
    'redisSessiondb'    => 2,
    'redisSessionPrefix'=> 'example',
    'redisPassword'     => '',
    
    'timeZone'          => 'PRC',                       // 时区
    
    'openGzip'          => FALSE,                       // 是否开启gzip压缩
    
    'templateSuffix'    => '.php',                      // 模板后缀
    
    'errorPage'         => null,
    
    /* 上传配置 */
    'uploadExts'        => "jpg,jpeg,gif,png,bmp,swf,txt,xls,xlsx,csv,tmp,pdf,doc,docx,mp3,wma,wav,mpeg,mpg,rmvb,mov,mp4,amr,rar,zip,7z,ppt,pptx",
    'uploadTypes'       => 'application/vnd.openxmlformats-officedocument.presentationml.presentation,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/kswps,application/pdf,application/msword,image/pjpeg,image/jpeg,image/png,image/x-png,image/gif,application/x-shockwave-flash,text/plain,application/octet-stream,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/octet-stream,application/octet-stream,audio/mpeg,audio/mp3,video/mpeg,video/vnd.rn-realvideo,video/quicktime,video/vnd.vivo,video/mp4,application/octet-stream',
    'uploadSize'        => 104857600,
    'uploadPath'        => __DIR__ . '/data/upload/',
    
    /* 表单签名密匙 */
    'inputSign'         => '0123456',
    
    'route'             => '',                          // 路由配置，可开启自定义路由，例如：\example\core\Route
];


