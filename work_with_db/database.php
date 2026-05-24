<?php

class Database
{
    protected static $pdo;

    public static function prepare($sqlQuery)
    {
        return static::getPdo()->prepare($sqlQuery);
    }

    public static function query($sqlQuery, array $params = [])
    {
        if(!empty($params))
            {
                $sql = static::prepare($sqlQuery);
                $sql->execute($params);
                return $sql;
            }
        return static::getPdo()->query($sqlQuery);
    }

    public static function exec($sqlQuery, array $params = [])
    {
        if(!empty($params))
            {
                $sql = static::prepare($sqlQuery);
                $sql->execute($params);
                return $sql->rowCount();
            }
        return static::getPdo()->exec($sqlQuery);
    }

    public static function getPdo()
    {
        if(empty(static::$pdo))
            {
                static::$pdo = new PDO(''); # my data here
            }
        return static::$pdo;
    }
}

?>