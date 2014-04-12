<?php
class RealEstate_Blank_Account extends Lis_Blank_Abstract
{
    public function init()
    {
        $environment = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $availableLanguages = $environment->getResource('AvailableLanguages');
        $maxLengths = $this->getLengthFields();

        $this->setAttrib('id', 'blank-account')
             ->setMethod('post');

        $login = new Zend_Form_Element_Text('account');

        $login->setLabel('Account')
              ->setAttrib('maxlength',$maxLengths['USER_ACCOUNT'])
              ->setRequired(true)
              ->setDecorators(array('CompositeElement'));
        $password = new Zend_Form_Element_Password('password');
        $password->setLabel('Password')
                 ->setAttrib('maxlength',$maxLengths['USER_PASSWORD'])
                 ->setDecorators(array('CompositeElement'))
                 ->setRequired(true);
        $changePassword = new Zend_Form_Element_Checkbox('changePassword');
        $changePassword->setLabel('ChangePasswordCommand')
                       ->setDecorators(array('CompositeElement'));

        $language = new Zend_Form_Element_Select('language');
        foreach ($availableLanguages as $lang) {
            $language->addMultiOption($lang, $environment->getResource('Translate')->translate($lang));
        }
        $language->setLabel('Language')
                 ->setDecorators(array('CompositeElement'))
                 ->setValue('uk');

        $this->addElement($login)
             ->addElement($password)
             ->addElement($changePassword)
             ->addElement($language)
             ->addDisplayGroup(array('account', 'password',  'changePassword', 'language'), 'accountdata', array('legend'=>'SystemAuthentificationData', 'class'=>'fieldset-visible'));

        $lastName = new Zend_Form_Element_Text('lastName');
        $lastName->setLabel('LastName')
                 ->setAttrib('maxlength',$maxLengths['USER_LASTNAME'])
                 ->setDecorators(array('CompositeElement'));
        $firstName = new Zend_Form_Element_Text('firstName');
        $firstName->setLabel('FirstName')
                  ->setAttrib('maxlength',$maxLengths['USER_FIRSTNAME'])
                  ->setDecorators(array('CompositeElement'));
        $secondName = new Zend_Form_Element_Text('secondName');
        $secondName->setLabel('SecondName')
                   ->setAttrib('maxlength',$maxLengths['USER_SECONDNAME'])
                   ->setDecorators(array('CompositeElement'));

        $this->addElement($lastName)
             ->addElement($firstName)
             ->addElement($secondName)
             ->addDisplayGroup(array('lastName', 'firstName', 'secondName'), 'profile', array('legend'=>'Employee', 'class'=>'fieldset-visible'));

        //contact data
        $address = new Zend_Form_Element_Textarea('address');
        $address->setLabel('Address')
                ->setDecorators(array('CompositeElement'));
        $addressPostalIndex = new Zend_Form_Element_Hidden('addressPostalIndex');
        $addressPostalIndex->setDecorators(array('ViewHelper'));
        $addressCountry = new Zend_Form_Element_Hidden('addressCountry');
        $addressCountry->setDecorators(array('ViewHelper'));
        $addressTerritory = new Zend_Form_Element_Hidden('addressTerritory');
        $addressTerritory->setDecorators(array('ViewHelper'));
        $addressStreetType = new Zend_Form_Element_Hidden('addressStreetType');
        $addressStreetType->setDecorators(array('ViewHelper'));
        $addressStreet = new Zend_Form_Element_Hidden('addressStreet');
        $addressStreet->setDecorators(array('ViewHelper'));
        $addressBuilding = new Zend_Form_Element_Hidden('addressBuilding');
        $addressBuilding->setDecorators(array('ViewHelper'));
        $addressRoom = new Zend_Form_Element_Hidden('addressRoom');
        $addressRoom->setDecorators(array('ViewHelper'));

        $phoneWork = new Zend_Form_Element_Text('phoneWork');
        $phoneWork->setLabel('PhoneWork')
                  ->setAttrib('maxlength',$maxLengths['USER_PHONE_WORK'])
                  ->setDecorators(array('CompositeElement'));
        $phoneMobile = new Zend_Form_Element_Text('phoneMobile');
        $phoneMobile->setLabel('PhoneMobile')
                  ->setAttrib('maxlength',$maxLengths['USER_PHONE_MOBILE'])
                  ->setDecorators(array('CompositeElement'));
        $phoneHome = new Zend_Form_Element_Text('phoneHome');
        $phoneHome->setLabel('PhoneHome')
                  ->setAttrib('maxlength',$maxLengths['USER_PHONE_HOME'])
                  ->setDecorators(array('CompositeElement'));
        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('E-mail')
                  ->setAttrib('maxlength',$maxLengths['USER_EMAIL'])
              ->setDecorators(array('CompositeElement'));
        $this->addElement($address)
             ->addElement($addressPostalIndex)
             ->addElement($addressCountry)
             ->addElement($addressTerritory)
             ->addElement($addressStreetType)
             ->addElement($addressStreet)
             ->addElement($addressBuilding)
             ->addElement($addressRoom)
             ->addElement($phoneWork)
             ->addElement($phoneMobile)
             ->addElement($phoneHome)
             ->addElement($email)
             ->addDisplayGroup(array(
                    'address',
                    'addressPostalIndex',
                    'addressCountry',
                    'addressTerritory',
                    'addressStreetType',
                    'addressStreet',
                    'addressBuilding',
                    'addressRoom',
                    'phoneWork',
                    'phoneMobile',
                    'phoneHome',
                    'email'
                 ), 'contacts', array('legend'=>'ContactData', 'class'=>'fieldset-visible'));

        //pasport data
        $pasportSerial = new Zend_Form_Element_Text('pasportSerial');
        $pasportSerial->setLabel('SerialAndNumber')
                      ->setAttrib('maxlength',$maxLengths['USER_PASPORT_SERIAL'])
                      ->setDecorators(array('CompositeElement'));
        $pasportIssuedBy = new Zend_Form_Element_Textarea('pasportIssuedBy');
        $pasportIssuedBy->setLabel('IssuedBy')
                        ->setAttrib('maxlength',$maxLengths['USER_PASPORT_ISSUEDBY'])
                        ->setDecorators(array('CompositeElement'));
        $pasportIssuedDate = new ZendX_JQuery_Form_Element_DatePicker('pasportIssuedDate');
        $pasportIssuedDate->setLabel('IssuedDate')
                  ->setJQueryParams(array(
                    'showOn'          => 'button',
                    'buttonImage'     => '/img/icons/16x16/calendar.gif',
                    'buttonImageOnly' => true,
                    'dateFormat'      => 'dd.mm.yy',
                    'changeMonth'     => true,
                    'changeYear'      => true,
                    'yearRange'       => '-85:+0',
                  ))
                  ->setDecorators(array('CompositeElement'));
        $this->addElement($pasportSerial)
             ->addElement($pasportIssuedBy)
             ->addElement($pasportIssuedDate)
             ->addDisplayGroup(array('pasportSerial', 'pasportIssuedBy', 'pasportIssuedDate'), 'pasport', array('legend'=>'PasportData', 'class'=>'fieldset-visible'));


        //education section
        $educationMain = new Zend_Form_Element_Textarea('educationMain');
        $educationMain->setLabel('EducationMain')
                      ->setAttrib('maxlength',$maxLengths['USER_EDUCATION_MAIN'])
                      ->setDecorators(array('CompositeElement'));
        $educationDiploma = new Zend_Form_Element_Textarea('educationDiploma');
        $educationDiploma->setLabel('EducationDiploma')
                         ->setAttrib('maxlength',$maxLengths['USER_EDUCATION_DIPLOMA'])
                         ->setDecorators(array('CompositeElement'));
        $educationAdditional = new Zend_Form_Element_Textarea('educationAdditional');
        $educationAdditional->setLabel('EducationAdditional')
                            ->setAttrib('maxlength',$maxLengths['USER_EDUCATION_ADDITIONAL'])
                            ->setDecorators(array('CompositeElement'));
        $this->addElement($educationMain)
             ->addElement($educationDiploma)
             ->addElement($educationAdditional)
             ->addDisplayGroup(array('educationMain', 'educationDiploma', 'educationAdditional'), 'education', array('legend'=>'Education', 'class'=>'fieldset-visible'));

        //other user personal data
        $birthdate = new ZendX_JQuery_Form_Element_DatePicker('birthdate');
        $birthdate->setLabel('Birthdate')
                  ->setJQueryParams(array(
                    'showOn'          => 'button',
                    'buttonImage'     => '/img/icons/16x16/calendar.gif',
                    'buttonImageOnly' => true,
                    'dateFormat'      => 'dd.mm.yy',
                    'changeMonth'     => true,
                    'changeYear'      => true,
                    'yearRange'       => '-85:+0',
                  ))
                  ->setDecorators(array('CompositeElement'));

        $sex = new Zend_Form_Element_Radio('sex');
        $sex->addMultiOption(0, 'Female')
            ->addMultioption(1, 'Male')
            ->setValue(0)
            ->setLabel('Sex')
            ->setDecorators(array('CompositeElement'));

        $marital = new Zend_Form_Element_Text('marital');
        $marital->setLabel('MaritalStatus')
                ->setAttrib('maxlength',$maxLengths['USER_MARITAL'])
                ->setDecorators(array('CompositeElement'));

        $nationality = new Zend_Form_Element_Text('nationality');
        $nationality->setLabel('Nationality')
                    ->setAttrib('maxlength',$maxLengths['USER_NATIONALITY'])
                    ->setDecorators(array('CompositeElement'));

        $this->addElement($birthdate)
             ->addElement($sex)
             ->addElement($marital)
             ->addElement($nationality)
             ->addDisplayGroup(array('birthdate', 'sex', 'marital', 'nationality'), 'otherpersonal', array('legend'=>'Other', 'class'=>'fieldset-visible'));

        //office data
        $appointment = new Zend_Form_Element_Text('appointment');
        $appointment->setLabel('Appointment')
                    ->setAttrib('maxlength',$maxLengths['USER_APPOINTMENT'])
                    ->setDecorators(array('CompositeElement'));
        $appointmentDate = new ZendX_JQuery_Form_Element_DatePicker('appointmentDate');
        $appointmentDate->setLabel('AppointmentDate')
                        ->setAttrib('maxlength',$maxLengths['USER_APPOINTMENT_DATE'])
                        ->setJQueryParams(array(
                            'showOn'          => 'button',
                            'buttonImage'     => '/img/icons/16x16/calendar.gif',
                            'buttonImageOnly' => true,
                            'dateFormat'      => 'dd.mm.yy',
                            'changeMonth'     => true,
                            'changeYear'      => true,
                            'yearRange'       => '-50:+0',
                        ))
                        ->setDecorators(array('CompositeElement'));
        $qualification = new Zend_Form_Element_Text('qualification');
        $qualification->setLabel('Qualification')
                      ->setAttrib('maxlength',$maxLengths['USER_QUALIFICATION'])
                      ->setDecorators(array('CompositeElement'));
        $experience = new Zend_Form_Element_Text('experience');
        $experience->setLabel('UserExperience')
                   ->setAttrib('maxlength',$maxLengths['USER_EXPERIENCE'])
                   ->setDecorators(array('CompositeElement'));
        $competence = new Zend_Form_Element_Text('competence');
        $competence->setLabel('Competence')
                   ->setAttrib('maxlength',$maxLengths['USER_COMPETENCE'])
                   ->setDecorators(array('CompositeElement'));
        $this->addElement($appointment)
             ->addElement($appointmentDate)
             ->addElement($qualification)
             ->addElement($experience)
             ->addElement($competence)
             ->addDisplayGroup(array('appointment', 'appointmentDate', 'qualification', 'experience', 'competence'), 'otheroffice', array('class'=>'fieldset-visible'));

         //notice
        $note = new Zend_Form_Element_Textarea('note');
        $note->setDecorators(array('ViewHelper'))
             ->setAttrib('maxlength',$maxLengths['USER_NOTICE']);
        $this->addElement($note)
             ->addDisplayGroup(array('note'), 'notice', array('legend'=>'Notice', 'class'=>'fieldset-visible'));

        $this->addDocument(new RealEstate_Document_Account(), 'Account');
        $this->_reflactions = array(
            array('connect' => array('account', array('Account', 'USER_ACCOUNT')),),
            array('connect' => array('password', array('Account', 'USER_PASSWORD')),),
            array('connect' => array('lastName', array('Account', 'USER_LASTNAME')),),
            array('connect' => array('firstName', array('Account', 'USER_FIRSTNAME')),),
            array('connect' => array('secondName', array('Account', 'USER_SECONDNAME')),),
            array('connect' => array('language', array('Account', 'USER_LANGUAGE')),),
            array('connect' => array('pasportSerial', array('Account', 'USER_PASPORT_SERIAL')),),
            array('connect' => array('pasportIssuedBy', array('Account', 'USER_PASPORT_ISSUEDBY')),),
            array('connect' => array('pasportIssuedDate', array('Account', 'USER_PASPORT_ISSUEDDATE')),),
            array('connect' => array('educationMain', array('Account', 'USER_EDUCATION_MAIN')),),
            array('connect' => array('educationDiploma', array('Account', 'USER_EDUCATION_DIPLOMA')),),
            array('connect' => array('educationAdditional', array('Account', 'USER_EDUCATION_ADDITIONAL')),),
            array('connect' => array('birthdate', array('Account', 'USER_BIRTHDATE')),),
            array('connect' => array('sex', array('Account', 'USER_ISMALE')),),
            array('connect' => array('marital', array('Account', 'USER_MARITAL')),),
            array('connect' => array('nationality', array('Account', 'USER_NATIONALITY')),),
            array('connect' => array('address', array('Account', 'USER_ADDRESS')),),
            array('connect' => array('addressPostalIndex', array('Account', 'USER_ADDRESS_ZIP')), 'reflector'=>'Lis_Blank_Reflector_ZeroInBlankIsNullInDb'),
            array('connect' => array('addressCountry', array('Account', 'USER_ADDRESS_COUNTRY')), 'reflector'=>'Lis_Blank_Reflector_ZeroInBlankIsNullInDb'),
            array('connect' => array('addressTerritory', array('Account', 'USER_ADDRESS_TERRITORY')), 'reflector'=>'Lis_Blank_Reflector_ZeroInBlankIsNullInDb'),
            array('connect' => array('addressStreetType', array('Account', 'USER_ADDRESS_STREETTYPE')), 'reflector'=>'Lis_Blank_Reflector_ZeroInBlankIsNullInDb'),
            array('connect' => array('addressStreet', array('Account', 'USER_ADDRESS_STREET')), 'reflector'=>'Lis_Blank_Reflector_ZeroInBlankIsNullInDb'),
            array('connect' => array('addressBuilding', array('Account', 'USER_ADDRESS_BUILDING')), 'reflector'=>'Lis_Blank_Reflector_ZeroInBlankIsNullInDb'),
            array('connect' => array('addressRoom', array('Account', 'USER_ADDRESS_ROOM')), 'reflector'=>'Lis_Blank_Reflector_ZeroInBlankIsNullInDb'),
            array('connect' => array('phoneWork', array('Account', 'USER_PHONE_WORK')),),
            array('connect' => array('phoneMobile', array('Account', 'USER_PHONE_MOBILE')),),
            array('connect' => array('phoneHome', array('Account', 'USER_PHONE_HOME')),),
            array('connect' => array('email', array('Account', 'USER_EMAIL')),),
            array('connect' => array('appointment', array('Account', 'USER_APPOINTMENT')),),
            array('connect' => array('appointmentDate', array('Account', 'USER_APPOINTMENT_DATE')),),
            array('connect' => array('qualification', array('Account', 'USER_QUALIFICATION')),),
            array('connect' => array('experience', array('Account', 'USER_EXPERIENCE')),),
            array('connect' => array('competence', array('Account', 'USER_COMPETENCE')),),
            array('connect' => array('note', array('Account', 'USER_NOTICE')),),
        );
    }

}