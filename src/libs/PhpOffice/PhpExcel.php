<?php
namespace Sf\Libs\PhpOffice;

use PHPExcel_IOFactory;
use PHPExcel_Writer_Excel2007;
use PHPExcel_Writer_Excel5;

class PhpExcel{

    /**
     * @param $filename
     * @return array
     * @throws \PHPExcel_Exception
     */
    public function importExcel($filename){
        $objPHPExcelReader = PHPExcel_IOFactory::load($filename);

        $sheet = $objPHPExcelReader->getSheet(0);        // 读取第一个工作表(编号从 0 开始)
        $highestRow = $sheet->getHighestRow();           // 取得总行数
        $highestColumn = $sheet->getHighestColumn();     // 取得总列数

        $arr = array('A','B','C','D','E','F','G','H','I','J','K','L','M', 'N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        // 一次读取一列
        $res_arr = array();
        for ($row = 2; $row <= $highestRow; $row++) {
            $row_arr = array();
            for ($column = 0; $arr[$column] != 'O'; $column++) {
                $val = $sheet->getCellByColumnAndRow($column, $row)->getValue();
                $row_arr[] = $val;
            }

            $res_arr[] = $row_arr;
        }

        return $res_arr;
    }

    /**
     * @param $filename
     * @return array
     */
    function importExcel2($filename){
        $objPHPExcelReader = PHPExcel_IOFactory::load($filename);

        $reader = $objPHPExcelReader->getWorksheetIterator();
        //循环读取sheet
        foreach($reader as $sheet) {
            //读取表内容
            $content = $sheet->getRowIterator();
            //逐行处理
            $res_arr = array();
            foreach($content as $key => $items) {

                $rows = $items->getRowIndex();              //行
                $columns = $items->getCellIterator();       //列
                $row_arr = array();
                //确定从哪一行开始读取
                if($rows < 2){
                    continue;
                }
                //逐列读取
                foreach($columns as $head => $cell) {
                    //获取cell中数据
                    $data = $cell->getValue();
                    $row_arr[] = $data;
                }
                $res_arr[] = $row_arr;
            }

        }

        return $res_arr;
    }

    /**
     * 创建(导出)Excel数据表格
     * @param  array   $data        要导出的数组格式的数据
     * @param  string  $filename    导出的Excel表格数据表的文件名
     * @param  array   $indexKey    $list数组中与Excel表格表头$header中每个项目对应的字段的名字(key值)
     * @param  array   $startRow    第一条数据在Excel表格中起始行
     * @param  [bool]  $excel2007   是否生成Excel2007(.xlsx)以上兼容的数据表
     * 比如: $indexKey与$list数组对应关系如下:
     *     $indexKey = array('id','username','sex','age');
     *     $list = array(array('id'=>1,'username'=>'YQJ','sex'=>'男','age'=>24));
     */
    public function exportExcel($data,$filename,$indexKey,$startRow=1,$excel2007=false){
        if(empty($filename)) $filename = time();
        if( !is_array($indexKey)) return false;

        $header_arr = array('A','B','C','D','E','F','G','H','I','J','K','L','M', 'N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        //初始化PHPExcel()
        $objPHPExcel = new PHPExcel();

        //设置保存版本格式
        if($excel2007){
            $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
            $filename = $filename.'.xlsx';
        }else{
            $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
            $filename = $filename.'.xls';
        }

        //接下来就是写数据到表格里面去
        $objActSheet = $objPHPExcel->getActiveSheet();
        //$startRow = 1;
        foreach ($data as $row) {
            foreach ($indexKey as $key => $value){
                //这里是设置单元格的内容
                $objActSheet->setCellValue($header_arr[$key].$startRow,$row[$value]);
            }
            $startRow++;
        }

        // 下载这个表格，在浏览器输出
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");;
        header('Content-Disposition:attachment;filename='.$filename.'');
        header("Content-Transfer-Encoding:binary");
        $objWriter->save('php://output');
    }

    /**
     * 设置模板并导出
     * @param $list
     * @param $filename
     * @param array $indexKey
     */
    public function exportExcel2($list,$filename,$indexKey=array()){
        $header_arr = array('A','B','C','D','E','F','G','H','I','J','K','L','M', 'N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

        //$objPHPExcel = new PHPExcel();                        //初始化PHPExcel(),不使用模板
        $template = dirname(__FILE__).'/template.xls';          //使用模板
        $objPHPExcel = PHPExcel_IOFactory::load($template);     //加载excel文件,设置模板

        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);  //设置保存版本格式

        //接下来就是写数据到表格里面去
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->setCellValue('A2',  "活动名称：江南极客");
        $objActSheet->setCellValue('C2',  "导出时间：".date('Y-m-d H:i:s'));
        $i = 4;
        foreach ($list as $row) {
            foreach ($indexKey as $key => $value){
                //这里是设置单元格的内容
                $objActSheet->setCellValue($header_arr[$key].$i,$row[$value]);
            }
            $i++;
        }

        // 1.保存至本地Excel表格
        //$objWriter->save($filename.'.xls');

        // 2.接下来当然是下载这个表格了，在浏览器输出就好了
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");;
        header('Content-Disposition:attachment;filename="'.$filename.'.xls"');
        header("Content-Transfer-Encoding:binary");
        $objWriter->save('php://output');
    }
}