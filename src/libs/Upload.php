<?php
namespace Sf\Libs;

use PHPMailer\PHPMailer\Exception;
use Sf\Base\Error;
use Sf\Common\Env;
use Sf\Logs\Log;

/**
 * 上传类
 * 1.upload：上传文件
 * 2.is_uploaded_file是否有文件上传
 * 3.move_uploaded_file上传是否成功
 * 4.setNewName:设置上传后新的文件名;
 * 5.检测目录
 * 6.检测类型
 * 7.检测大
 *  */
class Upload
{
    private $errorMsg;
    private $orginalName;
    private $tmpName;
    private $newName;
    private $size;
    private $type;
    private $isRandom = true;
    private $path;
    private $allowType;
    private $maxSize;
    private $errorNum;
    private $fieldName;

    /**
     * @param boolean $isRandom :随机文件名
     * @param int $maxSize :允许的最大值
     * @param string $fieldName :input的name
     * @param array $allowType :允许上传的文件类型;
     * @param array $_parameter :上传类构造方法的参数;
     */
    public function __construct($_parameter = array())
    {
        //数组类型，包含类的所有属性
        //var_dump(get_class_vars(get_class($this)));
        foreach ($_parameter as $key => $value) {
            //echo $key."<br>";
            //var_dump(get_class_vars(get_class($this)));
            if (array_key_exists($key, get_class_vars(get_class($this)))) {
                //echo "ok";
                $this->$key = $value;
            } else {
                exit($this->throwError(12));
            }
        }
    }

    /**
     * 统一错误处理：内置错误和自定义错误结合
     *
     * @param int $_errorNum
     */
    private function throwError($_errorNum)
    {
        $str = null;
        switch ($_errorNum) {
            case 1:
                $str = "上传文件超过php.ini的最大值";
                break;
            case 2:
                $str = "上传文件超过了表单允许的最大值";
                break;
            case 3:
                $str = "上传文件不完整";
                break;
            case 4:
                $str = "没有文件上传";
                break;
            case 5:
                $str = "上传文件的大小为0字节";
                break;
            case 6:
                $str = "临时文件没有生成";
                break;
            case 7:
                $str = "文件写入失败";
                break;
            case 8:
                $str = "文件类型错误";
                break;
            case 9:
                $str = "文件超过了允许的最大值" . $this->maxSize;
                break;
            case 10:
                $str = "上传文件目录错误";
                break;
            case 11:
                $str = "上传目录创建失败";
                break;
            case 12:
                $str = "类的属性名错误";
                break;
            default:
                $str = "未知错误";
                break;
        }
        return $str;
    }

    private function setNewName()
    {
        if ($this->isRandom) {
            $this->newName = date('YmdHis') . rand(100, 999) . "." . $this->type;
        } else {
            $this->newName = $this->orginalName;
        }
    }

    /**
     * 处理上传文件信息
     *   */
    private function uploadedFileInfo()
    {
        //处理中文文件名;
        //$this->orginalName=$_FILES[$_fieldName]['name'];
        //处理中文文件名;
        $this->orginalName = iconv("utf-8", "gb2312", $_FILES[$this->fieldName]['name']);
        $arr = explode(".", $this->orginalName);
        $this->type = $arr[count($arr) - 1];
        $this->tmpName = $_FILES[$this->fieldName]['tmp_name'];
        $this->size = $_FILES[$this->fieldName]['size'];
        //内置的错误号;0没有错误，1-7各种错误;
        $this->errorNum = $_FILES[$this->fieldName]['error'];
        //echo ($this->orginalName);
    }

    public function getError()
    {
        return $this->errorMsg;
    }

    /**检测文件类型;*/
    private function checkType()
    {
        //echo $this->type;
        if (!in_array(strtolower($this->type), $this->allowType)) {
            $this->errorMsg = $this->throwError(8);
            return false;
        }
        return true;
    }

    public function rootPath(){
        return ROOT_PATH;
    }

    /**
     * 获取上传成功的文件名;
     * @return string
     */
    public function getNewFile()
    {
        return $this->newName;
    }

