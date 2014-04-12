<?php
class RealEstate_Document_Collection_Role extends Lis_Document_Collection_Abstract
{
    protected function _configure()
    {
        $this->_name    = 'TBL_SEC_ROLE_LIST';
        $this->_primary = array('ROLE_ID');
        $this->_sequence = 'GEN_ROLE_ID';
    }
    public function fetchForUser($userId, $loadPermissions = false)
    {
        $roles = array();
        $rows = $this->fetchAll(array(
            'DEPARTMENT_ID IN (SELECT DEPARTMENT_ID FROM TBL_SEC_USERDEP_REL WHERE USER_ID=?)' => $userId,
            'SPECIALIZATION_ID IN (SELECT SPECIALIZATION_ID FROM TBL_SEC_USERSPEC_REL WHERE USER_ID=?)' => $userId,
        ));
        foreach ($rows as $row) {
            if ($loadPermissions) {
                $row->loadPermissions();
            }
            $roles[] = $row;
        }
        return $roles;
    }
}