<?php
class PageViewWorkOrders {
    public static function Generate($_dbInfo) {
        $returnedCode = "";
        $canAddWorkOrder = DatabaseManager::CheckPermissions($_dbInfo, ['cwo']);
        // Permission Check
        if (!DatabaseManager::CheckPermissions($_dbInfo, ['vwo'])) {
            die("You do not have permission to view this page. Speak to your account manager to gain access.");
        }

        $returnedCode .= <<<HTML
        <div id='rightpane_header'>
        <div class='listheaderbuttoncontainer'>
        HTML;

        if ($canAddWorkOrder) {
            $returnedCode .= <<<HTML
            <div class='listheaderbutton btn_newworkorderdialogue'><img src='img/order_green.png' class='img_icon_small' style='margin-right:6px'/> New</div>
            HTML;
        }

        $returnedCode .= <<<HTML
        </div>
        </div><div id='rightpane_viewport'>
        <table class='table_accounts'>
            <thead>
                <tr>
                    <th>Future Title</th>
                    <th>Future Title</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        </div>
        HTML;
        return $returnedCode;
    }
}
?>