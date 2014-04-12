<?php

class RealEstate_Coupling_File_Strategy_CallJournal implements RealEstate_Coupling_File_Strategy_Interface {

    public function execute(RealEstate_Coupling_File $fileParser) {
        $targetFile = $fileParser->getTargetFile();
        $fileContent = file_get_contents($targetFile);
        $fileContent = mb_convert_encoding($fileContent, "UTF-8", "windows-1251");
        $fileContentRows = explode("\r\n", $fileContent);
        $annoucements = new RealEstate_Coupling_Collection();
        foreach ($fileContentRows as $messageText) {
            if(!empty($messageText)) {
                $annoucement = new RealEstate_Coupling_Annoucement($messageText);
                $phones = $this->_extractPhoneNumbers($messageText);
                foreach ($phones as $phone) {
                    $annoucement->addPhoneNumber($phone);
                }
                $annoucements->addAnnoucement($annoucement);
            }
        }
        return $annoucements;
    }

    protected function _extractPhoneNumbers($message) {
        $placeHolders = array("Телефон");
        $searchPos = false;
        $i = 0;
        foreach ($placeHolders as $searchKey) {
            $searchPos = mb_strpos($message, $searchKey);
            if ($searchPos !== false) {
                break;
            }
            $i++;
        }
        if ($searchPos === false) {
            return array();
        }
        $reqSubstr = mb_substr($message, $searchPos);
        $reqSubstr = trim($reqSubstr);
        $reqSubstr = preg_replace("/\(|\)|-/", "", $reqSubstr);
        $phones = array();
        preg_match_all('/\d{5,}/', $reqSubstr, $phones);
        return $phones[0];
    }

}