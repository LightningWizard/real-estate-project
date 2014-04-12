<?php
class Lis_Controller_Plugin_Notifire extends Zend_Controller_Plugin_Abstract
{
    private $_notifires = array();

    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        $notifires = $request->getParam('__notifires__', null);
        if (null !== $notifires) {
            $this->_notifires = explode('_', $notifires);
            $request->setParam('__notifires__', null);
        }
    }

    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        if (!$request->isDispatched()) {
            return;
        }

        $notifire = array_shift($this->_notifires);

        switch ($notifire) {
            case 'data-save-notifire':
                $request->setModuleName('system')
                        ->setControllerName('main')
                        ->setActionName('data-save-notifire')
                        ->setParams(array('dataIsSaved' => true))
                        ->setDispatched(false);
                break;
            default:
                return;
        }

    }
}