<?php

class Lis_Grid_Abstract {
    protected $_view = null;
    protected $_viewHelper;
    
    public function setView(Zend_View_Interface $view = null) {
        $this->_view = $view;
        return $this;
    }

    public function getView() {
        if (null === $this->_view) {
            require_once 'Zend/Controller/Action/HelperBroker.php';
            $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
            $this->setView($viewRenderer->view);
        }

        return $this->_view;
    }

    public function render() {
        return $this->getView()->{$this->_viewHelper}($this);
    }
    
    public function __toString() {
        try {
            $content = $this->render();
            return $content;
        } catch (Exception $e) {
            $message = "Exception caught by form: " . $e->getMessage()
                    . "\nStack Trace:\n" . $e->getTraceAsString();
            trigger_error($message, E_USER_WARNING);
            return '';
        }
    }
}