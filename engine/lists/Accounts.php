<?php

class ListAccounts {
    public static function AsList($_retArray) {
        $returnedCode = "";
        $_accounts = $_retArray;
        if (is_array($_accounts)) {
            foreach($_accounts as $account) {

                $accountName = $account['name'];
                $accountType = $account['type'];
                $aid = $account['id'];

                $returnedCode .= <<<HTML
                <tr class='openaccountbutton' data-accountid='$aid'>
                    <td>$accountName</td>
                    <td>$accountType</td>
                </tr>
                HTML;
            }
        }

        $returnedCode .= <<<HTML
            <script>
                $('.openaccountbutton').click(function () { 
                    $(this).hide();
                    var aid = $(this).data('accountid');
                    var requestData = [
                    {name: 'action', value: 'LoadPage'},
                    {name: 'buttonid', value: 'ViewAccount'},
                    {name: 'accountid', value: aid}
                    ];
                    Action_LoadPage(xhrArray, requestData);
                });
            </script>
        HTML;

        return $returnedCode;
    }
}

?>