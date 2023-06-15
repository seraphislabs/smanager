<?php
    class PageManager {
        public static function GenerateAccountsPage($_dbInfo) {
            $returnedCode = "";
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
                OpenNewAccountPage()
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
            $returnedCode .= "<div class='listheaderbutton btn_newaccountdialogue'><img src='img/add_user_green.png' class='img_icon_small' style='margin-right:6px'/> New</div>";
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

        public static function GenerateViewAccountPage($_dbInfo, $_accountid) {

            $returnedCode = "";
            // Permission Check
            if (!DatabaseManager::CheckPermissions($_dbInfo, ['va'])) {
                die("You do not have permission to view this page. Speak to your account manager to gain access.");
            }

            // TODO: LOAD ACCOUNT INFO
            $accountInfo = DatabaseManager::GetAccount($_dbInfo, $_accountid);

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
            $returnedCode .= "</div><div id='rightpane_viewport' style='top:110px'>";
            $returnedCode .= "</div>";

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
                <input type='text' class='formsection_input formsection_serialize' data-validation='phone' data-serialize='primaryPhone' placeholder='Billing Primary Phone'/><input type='text' class='formsection_input formsection_serialize' data-validation='phone_nonrequired' data-serialize='secondaryPhone' placeholder='Billing Secondary Phone'/>
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
                    <input type='text' class='formsection_input formsection_serialize' data-validation='phone' data-serialize='contact_primaryPhone' placeholder='Primary Phone'/><input type='text' class='formsection_input formsection_serialize' data-validation='phone_nonrequired' data-serialize='contact_secondaryPhone' placeholder='Secondary Phone'/>
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
                            $('.billing_address_toggle_section').html(billingTemplate).slideDown(200);
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
                                }
                            });
                        }
                    });
                </script>
                HTML;

            $returnedCode .= "
            <div class='popup_topbar'><span style=\'color:white;\'>New</span> Account</div><div class='popup_scrollable'>
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
                                <input type='text' class='formsection_input formsection_serialize' data-validation='phone' data-serialize='primaryPhone' placeholder='Primary Phone'/><input type='text' class='formsection_input formsection_serialize' data-validation='phone_nonrequired' data-serialize='secondaryPhone' placeholder='Secondary Phone'/>
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
            ";
            
            return $returnedCode;
        }
    }
?>