    /**检测文件大小;*/
    private function checkSize()
    {
        if ($this->size > $this->maxSize) {
            $this->errorMsg = $this->throwError(9);
            return false;
        }
        return true;
    }

    private function checkPath()
    {
        if (empty($this->path)) {
            $this->errorMsg = $this->throwError(10);
            return false;
        }
        if (!file_exists($this->path)) {
            //mkdir($this->path,0777);
            if (!mkdir($this->path, 0777, true)) {
                //echo "failed";
                $this->errorMsg = $this->throwError(11);
            }
        }
        $this->path = rtrim($this->path, "/") . "/";
        return true;
    }

    private function checkExist(){
        if(empty($_FILES[$this->fieldName]['name'])){
            $this->errorMsg = $this->throwError(4);
            return false;
        }
        return true;
    }

    /**
     * 预检测
     * 1.上传目录检测
     * 2.检测类型
     *   */
    private function preCheck()
    {
        if ($this->errorNum) {
            $this->errorMsg = $this->throwError($this->errorNum);
            return false;
        }
        if (!$this->checkExist()) return false;
        if (!$this->checkType()) return false;
        if (!$this->checkSize()) return false;
        if (!$this->checkPath()) return false;
        return true;
    }

    /**
     * 上传方法
     * @method upload:上传
     * @param string $_fieldName :input的name值
     */
    public function upload($fileName,$configs)
    {
        $this->path = $configs['path'].'/'. date('Ymd',time());
        $this->allowType = $configs['allowType'];
        $this->maxSize = $configs['maxSize'];

        $this->fieldName = $fileName;
        if(is_array($_FILES[$fileName]['name'])){
            $num = count($_FILES[$fileName]['name']);
            /*检查*/
            for($i=0;$i<$num;$i++){
                //处理中文文件名;
                //$this->orginalName=$_FILES[$_fieldName]['name'];
                //处理中文文件名;
                $this->orginalName = iconv("utf-8", "gb2312", $_FILES[$this->fieldName]['name'][$i]);
                $arr = explode(".", $this->orginalName);
                $this->type = $arr[count($arr) - 1];
                $this->tmpName = $_FILES[$this->fieldName]['tmp_name'][$i];
                $this->size = $_FILES[$this->fieldName]['size'][$i];
                //内置的错误号;0没有错误，1-7各种错误;
                $this->errorNum = $_FILES[$this->fieldName]['error'][$i];
                if (!$this->preCheck()) return false;
            }

            /*统一开始上传*/
            for($i=0;$i<$num;$i++){
                $this->setNewName();

                if (is_uploaded_file($_FILES[$this->fieldName]['tmp_name'][$i])) {
                    if (move_uploaded_file($_FILES[$this->fieldName]['tmp_name'][$i], Env::getRootPath().'/public/'.$this->path .'/'. $this->newName)) {
                        $res[$i]['status'] = true;
                        $res[$i]['data'] = ['newPath' => $this->path, 'tmpName' => $this->tmpName, 'newName' => $this->newName, 'type' => $this->type];
                        $res[$i]['msg'] = 'success';
                    } else {
                        $this->errorMsg = $fileName."：临时文件移动失败";
                        Log::ERROR($this->getError(),$_FILES[$this->fieldName]);
                        return false;
                    }
                } else {
                    $this->errorMsg = $fileName."：没有文件上传";
                    Log::ERROR($this->getError(),$_FILES[$this->fieldName]);
                    return false;
                }
            }
            return $res;
        }else{
            /*单个图片上传，即html中的file的name没有[]的*/
            $this->uploadedFileInfo();
            if (!$this->preCheck()) {
                return false;
            }
            $this->setNewName();
            if (is_uploaded_file($_FILES[$this->fieldName]['tmp_name'])) {
                if (move_uploaded_file($_FILES[$this->fieldName]['tmp_name'], Env::getRootPath().'/public/'.$this->path .'/'. $this->newName)) {
                    $res[0]['status'] = true;
                    $res[0]['data'] = ['newPath' => $this->path, 'tmpName' => $this->tmpName, 'newName' => $this->newName, 'type' => $this->type];
                    $res[0]['msg'] = 'success';
                    return $res;
                } else {
                    $this->errorMsg = $fileName."：临时文件移动失败";
                    Log::ERROR($this->getError(),$_FILES[$this->fieldName]);
                    return false;
                }
            } else {
                $this->errorMsg = $fileName."：没有文件上传";
                Log::ERROR($this->getError(),$_FILES[$this->fieldName]);
                return false;
            }
        }

    }

