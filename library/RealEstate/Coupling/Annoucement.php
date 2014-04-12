<?php

class RealEstate_Coupling_Annoucement {

    const FROM_AGENCY = 1;
    const PHONE_EXIST_CT = 2;
    const PHONE_EXIST_MT = 3;
    const PHONE_NOT_EXIST = 4;

    public static function getAvailableStatuses(){
        return array(
            self::FROM_AGENCY, self::PHONE_EXIST_CT,
            self::PHONE_EXIST_MT, self::PHONE_NOT_EXIST
        );
    }

    private $_message;
    private $_phonesNumbers = array();
    private $_description;
    private $_status = 4;

    public function __construct($message = null) {
        if($message !== null) {
            $this->setMessage($message);
        }
    }

    public function setMessage($message) {
        $this->_message = (string) $message;
        return $this;
    }

    public function getMessage() {
        return $this->_message;
    }

    public function setPhonesNumbers(array $phonesNumbers) {
        foreach ($phonesNumbers as $phoneNumber) {
            $phoneNumber = (string) $phoneNumber;
            $this->addPhoneNumber($phoneNumber);
        }
        return $this;
    }

    public function addPhoneNumber($phoneNumber){
        if(array_key_exists($phoneNumber, $this->_phonesNumbers)) {
            unset($this->_phonesNumbers[$phoneNumber]);
        }
        $this->_phonesNumbers[$phoneNumber] = $phoneNumber;
        return $this;
    }

    public function getPhonesNumbers() {
        return $this->_phonesNumbers;
    }

    public function hasPhonesNumbers(){
        return (bool) count($this->_phonesNumbers);
    }

    public function setDescription($description){
        $this->_description = (string) $description;
        return $this;
    }

    public function getDescription() {
        return $this->_description;
    }


    public function setStatus($status) {
        $status = (int) $status;
        $availableStatuses = self::getAvailableStatuses();
        if(!in_array($status, $availableStatuses)){
            throw new Exception('Try to set undefined status for annoucement');
        }
        $this->_status = $status;
        return $this;
    }

    public function getStatus(){
        return $this->_status;
    }
}
