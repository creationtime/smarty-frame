<?php
namespace App\Web\Models;

use Sf\Cache\RedisCache;
use Sf\Db\DbClient;

class WelcomeModel{
    public static function tableName()
    {
        return 'user';
    }

    public static function select(){
        dd(DbClient::selectSql('select * from user'));
    }

    public static function insertSql(){
        dd(DbClient::insertSql("insert into user  (name,age) VALUES ('789<script>alert(123)</script>','12');"));
    }

    public static function updateSql(){
        dd(DbClient::updateSql("update user set age = '20' where name = 'brian';"));
    }

    public static function deleteSql(){
        dd(DbClient::deleteSql("delete from user where name = 'brian';"));
    }

    public static function transaction(){

        DbClient::startTransaction();
        $insert = DbClient::insertSql("insert into user  (name,age) VALUES ('brian','12');");
        var_dump($insert);
        echo 'insert<br>';
        if($insert === false){
            DbClient::rollBack();
            return false;
        }

        $update = DbClient::updateSql("update user set age = '20' where name = 'brian';");
        var_dump($update);
        echo 'update<br>';
        if($update === false){
            DbClient::rollBack();
            return false;
        }

        DbClient::commit();
        return true;

    }

    public static function secKill($key){
        for($i = 0;$i<100;$i++){
            $uid = rand(100000,999999);
            //获取redis里面已有的数量
            $num = 10;
            //如果当天人数少于10，则加入这个队列
            if(RedisCache::lLen($key) < $num){
                RedisCache::rPush($key,$uid.'%'.microtime());
                echo $uid.'秒杀成功';
            }else{
                //反之，秒杀完成
                echo '秒杀已结束';
            }
        }
        RedisCache::close();
    }

    public static function secKill2($key){
        $num = RedisCache::lLen($key);
        for($i=0;$i<$num;$i++){
            //从队列最左侧取出一个值
            $user = RedisCache::lPop($key);
            //然后判断这个值是否存在
            if(!$user || empty($user)){
                sleep(2);
                continue;
            }
            //切割出时间，uid
            $user_arr = explode('%',$user);
            $insert_data = [
                'uid' => $user_arr[0],
                'time_stamp' => $user_arr[1]
            ];
            //保存到数据库中
            $res = DbClient::insertSql_arr('redis_queue',$insert_data);
            //插入失败，回滚
            if($res == false){
                RedisCache::rPush($key,$user);
            }
        }
        //死循环，可在脚本中调试
//        while(1){
        //从队列最左侧取出一个值
//            $user = RedisCache::lPop($key);
//        //然后判断这个值是否存在
//            if(!$user || empty($user)){
//                sleep(2);
////                continue;
//            }
//        //切割出时间，uid
//        $user_arr = explode('%',$user);
//            $insert_data = [
//                'uid' => $user_arr[0],
//                'time_stamp' => $user_arr[1]
//            ];
//        //保存到数据库中
//            $res = DbClient::insertSql_arr('redis_queue',$insert_data);
//        //插入失败，回滚
//            if($res == false){
//                RedisCache::rPush($key,$user);
//            }
//            sleep(2);
//        }
        //释放redis
        RedisCache::close();
    }
}
?>
