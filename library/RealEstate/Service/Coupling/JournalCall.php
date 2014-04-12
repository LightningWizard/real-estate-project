<?php

class RealEstate_Service_Coupling_JournalCall {
    public function execute($targetFile){
        $strategy = new RealEstate_Coupling_File_Strategy_CallJournal();
        $parser = new RealEstate_Coupling_File($targetFile, $strategy);
        $parsedData = array();
        $parsedData =  $parser->execute();
        $dataManager = RealEstate_Service_Coupling_DataManager::getInstance();
        return $dataManager->save($parsedData, RealEstate_Document_CouplingUnit::TEXT_FILE);
    }
}

