<?php

class Lis_View_Helper_GridMeta extends Zend_View_Helper_Abstract{
    
    public function gridMeta(Lis_Grid_Meta $list){
       $content = "<table id='" . $list->getGridId() . "'></table>"
                  . "<div id='" . $list->getPagerId() . "'></div>";
       $jqHandler = ZendX_JQuery_View_Helper_JQuery::getJQueryHandler(); 
       $function = "('#" . $list->getGridId() . "').jqGrid(" . $jqHandler . ".extend({}, " . Zend_Json::encode($list->getGridParams(), false, array('enableJsonExprFinder' => true)) . "))";
       $this->view->jQuery()->addOnload($jqHandler . $function);
       return  $content;
    }
}
