<?php
namespace Sf\Web;

use Sf\Base\Error;
use Sf\View\Smarty;

/**
 * 包含控制器逻辑的基类
 * 不需要每写一个要去渲染页面的action，都要去找相应路径的view，然后把它require进来。
 * 所以抽象出一个Controller的基类，实现一个渲染页面的方法，让其他的controller继承，就可以使用相应的方法。
 */
class Controller extends \Sf\Base\Controller
{
    /**
     * 渲染视图
     * @param string $view 视图名称
     * @param array $params 应在视图中提供的参数（名称-值对）。
     */
    public function display($view, $params = [])
    {
        extract($params);
        list($viewDirName,$viewName) = explode('/',$view);
        $path = ROOT_PATH.'/application/'.$GLOBALS['route']['module'].'/Views/'.ucfirst($viewDirName).'/'.ucfirst($viewName).'.php';
        if(!file_exists($path)){
            Error::error($view.'文件不存在');
        }
        return require $path;
    }

    public function render($view, $params = [])
    {
        list($viewDirName,$viewName) = explode('/',$view);
        $file = ROOT_PATH.'/application/'.$GLOBALS['route']['module'].'/Views/'.ucfirst($viewDirName).'/'.ucfirst($viewName).'.html';
        $fileContent = file_get_contents($file);
        $result = '';
        foreach (token_get_all($fileContent) as $token) {
            if (is_array($token)) {
                list($id, $content) = $token;
                if ($id == T_INLINE_HTML) {
                    $content = preg_replace('/{{(.*)}}/', '<?php echo $1 ?>', $content);
                }
                $result .= $content;
            } else {
                $result .= $token;
            }
        }
        $generatedFile = '../runtime/cache/' . md5($file);
        file_put_contents($generatedFile, $result);
        extract($params);
        require_once $generatedFile;
    }

    /**
     * 将数组转换为JSON字符串
     * @param string $data
     */
    public function toJson($data)
    {
        if (is_string($data)) {
            return $data;
        }
        return json_encode($data);
    }

    /**
     * smarty渲染视图
     * @param $path
     * author Fox
     */
    public function smartyDisplay($path){
        if(empty($this->smarty)){
            $this->smarty = new Smarty;
        }
        $this->smarty->display($path);
    }

    /**
     * smarty渲染视图
     * @param $path
     * author Fox
     */
    public function smartyAssign($key,$value){
        if(empty($this->smarty)){
            $this->smarty = new Smarty;
        }
        $this->smarty->assign($key, $value);
    }

}