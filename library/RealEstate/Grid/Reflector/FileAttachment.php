<?php
class RealEstate_Grid_Reflector_FileAttachment implements Lis_Grid_Reflector_Interface
{
    public function execute($value)
    {
        $src   = '/img/icons/16x16/';
        $class = 'bool-';
        if (null !== $value) {
            $src .= 'document-save.gif';
            $class .= 'true';
        } else {
            $src .= 'cross.gif';
            $class .= 'false';
        }
        $result = '<img src="' . $src . '" class="' . $class . '" />';
        if (null === $value) {
            return $result;
        } else {
            if (Zend_Registry::isRegistered('Zend_Translate')) {
                $title = Zend_Registry::get('Zend_Translate')->translate('DownloadCommand');
            } else {
                $title = 'download';
            }

            return '<div '
                 . 'class="tc-repository" '
                 . 'title="' . htmlspecialchars($title) . '" '
                 . 'onclick="document.location =\'/workspace/file-attachments/download-attachment/id/' . $value . '/key/' . md5('download' . $value . Zend_Session::getId()) . '\'; return false;">'
                 . $result
                 . '</div>';
        }
    }
}