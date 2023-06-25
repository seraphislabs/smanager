<?php
class PageViewAccounts {
    public static function Generate($_dbInfo) {
    $returnedCode = "";
    $canAddAccount = DatabaseManager::CheckPermissions($_dbInfo, ['ca']);
    // Permission Check
    if (!DatabaseManager::CheckPermissions($_dbInfo, ['va'])) {
        die("You do not have permission to view this page. Speak to your account manager to gain access.");
    }

    $retArray = DatabaseManager::GetAllAccounts($_dbInfo);

    $returnedCode .= <<<HTML
    <script type='text/javascript'>           
    function OpenNewAccountPage() {
        $('.popup_darken').fadeIn(500);
        $('.popup_wrapper').fadeIn(500);
        SetLoadingIcon('.popup_scrollable');
        var requestData = [
            {name: 'action', value: 'GenerateNewAccountPage'}
        ];
        CancelAllAjaxCalls();
        AjaxCall(xhrArray, requestData, function(status, response) {
            if (status) {
            $('.popup_content').html(response).show();
            }
        });
    }
    $('.btn_newaccountdialogue').click(function() {
        OpenNewAccountPage();
    });
    </script>
    HTML;

    $returnedCode .= <<<HTML
    <div id='rightpane_header'>
    <div class='listheaderbuttoncontainer'>
    HTML;
    if ($canAddAccount) {
        $returnedCode .= <<<HTML
        <div class='listheaderbutton btn_newaccountdialogue'><img src='img/add_user_green.png' class='img_icon_small' style='margin-right:6px'/> New</div>
        HTML;
    }
    $returnedCode .= <<<HTML
    </div>
    </div><div id='rightpane_viewport'>
    HTML;

    $returnedCode .= <<<HTML
    <table class='table_accounts'>
        <thead>
            <tr>
                <th>Name</th>
                <th>Type</th>
            </tr>
        </thead>
        <tbody>
    HTML;

    $accountsList = DatabaseManager::GetAllAccounts($_dbInfo);
    $accountCode = ListAccounts::AsList($accountsList);
    $returnedCode .= $accountCode;
        
    $returnedCode .= <<<HTML
        </tbody>
    </table>
    </div>
    HTML;
    return $returnedCode;
    }
}
?>