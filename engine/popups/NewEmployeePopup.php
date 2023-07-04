<?php

class PopupNewEmployee {
    public static function Generate($_dbInfo, $_postData) {
        $returnedCode = "";
        // Permission Check
        if (!DatabaseManager::CheckPermission('ce')) {
            die("You do not have permission to view this page. Speak to your account manager to gain access.");
        }

        $roleOptions = ListEmployeeRoles::AsSelect($_dbInfo);
        $shiftOptions = ListEmployeeShifts::AsSelect($_dbInfo);

        $driversLicenseTemplate = <<<HTML
        <div class='formsection_line'>
            <input type='text' class='formsection_input formsection_serialize' data-validation='name' data-serialize='dlNumber' placeholder='License Number'/>
            <input type='text' class='formsection_input formsection_serialize formsection_date_my_mask' data-validation='date_my' data-serialize='dlExpiration' placeholder='Expiration (month/year)'/>
        </div>
        HTML;

        $personalVehicleInformationTemplate = <<<HTML
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
        HTML;

        $companyVehicleInformationTemplate = <<<HTML
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
        HTML;

        $returnedCode .= <<<HTML
            <script>
                $('select').click(function () {
                    $(this).css("color", "black");
                });
                $("#btn_close_popup").click(function () {
                    ClosePopup();
                });
                $('#submit_new_employee_form').click(function () {

                    if(!$(this).hasClass('disabled')) {
                        $(this).addClass('disabled');
                    }

                    Action_AddNewEmployee();
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

        $returnedCode .= <<<HTML
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
                    <div class='formsection_header'>Role & Shift</div>
                    <div class='formsection_content'>
                    <div class='formsection_line'>
                        <select name='' class='formsection_input formsection_serialize formsection_select' style='color:#b2b0bd;' value='false' data-validation='selectnumvalue' data-serialize='role'>
                            <option class='formsection_input_option' value='false' disabled selected>Select Role</option>
                            $roleOptions;
                        </select>
                        <select name='' class='formsection_input formsection_serialize formsection_select' style='color:#b2b0bd;' value='false' data-validation='selectnumvalue' data-serialize='shift' id=''>
                            <option class='formsection_input_option' value='false' disabled selected>Select Shift</option>
                            $shiftOptions;
                        </select>
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
        <div class='popup_footer'>
            <div id='submit_new_employee_form' class='button_type_2'>Save</div><div class='button_type_1' id='btn_close_popup'>Discard</div>
        </div>
        HTML;

        return $returnedCode;
    }
}

?>