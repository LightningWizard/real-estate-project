<?php 
class RealEstate_ProposalBlankManager {
    private static $_instance = null;
    
    protected $_blank;
    protected $_settingsGroup;
    
    protected $_blankFields = null;

    public static function getInstance(RealEstate_Blank_RealEstateProposal $blank, RealEstate_Document_ProposalBlankSettings $settingsGroup) {
        if(self::$_instance === null) {
            self::$_instance = new self($blank, $settingsGroup);
        }
        return self::$_instance;
    }
    
    private function __construct(RealEstate_Blank_RealEstateProposal $blank, RealEstate_Document_ProposalBlankSettings $settingsGroup) {
        $this->_blank = $blank;
        $this->_settingsGroup = $settingsGroup;
    }
    
    private function __clone() {}
    
    protected function _getFieldsFromBlank() {
        if($this->_blankFields === null){
            $elements = $this->_blank->getElements();
            foreach($elements as $element) {
                $elementType = $element->getType();
                if($elementType !== 'Zend_Form_Element_Hidden') {
                    $fieldName = $element->getName();
                    $label =  $element->getLabel();
                    if(!empty($label)) {
                        $this->_blankFields[$fieldName] = $label;
                    }
                }
            }
        }
        return $this->_blankFields !== null ? $this->_blankFields : array();

    }
    
    public function getBlankFieldsSettings(){
        $blankFields = $this->_getFieldsFromBlank();
        $fieldsData = array();
        if(count($blankFields) > 0) {
            $hiddenFields = $this->_settingsGroup->getHiddenFields();
            if(count($hiddenFields) > 0) {
                 foreach ($blankFields as $fieldName => $label) {
                    $ids = array_keys($hiddenFields, $fieldName);
                    $id = empty($ids) ? $this->_getTemporaryId() : $ids[0];
                    $fieldsData[] = array(
                        'id' => $id,
                        'FieldName' => $label,
                        'FieldIsVisible' => '<input type="checkbox" name="hfields[' . $id . ']" '
                                            . (empty($ids) ? ' ' : 'checked="checked" ') 
                                            .  'class="settings-cbox" value="' . $fieldName . '" />'
                    );
                    unset($ids);
                }
            } else {
                foreach ($blankFields as $fieldName => $label) {
                    $id = $this->_getTemporaryId();
                    $fieldsData[] = array(
                        'id' => $id,
                        'FieldName' => $label,
                        'FieldIsVisible' => '<input type="checkbox" name="hfields[' . $id . ']" class="settings-cbox" value="' . $fieldName . '" />'
                    );
                }
            }
        }
        return $fieldsData;
    }
    
    protected function _getTemporaryId() {
        return 'tmp_' . uniqid();
    }
    
}
