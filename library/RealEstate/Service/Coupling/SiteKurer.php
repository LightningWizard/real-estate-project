<?php
class RealEstate_Service_Coupling_SiteKurer {

    private static $_couplingLinks = array(
        RealEstate_Document_CouplingUnit::KURER_BUY      => 'http://kurer.ua/pokupaut.html',
        RealEstate_Document_CouplingUnit::KURER_SELL     => 'http://kurer.ua/prodaut.html',
        RealEstate_Document_CouplingUnit::KURER_EXCHANGE => 'http://kurer.ua/obmen.html',
    );

    public static function getCouplingLink($linkCode) {
        if(array_key_exists($linkCode, self::$_couplingLinks)){
            return self::$_couplingLinks[$linkCode];
        }
        return null;
    }

    public function execute($uri, $htmlClient = null) {
        $strategy = new RealEstate_Coupling_Html_Strategy_SiteKurer();
        $parser = new RealEstate_Coupling_Html($uri, $strategy, $htmlClient);
        $parsedData = $parser->execute();
        $dataManager = RealEstate_Service_Coupling_DataManager::getInstance();
        $sourceCode = array_keys(self::$_couplingLinks, $uri);
        $sourceCode = $sourceCode[0];
        return $dataManager->save($parsedData, $sourceCode);
    }
}