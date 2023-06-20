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

class ViewEmployeeRollsList {
    public static function GenerateListItems($_retArray) {
        $count = 0;
        $returnedCode = "";
        $_roles = $_retArray;
        if (is_array($_roles)) {
            foreach($_roles as $role) {
                $count++;
                $color = "#E0DFE5";

                if ($count%2 == 0) {
                    $color = "#FAFAFA";
                }

                $roleName = $role['name'];
                $roleId = $role['id'];

                $returnedCode .= "
                <div class='formsection_line_leftjustify edit_role_button' data-roleid='$roleId'>
                    <img src='img/edit_green.png' style='width:20px;'/>$roleName
                </div>
                ";

                /*$accountName = $account['name'];
                $accountType = $account['type'];
                $aid = $account['id'];
                $returnedCode .= "<div class='accountviewlistitem' data-accountid='$aid' style='background-color:$color'><div class='accountviewlistitemsub'>$accountName</div><div class='accountviewlistitemsub'>$accountType</div></div>";*/
            }
        }

        $returnedCode .= <<<HTML
            <script>
                $(".edit_role_button").click(function() {
                        var roleid = $(this).data('roleid');
                        $('.popup_darken').fadeIn(500);
                        $('.popup_wrapper').fadeIn(500);
                        var requestData = [
                            {name: 'action', value: 'GenerateNewRolePage'},
                            {name: 'roleid', value: roleid}
                        ];
                        CancelAllAjaxCalls();
                        AjaxCall(xhrArray, requestData, function(status, response) {
                            if (status) {
                                $('.popup_content').html(response).show();
                            }
                        });
                    });
            </script>
        HTML;

        return $returnedCode;
    }
}

?>