<?php
class RealEstate_Document_AdministrativeDistrict extends Lis_Document_Abstract {
    static protected $_districtType = 2;
    
    public static function getDistrictType(){
        return self::$_districtType;
    }
    
    public function __construct($config = null, $primary = null) {
        if (is_array($config)) {
            $config['data']['DISTRICT_TYPE'] = self::getDistrictType();
        }
        parent::__construct($config, $primary);
    }
    public function instantiate() {
        parent::instantiate();
        $this->DISTRICT_TYPE = self::getDistrictType();
    }
}
