<?php

class Lis_Grid_Reflector_Translate implements Lis_Grid_Reflector_Interface {

    protected static $_translator;

    public static function getTranslator(){
        if(self::$_translator === null){
            if(Zend_Registry::isRegistered('Zend_Translate')){
                self::$_translator = Zend_Registry::get('Zend_Translate');
            }
        }
        return self::$_translator;
    }

    public function execute($value) {
        $translator = self::getTranslator();
        if($translator !== null) {
            return $translator->translate($value);
        }
        return $value;
    }
}