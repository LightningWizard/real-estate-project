<?php

class Lis_Query2XML_ReportBuilder {
    private $_connection;
    private $_query2XML;

    public function __construct($dbConnection = null) {
        if($dbConnection !== null) {
            $this->setDbConnection($dbConnection);
        }
    }

    public function setDbConnection($dbConnection) {
        if($this->_connection === null) {
            $this->_connection = $dbConnection;
            $this->_buildQuery2XML();
            return $this;
        }
        if ($this->_connection === $dbConnection) {
            return $this;
        }
        if ($this->_connection && $this->_connection !== $dbConnection){
            unset($this->_connection);
            $this->_connection = $dbConnection;
            $this->_buildQuery2XML();
            return $this;
        }

    }

    protected function _buildQuery2XML() {
        if($this->_query2XML !== null) {
            unset($this->_query2XML);
        }
        if($this->_connection === null) {
            throw new Lis_Query2XML_ReportBuilder_Exception('Invalid programm logic. At first set connection to database');
        }
        $this->_query2XML = XML_Query2XML::factory($this->_connection);
    }

    public function generateReport($mainQuery, $options = null) {
        if($this->_connection === null) {
            throw new Lis_Query2XML_ReportBuilder_Exception('Invalid programm logic. At first set connection to database');
        }
        if($options === null) {
            return $this->_query2XML->getFlatXML($mainQuery);
        }
        return $this->_query2XML->getXML($mainQuery, $options);
    }
}
