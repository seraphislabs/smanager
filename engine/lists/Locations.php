<?php

class ListLocations {
    public static function AsList($_dbInfo, $_retArray) {
        $returnedCode = "";
        $count = 0;
        $_locations = $_retArray;
        if (is_array($_locations)) {
            foreach($_locations as $location) {
                $count++;
                $locationName = $location['name'];
                $locationAddress = $location['city'] . "<span class='textcolor_green'>&nbsp;|&nbsp;</span>" .$location['street1'];
                $lid = $location['id'];
                $lcs = $location['contacts'];
                $lcsf = explode("|", $lcs);
                $lcdata = DatabaseManager::GetContact($_dbInfo, $lcsf[0]);

                $locationContactName = $lcdata['firstname'] . " " . $lcdata['lastname'];
                $locationContactPhone = $lcdata['primaryphone'];

                if (count($_locations) > 1 ) {
                    if ($locationName == "") {
                        $locationName = "Location $count";
                    }
                }
                else {
                    if ($locationName == "") {
                        $locationName = "Primary Location";
                    }
                }

                $returnedCode .= <<<HTML
                <tr class='openlocationbuttonx' data-accountid='$lid'>
                    <td>$locationName</td>
                    <td>$locationAddress</td>
                    <td>$locationContactName</td>
                    <td>$locationContactPhone</td>
                </tr>
                HTML;
            }
        }

        $returnedCode .= <<<HTML
            <script>
                /*TODO*/
                $('.openlocationbutton').click(function () { 
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