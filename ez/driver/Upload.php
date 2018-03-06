<?php
namespace ez\driver;

/**
 * 文件长传类，支持分段上传
 *
 * @author lxjwork
 */
class Upload {
    
    /**
     * @var string 文件后缀
     */
    public $ext;
    
    /**
     * @var string 允许文件后缀
     */
    public $exts;
    
    /**
     * @var string 允许文件类型
     */
    public $types;
    
    /**
     * @var integer 允许上传文件大小
     */
    public $size;
    
    /**
     * @var string 文件长传路径
     */
    public $path;
    
    /**
     * @var array 上传结果
     */
    public $result;
    
    
    /**
     * 构造函数
     * 
     * @param array $conf 上传配置数组
     * @access public
     */
    public function __construct($conf = [])
    {
        set_time_limit(0);
        
        // Make sure file is not cached (as it happens for example on iOS devices)
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        
        // finish preflight CORS requests here
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            exit;
        }
        
        $this->exts     = isset($conf['uploadExts']) ? $conf['uploadExts'] : \ez\core\Ez::config('uploadExts');
        $this->types    = isset($conf['uploadTypes']) ? $conf['uploadTypes'] : \ez\core\Ez::config('uploadTypes');
        $this->size     = isset($conf['uploadSize']) ? $conf['uploadSize'] : \ez\core\Ez::config('uploadSize');
        $this->path     = isset($conf['uploadPath']) ? $conf['uploadPath'] : \ez\core\Ez::config('uploadPath');
    }
    
    /**
     * 设置上传路径
     * 
     * @param string $path 保存路径
     * @access public
     */
    public function setPath($path) {
        if (!is_dir($path)) {
            @mkdir($path, 0777, TRUE);
        }
        
        $this->path = $path;
    }
    
    /**
     * 设置上传最大限制
     * 
     * @param int $size 大小限制
     * @access public
     */
    public function setSize($size) {
        $this->size = $size;
    }
    
    /**
     * 设置允许上传后缀名
     * 
     * @param string $exts 后缀名
     * @access public
     */
    public function setExts($exts) {
        $this->exts = $exts;
    }
    
    /**
     * 检查文件是否符合标准
     * 
     * @access public
     */
    public function check()
    {
        $name   = explode('.', $_FILES['file']['name']);
        $this->ext = $ext = strtolower($name[count($name) - 1]);
        $type   = $_FILES['file']['type'];
        $size   = $_FILES['file']['size'];
        
        /* 是否是允许的扩展名 */
        if (strpos($this->exts, $ext) === FALSE) {
            $this->result['code'] = -1;
            $this->result['msg']  = "不允许的文件扩展名";
            return $this->result;
        }

        /* 是否是允许的文件类型 */
        if (strpos($this->types, $type) === FALSE) {
            $this->result['code'] = -2;
            $this->result['msg']  = "不允许的文件类型";
            return $this->result;
        }

        /* 是否在允许的最大上传文件范围内 */
        if ($size > $this->size) {
            $this->result['code'] = -3;
            $this->result['msg']  = "文件过大，限制" . sprintf('%.2f', $this->size/1024/1024) . "MB";
            return $this->result;
        }

        /* 判断是否选择了上传文件 */
        if ($size == 0) {
            $this->result['code'] = -4;
            $this->result['msg']  = "文件大小为0";
            return $this->result;
        }
    }
    
    /**
     * 上传文件
     * 
     * @access public
     */
    public function doUpload()
    {
        $this->check();
        if ($this->result['code'] < 0) {
            return $this->result;
        }
        
        $targetDir = $this->path . 'upload_tmp/';
        $uploadDir = $this->path . date('Ymd') . '/';

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, TRUE);
        }
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, TRUE);
        }
        
        if (isset($_REQUEST["name"])) {
            $trueName = filter_var($_REQUEST["name"]);
            
            // 防止分片上传时文件片段名同名的混乱
            $fileName = $_REQUEST["name"];
            if (isset($_REQUEST["type"])) {
                $fileName .= $_REQUEST["type"];
            }
            if (isset($_REQUEST["lastModifiedDate"])) {
                $fileName .= $_REQUEST["lastModifiedDate"];
            }
            if (isset($_REQUEST["size"])) {
                $fileName .= $_REQUEST["size"];
            }
            $fileName = md5($fileName);
            
        } elseif (!empty($_FILES)) {
            $trueName = filter_var($_FILES["file"]["name"]);
            $fileName = $_FILES["file"]["name"];
        } else {
            $this->result['code'] = -5;
            $this->result['msg']  = "没有上传文件";
            return $this->result;
        }
        
        $fileName   = iconv('UTF-8', 'GBK', $fileName);
        $filePath   = $targetDir . $fileName;

        // Chunking might be enabled
        $chunk  = isset($_REQUEST["chunk"])  ? intval($_REQUEST["chunk"])  : 0;
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 1;

        // Open temp file
        if (!$out = fopen("{$filePath}_{$chunk}.parttmp", "wb")) {
            $this->result['code'] = -6;
            $this->result['msg']  = "服务器内部异常";
            return $this->result;
        }

        if (!empty($_FILES)) {
            if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
                $this->result['code'] = -1;
                $this->result['msg']  = "文件上传失败。";
                return $this->result;
            }

            // Read binary input stream and append it to temp file
            if (!$in = fopen($_FILES["file"]["tmp_name"], "rb")) {
                $this->result['code'] = -1;
                $this->result['msg']  = "文件上传失败！";
                return $this->result;
            }
        } else {
            $this->result['code'] = -1;
            $this->result['msg']  = "文件上传失败";
            return $this->result;
        }

        while ($buff = fread($in, 4096)) {
            fwrite($out, $buff);
        }

        fclose($out);
        fclose($in);

        rename("{$filePath}_{$chunk}.parttmp", "{$filePath}_{$chunk}.part");

        $index = 0;
        $done = true;
        for($index = 0; $index < $chunks; $index++) {
            if ( !is_file("{$filePath}_{$index}.part") ) {
                $done = false;
                break;
            }
        }
        
        if ($done) {
            $saveName   = md5($fileName . mt_rand(0, 99999) . microtime(TRUE)) . ".$this->ext";
            $uploadPath = $uploadDir . $saveName;
            
            if (!$out = fopen($uploadPath, "wb")) {
                $this->result['code'] = -6;
                $this->result['msg']  = "服务器内部异常";
                return $this->result;
            }

            if (flock($out, LOCK_EX)) {
                for($index = 0; $index < $chunks; $index++) {
                    if (!$in = fopen("{$filePath}_{$index}.part", "rb")) {
                        break;
                    }

                    while ($buff = fread($in, 4096)) {
                        fwrite($out, $buff);
                    }

                    fclose($in);
                    unlink("{$filePath}_{$index}.part");
                }

                flock($out, LOCK_UN);
            }
            fclose($out);
            
            $this->result['code']       = 0;
            $this->result['msg']        = "上传成功";
            $this->result['savePath']   = $uploadPath;
            $this->result['fileName']   = $trueName;
            return $this->result;
            
        } else {
            $this->result['code'] = 1;
            $this->result['msg']  = "文件继续上传中";
            return $this->result;
        }
    }
    
}
