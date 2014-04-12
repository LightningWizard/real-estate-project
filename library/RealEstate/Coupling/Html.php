<?php
class RealEstate_Coupling_Html implements RealEstate_Coupling_Interface {

    protected static $_defaultHttpClient = null;

    protected $_uri;
    protected $_httpClient;
    protected $_parsingStrategy = null;

    public static function detectEncoding($htmlContent) {
        $domQuery = new Zend_Dom_Query();
        $domQuery->setDocument($htmlContent);
        $metaTags = $domQuery->query('meta[http-equiv="Content-Type"]');
        $htmlCharset = '';
        if(count($metaTags)> 0) {
            $metaTags->rewind();
            $metaTag = $metaTags->current();
            $content = $metaTag->getAttribute('content');
            $chrPos = strpos($content, 'charset=');
            if($chrPos !== false) {
                $htmlCharset = substr($content, $chrPos);
                $htmlCharset = str_replace('charset=', '', $htmlCharset);
                $htmlCharset = str_replace(' ', '', $htmlCharset);
            }
        }
        return $htmlCharset;
    }

    public static function prepareHtmlContent($html, $tidyConfig = null){
        if($tidyConfig === null) {
            $tidyConfig = array(
                'drop-font-tags' => true,
                'drop-proprietary-attributes' => true,
                'hide-comments' => true,
                'indent' => true,
                'logical-emphasis' => true,
                'numeric-entities' => true,
                'output-xhtml' => true,
                'wrap' => 0
            );
        }
        $tidy = new tidy();
        $tidy->parseString($html, $tidyConfig, 'utf8');
        $tidy->cleanRepair();
        $html = $tidy->value;
        $html = preg_replace('#<meta[^>]+>#isu', '', $html);
        $html = preg_replace('#<head\b[^>]*>#isu', "<head>\r\n<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />", $html);
        return $html;
    }

    public static function getDefaultHttpClient(){
        if(self::$_defaultHttpClient === null) {
            $config = array(
                'maxredirects' => 0,
                'timeout'      => 60,
                'adapter' => 'Zend_Http_Client_Adapter_Curl',
            );
            self::$_defaultHttpClient = new Zend_Http_Client(null, $config);
        }
        return self::$_defaultHttpClient;
    }

    public function __construct($uri = null, $parsingStrategy = null, $httpClient = null) {
        if($uri !== null) {
            $this->setUri($uri);
        }
        if($parsingStrategy !== null) {
            $this->setParsingStrategy($parsingStrategy);
        }
        if($httpClient !== null) {
            $this->setHttpClient($httpClient);
        }
    }

    public function setUri($uri) {
        $urlValidator = new RealEstate_Validate_Uri();
        if(!$urlValidator->isValid($uri)) {
            throw new RealEstate_Coupling_Html_Exception('Invalid URI');
        }
        $this->_uri = $uri;
        return $this;
    }

    public function getUri() {
        return $this->_uri;
    }

    public function setHttpClient($httpClient) {
        if(is_array($httpClient)) {
            $this->_httpClient = new Zend_Http_Client(null, $httpClient);
        } else if ($httpClient instanceof Zend_Http_Client) {
            $this->_httpClient = $httpClient;
        } else {
            throw new RealEstate_Coupling_Html_Exception('Invalid http client');
        }
        return $this;
    }

    public function getHttpClient(){
        if($this->_httpClient === null) {
            return self::getDefaultHttpClient();
        }
        return $this->_httpClient;
    }

    public function setParsingStrategy(RealEstate_Coupling_Html_Strategy_Interface $strategy){
        $this->_parsingStrategy = $strategy;
        return $this;
    }

    public function execute() {
        return $this->_parsingStrategy->execute($this);
    }

    public function loadHtml() {
        $client = $this->getHttpClient();
        $client->setUri($this->getUri());
        $response = $client->request();
        return $response;
    }

}
