<?php

class ListButtons {
    public static function GenerateLeftPaneButton($buttonType) {
        $returnedCode = "";
        switch ($buttonType) {
            case "Accounts":
                $returnedCode = <<<HTML
                <div class='leftpanebutton' data-buttonid='ViewAccounts'><img src='img/customer_green.png' class='img_icon leftpanebuttonicon'/><span class='leftpanebuttontext'>Accounts</span></div>
                HTML;
                break;
            case "Dashboard":
                $returnedCode = <<<HTML
                <div class='leftpanebutton' data-buttonid='Dashboard'><img src='img/menu_green.png' class='img_icon leftpanebuttonicon'/><span class='leftpanebuttontext'>Dashboard</span></div>
                HTML;
                break;
            case "Employees":
                $returnedCode = <<<HTML
                <div class='leftpanebutton' data-buttonid='ViewEmployees'><img src='img/tech_green.png' class='img_icon leftpanebuttonicon'/><span class='leftpanebuttontext'>Employees</span></div>
                HTML;
                break;
            case "WorkOrders":
                $returnedCode = <<<HTML
                <div class='leftpanebutton' data-buttonid='ViewWorkOrders'><img src='img/order_green.png' class='img_icon leftpanebuttonicon'/><span class='leftpanebuttontext'>Work Orders</span></div>
                HTML;
                break;
            case "Invoices":
                $returnedCode = <<<HTML
                <div class='leftpanebutton' data-buttonid='ViewInvoices'><img src='img/invoice_green.png' class='img_icon leftpanebuttonicon'/><span class='leftpanebuttontext'>Invoices</span></div>
                HTML;
                break;
        }

        return $returnedCode;
    }
}

?>