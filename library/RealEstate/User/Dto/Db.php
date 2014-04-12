<?php

require_once 'Lis/User/Dto/Db.php';

class RealEstate_User_Dto_Db extends Lis_User_Dto_Db
{
    public function load(Lis_User $user)
    {
        if (null === $user)
            throw new Exception('Try to load null user');
        $db = $this->getDbAdapter();

        $userData = $db->fetchRow($db->select()->from('TBL_SEC_USER_LIST')->where('USER_ID=?', $user->getId()));
        if ($userData) {
            $roles = array();
            foreach (Lis_Document_Collection_Factory::factory('RealEstate_Document_Collection_Role')->fetchForUser($user->getId(), true) as $role) {
                $roles[] = new RealEstate_Acl_Role($role);
            }
            $user->setFirstName($userData['USER_FIRSTNAME'])
                 ->setSecondName($userData['USER_SECONDNAME'])
                 ->setLastName($userData['USER_LASTNAME'])
                 ->setLanguage($userData['USER_LANGUAGE'])
                 ->setRoles($roles);

        } else {
            throw new Exception("Can't load current user data");
        }
    }
    public function save(Lis_User $user)
    {
        //метод не поддерживается
    }
}
