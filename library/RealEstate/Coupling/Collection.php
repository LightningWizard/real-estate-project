<?php

class RealEstate_Coupling_Collection implements Iterator {

    private $_annoucements = array();

    public function addAnnoucement(RealEstate_Coupling_Annoucement $annoucment){
        if(!in_array($annoucment, $this->_annoucements, true)){
            $this->_annoucements[] = $annoucment;
        }
        return $this;
    }

    public function removeAnnoucment(RealEstate_Coupling_Annoucement $annoucment) {
        $keysForRemove = array_keys($this->_annoucements, $annoucment, true);
        foreach ($keysForRemove as $key) {
            unset($this->_annoucements[$key]);
        }
        return $this;
    }

    public function current() {
        return current($this->_annoucements);
    }

    public function key() {
        return key($this->_annoucements);
    }

    public function next() {
        return next($this->_annoucements);
    }

    public function rewind() {
        reset($this->_annoucements);
    }

    public function valid() {
        return (bool) (current($this->_annoucements) !== false);
    }

}