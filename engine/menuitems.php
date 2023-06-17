<?php

class LeftPaneMenuItem {
    public static function GenerateButton($buttonType) {
        $returnedCode = "";
        switch ($buttonType) {
            case "Accounts":
                $returnedCode = "<div class='leftpanebutton'><div class='buttonid' style='display:none'>Accounts</div><img src='img/customer_green.png' class='img_icon leftpanebuttonicon'/><span class='leftpanebuttontext'>Accounts</span></div>";
                break;
            case "Dashboard":
                $returnedCode = "<div class='leftpanebutton'><div class='buttonid' style='display:none'>Dashboard</div><img src='img/menu_green.png' class='img_icon leftpanebuttonicon'/><span class='leftpanebuttontext'>Dashboard</span></div>";
                break;
            case "Employees":
                $returnedCode = "<div class='leftpanebutton'><div class='buttonid' style='display:none'>employees</div><img src='img/tech_green.png' class='img_icon leftpanebuttonicon'/><span class='leftpanebuttontext'>Employees</span></div>";
                break;
        }

        return $returnedCode;
    }
}

class ViewAccount {
    public static function GenerateAccountDetails($_account) { 
        $returnedCode = "";
        if (is_array($_account)) { 
            $accountType = $_account['type'];
            $accountStreet1 = $_account['street1'];
            $accountStreet2 = $_account['street2'];
            $accountCity = $_account['city'];
            $accountState = $_account['state'];
            $accountZipcode = $_account['zipcode'];

            $returnedCode .= "<div class='accountviewlistitem' style='background-color:#FAFAFA'><div class='accountviewlistitemsub'>$accountType</div></div>";
            $returnedCode .= "<div class='accountviewlistitem' style='background-color:#E0DFE5'><div class='accountviewlistitemsub'>$accountStreet1 $accountStreet2 $accountCity, $accountState, $accountZipcode</div></div>";
        }
        return $returnedCode;
    }

    public static function GenerateAccountContactDetails($_primaryContact) {
        if (is_array($_primaryContact)) { 
            $accountFirstName = $_primaryContact['firstname'];
            $accountLastName = $_primaryContact['lastname'];
            $accountPrimaryPhone = $_primaryContact['primaryphone'];
            $accountSecondaryPhone = $_primaryContact['secondaryphone'];
            $accountEmail = $_primaryContact['email'];

            $returnedCode = "<div class='accountviewlistitem' style='background-color:#FAFAFA'><div class='accountviewlistitemsub'>$accountFirstName $accountLastName</div></div>";
            $returnedCode .= "<div class='accountviewlistitem' style='background-color:#E0DFE5'><div class='accountviewlistitemsub'>$accountPrimaryPhone</div></div>";
            if (strlen($accountSecondaryPhone) > 0) {
                $returnedCode .= "<div class='accountviewlistitem' style='background-color:#E0DFE5'><div class='accountviewlistitemsub'>$accountSecondaryPhone</div></div>";
            }
            $returnedCode .= "<div class='accountviewlistitem' style='background-color:#FAFAFA'><div class='accountviewlistitemsub'>$accountEmail</div></div>";
        }
        return $returnedCode;
    }
}

class ViewAccountList {
    public static function GenerateListItems($_retArray) {
        $count = 0;
        $returnedCode = "";
        $_accounts = $_retArray;
        if (is_array($_accounts)) {
            foreach($_accounts as $account) {
                $count++;
                $color = "#E0DFE5";

                if ($count%2 == 0) {
                    $color = "#FAFAFA";
                }

                $accountName = $account['name'];
                $accountType = $account['type'];
                $aid = $account['id'];
                $returnedCode .= "<div class='accountviewlistitem' data-accountid='$aid' style='background-color:$color'><div class='accountviewlistitemsub'>$accountName</div><div class='accountviewlistitemsub'>$accountType</div></div>";
            }
        }
        return $returnedCode;
    }
}

?>