    /**
     * 返回错误
     */
    public function sendError()
    {
        return Error::error($this->getError());
    }


    /**
     *
     * 制作缩略图
     * @param $src_path string 原图路径
     * @param $max_w int 画布的宽度
     * @param $max_h int 画布的高度
     * @param $flag bool 是否是等比缩略图  默认为false
     * @param $prefix string 缩略图的前缀  默认为'sm_'
     *
     */
    public function imageResize($src_path, $max_w, $max_h, $prefix = 'sm_', $flag = false)
    {
        try{
            //获取文件的后缀
            $ext = strtolower(strrchr($src_path, '.'));

            //判断文件格式
            switch ($ext) {
                case '.jpg':
                    $type = 'jpeg';
                    break;
                case '.gif':
                    $type = 'gif';
                    break;
                case '.png':
                    $type = 'png';
                    break;
                default:
                    $this->errorMsg = '文件格式不正确';
                    return false;
            }

            //拼接打开图片的函数
            $open_fn = 'imagecreatefrom' . $type;
            //打开源图
            $src = $open_fn($src_path);
            //创建目标图
            $dst = imagecreatetruecolor($max_w, $max_h);
            if($dst === false){
                $this->errorMsg = '创建目标图失败';
                return false;
            }
            //源图的宽
            $src_w = imagesx($src);
            //源图的高
            $src_h = imagesy($src);

            //是否等比缩放
            if ($flag) { //等比

                //求目标图片的宽高
                if ($max_w / $max_h < $src_w / $src_h) {

                    //横屏图片以宽为标准
                    $dst_w = $max_w;
                    $dst_h = $max_w * $src_h / $src_w;
                } else {

                    //竖屏图片以高为标准
                    $dst_h = $max_h;
                    $dst_w = $max_h * $src_w / $src_h;
                }
                //在目标图上显示的位置
                $dst_x = (int)(($max_w - $dst_w) / 2);
                $dst_y = (int)(($max_h - $dst_h) / 2);
            } else {    //不等比

                $dst_x = 0;
                $dst_y = 0;
                $dst_w = $max_w;
                $dst_h = $max_h;
            }

            //生成缩略图
            $imagecopyresampled = imagecopyresampled($dst, $src, $dst_x, $dst_y, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
            if($imagecopyresampled !== true){
                $this->errorMsg = '生成缩略图失败';
                return false;
            }
            //文件名
            $filename = basename($src_path);
            if($filename == ''){
                $this->errorMsg = '生成文件名失败';
                return false;
            }
            //文件夹名
            $foldername = substr(dirname($src_path), 0);
            if($foldername == ''){
                $this->errorMsg = '生成文件夹名失败';
                return false;
            }
            //缩略图存放路径
            $thumb_path = $foldername . '/' . $prefix . $filename;
            if($thumb_path == ''){
                $this->errorMsg = '生成缩略图存放路径名失败';
                return false;
            }

            //把缩略图上传到指定的文件夹
            $imagepng = imagepng($dst, $thumb_path);
            if($imagepng !== true){
                $this->errorMsg = '把缩略图上传到指定的文件夹失败';
                return false;
            }
            //销毁图片资源
            imagedestroy($dst);
            imagedestroy($src);

            //返回新的缩略图的文件名
            $res['status'] = true;
            $res['data'] = ['path' => $thumb_path, 'newName' => $prefix . $filename];
            $res['msg'] = 'success';
            return $res;
        }catch (Exception $e){
            $this->errorMsg = $e;
            Log::ERROR($e);
            return false;
        }
    }


}