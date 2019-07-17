<?php
namespace App\Api\Models;

use Sf\Base\Error;
use Sf\Db\DbClient;
use Sf\Libs\PhpOffice\PhpExcel;
use Sf\Libs\Upload;
use Sf\Libs\PhpOffice\PhpSpreadsheet;
use Sf\Unit\Sms;

class WelcomeModel{

    /**
     * 导入excel
     * @param $name
     * @throws \Exception
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    public static function import($name,$configs){
//        $upload = new Upload();
//        $info = $upload->upload($name,$configs);
//        $type = $info['data']['type'];
//        if (empty($info))
//        {
//            Error::error($upload->getErrorMsg());
//        }else{
//            $file_name = $upload->rootPath.strtolower($info["data"]['newPath'].$info["data"]["newName"]);
//        }
//
//        $PHPExcel = new PhpExcel();
//        if ($type == 'xls') {
//            $objReader = \PHPExcel_IOFactory::createReader('Excel5');
//        } elseif ($type == 'xlsx') {
//            $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
//        }
//
//        $objPHPExcel = $objReader->load($file_name,$encode='utf-8');
//
//        $sheet = $objPHPExcel->getActiveSheet();
//        $highestRow = $sheet->getHighestRow(); // 取得总行数
//        $highestColumn = $sheet->getHighestColumn(); // 取得总列数
////        dd($highestRow);
////        dd($highestColumn);
//
//        $num = 0;
//        $data = [];
//        $data1 = [];
//        for($i=1;$i<=$highestRow;$i++)
//        {
////列数循环 , 列数是以A列开始
//
//            for ($column = 'A'; $column <= $highestColumn; $column++) {
//
//                $value = trim($objPHPExcel->getActiveSheet()->getCell($column.$i)->getCalculatedValue());
//                if(empty($value) || strstr($value,'=')){
//                    continue;
//                }
//                $data[] = $value;
//
//                if($value == '合计'){
//                    $num += 1;
//                }
//                if($num > 4 && ($num % 4) == 1 && $value == '合计'){
//                    end($data);
//                    $num == 5 ? $start = 0 : $start = key($data)-2;//获取当前位置
//                    $data1[$num][] = array_slice($data,$start,154);
//                    echo '<br>';
//                    var_dump('获取当前合计key位置start：'.$start.'<br>');
//                    var_dump('value：'.$value.'<br>');
//                    var_dump('num：'.$num.'<br>');
//                    var_dump('i：'.$i.'<br>');
//                    var_dump('column：'.$column.'<br>');
//                    var_dump('highestColumn：'.$highestColumn.'<br>');
//                    echo '<br>';
//                }
//
////                echo $column.$i.":".$objPHPExcel->getActiveSheet()->getCell($column.$i)->getValue()."<br />";
//
//            }
//        }
////        for($i=2;$i<=$highestRow;$i++)
////        {
////            $data[$i]['A'] = trim($objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue());
////            $data[$i]['B'] = trim($objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue());
////            $data[$i]['C'] = trim($objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue());
////            $data[$i]['D'] = trim($objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue());
////            $data[$i]['E'] = trim($objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue());
////            $data[$i]['F'] = trim($objPHPExcel->getActiveSheet()->getCell("F".$i)->getValue());
////            $data[$i]['G'] = trim($objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue());
////            $data[$i]['H'] = trim($objPHPExcel->getActiveSheet()->getCell("H".$i)->getValue());
////            $data[$i]['I'] = trim($objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue());
////            $data[$i]['J'] = trim($objPHPExcel->getActiveSheet()->getCell("J".$i)->getValue());
////            $data[$i]['K'] = trim($objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue());
////            $data[$i]['L'] = trim($objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue());
////            $data[$i]['M'] = trim($objPHPExcel->getActiveSheet()->getCell("M".$i)->getValue());
////            $data[$i]['N'] = trim($objPHPExcel->getActiveSheet()->getCell("N".$i)->getValue());
////        }
//
////        dd($data);
//        dd($data1);

        $upload = new Upload();
        $info = $upload->upload($name,$configs);
        $type = $info['data']['type'];
        if (empty($info))
        {
            Error::error($upload->getErrorMsg());
        }else{
            $file_name = $upload->rootPath.strtolower($info["data"]['newPath'].$info["data"]["newName"]);
        }
        $PhpExcel = new PhpExcel();
        $res = $PhpExcel->importExcel($file_name);
        dd($res);
    }

    /**
     * PhpSpreadsheet导入数据
     * @param $name
     * @param $configs
     * @throws \Exception
     */
    public static function import2($name,$configs){
        $upload = new Upload();
        $info = $upload->upload($name,$configs);
        $type = $info['data']['type'];
        if (empty($info))
        {
            Error::error($upload->getErrorMsg());
        }else{
            $file_name = $upload->rootPath.strtolower($info["data"]['newPath'].$info["data"]["newName"]);
        }

        $PhpSpreadsheet = new PhpSpreadsheet();
        $res = $PhpSpreadsheet->importExcel($file_name);
//        dd($res);
        self::settleData($res);
    }

    static function settleData($data){
        DbClient::startTransaction();
        foreach($data as $k=>$v){
            if($v['A'] == '合计' || $v['A'] == '项目'){
                continue;
            }
            $res = DbClient::insertSql("insert into xyf_fas (project_name,one,two,three,four,five,six,seven,eight,nine,ten,eleven,twelve,summ) VALUES ('$v[A]','$v[B]','$v[C]','$v[D]','$v[E]','$v[F]','$v[G]','$v[H]','$v[I]','$v[J]','$v[K]','$v[L]','$v[M]','$v[N]');");
            if(!$res){
                DbClient::rollBack();
                dd('添加失败');
            }
        }
        DbClient::commit();
        dd(true);
    }

    /**
     * 短信发送
     * @param $data
     * @return array
     */
    public function onSmsAction(){
        $data['phone'] = '18501796899';
        $data['hid'] = '88';
        $data['code'] = rand(1000,9999);

        $result = $this->sms($data);
        dd($result);
    }

    /**
     * 短信发送
     * @param $data
     * @return array
     */
    public function sms($data){

        $sms = Sms::getInstance($data['hid']);
        if($sms){
            $params = array();
            $params['mobile'] 		= $data['phone'];
            $params['verify_code']	= $data['code'];

            $ret = $sms->sendVerifyCode($params);
            if(!$ret){
                return $sms->get_error();
            }else{
                return $ret;
            }

        }
        else{
            return array(0=>"创建短信服务对象失败");
        }

    }
}
?>
