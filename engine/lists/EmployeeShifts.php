<?php

class ListEmployeeShifts {
    public static function AsSelect($_dbInfo) {
        $returnedCode = "";

        $retVar = DatabaseManager::GetAllEmployeeShifts($_dbInfo, false);

        foreach($retVar as $shift) {
            $shiftName = $shift['name'];
            $shiftID = $shift['id'];
            $returnedCode .= <<<HTML
            <option value='$shiftID'>$shiftName</option>
            HTML;
        }

        return $returnedCode;
    }

    public static function AsList($_retArray) {
        $count = 0;
        $returnedCode = "";
        $_shifts = $_retArray;
        if (is_array($_shifts)) {
            foreach($_shifts as $shift) {
                $count++;
                $color = "#E0DFE5";

                if ($count%2 == 0) {
                    $color = "#FAFAFA";
                }

                $shiftName = $shift['name'];
                $shiftId = $shift['id'];

                $returnedCode .= <<<HTML
                <div class='formsection_line_leftjustify_width_unset edit_shift_button' data-shiftid='$shiftId'>
                    <img src='img/edit_green.png' style='width:20px;'/><span class='tooltip_trigger'>$shiftName
                    <span class='mytooltip' style='display:none;'>
                    <span style='color:#14A76C'>$shiftName</span><br/>
                    Monday: 9-5<br/>
                    Tuesday: 9-5<br/>
                    Wednesday: 9-5<br/>
                    Thursday: 9-5<br/>
                    Friday: 9-5
                    </span>
                    </span>
                </div>
                HTML;
            }
        }

        $returnedCode .= <<<HTML
            <script>
                $(".edit_shift_button").click(function() {
                        var shiftid = $(this).data('shiftid');
                        $('.popup_darken').fadeIn(500);
                        $('.popup_wrapper').fadeIn(500);
                        $('.popup_content').fadeIn(300);
                        SetLoadingIcon('.popup_content');
                        var data = {};
                        data['shiftid'] = shiftid;
                        var requestData = [
                            {name: 'action', value: 'LoadPopup'},
                            {name: 'buttonid', value: 'NewShift'},
                            {name: 'data', value: JSON.stringify(data)}
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