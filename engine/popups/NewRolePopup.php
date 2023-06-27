<?php

class PopupNewRole {
    public static function Generate($_dbInfo, $_postData) {
        $_roleid = $_postData['roleid'];
        if (!DatabaseManager::CheckPermissions($_dbInfo, ['emes'])) {
            die("You do not have permission to view this page. Speak to your account manager to gain access.");
        }
        $returnedCode = <<<HTML
            <script>
                var phpRoleId = `$_roleid`;
                if (phpRoleId > 0) {
                    $('.popup_scrollable').hide();
                }
                $("#submit_new_role").click(function() {
                    if(!$(this).hasClass('disabled')) {
                        $(this).addClass('disabled');
                    }

                    Action_AddNewRole(phpRoleId)
                });
                $("#btn_close_popup").click(function () {
                    ClosePopup();
                });
            </script>
        HTML;

        if ($_roleid > 0) {
            $returnedCode .= <<<HTML
            <div class='popup_topbar'><span style='color:white;'>Edit</span> Role</div>
            <div class='popup_scrollable'>
            HTML;
        }
        else {
            $returnedCode .= <<<HTML
            <div class='popup_topbar'><span style='color:white;'>New</span> Role</div>
            <div class='popup_scrollable'>
            HTML;
        }

        $returnedCode .= <<<HTML
            <div class='formsectionfull'>
                <div class='formsection_line_leftjustify'>
                <div class='formsection_label_1'>Role Name: </div><input data-roleid='$_roleid' class='formsection_input formsection_rolename'/>
                </div>
                <div class='formsection_line_leftjustify' style='padding-left:20px;'>
                <div class='checkbox_switch'>
                    <label class='switch'>
                        <input type='checkbox' class='checkbox_can_be_dispatched'>
                        <span class='slider round'></span>
                    </label>
                    <span class='checkbox_switch_label'>Can be dispatched</span>
                </div>
                </div>
                <div class='formsection_mass_control_group' id='permissions_listings'>
                    <div class='formsection_control_group'>
                        <div class='formsection_control_header'>Accounts</div>
                        <div class='formsection_control_options'>
                            <div class='formsection_control_option'><input type='checkbox' class='formsection_permissions_checkbox checkbox_type_1' data-flag='va'/>View Accounts</div>
                            <div class='formsection_control_option'><input type='checkbox' class='formsection_permissions_checkbox checkbox_type_1' data-flag='ca'/>Create Accounts</div>
                            <div class='formsection_control_option'><input type='checkbox' class='formsection_permissions_checkbox checkbox_type_1' data-flag='ea'/>Edit Accounts</div>
                            <div class='formsection_control_option'><input type='checkbox' class='formsection_permissions_checkbox checkbox_type_1' data-flag='da'/>Delete Accounts</div>
                        </div>
                    </div>
                    <div class='formsection_control_group'>
                        <div class='formsection_control_header'>Work Orders</div>
                        <div class='formsection_control_options'>
                            <div class='formsection_control_option'><input type='checkbox' class='formsection_permissions_checkbox checkbox_type_1' data-flag='vwo'/>View Work Orders</div>
                            <div class='formsection_control_option'><input type='checkbox' class='formsection_permissions_checkbox checkbox_type_1' data-flag='cwo'/>Create Work Order</div>
                        </div>
                    </div>
                    <div class='formsection_control_group'>
                        <div class='formsection_control_header'>Service Reports</div>
                        <div class='formsection_control_options'>
                            <div class='formsection_control_option'><input type='checkbox' class='formsection_permissions_checkbox checkbox_type_1' data-flag='vsr'/>View Service Reports</div>
                            <div class='formsection_control_option'><input type='checkbox' class='formsection_permissions_checkbox checkbox_type_1' data-flag='csr'/>Create Service Report</div>
                        </div>
                    </div>
                    <div class='formsection_control_group'>
                        <div class='formsection_control_header'>Invoices</div>
                        <div class='formsection_control_options'>
                            <div class='formsection_control_option'><input type='checkbox' class='formsection_permissions_checkbox checkbox_type_1' data-flag='vi'/>View Invoices</div>
                            <div class='formsection_control_option'><input type='checkbox' class='formsection_permissions_checkbox checkbox_type_1' data-flag='ci'/>Create Invoice</div>
                        </div>
                    </div>
                    <div class='formsection_control_group'>
                        <div class='formsection_control_header'>Employees</div>
                        <div class='formsection_control_options'>
                            <div class='formsection_control_option'><input type='checkbox' class='formsection_permissions_checkbox checkbox_type_1' data-flag='ves'/>View Employee Schedule</div>
                            <div class='formsection_control_option'><input type='checkbox' class='formsection_permissions_checkbox checkbox_type_1' data-flag='vel'/>View Employee List</div>
                            <div class='formsection_control_option'><input type='checkbox' class='formsection_permissions_checkbox checkbox_type_1' data-flag='ce'/>Add Employees</div>
                            <div class='formsection_control_option'><input type='checkbox' class='formsection_permissions_checkbox checkbox_type_1' data-flag='ee'>Edit Employee Details</div>
                            <div class='formsection_control_option'><input type='checkbox' class='formsection_permissions_checkbox checkbox_type_1' data-flag='ses'/>See Schedule</div>
                        </div>
                    </div> 
                    <div class='formsection_control_group'>
                        <div class='formsection_control_header'>Company Settings</div>
                        <div class='formsection_control_options'>
                        <div class='formsection_control_option'><input type='checkbox' class='formsection_permissions_checkbox checkbox_type_1' data-flag='emss'/>Edit Schedule Settings</div>
                            <div class='formsection_control_option'><input type='checkbox' class='formsection_permissions_checkbox checkbox_type_1' data-flag='emes'/>Edit Role Settings</div>
                        </div>
                    </div>         
                </div>
            </div>
        </div>
        <div class='popup_footer'>
        <div id='submit_new_role' class='button_type_2'>Save</div><div class='button_type_1' id='btn_close_popup'>Discard</div>
        </div>
        HTML;

        if ($_roleid > 0) {
            $roleInformation = json_encode(DatabaseManager::GetEmployeeRole($_dbInfo, $_roleid));

            $returnedCode .= <<<HTML
            <script tyle='text/javascript'>
                var roleInformation = `$roleInformation`;
                var roleData = JSON.parse(roleInformation);
                
                var permString = roleData['permissions'];
                var perms = permString.split('|');

                $('.formsection_rolename').val(roleData['name']);
                if (roleData['dispatchable'] == "true") {
                    $('.checkbox_can_be_dispatched').prop("checked", true);
                }
                $('.formsection_permissions_checkbox').each(function () {
                    var flag = $(this).data('flag');
                    if (perms.includes(flag)) {
                        $(this).prop("checked", true);
                    }
                });
                $('.popup_scrollable').show();
            </script>
            HTML;
        }

        return $returnedCode;
    }
}

?>