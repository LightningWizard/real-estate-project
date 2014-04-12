<?php

class RealEstate_Query2XML_Factory {
    private static $_connection;

    public static function getDefaultConnection() {
         if(self::$_connection === null) {
            $dbConfig = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('SystemConfig')->database;
            $config->database->servername = $dbConfig->host;
            $config->database->dbname = $dbConfig->dbname;
            $config->database->user = $dbConfig->username;
            $config->database->password = $dbConfig->password;
            $dsn = 'ibase://' . $dbConfig->username . ':' . $dbConfig->password . '@' . $dbConfig->host . '/' . $dbConfig->dbname;
            self::$_connection = MDB2::factory($dsn);
            self::$_connection->setCharset('UTF-8');
         }
         return self::$_connection;
    }
    public static function factory($dbConnection = null){
        if($dbConnection === null){
            $dbConnection = self::getDefaultConnection();
        }
        $reportBuilder = new Lis_Query2XML_ReportBuilder();
        $reportBuilder->setDbConnection($dbConnection);
        return $reportBuilder;
    }
}