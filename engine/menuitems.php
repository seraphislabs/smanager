<?php

class LeftPaneMenuItem {
    public static function GenerateButton($buttonType) {
        $returnedCode = "";
        switch ($buttonType) {
            case "Accounts":
                $returnedCode = "<div class='leftpanebutton'><img src='img/customer_green.png' class='img_icon leftpanebuttonicon'/><span class='leftpanebuttontext'>Accounts</span></div>";
                break;
            case "Dashboard":
                $returnedCode = "<div class='leftpanebutton'><img src='img/menu_green.png' class='img_icon leftpanebuttonicon'/><span class='leftpanebuttontext'>Dashboard</span></div>";
                break;
        }

        return $returnedCode;
    }
}

?>