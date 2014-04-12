<?php
require_once 'Lis/Document/Abstract.php';
class RealEstate_Document_Account extends Lis_Document_Abstract
{
    protected $_departments = null;
    protected $_specializations = null;
    public function save()
    {
        $db = $this->getCollection()->getAdapter();
        $db->beginTransaction();
        try {
            parent::save();

            if (is_array($this->_departments)) {
                $db->delete('TBL_SEC_USERDEP_REL', $db->quoteInto('USER_ID=?', $this->USER_ID));
                foreach ($this->_departments as $department) {
                    $db->insert('TBL_SEC_USERDEP_REL', array(
                        'USER_ID' => $this->USER_ID,
                        'DEPARTMENT_ID' => $department,
                    ));
                }
            }

            if (is_array($this->_specializations)) {
                $db->delete('TBL_SEC_USERSPEC_REL', $db->quoteInto('USER_ID=?', $this->USER_ID));
                foreach ($this->_specializations as $specialization) {
                    $db->insert('TBL_SEC_USERSPEC_REL', array(
                        'USER_ID' => $this->USER_ID,
                        'SPECIALIZATION_ID' => $specialization,
                    ));
                }
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this;
    }
    public function setDepartmentsForSave(array $departments)
    {
        $this->_departments = $departments;
        return $this;
    }
    public function setSpecializationsForSave(array $specializations)
    {
        $this->_specializations = $specializations;
        return $this;
    }

    public function getFullName($onlyInitials = false)
    {
        $fullname = array();
        if ($this->USER_LASTNAME) {
            $fullname[] = $this->USER_LASTNAME;
        }
        if ($this->USER_FIRSTNAME) {
            $fullname[] = $onlyInitials ? (substr($this->USER_FIRSTNAME, 0, 1) . '.') : $this->USER_FIRSTNAME;
        }
        if ($this->USER_SECONDNAME) {
            $fullname[] = $onlyInitials ? (substr($this->USER_SECONDNAME, 0, 1) . '.') : $this->USER_SECONDNAME;
        }
        return join(' ', $fullname);
    }

    public function getDepartments()
    {
        $db = $this->getCollection()->getAdapter();
        $select = $db->select()
                     ->from(array('D' => new Zend_Db_Expr('proc_getdepartment')),
                            array(
                                'DEPARTMENT_ID',
                                'DEPARTMENT_TITLE',
                                'DEPARTMENT_DEAP',
                                'DEPARTMENT_ID_PARENT',
                                'DEPARTMENT_HASSUBDEPARTMENTS'
                            ));
        if (null !== $this->USER_ID) {
            $select->joinLeft(array('UD'=>'TBL_SEC_USERDEP_REL'),
                              $db->quoteInto('"UD"."DEPARTMENT_ID"="D"."DEPARTMENT_ID" and "UD".USER_ID=?', $this->USER_ID),
                              array('DEPARTMENT_ISENABLE' => new Zend_Db_Expr('iif("UD"."DEPARTMENT_ID" is not null, 1, 0)')));
        } else {
            $select->columns(array('DEPARTMENT_ISENABLE' => new Zend_Db_Expr('0')));
        }
        return $db->fetchAll($select);
    }
    public function getSpecializations()
    {
        $db = $this->getCollection()->getAdapter();
        $select = $db->select()
                     ->from(array('S' => new Zend_Db_Expr('proc_getspecialization')),
                            array(
                                'SPECIALIZATION_ID',
                                'SPECIALIZATION_TITLE',
                                'SPECIALIZATION_DEAP',
                                'SPECIALIZATION_ID_PARENT',
                                'SPECIALIZATION_HASCHILDREN'
                            ));
        if (null !== $this->USER_ID) {
            $select->joinLeft(array('US'=>'TBL_SEC_USERSPEC_REL'),
                              $db->quoteInto('"US"."SPECIALIZATION_ID"="S"."SPECIALIZATION_ID" and "US".USER_ID=?', $this->USER_ID),
                              array('SPECIALIZATION_ISENABLE' => new Zend_Db_Expr('iif("US"."SPECIALIZATION_ID" is not null, 1, 0)')));
        } else {
            $select->columns(array('SPECIALIZATION_ISENABLE' => new Zend_Db_Expr('0')));
        }
        return $db->fetchAll($select);
    }
}