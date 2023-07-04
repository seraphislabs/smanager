<?php
    class ValidateField {
        public static function Validate($form_input, $validation_type)
        {
            $retVal = false;

            switch ($validation_type) {
                case 'time':
                    $timeRegex = '/^(1[0-2]|0?[0-9]):[0-5][0-9] (AM|PM)$/';
                    if (strlen($form_input) == 0) {
                        return true;
                    }
                    else if (!preg_match($timeRegex, $form_input)) {
                        error_Log($form_input . " has failed");
                        return $retVal;
                    }
                    break;
                case 'year':
                    $yearRegex = '/^\d{4}$/';
                    if (!preg_match($yearRegex, $form_input)) {
                        return $retVal;
                    }
                    break;
                case 'date':
                    $dateRegex = '/^(0[1-9]|1[0-2])\/(0[1-9]|1\d|2[0-9]|3[0-1])\/\d{4}$/';
                    if (!preg_match($dateRegex, $form_input)) {
                        return $retVal;
                    }
                    break;
                case 'date_my':
                    $datemyRegex = '/^(0[1-9]|1[0-2])\/\d{4}$/';
                    if (!preg_match($datemyRegex, $form_input)) {
                        return $retVal;
                    }
                    break;
                case 'email':
                    $emailRegex = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
                    if (!preg_match($emailRegex, $form_input)) {
                        return $retVal;
                    }
                    break;

                case 'phone':
                    $phoneRegex = '/^(?=.*\d)\(\d{3}\) \d{3}-\d{4}$/';
                    if (!preg_match($phoneRegex, $form_input)) {
                        return $retVal;
                    }
                    break;

                case 'selectnumvalue':
                    $selectnumvalueRegex = '/^-?\d+$/';
                    if (strlen($form_input) > 0 && !preg_match($selectnumvalueRegex, $form_input)) {
                        return $retVal;
                    }
                    break;

                case 'phone_nonrequired':
                    $phoneRegex = '/^\(\d{3}\) \d{3}-\d{4}$/';
                    if (strlen($form_input) > 0 && !preg_match($phoneRegex, $form_input)) {
                        return $retVal;
                    }
                    break;

                case 'zipCode':
                    $zipcodeRegex = '/^\d{5}$/';
                    if (!preg_match($zipcodeRegex, $form_input)) {
                        return $retVal;
                    }
                    break;

                case 'address':
                    // Customize the regular expression for street address validation
                    $streetAddressRegex = '/^[a-zA-Z0-9\s.,\'-]+$/';
                    if (!preg_match($streetAddressRegex, $form_input)) {
                        return $retVal;
                    }
                    break;

                case 'address_nonrequired':
                    // Customize the regular expression for street address validation
                    $streetAddressRegex = '/^[a-zA-Z0-9\s.,\'-]+$/';
                    if (!empty($form_input)) {
                        if (!preg_match($streetAddressRegex, $form_input)) {
                            return $retVal;
                        } elseif (strlen($form_input) <= 3) {
                            return $retVal;
                        }
                    }
                    break;

                case 'name':
                    if (strlen($form_input) <= 2) {
                        return $retVal;
                    }
                    break;

                case 'name_nonrequired':
                    if (strlen($form_input) > 0 && strlen($form_input) <= 2) {
                        return $retVal;
                    }
                    break;

                case 'contractType':
                    if ($form_input === null) {
                        return $retVal;
                    }
                    break;

                case 'state':
                    $stateRegex = '/^[a-zA-Z]+$/';
                    if (!preg_match($stateRegex, $form_input) || strlen($form_input) != 2) {
                        return $retVal;
                    }
                    break;

                default:
                    $retVal = true;
                    return $retVal;
            }

            // Validation passed
            $retVal = true;
            return $retVal;
        }
    }
?>