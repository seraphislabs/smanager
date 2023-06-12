<?php

class PasswordEncrypt {
    public static function Encrypt($_password) {
        return sodium_crypto_pwhash_str($_password,SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE);
    }
    public static function Check($entered_password, $stored_password) {
        return sodium_crypto_pwhash_str_verify($stored_password, $entered_password);
    }
}

class NewAccountInformation {
    public $id;
    public $name;
    public $type;
    public $primarylocationid;
    public $locationids;
    public $contactids;

    public function explode() {
        // Dynamically explode the public variables into an associative array
        $explodedArray = [];
        $reflectionClass = new ReflectionClass($this);
        $properties = $reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $explodedArray[$propertyName] = $this->$propertyName;
        }
        return $explodedArray;
    }

    public static function implode($obj, $data) {
        // Dynamically implode the array values into the object's public variables
        foreach ($data as $key => $value) {
            $obj->$key = $value;
        }
    }
}

class NewLocationInformation {
    public $id;
    public $street1;
    public $street2;
    public $city;
    public $state;
    public $zip;
    public $notes;

    public function explode() {
        // Dynamically explode the public variables into an associative array
        $explodedArray = [];
        $reflectionClass = new ReflectionClass($this);
        $properties = $reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $explodedArray[$propertyName] = $this->$propertyName;
        }
        return $explodedArray;
    }

    public static function implode($obj, $data) {
        // Dynamically implode the array values into the object's public variables
        foreach ($data as $key => $value) {
            $obj->$key = $value;
        }
    }
}

class NewContactInformation {
    public $id;
    public $firstname;
    public $lastname;
    public $email;
    public $primaryphone;
    public $secondaryphone;
    public $locationid;

    public function explode() {
        // Dynamically explode the public variables into an associative array
        $explodedArray = [];
        $reflectionClass = new ReflectionClass($this);
        $properties = $reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $explodedArray[$propertyName] = $this->$propertyName;
        }
        return $explodedArray;
    }

    public static function implode($obj, $data) {
        // Dynamically implode the array values into the object's public variables
        foreach ($data as $key => $value) {
            $obj->$key = $value;
        }
    }
}

class NewAccountForm {
    public $accountInformation;
    public $locations;
    public $contacts;

    public function Validate() {

    }
}

class NewLocationForm {

}

class NewContactForm {

}

class ServerResponsePacket {
    public $status;
    public $response;
}

?>