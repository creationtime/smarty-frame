<?php
namespace Sf\Db;

/**
 * Interface ModelInterface
 * @package Sf\Db
 */
interface ModelInterface
{
    public static function tableName();

    public static function primaryKey();

    public static function findOne($condition);

    public static function findAll($condition);

    public static function updateAll($condition, $attributes);

    public static function deleteAll($condition);

    public static function updateSql($condition);

    public static function insertSql($condition);

    public static function selectSql($condition);

    public static function selectRowSql($condition);

    public static function startTransaction();

    public static function rollBack();

    public static function commit();

    public function insert();

    public function update();

    public function delete();

    public static function getError();

    public static function getSql();

}