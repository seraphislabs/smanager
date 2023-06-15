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