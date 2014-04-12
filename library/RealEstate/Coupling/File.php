<?php

class RealEstate_Coupling_File implements RealEstate_Coupling_Interface {
    protected $_targetFile;
    protected $_parsingStrategy = null;

    public function __construct($targetFile = null, RealEstate_Coupling_File_Strategy_Interface $strategy = null) {
        if($targetFile !== null) {
            $this->setTargetFile($targetFile);
        }
        if($strategy !== null) {
            $this->setParsingStrategy($strategy);
        }
    }

    public function setTargetFile($targetFile) {
        if(!file_exists($targetFile)) {
            throw new RealEstate_Coupling_File_Exception('File for parsing ' . $targetFile . ' does not exists');
        } else if(!is_readable($targetFile)){
            throw new RealEstate_Coupling_File_Exception('File for parsing ' .$targetFile . 'is not readable');
        }
        $this->_targetFile = $targetFile;
        return $this;
    }

    public function getTargetFile() {
        return $this->_targetFile;
    }

    public function setParsingStrategy(RealEstate_Coupling_File_Strategy_Interface $strategy){
        $this->_parsingStrategy = $strategy;
        return $this;
    }

    public function execute(){
        if($this->_targetFile === null) {
            throw new RealEstate_Coupling_File_Exception('Set target file for executing');
        } elseif($this->_parsingStrategy === null) {
            throw new RealEstate_Coupling_File_Exception('Set file parsing strategy for executing');
        }
        return $this->_parsingStrategy->execute($this);
    }
}