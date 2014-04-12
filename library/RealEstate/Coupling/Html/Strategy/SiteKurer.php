<?php

class RealEstate_Coupling_Html_Strategy_SiteKurer implements RealEstate_Coupling_Html_Strategy_Interface {

    private static $_searchPhrases = array(
        'пятикомнат',
        'однокомнат', 'двухкомнат', 'трехкомнат', 'четырехкомнат',
        'квартир', 'малосемей', 'комнат', 'дом', 'дач', 'участ',
        'гараж'
    );
    protected $_usingSearchPhrases;
    protected $_lastSuccessPhrase;

    public function execute(RealEstate_Coupling_Html $htmlParser) {
        try {
            $response = $htmlParser->loadHtml();
            $htmlContent = $response->getBody();
            $encoding = RealEstate_Coupling_Html::detectEncoding($htmlContent);
            if(!empty($encoding)) {
                $htmlContent = iconv($encoding, 'UTF-8//TRANSLIT', $htmlContent);
            }
            $htmlContent = RealEstate_Coupling_Html::prepareHtmlContent($htmlContent);
            $parsedData = array();
            $matches = array();
            preg_match_all("/<td>(?:\t*|.*|\s*)*<\/td>/i", $htmlContent, $matches);
            unset($response, $htmlContent);
            $patterns = array("/<[^>]+>/", "/[\r\n]/");
            $matches = preg_replace($patterns, "", $matches[0]);
            $matches = array_map(function($value){return trim($value);}, $matches);
            $annoucements = new RealEstate_Coupling_Collection();
            foreach($matches as $messageText) {
                $searhed = $this->_parseMessage($messageText);
                if ($searhed !== false) {
                    $annoucement = new RealEstate_Coupling_Annoucement($messageText);
                    $phones = $this->_extractPhoneNumbers($messageText);
                    foreach ($phones as $phone) {
                        $annoucement->addPhoneNumber($phone);
                    }
                    $annoucements->addAnnoucement($annoucement);
                }
            }
            return $annoucements;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    protected function _parseMessage($message) {
        $searched = false;
        $this->_usingSearchPhrases = self::$_searchPhrases;
        for ($i = 0, $j = count($this->_usingSearchPhrases); $i < $j; $i++) {
            if (mb_strpos($message, $this->_usingSearchPhrases[$i]) !== false) {
                $searched = $this->_usingSearchPhrases[$i];
                if ($searched !== $this->_lastSuccessPhrase) {
                    $this->_modifySearchArray($i, $this->_lastSuccessPhrase);
                    $this->_lastSuccessPhrase = $searched;
                }
                break;
            }
        }
        return $searched;
    }

    protected function _modifySearchArray($index, $lastSuccessPhrase) {
        if ($index !== 0) {
            $phCount = count($this->_usingSearchPhrases);
            if ($lastSuccessPhrase !== null) {
                $lPhIndex = array_keys($this->_usingSearchPhrases, $lastSuccessPhrase);
                $lPhIndex = $lPhIndex[0];
            }

            $firstVal = $this->_usingSearchPhrases[0];
            $this->_usingSearchPhrases[0] = $this->_usingSearchPhrases[$index];
            $this->_usingSearchPhrases[$index] = $firstVal;
            for ($i = $index, $j = $phCount - 1; $i < $j; $i++) {
                $key = $this->_usingSearchPhrases[$i];
                $this->_usingSearchPhrases[$i] = $this->_usingSearchPhrases[$i + 1];
                $this->_usingSearchPhrases[$i + 1] = $key;
            }
        }
    }

    protected function _extractPhoneNumbers($message) {
        $placeHolders = array("Тел.", "Tел.", "Teл.");
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