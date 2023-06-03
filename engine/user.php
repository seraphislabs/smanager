<?php

class SM_User {
    public $email;
    public $password;

    public function AsArray() {
        $reflection = new ReflectionObject($this);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
        
        $result = [];
        
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $propertyValue = $property->getValue($this);
            $result[$propertyName] = $propertyValue;
        }
        
        return $result;
    }
}

class SM_UserManager {
    public static function CreateUser($_email, $_password) {
        $nUser = new SM_User();
        $nUser->email = $_email;
        $nUser->password = $_password;

        return $nUser;
    }
    public static function DeleteUser() {
        // TODO
    }
}

?>