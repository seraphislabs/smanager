<?php
    class PageManager {
        public static function GenerateAccountsPage($_dbInfo) {
            $returnedCode = "";
            $canAddAccount = DatabaseManager::CheckPermissions($_dbInfo, ['ca']);
            // Permission Check
            if (!DatabaseManager::CheckPermissions($_dbInfo, ['va'])) {
                die("You do not have permission to view this page. Speak to your account manager to gain access.");
            }

            $retArray = DatabaseManager::GetAccounts($_dbInfo);

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

            $('.accountviewlistitem').click(function () { 
                $(this).hide();
                var aid = $(this).data('accountid');
                var requestData = [
                {name: 'action', value: 'ViewAccount'},
                {name: 'accountid', value: aid}
                ];
                CancelAllAjaxCalls();
                SetLoadingIcon('#rightpane_container');
                AjaxCall(xhrArray, requestData, function(status, response) {
                    if (status) {
                        $('#rightpane_container').html(response);
                    }
                });
            });
            </script>
            HTML;

            $returnedCode .= "<div id='rightpane_header'>";
            $returnedCode .= "<div class='listheaderbuttoncontainer'>";
            if ($canAddAccount) {
                $returnedCode .= "<div class='listheaderbutton btn_newaccountdialogue'><img src='img/add_user_green.png' class='img_icon_small' style='margin-right:6px'/> New</div>";
            }
            $returnedCode .= "</div>";
            $returnedCode .= "<div class='accountviewlistcontainer'>";
            $returnedCode .= "<div class='accountviewlistheaders'>
            <div class='accountviewlistheaderitem'>Name</div>
            <div class='accountviewlistheaderitem'>Type</div>
            </div>";
            $returnedCode .= "</div>";
            $returnedCode .= "</div><div id='rightpane_viewport' style='top:110px'>";

            $returnedCode .= ViewAccountList::GenerateListItems($retArray);

            $returnedCode .= "</div>";

            $returnedCode .= "<div id='rightpane_footer'>";
            $returnedCode .= "</div>";
            return $returnedCode;
        }

        public static function GenerateNewShiftPage($_dbInfo, $_shiftid) {
            if (!DatabaseManager::CheckPermissions($_dbInfo, ['emes'])) {
                die("You do not have permission to view this page. Speak to your account manager to gain access.");
            }
            $returnedCode = <<<HTML
                <script>
                    var phpShiftId = `$_shiftid`;
                    InitTimePickers();

                    if (phpShiftId > 0) {
                        $('.popup_scrollable').hide();
                    }
                    $('.schedule_enable').change(function() {
                        var timeField = $(this).closest('.formsection_line_leftjustify').children('.formsection_toggle_time_fields');
                        if (timeField.is(':visible')) {
                            timeField.fadeOut(200);
                        }
                        else {
                            timeField.fadeIn(200);
                        }
                    });
                    $("#submit_new_shift").click(function() {
                        var endperms = "";
                        var first = true;

                        if(!$(this).hasClass('disabled')) {
                            $(this).addClass('disabled');
                        }

                        var returnInformation = SerializeNewShiftForm();
                        var shiftInformation = JSON.stringify(returnInformation['shiftInformation']);

                        var requestData = [
                            {name: 'action', value: 'AddNewShift'},
                            {name: 'shiftInformation', value: shiftInformation}            
                        ];
                        CancelAllAjaxCalls();
                        AjaxCall(xhrArray, requestData, function(status, response) {
                            if (status) {
                                var resVar = response.split('|');
                                if (resVar[0] == 'true') {
                                    $('.popup_wrapper').hide();
                                    $('.popup_darken').fadeOut(400);
                                    ClickLeftPaneMenuItem('EmployeeSettings', false);
                                }
                                else {
                                    $('.popup_scrollable').prepend("<div class='formsection_line_centered'><div class='formsection_input_centered_text'>" + resVar[1] + "</div></div>");
                                }
                                if($('#submit_new_role').hasClass('disabled')) {
                                    $('#submit_new_role').removeClass('disabled');
                                }
                            }
                        });
                    });
                    $("#btn_close_popup").click(function () {
                        ClosePopup();
                    });
                </script>
            HTML;

            if ($_shiftid > 0) {
                $returnedCode .= "
                <div class='popup_topbar'><span style='color:white;'>Edit</span> Shift</div>
                <div class='popup_scrollable'>";
            }
            else {
                $returnedCode .= "
            <div class='popup_topbar'><span style='color:white;'>New</span> Shift</div>
            <div class='popup_scrollable'>";
            }

            $returnedCode .= "
                <div class='formsectionfull' id='timeselections'>
                    <div class='formsection_line_leftjustify'>
                    <div class='formsection_label_1'>Shift Name: </div><input data-shiftid='$_shiftid' class='formsection_input formsection_data_shift_name'/>
                    </div>
                    <div class='formsection_line_leftjustify' style='height:40px;'><div class='formsection_label_1'>Monday:</div>
                    <div class='checkbox_switch'>
                        <label class='switch'>
                            <input type='checkbox' class='schedule_enable formsection_data_checkbox_monday' checked>
                            <span class='slider round'></span>
                        </label>
                    </div>
                    <div class='formsection_toggle_time_fields formsection_data_toggle_monday'>
                    <input type='text' data-defaulttime='09:00am' class='formsection_input_fixed_2 formsection_input_timepicker formsection_data_monday_start' />
                    to
                    <input type='text' data-defaulttime='05:00pm' class='formsection_input_fixed_2 formsection_input_timepicker formsection_data_monday_end' />
                    </div>
                    </div>
                    <div class='formsection_line_leftjustify' style='height:40px;'><div class='formsection_label_1'>Tuesday:</div>
                    <div class='checkbox_switch'>
                        <label class='switch'>
                            <input type='checkbox' class='schedule_enable formsection_data_checkbox_tuesday' checked>
                            <span class='slider round'></span>
                        </label>
                    </div>
                    <div class='formsection_toggle_time_fields formsection_data_toggle_tuesday'>
                    <input type='text' data-defaulttime='09:00am' class='formsection_input_fixed_2 formsection_input_timepicker formsection_data_tuesday_start' />
                    to
                    <input type='text' data-defaulttime='05:00pm' class='formsection_input_fixed_2 formsection_input_timepicker formsection_data_tuesday_end' />
                    </div>
                    </div>
                    <div class='formsection_line_leftjustify' style='height:40px;'><div class='formsection_label_1'>Wednesday:</div>
                    <div class='checkbox_switch'>
                        <label class='switch'>
                            <input type='checkbox' class='schedule_enable formsection_data_checkbox_wednesday' checked>
                            <span class='slider round'></span>
                        </label>
                    </div>
                    <div class='formsection_toggle_time_fields formsection_data_toggle_wednesday'>
                    <input type='text' data-defaulttime='09:00am' class='formsection_input_fixed_2 formsection_input_timepicker formsection_data_wednesday_start' />
                    to
                    <input type='text' data-defaulttime='05:00pm' class='formsection_input_fixed_2 formsection_input_timepicker formsection_data_wednesday_end' />
                    </div>
                    </div>
                    <div class='formsection_line_leftjustify' style='height:40px;'><div class='formsection_label_1'>Thursday:</div>
                    <div class='checkbox_switch'>
                        <label class='switch'>
                            <input type='checkbox' class='schedule_enable formsection_data_checkbox_thursday' checked>
                            <span class='slider round'></span>
                        </label>
                    </div>
                    <div class='formsection_toggle_time_fields formsection_data_toggle_thursday'>
                    <input type='text' data-defaulttime='09:00am' class='formsection_input_fixed_2 formsection_input_timepicker formsection_data_thursday_start' />
                    to
                    <input type='text' data-defaulttime='05:00pm' class='formsection_input_fixed_2 formsection_input_timepicker formsection_data_thursday_end' />
                    </div>
                    </div>
                    <div class='formsection_line_leftjustify' style='height:40px;'><div class='formsection_label_1'>Friday:</div>
                    <div class='checkbox_switch'>
                        <label class='switch'>
                            <input type='checkbox' class='schedule_enable formsection_data_checkbox_friday' checked>
                            <span class='slider round'></span>
                        </label>
                    </div>
                    <div class='formsection_toggle_time_fields formsection_data_toggle_friday'>
                    <input type='text' data-defaulttime='09:00am' class='formsection_input_fixed_2 formsection_input_timepicker formsection_data_friday_start' />
                    to
                    <input type='text' data-defaulttime='05:00pm' class='formsection_input_fixed_2 formsection_input_timepicker formsection_data_friday_end' />
                    </div>
                    </div>
                    <div class='formsection_line_leftjustify' style='height:40px;'><div class='formsection_label_1'>Saturday:</div>
                    <div class='checkbox_switch'>
                        <label class='switch'>
                            <input type='checkbox' class='schedule_enable formsection_data_checkbox_saturday'>
                            <span class='slider round'></span>
                        </label>
                    </div>
                    <div class='formsection_toggle_time_fields formsection_data_toggle_saturday' style='display:none;'>
                    <input type='text' data-defaulttime='09:00am' class='formsection_input_fixed_2 formsection_input_timepicker formsection_data_saturday_start' />
                    to
                    <input type='text' data-defaulttime='05:00pm' class='formsection_input_fixed_2 formsection_input_timepicker formsection_data_saturday_end' />
                    </div>
                    </div>
                    <div class='formsection_line_leftjustify' style='height:40px;'><div class='formsection_label_1'>Sunday:</div>
                    <div class='checkbox_switch'>
                        <label class='switch'>
                            <input type='checkbox' class='schedule_enable formsection_data_checkbox_sunday'>
                            <span class='slider round'></span>
                        </label>
                    </div>
                    <div class='formsection_toggle_time_fields formsection_data_toggle_sunday' style='display:none;'>
                    <input type='text' data-defaulttime='09:00am' class='formsection_input_fixed_2 formsection_input_timepicker formsection_data_sunday_start' />
                    to
                    <input type='text' data-defaulttime='05:00pm' class='formsection_input_fixed_2 formsection_input_timepicker formsection_data_sunday_end' />
                    </div>

                </div>
            ";

            $returnedCode .= "
            </div>
            <div class='popup_footer'>
            <div id='submit_new_shift' class='button_type_2'>Save</div><div class='button_type_1' id='btn_close_popup'>Discard</div>
            </div>
            ";

            if ($_shiftid > 0) {
                $shiftInformation = json_encode(DatabaseManager::GetShift($_dbInfo, $_shiftid));

                $returnedCode .= <<<HTML
                <script type='text/javascript'>
                    var shiftInformation = `$shiftInformation`;
                    var shiftData = JSON.parse(shiftInformation);

                    var shiftName = shiftData['name'];

                    var mondayString = shiftData['monday'].split('|');
                    var mondayStart = mondayString[0];
                    var mondayEnd = mondayString[1];
                    var tuesdayString = shiftData['tuesday'].split('|');
                    var tuesdayStart = tuesdayString[0];
                    var tuesdayEnd = tuesdayString[1];
                    var wednesdayString = shiftData['wednesday'].split('|');
                    var wednesdayStart = wednesdayString[0];
                    var wednesdayEnd = wednesdayString[1];
                    var thursdayString = shiftData['thursday'].split('|');
                    var thursdayStart = thursdayString[0];
                    var thursdayEnd = thursdayString[1];
                    var fridayString = shiftData['friday'].split('|');
                    var fridayStart = fridayString[0];
                    var fridayEnd = fridayString[1];
                    var saturdayString = shiftData['saturday'].split('|');
                    var saturdayStart = saturdayString[0];
                    var saturdayEnd = saturdayString[1];
                    var sundayString = shiftData['sunday'].split('|');
                    var sundayStart = sundayString[0];
                    var sundayEnd = sundayString[1];

                    $('.formsection_data_shift_name').val(shiftName);

                    $('.schedule_enable').each(function () {
                        $(this).prop('checked', false);
                    });

                    $('.formsection_toggle_time_fields').each(function () {
                        $(this).hide();
                    });

                    if (shiftData['monday'] !== undefined && shiftData['monday'] !== null && shiftData['monday'].length > 0) {  
                        $('.formsection_data_monday_start').timepicker('destroy');
                        $('.formsection_data_monday_start').data('defaulttime',mondayStart);
                        $('.formsection_data_monday_end').timepicker('destroy');
                        $('.formsection_data_monday_end').data('defaulttime',mondayEnd); 
                        $('.formsection_data_checkbox_monday').prop('checked', true);
                        $('.formsection_data_toggle_monday').show();
                    }
                    if (shiftData['tuesday'] !== undefined && shiftData['tuesday'] !== null && shiftData['tuesday'].length > 0) {
                        $('.formsection_data_tuesday_start').timepicker('destroy');
                        $('.formsection_data_tuesday_start').data('defaulttime',tuesdayStart);
                        $('.formsection_data_tuesday_end').timepicker('destroy');
                        $('.formsection_data_tuesday_end').data('defaulttime',tuesdayEnd);
                        $('.formsection_data_checkbox_tuesday').prop('checked', true);
                        $('.formsection_data_toggle_tuesday').show();
                    }
                    if (shiftData['wednesday'] !== undefined && shiftData['wednesday'] !== null && shiftData['wednesday'].length > 0) {
                        $('.formsection_data_wednesday_start').timepicker('destroy');
                        $('.formsection_data_wednesday_start').data('defaulttime',wednesdayStart);
                        $('.formsection_data_wednesday_end').timepicker('destroy');
                        $('.formsection_data_wednesday_end').data('defaulttime',wednesdayEnd);
                        $('.formsection_data_checkbox_wednesday').prop('checked', true);
                        $('.formsection_data_toggle_wednesday').show();
                    }
                    if (shiftData['thursday'] !== undefined && shiftData['thursday'] !== null && shiftData['thursday'].length > 0) {
                        $('.formsection_data_thursday_start').timepicker('destroy');
                        $('.formsection_data_thursday_start').data('defaulttime',thursdayStart);
                        $('.formsection_data_thursday_end').timepicker('destroy');
                        $('.formsection_data_thursday_end').data('defaulttime',thursdayEnd);
                        $('.formsection_data_checkbox_thursday').prop('checked', true);
                        $('.formsection_data_toggle_thursday').show();
                    }
                    if(shiftData['friday'] !== undefined && shiftData['friday'] !== null && shiftData['friday'].length > 0) {
                        $('.formsection_data_friday_start').timepicker('destroy');
                        $('.formsection_data_friday_start').data('defaulttime',fridayStart);
                        $('.formsection_data_friday_end').timepicker('destroy');
                        $('.formsection_data_friday_end').data('defaulttime',fridayEnd);
                        $('.formsection_data_checkbox_friday').prop('checked', true);
                        $('.formsection_data_toggle_friday').show();
                    }
                    if (shiftData['saturday'] !== undefined && shiftData['saturday'] !== null && shiftData['saturday'].length > 0) {
                        $('.formsection_data_saturday_start').timepicker('destroy');
                        $('.formsection_data_saturday_start').data('defaulttime', saturdayStart);
                        $('.formsection_data_saturday_end').timepicker('destroy');
                        $('.formsection_data_saturday_end').data('defaulttime', saturdayEnd);
                        $('.formsection_data_checkbox_saturday').prop('checked', true);
                        $('.formsection_data_toggle_saturday').show();
                    }
                    if (shiftData['sunday'] !== undefined && shiftData['sunday'] !== null && shiftData['sunday'].length > 0) {
                        $('.formsection_data_sunday_start').timepicker('destroy');
                        $('.formsection_data_sunday_start').data('defaulttime',sundayStart);
                        $('.formsection_data_sunday_end').timepicker('destroy');
                        $('.formsection_data_sunday_end').data('defaulttime',sundayEnd);
                        $('.formsection_data_checkbox_sunday').prop('checked', true);
                        $('.formsection_data_toggle_sunday').show();
                    }
                    InitTimePickers();
                    $('.popup_scrollable').show();
                </script>
                HTML;
            }

            return $returnedCode;
        }

        public static function GenerateEmployeesPage($_dbInfo) {
            $canAddEmployee = DatabaseManager::CheckPermissions($_dbInfo, ['ce']);
            if (!DatabaseManager::CheckPermissions($_dbInfo, ['ve'])) {
                die("You do not have permission to view this page. Speak to your account manager to gain access.");
            }

            $returnedCode = <<<HTML
                <script type='text/javascript'>
                    function OpenNewEmployeePage() {
                        $('.popup_darken').fadeIn(500);
                        $('.popup_wrapper').fadeIn(500);
                        SetLoadingIcon('.popup_scrollable');
                        var requestData = [
                            {name: 'action', value: 'GenerateNewEmployeePage'}
                        ];
                        CancelAllAjaxCalls();
                        AjaxCall(xhrArray, requestData, function(status, response) {
                            if (status) {
                                $('.popup_content').html(response).show();
                            }
                        });
                    }
                    $('.btn_newemployeedialogue').click(function() {
                        OpenNewEmployeePage();
                    });
                </script>
            HTML;

            $returnedCode .= "<div id='rightpane_header'>";
            $returnedCode .= "<div class='listheaderbuttoncontainer'>";
            if ($canAddEmployee) {
                $returnedCode .= "<div class='listheaderbutton btn_newemployeedialogue'><img src='img/add_user_green.png' class='img_icon_small' style='margin-right:6px'/> New</div>";
            }
            $returnedCode .= "</div>";
            $returnedCode .= "<div class='accountviewlistcontainer'>";
            $returnedCode .= "<div class='accountviewlistheaders'>
            <div class='accountviewlistheaderitem'></div>
            <div class='accountviewlistheaderitem'></div>
            </div>";
            $returnedCode .= "</div>";
            $returnedCode .= "</div><div id='rightpane_viewport' style='top:110px'>";

            $returnedCode .= "</div>";

            //$employees = DatabaseManager::GetEmployeeAccounts($_dbInfo);

            $returnedCode .= "</div>";

            $returnedCode .= "<div id='rightpane_footer'>";
            $returnedCode .= "</div>";
            return $returnedCode;
        }

        public static function GenerateNewRolePage($_dbInfo, $_roleid) {
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
                        var endperms = "";
                        var first = true;
                        $("#permissions_listings").find('.formsection_permissions_checkbox').each(function () {
                            if ($(this).is(':checked')) {
                                if (!first) {
                                    endperms += "|";
                                }
                                endperms += $(this).data('flag');
                                first = false;
                            }
                        });

                        if(!$(this).hasClass('disabled')) {
                            $(this).addClass('disabled');
                        }

                        var isDispatchable = $('.checkbox_can_be_dispatched').is(":checked");
                        var roleName = $(".formsection_rolename").val();
                        var requestData = [
                            {name: 'action', value: 'AddNewRole'},
                            {name: 'name', value: roleName},
                            {name: 'perms', value: endperms},
                            {name: 'isDispatchable', value: isDispatchable},
                            {name: 'roleid', value: $_roleid}                    
                        ];
                        CancelAllAjaxCalls();
                        AjaxCall(xhrArray, requestData, function(status, response) {
                            if (status) {
                                var resVar = response.split('|');
                                if (resVar[0] == 'true') {
                                    $('.popup_wrapper').hide();
                                    $('.popup_darken').fadeOut(400);
                                    ClickLeftPaneMenuItem('EmployeeSettings', false);
                                }
                                else {
                                    $('.popup_scrollable').prepend("<div class='formsection_line_centered'><div class='formsection_input_centered_text'>" + resVar[1] + "</div></div>");
                                }
                                if($('#submit_new_role').hasClass('disabled')) {
                                    $('#submit_new_role').removeClass('disabled');
                                }
                            }
                        });
                    });
                    $("#btn_close_popup").click(function () {
                        ClosePopup();
                    });
                </script>
            HTML;

            if ($_roleid > 0) {
                $returnedCode .= "
                <div class='popup_topbar'><span style='color:white;'>Edit</span> Role</div>
                <div class='popup_scrollable'>";
            }
            else {
                $returnedCode .= "
            <div class='popup_topbar'><span style='color:white;'>New</span> Role</div>
            <div class='popup_scrollable'>";
            }

            $returnedCode .= "
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
                            <div class='formsection_control_header'>Employees</div>
                            <div class='formsection_control_options'>
                                <div class='formsection_control_option'><input type='checkbox' class='formsection_permissions_checkbox checkbox_type_1' data-flag='ve'/>View Employees</div>
                                <div class='formsection_control_option'><input type='checkbox' class='formsection_permissions_checkbox checkbox_type_1' data-flag='ce'/>Add Employees</div>
                                <div class='formsection_control_option'><input type='checkbox' class='formsection_permissions_checkbox checkbox_type_1' data-flag='ee'>Edit Employee Details</div>
                                <div class='formsection_control_option'><input type='checkbox' class='formsection_permissions_checkbox checkbox_type_1' data-flag='ses'/>See Schedule</div>
                                <div class='formsection_control_option'><input type='checkbox' class='formsection_permissions_checkbox checkbox_type_1' data-flag='ees'/>Edit Schedule</div>
                                <div class='formsection_control_option'><input type='checkbox' class='formsection_permissions_checkbox checkbox_type_1' data-flag='cer'/>Create New Roles</div>
                                <div class='formsection_control_option'><input type='checkbox' class='formsection_permissions_checkbox checkbox_type_1' data-flag='eerp'/>Edit Role Permissions</div>
                                <div class='formsection_control_option'><input type='checkbox' class='formsection_permissions_checkbox checkbox_type_1' data-flag='edwh'/>Edit Default Work Hours</div>
                            </div>
                        </div> 
                        <div class='formsection_control_group'>
                            <div class='formsection_control_header'>Company Settings</div>
                            <div class='formsection_control_options'>
                                <div class='formsection_control_option'><input type='checkbox' class='formsection_permissions_checkbox checkbox_type_1' data-flag='emes'/>Edit Roles / Shifts / Permissions</div>
                            </div>
                        </div>         
                    </div>
                </div>
            ";

            $returnedCode .= "
            </div>
            <div class='popup_footer'>
            <div id='submit_new_role' class='button_type_2'>Save</div><div class='button_type_1' id='btn_close_popup'>Discard</div>
            </div>
            ";

            if ($_roleid > 0) {
                $roleInformation = json_encode(DatabaseManager::GetRole($_dbInfo, $_roleid));

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

        public static function GenerateEmployeeSettingsPage($_dbInfo) {
            if (!DatabaseManager::CheckPermissions($_dbInfo, ['emes'])) {
                die("You do not have permission to view this page. Speak to your account manager to gain access.");
            }
            $returnedCode = <<<HTML
                <script type='text/javascript'>
                    InitTimePickers();
                    $('.btn_open_new_role').click(function() {
                        $('.popup_darken').fadeIn(500);
                        $('.popup_wrapper').fadeIn(500);
                        SetLoadingIcon('.popup_content');
                        var requestData = [
                            {name: 'action', value: 'GenerateNewRolePage'}
                        ];
                        CancelAllAjaxCalls();
                        AjaxCall(xhrArray, requestData, function(status, response) {
                            if (status) {
                                $('.popup_content').html(response).fadeIn(500);
                            }
                        });
                    });
                    $('.btn_open_new_shift').click(function() {
                        $('.popup_darken').fadeIn(500);
                        $('.popup_wrapper').fadeIn(500);
                        SetLoadingIcon('.popup_content');
                        var requestData = [
                            {name: 'action', value: 'GenerateNewShiftPage'}
                        ];
                        CancelAllAjaxCalls();
                        AjaxCall(xhrArray, requestData, function(status, response) {
                            if (status) {
                                $('.popup_content').html(response).fadeIn(500);
                            }
                        });
                    });
                    $('.schedule_enable').change(function() {
                        var timeField = $(this).closest('.formsection_line_leftjustify').children('.formsection_toggle_time_fields');
                        if (timeField.is(':visible')) {
                            timeField.fadeOut(200);
                        }
                        else {
                            timeField.fadeIn(200);
                        }
                    });
                </script>
            HTML;

            $getRoles = DatabaseManager::GetRoles($_dbInfo);
            $rolesList = ViewEmployeeRollsList::GenerateListItems($getRoles);

            $getShifts = DatabaseManager::GetShifts($_dbInfo);
            $shiftsList = ViewShiftsList::GenerateListItems($getShifts);

            $returnedCode .= "<div id='rightpane_viewport' style='top:0px'>";

            // Get Settings
            /*$returnedCode .= 
            "
                <div class='formsection_width_unset' style='width:800px'>
                    <div class='formsection_header'>
                    Default Working Hours
                    </div>
                    <div class='formsection_content'>
                        <div class='formsection_subheader_title' style='padding-top:10px;padding-bottom:10px;'>
                        Enter the general weekly business hours for employees. When this setting is used, employees will be scheduled for these hours automatically.
                        </div>
                        <div class='formsection_line_leftjustify'><div class='formsection_label_1'>Monday:</div>
                        <div class='checkbox_switch'>
                            <label class='switch'>
                                <input type='checkbox' class='schedule_enable' checked>
                                <span class='slider round'></span>
                            </label>
                        </div>
                        <div class='formsection_toggle_time_fields'>
                        <input type='text' data-defaulttime='09:00am' class='formsection_input_fixed_2 formsection_input_timepicker' />
                        to
                        <input type='text' data-defaulttime='05:00pm' class='formsection_input_fixed_2 formsection_input_timepicker' />
                        </div>
                        </div>
                        <div class='formsection_line_leftjustify'><div class='formsection_label_1'>Tuesday:</div>
                        <div class='checkbox_switch'>
                            <label class='switch'>
                                <input type='checkbox' class='schedule_enable' checked>
                                <span class='slider round'></span>
                            </label>
                        </div>
                        <div class='formsection_toggle_time_fields'>
                        <input type='text' data-defaulttime='09:00am' class='formsection_input_fixed_2 formsection_input_timepicker' />
                        to
                        <input type='text' data-defaulttime='05:00pm' class='formsection_input_fixed_2 formsection_input_timepicker' />
                        </div>
                        </div>
                        <div class='formsection_line_leftjustify'><div class='formsection_label_1'>Wednesday:</div>
                        <div class='checkbox_switch'>
                            <label class='switch'>
                                <input type='checkbox' class='schedule_enable' checked>
                                <span class='slider round'></span>
                            </label>
                        </div>
                        <div class='formsection_toggle_time_fields'>
                        <input type='text' data-defaulttime='09:00am' class='formsection_input_fixed_2 formsection_input_timepicker' />
                        to
                        <input type='text' data-defaulttime='05:00pm' class='formsection_input_fixed_2 formsection_input_timepicker' />
                        </div>
                        </div>
                        <div class='formsection_line_leftjustify'><div class='formsection_label_1'>Thursday:</div>
                        <div class='checkbox_switch'>
                            <label class='switch'>
                                <input type='checkbox' class='schedule_enable' checked>
                                <span class='slider round'></span>
                            </label>
                        </div>
                        <div class='formsection_toggle_time_fields'>
                        <input type='text' data-defaulttime='09:00am' class='formsection_input_fixed_2 formsection_input_timepicker' />
                        to
                        <input type='text' data-defaulttime='05:00pm' class='formsection_input_fixed_2 formsection_input_timepicker' />
                        </div>
                        </div>
                        <div class='formsection_line_leftjustify'><div class='formsection_label_1'>Friday:</div>
                        <div class='checkbox_switch'>
                            <label class='switch'>
                                <input type='checkbox' class='schedule_enable' checked>
                                <span class='slider round'></span>
                            </label>
                        </div>
                        <div class='formsection_toggle_time_fields'>
                        <input type='text' data-defaulttime='09:00am' class='formsection_input_fixed_2 formsection_input_timepicker' />
                        to
                        <input type='text' data-defaulttime='05:00pm' class='formsection_input_fixed_2 formsection_input_timepicker' />
                        </div>
                        </div>
                        <div class='formsection_line_leftjustify'><div class='formsection_label_1'>Saturday:</div>
                        <div class='checkbox_switch'>
                            <label class='switch'>
                                <input type='checkbox' class='schedule_enable'>
                                <span class='slider round'></span>
                            </label>
                        </div>
                        <div class='formsection_toggle_time_fields' style='display:none;'>
                        <input type='text' data-defaulttime='09:00am' class='formsection_input_fixed_2 formsection_input_timepicker' />
                        to
                        <input type='text' data-defaulttime='05:00pm' class='formsection_input_fixed_2 formsection_input_timepicker' />
                        </div>
                        </div>
                        <div class='formsection_line_leftjustify'><div class='formsection_label_1'>Sunday:</div>
                        <div class='checkbox_switch'>
                            <label class='switch'>
                                <input type='checkbox' class='schedule_enable'>
                                <span class='slider round'></span>
                            </label>
                        </div>
                        <div class='formsection_toggle_time_fields' style='display:none;'>
                        <input type='text' data-defaulttime='09:00am' class='formsection_input_fixed_2 formsection_input_timepicker' />
                        to
                        <input type='text' data-defaulttime='05:00pm' class='formsection_input_fixed_2 formsection_input_timepicker' />
                        </div>
                        </div>
                    </div>
                </div>";*/

                $returnedCode .= 
                   "<div class='formsection_width_unset' style='width:800px'>
                    <div class='formsection_header'>
                    Employee Roles / Permissions
                    </div>
                    <div class='formsection_content'>
                        <div class='formsection_subheader_title'>
                            <div class='formsection_line_centered_between'>
                                This is a list of your employee roles and the permissions for each role.
                                <div class='button_type_1 btn_open_new_role'><img src='img/add_user_green.png' style ='width:20px;padding-right:10px;'/>New Role</div>
                            </div>
                        </div>
                        <div style='padding-left:40px;' id='display_employee_roles'>
                        $rolesList
                        </div>
                    </div>
                </div>   
            ";

            $returnedCode .= 
                   "<div class='formsection_width_unset' style='width:800px;margin-top:20px;'>
                    <div class='formsection_header'>
                    Employee Shifts
                    </div>
                    <div class='formsection_content'>
                        <div class='formsection_subheader_title'>
                            <div class='formsection_line_centered_between'>
                                This is a list of assignable shifts for your employees.
                                <div class='button_type_1 btn_open_new_shift'><img src='img/add_user_green.png' style ='width:20px;padding-right:10px;'/>New Shift</div>
                            </div>
                        </div>
                        <div style='padding-left:40px;' id='display_shifts'>
                        $shiftsList
                        </div>
                    </div>
                </div>   
            ";

            $returnedCode .= "</div>";
            return $returnedCode;
        }

        public static function GenerateSettingsPage($_dbInfo) {

            $returnedCode = <<<HTML
                <script type='text/javascript'>
                    function OpenNewEmployeePage() {
                        $('.popup_darken').fadeIn(500);
                        $('.popup_wrapper').fadeIn(500);
                        SetLoadingIcon('.popup_scrollable');
                        var requestData = [
                            {name: 'action', value: 'GenerateNewEmployeePage'}
                        ];
                        CancelAllAjaxCalls();
                        AjaxCall(xhrArray, requestData, function(status, response) {
                            if (status) {
                                $('.popup_content').html(response).show();
                            }
                        });
                    }
                    $('.btn_newemployeedialogue').click(function() {
                        OpenNewEmployeePage();
                    });
                </script>
            HTML;

            $returnedCode .= "<div id='rightpane_viewport' style='top:0px'>";

            // Get Settings
            $returnedCode .= "<div class='formsection_line'>";
            $returnedCode .= "<div class='button_type_1'>Dashboard Settings</div>";
            $returnedCode .= "</div>";
            $returnedCode .= "<div class='formsection_line'>";
            $returnedCode .= "<div class='button_type_1'>Dashboard Settings</div>";
            $returnedCode .= "</div>";
            $returnedCode .= "<div class='formsection_line'>";
            $returnedCode .= "<div class='button_type_1'>Dashboard Settings</div>";
            $returnedCode .= "</div>";
            $returnedCode .= "<div class='formsection_line'>";
            $returnedCode .= "<div class='button_type_1'>Dashboard Settings</div>";
            $returnedCode .= "</div>";
            $returnedCode .= "<div class='formsection_line'>";
            $returnedCode .= "<div class='button_type_1'>Dashboard Settings</div>";
            $returnedCode .= "</div>";
            $returnedCode .= "<div class='formsection_line'>";
            $returnedCode .= "<div class='button_type_1'>Dashboard Settings</div>";
            $returnedCode .= "</div>";
            $returnedCode .= "<div class='formsection_line'>";
            $returnedCode .= "<div class='button_type_1'>Dashboard Settings</div>";
            $returnedCode .= "</div>";
            $returnedCode .= "<div class='formsection_line'>";
            $returnedCode .= "<div class='button_type_1'>Dashboard Settings</div>";
            $returnedCode .= "</div>";
            $returnedCode .= "<div class='formsection_line'>";
            $returnedCode .= "<div class='button_type_1'>Dashboard Settings</div>";
            $returnedCode .= "</div>";

           
            $returnedCode .= "<div id='rightpane_footer'>";
            $returnedCode .= "</div>";
            return $returnedCode;
        }

        public static function GenerateViewAccountPage($_dbInfo, $_accountid) {

            $returnedCode = "";
            // Permission Check
            if (!DatabaseManager::CheckPermissions($_dbInfo, ['va'])) {
                die("You do not have permission to view this page. Speak to your account manager to gain access.");
            }

            $accountInfo = DatabaseManager::GetAccount($_dbInfo, $_accountid);
            $primaryContactInfo = DatabaseManager::GetContact($_dbInfo, $accountInfo['primarycontactid']);

            if (!is_array($accountInfo)) {
                die();
            }

            if (count($accountInfo) <= 0) {
                die();
            }
            $retArray = "";

            $returnedCode .= "<script type='text/javascript'>           

            </script>";

            $returnedCode .= "<div id='rightpane_header'>";
            $returnedCode .= "<div class='rightpane_fullheader'>";
            $returnedCode .= $accountInfo['name'];
            $returnedCode .= "</div>";
            $returnedCode .= "</div><div id='rightpane_viewport' style='top:50px'>";

            $returnedCode .= "<div class='formsection_row_even'>";

            $returnedCode .= "<div class='formsectionhalf'>";
            $returnedCode .= "<div class='formsection_header'>Account Details</div>";
            $returnedCode .= ViewAccount::GenerateAccountDetails($accountInfo);
            $returnedCode .= "</div>";

            $returnedCode .= "<div class='formsectionhalf'>";
            $returnedCode .= "<div class='formsection_header'>Contact Information</div>";
            $returnedCode .= ViewAccount::GenerateAccountContactDetails($primaryContactInfo);
            $returnedCode .= "</div>";

            $returnedCode .= "</div>";

            $returnedCode .= "<div class='formsectionfull'>";
            $returnedCode .= "<div class='formsection_header'>WorkOrders</div>";

            $returnedCode .= "</div>";

            $returnedCode .= "<div class='formsectionfull'>";
            $returnedCode .= "<div class='formsection_header'>Quotes</div>";

            $returnedCode .= "</div>";

            $returnedCode .= "<div class='formsectionfull'>";
            $returnedCode .= "<div class='formsection_header'>Service Locations/Units</div>";

            $returnedCode .= "</div>";

            $returnedCode .= "<div class='formsectionfull'>";
            $returnedCode .= "<div class='formsection_header'>Invoices</div>";

            $returnedCode .= "</div>";

            $returnedCode .= "</div>";

            return $returnedCode;
        }

        public static function GenerateNewEmployeePage($_dbInfo) {
            $returnedCode = "";
            // Permission Check
            if (!DatabaseManager::CheckPermissions($_dbInfo, ['ce'])) {
                die("You do not have permission to view this page. Speak to your account manager to gain access.");
            }

            $driversLicenseTemplate = "
            <div class='formsection_line'>
                <input type='text' class='formsection_input formsection_serialize' data-validation='name' data-serialize='dlNumber' placeholder='License Number'/>
                <input type='text' class='formsection_input formsection_serialize formsection_date_my_mask' data-validation='date_my' data-serialize='dlExpiration' placeholder='Expiration (month/year)'/>
            </div>
            ";

            $personalVehicleInformationTemplate = "
            <div class='formsection_subheader_title' style='padding-top:6px;'>Personal Vehicle Information</div>
            <div class='formsection_line'>
                <input type='text' class='formsection_input formsection_serialize' data-validation='name' data-serialize='pvMake' placeholder='Make'/>
                <input type='text' class='formsection_input formsection_serialize' data-validation='name' data-serialize='pvModel' placeholder='Model'/>                           
            </div>
            <div class='formsection_line'>
                <input type='text' class='formsection_input formsection_serialize' data-validation='name' data-serialize='pvColor' placeholder='Color'/>
                <input type='text' class='formsection_input formsection_serialize' data-validation='name_nonrequired' data-serialize='pvPlate' placeholder='Plate #'/>
                <input type='text' class='formsection_input formsection_serialize' data-validation='year' data-serialize='pvYear' placeholder='Year'/>                          
            </div>
            ";

            $companyVehicleInformationTemplate = "
            <div class='formsection_subheader_title' style='padding-top:6px;'>Company Vehicle Information</div>
            <div class='formsection_line'>
                <input type='text' class='formsection_input formsection_serialize' data-validation='name' data-serialize='cvMake' placeholder='Make'/>
                <input type='text' class='formsection_input formsection_serialize' data-validation='name' data-serialize='cvModel' placeholder='Model'/>
                <input type='text' class='formsection_input formsection_serialize' data-validation='year' data-serialize='cvYear' placeholder='Year'/>                           
            </div>
            <div class='formsection_line'>
                <input type='text' class='formsection_input formsection_serialize' data-validation='name' data-serialize='cvVID' placeholder='VIN #'/>
                <input type='text' class='formsection_input formsection_serialize' data-validation='name' data-serialize='cvPlate' placeholder='Plate #'/>
                <input type='text' class='formsection_input formsection_serialize formsection_date_my_mask' data-validation='date_my' data-serialize='cvRegExp' placeholder='Reg Exp (month/year)'/>        
            </div>
            ";

            $returnedCode .= <<<HTML
                <script>
                    $("#btn_close_popup").click(function () {
                        ClosePopup();
                    });
                    $('#submit_new_employee_form').click(function () {

                        if(!$(this).hasClass('disabled')) {
                            $(this).addClass('disabled');
                        }

                        var formattedString = SerializeNewEmployeeForm();
                        var formInfo = JSON.stringify(formattedString['formInformation']);

                        if (formattedString.success) {
                            var requestData = [
                            {name: 'action', value: 'SubmitNewEmployeeForm'},
                            {name: 'formdata', value: formInfo}
                            ];
                            CancelAllAjaxCalls();
                            AjaxCall(xhrArray, requestData, function(status, response) {
                                if (status) {
                                    var resVar = response.split('|');
                                    if (resVar[0] == 'true') {
                                        $('.popup_wrapper').hide();
                                        $('.popup_darken').fadeOut(400);
                                        ClickLeftPaneMenuItem('Employees', false);
                                    }
                                    else {
                                        $('.popup_scrollable').prepend("<div class='formsection_line_centered'><div class='formsection_input_centered_text'>" + resVar[1] + "</div></div>");
                                    }
                                    if($('#submit_new_employee_form').hasClass('disabled')) {
                                      $('#submit_new_employee_form').removeClass('disabled');
                                    }
                                }
                                else {
                                    location.reload(true);
                                }
                            });
                        }
                        else {
                            if($('#submit_new_employee_form').hasClass('disabled')) {
                              $('#submit_new_employee_form').removeClass('disabled');
                            }
                        }
                    });
                    $(document).on('change', '.checkbox_drivers_license', function() {
                        var toggleableChildren = $('.formsection_drivers_license');

                        var driversLicenseTemplate = `$driversLicenseTemplate`;

                        if (!$(this).is(':checked')) {
                            toggleableChildren.slideUp({
                                duration: 200,
                                step: function(now, fx) {
                                    var scrollView = $('.popup_scrollable');
                                    scrollView.scrollTop(scrollView[0].scrollHeight);
                                    scrollView.css('overflow-y', 'scroll');
                                    $('html, body').scrollTop(scrollView.offset().top);
                                },
                                complete: function() {
                                    var scrollView = $('.popup_scrollable');
                                    scrollView.css('overflow-y', 'scroll');
                                    $(toggleableChildren).html("");  
                                }
                            });
                        } else {
                            toggleableChildren.hide().html(driversLicenseTemplate).slideDown({
                                duration: 200,
                                step: function(now, fx) {
                                    var scrollView = $('.popup_scrollable');
                                    scrollView.scrollTop(scrollView[0].scrollHeight);
                                    scrollView.css('overflow-y', 'scroll');
                                    $('html, body').scrollTop(scrollView.offset().top);
                                },
                                complete: function() {
                                    var scrollView = $('.popup_scrollable');
                                    scrollView.css('overflow-y', 'scroll');
                                    InitInputMasks();
                                }
                            });
                        }
                    });
                    $(document).on('change', '.checkbox_company_vehicle_information', function() {
                        var toggleableChildren = $('.formsection_company_vehicle_information');

                        var companyVehicleInformationTemplate = `$companyVehicleInformationTemplate`;

                        if (!$(this).is(':checked')) {
                            toggleableChildren.slideUp({
                                duration: 200,
                                step: function(now, fx) {
                                    var scrollView = $('.popup_scrollable');
                                    scrollView.scrollTop(scrollView[0].scrollHeight);
                                    scrollView.css('overflow-y', 'scroll');
                                    $('html, body').scrollTop(scrollView.offset().top);
                                },
                                complete: function() {
                                    var scrollView = $('.popup_scrollable');
                                    scrollView.css('overflow-y', 'scroll');
                                    $(toggleableChildren).html("");  
                                }
                            });
                        } else {
                            toggleableChildren.hide().html(companyVehicleInformationTemplate).slideDown({
                                duration: 200,
                                step: function(now, fx) {
                                    var scrollView = $('.popup_scrollable');
                                    scrollView.scrollTop(scrollView[0].scrollHeight);
                                    scrollView.css('overflow-y', 'scroll');
                                    $('html, body').scrollTop(scrollView.offset().top);
                                },
                                complete: function() {
                                    var scrollView = $('.popup_scrollable');
                                    scrollView.css('overflow-y', 'scroll');
                                    InitInputMasks();
                                }
                            });
                        }
                    });
                    $(document).on('change', '.checkbox_personal_vehicle_information', function() {
                        var toggleableChildren =$('.formsection_personal_vehicle_information');

                        var personalVehicleInformationTemplate = `$personalVehicleInformationTemplate`;

                        if (!$(this).is(':checked')) {
                            toggleableChildren.slideUp({
                                duration: 200,
                                step: function(now, fx) {
                                    var scrollView = $('.popup_scrollable');
                                    scrollView.scrollTop(scrollView[0].scrollHeight);
                                    scrollView.css('overflow-y', 'scroll');
                                    $('html, body').scrollTop(scrollView.offset().top);
                                },
                                complete: function() {
                                    var scrollView = $('.popup_scrollable');
                                    scrollView.css('overflow-y', 'scroll');
                                    $(toggleableChildren).html("");  
                                }
                            });
                        } else {
                            toggleableChildren.hide().html(personalVehicleInformationTemplate).slideDown({
                                duration: 200,
                                step: function(now, fx) {
                                    var scrollView = $('.popup_scrollable');
                                    scrollView.scrollTop(scrollView[0].scrollHeight);
                                    scrollView.css('overflow-y', 'scroll');
                                    $('html, body').scrollTop(scrollView.offset().top);
                                },
                                complete: function() {
                                    var scrollView = $('.popup_scrollable');
                                    scrollView.css('overflow-y', 'scroll');
                                    InitInputMasks();
                                }
                            });
                        }
                    });
                    InitInputMasks();
                </script>
            HTML;

            $returnedCode .= "
            <div class='popup_topbar'><span style='color:white;'>New</span> Employee</div>
            <div class='popup_scrollable'>
                
                    <div class='formsectionfull' id='formsection_employee_details'>
                        <div class='formsection_header'>Employee Details</div>
                        <div class='formsection_content'>
                            <div class='formsection_line'>
                                <input type='text' class='formsection_input formsection_serialize' data-validation='name' data-serialize='firstName' placeholder='First Name'/><input type='text' class='formsection_input formsection_serialize' data-validation='name' data-serialize='lastName' placeholder='Last Name'/>
                            </div>
                            <div class='formsection_line'>
                                <input type='text' class='formsection_input formsection_serialize' data-validation='address' data-serialize='street1' placeholder='Address Line 1'/>
                            </div>
                            <div class='formsection_line'>
                                <input type='text' class='formsection_input formsection_serialize' data-validation='address_nonrequired' data-serialize='street2' placeholder='Address Line 2'/>
                            </div>
                            <div class='formsection_line'>
                                <input type='text' class='formsection_input formsection_serialize' data-validation='name' data-serialize='city' placeholder='City'/><input type='text' class='formsection_input_fixed formsection_serialize' data-validation='state' data-serialize='state' maxlength='2' placeholder='AZ'/><input type='text' class='formsection_input formsection_serialize' data-validation='zipCode' data-serialize='zipCode' placeholder='Zip Code'/>
                            </div>
                            <div class='formsection_line'>
                                <input type='text' class='formsection_input formsection_serialize formsection_phone_mask' data-validation='phone' data-serialize='phone' placeholder='Phone Number'/><input type='text' class='formsection_input formsection_serialize' data-validation='email' data-serialize='email' placeholder='Email'/>
                            </div>
                            <div class='formsection_line_width_unset' style='width:200px;'>
                                <input type='text' class='formsection_input formsection_serialize formsection_date_full_mask' data-validation='date' data-serialize='dob' placeholder='Date of Birth (day/month/year)'/>
                            </div>
                        </div>
                    </div>
                    <div class='formsectionfull' id='formsection_business_contact_information'>
                        <div class='formsection_header'>Business Contact Information</div>
                        <div class='formsection_content'>
                            <div class='formsection_subheader_title' style='padding-top:6px;'>*Work Email will be used as the login email</div>
                            <div class='formsection_line'>
                                <input type='text' class='formsection_input formsection_serialize formsection_phone_mask' data-validation='phone_nonrequired' data-serialize='workPhone' placeholder='Work Phone'/>
                                <input type='text' class='formsection_input formsection_serialize' data-validation='email' data-serialize='workEmail' placeholder='Work Email'/>
                            </div>
                        </div>
                    </div>
                    <div class='formsectionfull'>
                        <div class='formsection_header'>Scheduling</div>
                        <div class='formsection_content'>
                        </div>
                    </div>
                    <div class='formsectionfull' id='formsection_vehicle_information'>
                        <div class='formsection_header'>Vehicle Information</div>
                        <div class='formsection_content'>
                            <div class='formsection_line'>
                                <div class='checkbox_switch'>
                                    <label class='switch'>
                                        <input type='checkbox' class='checkbox_drivers_license'>
                                        <span class='slider round'></span>
                                    </label>
                                    <span class='checkbox_switch_label'>Employee is approved to drive</span>
                                </div>
                            </div>
                        </div>
                        <div class='formsection_drivers_license' style='display:none;'>     
                        </div>
                        <div class='formsection_line'>
                            <div class='checkbox_switch'>
                                <label class='switch'>
                                    <input type='checkbox' class='checkbox_personal_vehicle_information'>
                                    <span class='slider round'></span>
                                </label>
                                <span class='checkbox_switch_label'>Add Personal Vehicle Information</span>
                            </div>
                        </div>
                        <div class='formsection_personal_vehicle_information' style='display:none;'>
                        </div>
                        <div class='formsection_line'>
                            <div class='checkbox_switch'>
                                <label class='switch'>
                                    <input type='checkbox' class='checkbox_company_vehicle_information'>
                                    <span class='slider round'></span>
                                </label>
                                <span class='checkbox_switch_label'>Add Company Vehicle Information</span>
                            </div>
                        </div>
                        <div class='formsection_company_vehicle_information' style='display:none;'>
                            
                        </div>
                    </div>
                    <div class='formsectionfull' id='formsection_newaccount_details'>
                        <div class='formsection_header'>Notes</div>
                        <div class='formsection_content'>

                        </div>
                    </div>
                </div>
            ";

            $returnedCode .= "<div class='popup_footer'>
            <div id='submit_new_employee_form' class='button_type_2'>Save</div><div class='button_type_1' id='btn_close_popup'>Discard</div>
            </div>
            ";

            return $returnedCode;
        }

        public static function GenerateSettingsMenu($_dbInfo) {
            $myPerms = DatabaseManager::GetUserPermissions($_dbInfo);

            $returnedCode = <<<HTML
                <script type='text/javascript'>
                    $('.open_employee_settings_page').click(function() { 
                        var requestData = [
                        {name: 'action', value: 'LeftPaneButtonClick'},
                        {name: 'buttonid', value: 'EmployeeSettings'}
                        ];
                        SetLoadingIcon('#rightpane_container');
                        CancelAllAjaxCalls();
                        AjaxCall(xhrArray, requestData, function(status, response) {
                            if (status) {
                                $("#rightpane_container").html(response);
                            }
                        });
                    });
                </script>
            HTML;

            $returnedCode .= "<div class='settingsmenu_header'>Settings</div>
            <div class='settingsmenu_divider'></div>
            <div class='settingsmenu_button'><img src='img/report_green.png' width='30px' style='padding-right:10px;'/>Dashboard</div>
            <div class='settingsmenu_button'><img src='img/report_green.png' width='30px' style='padding-right:10px;'/>Dispatch</div>
            <div class='settingsmenu_button'><img src='img/report_green.png' width='30px' style='padding-right:10px;'/>Message</div>
            <div class='settingsmenu_button'><img src='img/report_green.png' width='30px' style='padding-right:10px;'/>Work Order</div>
            <div class='settingsmenu_button'><img src='img/report_green.png' width='30px' style='padding-right:10px;'/>Serice Report</div>
            <div class='settingsmenu_button'><img src='img/report_green.png' width='30px' style='padding-right:10px;'/>Invoice</div>
            <div class='settingsmenu_button'><img src='img/report_green.png' width='30px' style='padding-right:10px;'/>Account</div>";

            if (DatabaseManager::ManuallyCheckPermissions($myPerms, ["emes"])) {
                $returnedCode .= "<div class='settingsmenu_button open_employee_settings_page'><img src='img/tech_green.png' width='30px' style='padding-right:10px;'/>Employee Hours / Roles</div>";
            }
            
            $returnedCode .= "
            <div class='settingsmenu_button'><img src='img/report_green.png' width='30px' style='padding-right:10px;'/>Report</div>
            <div class='settingsmenu_button'><img src='img/report_green.png' width='30px' style='padding-right:10px;'/>Inventory</div>
            <div class='settingsmenu_button'><img src='img/report_green.png' width='30px' style='padding-right:10px;'/>Contract Builder</div>
            <div class='settingsmenu_button'><img src='img/report_green.png' width='30px' style='padding-right:10px;'/>Branding</div>";
            return $returnedCode;
        }

        public static function GenerateNewAccountPage($_dbInfo) {
            $returnedCode = "";
            // Permission Check
            if (!DatabaseManager::CheckPermissions($_dbInfo, ['ca'])) {
                die("You do not have permission to view this page. Speak to your account manager to gain access.");
            }

            $billingTemplate = "
            <div class='formsection_line'>
                <input type='text' class='formsection_input formsection_serialize' data-validation='name' data-serialize='firstName' placeholder='Billing Contact First Name'/><input type='text' class='formsection_input formsection_serialize' data-validation='name' data-serialize='lastName' placeholder='Billing Contact Last Name'/>
            </div>
            <div class='formsection_line'>
                <input type='text' class='formsection_input formsection_serialize' data-validation='address' data-serialize='street1' placeholder='Billing Address Line 1'/>
            </div>
            <div class='formsection_line'>
                <input type='text' class='formsection_input formsection_serialize' data-validation='address_nonrequired' data-serialize='street2' placeholder='Billing Address Line 2'/>
            </div>
            <div class='formsection_line'>
                <input type='text' class='formsection_input formsection_serialize' data-validation='name' data-serialize='city' placeholder='City'/><input type='text' class='formsection_input_fixed formsection_serialize' data-validation='state' data-serialize='state' maxlength='2' placeholder='AZ'/><input type='text' class='formsection_input formsection_serialize' data-validation='zipCode' data-serialize='zipCode' placeholder='Zip Code'/>
            </div>
            <div class='formsection_line'>
                <input type='text' class='formsection_input formsection_serialize formsection_phone_mask' data-validation='phone' data-serialize='primaryPhone' placeholder='Billing Primary Phone'/><input type='text' class='formsection_input formsection_serialize formsection_phone_mask' data-validation='phone_nonrequired' data-serialize='secondaryPhone' placeholder='Billing Secondary Phone'/>
            </div>
            <div class='formsection_line'>
                <input type='text' class='formsection_input formsection_serialize' data-validation='email' data-serialize='email' placeholder='Billing Email'/>
            </div>
            ";

            $serviceAddressContactTemplate = "
            <div class='formsection_subheader_title'>Location Contact Information</div>
                <div class='formsection_line'>
                    <input type='text' class='formsection_input formsection_serialize' data-validation='name_nonrequired' data-serialize='contact_title' placeholder='Title'/>
                </div>
                <div class='formsection_line'>
                    <input type='text' class='formsection_input formsection_serialize' data-validation='name' data-serialize='contact_firstName' placeholder='Contact First Name'/><input type='text' class='formsection_input formsection_serialize' data-validation='name' data-serialize='contact_lastName' placeholder='Contact Last Name'/>
                </div>
                <div class='formsection_line'>
                    <input type='text' class='formsection_input formsection_serialize formsection_phone_mask' data-validation='phone' data-serialize='contact_primaryPhone' placeholder='Primary Phone'/><input type='text' class='formsection_input formsection_serialize formsection_phone_mask' data-validation='phone_nonrequired' data-serialize='contact_secondaryPhone' placeholder='Secondary Phone'/>
                </div>
                <div class='formsection_line'>
                    <input type='text' class='formsection_input formsection_serialize' data-validation='email' data-serialize='contact_email' placeholder='Email'/>
                </div>
            </div>";

            $serviceAddressTemplate = "
            <div class='formsection_subheader formsection_location_entry'>
                <div class='formsection_subheader_title'>Location 1</div>
                <div class='formsection_line'>
                    <input type='text' class='formsection_input formsection_serialize' data-serialize='name' data-validation='name_nonrequired' placeholder='Location Name'/>
                </div>
                <div class='formsection_line'>
                    <input type='text' class='formsection_input formsection_serialize' data-serialize='street1' data-validation='address' placeholder='Address Line 1'/>
                </div>
                <div class='formsection_line'>
                    <input type='text' class='formsection_input formsection_serialize' data-serialize='street2' data-validation='address_nonrequired' placeholder='Address Line 2'/>
                </div>
                <div class='formsection_line'>
                    <input type='text' class='formsection_input formsection_serialize' data-serialize='city' data-validation='name' placeholder='City'/><input type='text' class='formsection_input_fixed formsection_serialize' data-serialize='state' data-validation='state' maxlength='2' placeholder='AZ'/><input type='text' class='formsection_input formsection_serialize' data-serialize='zipCode' data-validation='zipCode' placeholder='Zip Code'/>
                </div>
                <div class='formsection_line'>
                    <div class='checkbox_switch'>
                        <label class='switch'>
                            <input type='checkbox' class='checkbox_contact_isprimary formsection_serialize' data-validation='none' data-serialize='copyContact' checked>
                            <span class='slider round'></span>
                        </label>
                        <span class='checkbox_switch_label'>Account Contact is Location Contact</span>
                    </div>
                </div>
                <div class ='toggleable_contact_info' style='display:none;padding-top:4px;'>
                
            </div>";

            $returnedCode .= <<<HTML
                <script type="text/javascript">
                    $("#btn_close_popup").click(function () {
                        ClosePopup();
                    });
                    $('#submit_new_account_form').click(function () {

                        if(!$(this).hasClass('disabled')) {
                            $(this).addClass('disabled');
                        }

                        var formattedString = SerializeNewAccountForm();
                        var formInfo = JSON.stringify(formattedString['formInformation']);

                        if (formattedString.success) {
                            var requestData = [
                            {name: 'action', value: 'SubmitNewAccountForm'},
                            {name: 'formdata', value: formInfo}
                            ];
                            CancelAllAjaxCalls();
                            AjaxCall(xhrArray, requestData, function(status, response) {
                                if (status) {
                                    var resVar = response.split('|');
                                    if (resVar[0] == 'true') {
                                        $('.popup_wrapper').hide();
                                        // TODO: Add Ajax call to load the account in account view screen
                                        $('.popup_darken').fadeOut(400);
                                        ClickLeftPaneMenuItem('Accounts', false);
                                    }
                                    else {
                                        $('.popup_scrollable').prepend("<div class='formsection_line_centered'><div class='formsection_input_centered_text'>" + resVar[1] + "</div></div>");
                                    }
                                    if($('#submit_new_account_form').hasClass('disabled')) {
                                      $('#submit_new_account_form').removeClass('disabled');
                                    }
                                }
                                else {
                                    location.reload(true);
                                }
                            });
                        }
                        else {
                            if($('#submit_new_account_form').hasClass('disabled')) {
                              $('#submit_new_account_form').removeClass('disabled');
                            }
                        }
                    });
                    $('#copy_billing_address_checkbox').change(function() {
                        var billingTemplate = `$billingTemplate`;
                        if (this.checked) {
                            $('.billing_address_toggle_section').slideUp(200, function() {
                                $('.billing_address_toggle_section').html("");
                            });
                        } else {
                            $('.billing_address_toggle_section').html(billingTemplate).slideDown(200, function() {
                                InitInputMasks();
                            });
                        }
                    });
                    $('#formsection_cid_1_combo').change(function() { 
                        if (this.value !== 'Residential') {
                            $('#formsection_cid_1').slideDown(200);
                        } else {
                            $('#formsection_cid_1').slideUp(200);
                        }
                    });
                    $('#additional_service_addresses_btn').change(function() {
                        var serviceAddressTemplate = `$serviceAddressTemplate`;
                        if (this.checked) {
                            $('#formsection_locations_list').slideUp({
                                duration: 200,
                                step: function(now, fx) {
                                    var scrollView = $('.popup_scrollable');
                                    scrollView.scrollTop(scrollView[0].scrollHeight);
                                    scrollView.css('overflow-y', 'scroll');
                                    $('html, body').scrollTop(scrollView.offset().top);
                                },
                                complete: function() {
                                    var scrollView = $('.popup_scrollable');
                                    scrollView.css('overflow-y', 'scroll');
                                    $('#formsection_locations_list').html(""); 
                                }
                            });
                        } else {
                            $('#formsection_locations_list').html(serviceAddressTemplate).slideDown({
                                duration: 200,
                                step: function(now, fx) {
                                    var scrollView = $('.popup_scrollable');
                                    scrollView.scrollTop(scrollView[0].scrollHeight);
                                    scrollView.css('overflow-y', 'scroll');
                                    $('html, body').scrollTop(scrollView.offset().top);
                                },
                                complete: function() {
                                    var scrollView = $('.popup_scrollable');
                                    scrollView.css('overflow-y', 'scroll');
                                    InitInputMasks();
                                }
                            });
                        }
                    });
                    $(document).on('change', '.checkbox_contact_isprimary', function() {
                        var parentSection = $(this).closest('.formsection_subheader');
                        var toggleableChildren = parentSection.find('.toggleable_contact_info');

                        var serviceAddressContactTemplate = `$serviceAddressContactTemplate`;

                        if ($(this).is(':checked')) {
                            toggleableChildren.slideUp({
                                duration: 200,
                                step: function(now, fx) {
                                    var scrollView = $('.popup_scrollable');
                                    scrollView.scrollTop(scrollView[0].scrollHeight);
                                    scrollView.css('overflow-y', 'scroll');
                                    $('html, body').scrollTop(scrollView.offset().top);
                                },
                                complete: function() {
                                    var scrollView = $('.popup_scrollable');
                                    scrollView.css('overflow-y', 'scroll');
                                    $(toggleableChildren).html("");  
                                }
                            });
                        } else {
                            toggleableChildren.html(serviceAddressContactTemplate).slideDown({
                                duration: 200,
                                step: function(now, fx) {
                                    var scrollView = $('.popup_scrollable');
                                    scrollView.scrollTop(scrollView[0].scrollHeight);
                                    scrollView.css('overflow-y', 'scroll');
                                    $('html, body').scrollTop(scrollView.offset().top);
                                },
                                complete: function() {
                                    var scrollView = $('.popup_scrollable');
                                    scrollView.css('overflow-y', 'scroll');
                                    InitInputMasks();
                                }
                            });
                        }
                    });
                </script>
                HTML;

            $returnedCode .= "
            <div class='popup_topbar'><span style='color:white;'>New</span> Account</div><div class='popup_scrollable'>
                <div class='formsection_row'>
                    <div class='formsection' id='formsection_newaccount_details'>
                        <div class='formsection_header'>Account Details</div>
                        <div class='formsection_content'>
                            <div class='formsection_line'>
                                <select name='' class='formsection_input formsection_serialize' data-validation='contractType' data-serialize='type' id='formsection_cid_1_combo'>
                                    <option class='formsection_input_option' value='' disabled selected>Select Account Type</option>
                                    <option value='Residential'>Residential</option>
                                    <option value='Industrial'>Industrial</option>
                                    <option value='Commercial'>Commercial</option>
                                    <option value='Government'>Government</option>
                                </select>
                            </div>
                            <div class='formsection_line' id='formsection_cid_1' style='display:none;'>
                                <input type='text' class='formsection_input formsection_serialize' data-validation='name_nonrequired' data-serialize='name' placeholder='Account Name'/>
                            </div>
                            <div class='formsection_line'>
                                <input type='text' class='formsection_input formsection_serialize' data-validation='name' data-serialize='firstName' placeholder='Contact First Name'/><input type='text' class='formsection_input formsection_serialize' data-validation='name' data-serialize='lastName' placeholder='Contact Last Name'/>
                            </div>
                            <div class='formsection_line'>
                                <input type='text' class='formsection_input formsection_serialize' data-validation='address' data-serialize='street1' placeholder='Contact Address Line 1'/>
                            </div>
                            <div class='formsection_line'>
                                <input type='text' class='formsection_input formsection_serialize' data-validation='address_nonrequired' data-serialize='street2' placeholder='Contact Address Line 2'/>
                            </div>
                            <div class='formsection_line'>
                                <input type='text' class='formsection_input formsection_serialize' data-validation='name' data-serialize='city' placeholder='Contact City'/><input type='text' class='formsection_input_fixed formsection_serialize' data-validation='state' data-serialize='state' maxlength='2' placeholder='AZ'/><input type='text' class='formsection_input formsection_serialize' data-validation='zipCode' data-serialize='zipCode' placeholder='Zip Code'/>
                            </div>
                            <div class='formsection_line'>
                                <input type='text' class='formsection_input formsection_serialize formsection_phone_mask' data-validation='phone' data-serialize='primaryPhone' placeholder='Primary Phone'/><input type='text' class='formsection_input formsection_serialize formsection_phone_mask' data-validation='phone_nonrequired' data-serialize='secondaryPhone' placeholder='Secondary Phone'/>
                            </div>
                            <div class='formsection_line'>
                                <input type='text' class='formsection_input formsection_serialize' data-validation='email' data-serialize='email' placeholder='Email'/>
                            </div>
                        </div>
                    </div>
            ";

            $returnedCode .= "
                <div class='formsection' id='formsection_billing_info'>
                    <div class='formsection_header'>Billing Information</div>
                    <div class='formsection_content'>
                        <div class='formsection_line'>
                            <div class='checkbox_switch'>
                                <label class='switch'>
                                    <input type='checkbox' class='formsection_serialize' data-serialize='billing_is_same' id='copy_billing_address_checkbox' checked>
                                    <span class='slider round'></span>
                                </label>
                                <span class='checkbox_switch_label'>Billing Information is the same as Account Contact</span>
                            </div>
                        </div>
                        <div class='billing_address_toggle_section' style='display:none;'>

                        </div>
                    </div>
                </div>
                </div>
            ";

            $returnedCode .= "
                <div class='formsection'>
                    <div class='formsection_header'>Service Locations</div>
                    <div class='formsection_content'>
                        <div class='formsection_line'>
                            <div class='checkbox_switch'>
                                <label class='switch'>
                                    <input type='checkbox' id='additional_service_addresses_btn' data-validation='none' checked>
                                    <span class='slider round'></span>
                                </label>
                                <span class='checkbox_switch_label'>Contact Address is the Only Service Address</span>
                            </div>
                        </div>
                        <div class='list_of_service_addresses'>
                            <div id='formsection_locations_list' style='display:none'></div>
                            <div class='formsection_line_rightjustify' style='display:none;'>
                                <div class='button_type_3'>Add Another Location</div>
                            </div>
                        </div>
                    </div>
                </div>
            ";

            $returnedCode .= "</div>
            <div class='popup_footer'>
            <div id='submit_new_account_form' class='button_type_2'>Save</div><div class='button_type_1' id='btn_close_popup'>Discard</div>
            </div>
            ";

            $returnedCode .= <<<HTML
                <script>
                    InitInputMasks();
                </script>
            HTML;
            
            return $returnedCode;
        }
    }
?>