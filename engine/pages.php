<?php
    class PageManager {
        public static function GeneratePopupFrame($_windowName, $_windowContent) {
            echo ("<div class='draggable-window'>
            <div class='window-header'>
              <h3 class='window-title'>$windowName</h3>
              <button class='window-close'>Close</button>
            </div>
            <div class='window-content'>
            $_windowContent
            </div>
          </div>");
        }
        public static function GenerateAccountsPage($_dbInfo, $_email, $_password, $_companyid, $_currentPage) {
            $returnedCode = "";
            // Permission Check
            if (!DatabaseManager::CheckPermissions($_dbInfo, $_email, $_password, ['va'])) {
                die("You do not have permission to view this page. Speak to your account manager to gain access.");
            }

            $retArray = DatabaseManager::GetAccounts($_dbInfo, $_email, $_password, $_companyid, $_currentPage);

            // Ensure its always at least 1
            if ($_currentPage <= 0) {
                $_currentPage = 1;
            }

            $returnedCode .= "<script type='text/javascript'>           
            function OpenNewAccountPage() {
            $('.popup_darken').fadeIn(500);
            $('.popup_wrapper').fadeIn(500);
            SetLoadingIcon('.popup_scrollable');
            var requestData = [
                {name: 'action', value: 'GenerateNewAccountPage'}
              ];
              AjaxCall(requestData, function(status, response) {
                if (status) {
                  $('.popup_topbar').html('<span style=\'color:white;\'>New</span> Account');
                  $('.popup_scrollable').html(response);
                }
              });
            }
            $('.btn_newaccountdialogue').click(function() {
                OpenNewAccountPage()
            });
            </script>";

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
            $returnedCode .= "</div><div id='rightpane_viewport' style='top:125px'>";

            $returnedCode .= ViewAccountList::GenerateListItems($retArray, $_currentPage);

            $returnedCode .= "</div>";

            $returnedCode .= self::GenerateAccountsViewListFooter($retArray, $_currentPage);
            return $returnedCode;
        }

        public static function GenerateNewAccountPage($_dbInfo, $_email, $_password) {
            $returnedCode = "";
            // Permission Check
            if (!DatabaseManager::CheckPermissions($_dbInfo, $_email, $_password, ['ca'])) {
                die("You do not have permission to view this page. Speak to your account manager to gain access.");
            }

            $serviceAddressTemplate = "
            <div class='formsection_subheader' data-location-id='1'>
                <div class='formsection_subheader_title'>Location 1</div>
                <div class='formsection_line'>
                    <input type='text' class='formsection_input' placeholder='Location Name'/>
                </div>
                <div class='formsection_line'>
                    <input type='text' class='formsection_input' placeholder='Address Line 1'/>
                </div>
                <div class='formsection_line'>
                    <input type='text' class='formsection_input' placeholder='Address Line 2'/>
                </div>
                <div class='formsection_line'>
                    <input type='text' class='formsection_input' placeholder='City'/><input type='text' class='formsection_input_fixed' maxlength='2' placeholder='AZ'/><input type='text' class='formsection_input' placeholder='Zip Code'/>
                </div>
                <div class='formsection_line'>
                    <div class='checkbox_switch'>
                        <label class='switch'>
                            <input type='checkbox' class='checkbox_contact_isprimary' checked>
                            <span class='slider round'></span>
                        </label>
                        <span class='checkbox_switch_label'>Account Contact is Location Contact</span>
                    </div>
                </div>
                <div class ='toggleable_contact_info' style='display:none;'>
                    <div class='formsection_line'>
                        <input type='text' class='formsection_input' placeholder='Title'/>
                    </div>
                    <div class='formsection_line'>
                        <input type='text' class='formsection_input' placeholder='Contact First Name'/><input type='text' class='formsection_input' placeholder='Contact Last Name'/>
                    </div>
                    <div class='formsection_line'>
                        <input type='text' class='formsection_input' placeholder='Contact Address Line 1'/>
                    </div>
                    <div class='formsection_line'>
                        <input type='text' class='formsection_input' placeholder='Contact Address Line 2'/>
                    </div>
                    <div class='formsection_line'>
                        <input type='text' class='formsection_input' placeholder='Contact City'/><input type='text' class='formsection_input_fixed' maxlength='2' placeholder='AZ'/><input type='text' class='formsection_input' placeholder='Zip Code'/>
                    </div>
                    <div class='formsection_line'>
                        <input type='text' class='formsection_input' placeholder='Primary Phone'/><input type='text' class='formsection_input' placeholder='Secondary Phone'/>
                    </div>
                    <div class='formsection_line'>
                        <input type='text' class='formsection_input' placeholder='Email'/>
                    </div>
                </div>
            </div>";

            $returnedCode .= "<script type='text/javascript'>
            $('#copy_billing_address_checkbox').change(function() {
                if(this.checked) {
                    $('.billing_address_toggle_section').slideUp(200);
                }
                else {
                    $('.billing_address_toggle_section').slideDown(200);
                }
            });
            $('#account_name_checkbox').change(function() { 
                if (this.checked) {
                    $('#formsection_cid_1').slideDown(200);
                }
                else {
                    $('#formsection_cid_1').slideUp(200);
                }
            });
            $('#additional_service_addresses_btn').change(function() {
                if (this.checked) {
                    $('.list_of_service_addresses').slideUp(200);
                }
                else {
                    $('.list_of_service_addresses').slideDown(200);
                }
            });
            $('.checkbox_contact_isprimary').change(function() {
                var \$parentSection = \$(this).closest('.formsection_subheader');
                var \$toggleableChildren = \$parentSection.find('.toggleable_contact_info');
                
                if ($(this).is(':checked')) {
                    \$toggleableChildren.slideUp({
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
                  } else {
                    \$toggleableChildren.slideDown({
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
            </script>";

            $returnedCode .= "
            <div class='formsection_row'>
                <div class='formsection'>
                <div class='formsection_header'>Account Details</div>
                <div class='formsection_content'>
                    <div class='formsection_line'>
                    <div class='checkbox_switch'>
                        <label class='switch'>
                            <input type='checkbox' id='account_name_checkbox'>
                            <span class='slider round'></span>
                        </label>
                        <span class='checkbox_switch_label'>Add a Custom Account Name</span>
                    </div>
                    </div>
                    <div class='formsection_line' id='formsection_cid_1' style='display:none;'>
                    <input type='text' class='formsection_input' placeholder='Account Name'/>
                    </div>
                    <div class='formsection_line'>
                    <select name='' class='formsection_input'>
                        <option class='formsection_input_option' value='' disabled selected>Select Account Type</option>
                        <option value='Residential'>Residential</option>
                        <option value='Industrial'>Industrial</option>
                        <option value='Commercial'>Commercial</option>
                        <option value='Government'>Government</option>
                    </select>
                    </div>
                    <div class='formsection_line'>
                    <input type='text' class='formsection_input' placeholder='Contact First Name'/><input type='text' class='formsection_input' placeholder='Contact Last Name'/>
                    </div>
                    <div class='formsection_line'>
                    <input type='text' class='formsection_input' placeholder='Contact Address Line 1'/>
                    </div>
                    <div class='formsection_line'>
                    <input type='text' class='formsection_input' placeholder='Contact Address Line 2'/>
                    </div>
                    <div class='formsection_line'>
                    <input type='text' class='formsection_input' placeholder='Contact City'/><input type='text' class='formsection_input_fixed' maxlength='2' placeholder='AZ'/><input type='text' class='formsection_input' placeholder='Zip Code'/>
                    </div>
                    <div class='formsection_line'>
                    <input type='text' class='formsection_input' placeholder='Primary Phone'/><input type='text' class='formsection_input' placeholder='Secondary Phone'/>
                    </div>
                    <div class='formsection_line'>
                    <input type='text' class='formsection_input' placeholder='Email'/>
                    </div>
                </div>
            </div>
            ";

            $returnedCode .= 
            "<div class='formsection'>
                <div class='formsection_header'>Billing Information</div>
                <div class='formsection_content'>
                <div class='formsection_line'>
                    <div class='checkbox_switch'>
                        <label class='switch'>
                            <input type='checkbox' id='copy_billing_address_checkbox' checked>
                            <span class='slider round'></span>
                        </label>
                        <span class='checkbox_switch_label'>Billing Information is the same as Account Contact</span>
                    </div>
                </div>
                <div class='billing_address_toggle_section' style='display:none;'>
                    <div class='formsection_line'>
                        <input type='text' class='formsection_input' placeholder='Billing Contact First Name'/><input type='text' class='formsection_input' placeholder='Billing Contact Last Name'/>
                    </div>
                    <div class='formsection_line'>
                        <input type='text' class='formsection_input' placeholder='Billing Address Line 1'/>
                    </div>
                    <div class='formsection_line'>
                        <input type='text' class='formsection_input' placeholder='Billing Address Line 2'/>
                    </div>
                    <div class='formsection_line'>
                        <input type='text' class='formsection_input' placeholder='City'/><input type='text' class='formsection_input_fixed' maxlength='2' placeholder='AZ'/><input type='text' class='formsection_input' placeholder='Zip Code'/>
                    </div>
                    <div class='formsection_line'>
                        <input type='text' class='formsection_input' placeholder='Billing Primary Phone'/><input type='text' class='formsection_input' placeholder='Billing Secondary Phone'/>
                    </div>
                    <div class='formsection_line'>
                        <input type='text' class='formsection_input' placeholder='Billing Email'/>
                    </div>
                </div>
                </div>";

            $returnedCode .= "</div></div>";

            $returnedCode .= "<div class='formsection'>
            <div class='formsection_header'>Service Locations</div>
            <div class='formsection_content'>
                <div class='formsection_line'>
                    <div class='checkbox_switch'>
                        <label class='switch'>
                            <input type='checkbox' id='additional_service_addresses_btn' checked>
                            <span class='slider round'></span>
                        </label>
                        <span class='checkbox_switch_label'>Contact Address is the Only Service Address</span>
                    </div>
                </div>
                <div class='list_of_service_addresses' style='display:none'>
                    $serviceAddressTemplate
                    <div class='formsection_line_rightjustify'>
                    <div class='button_type_3'>Add</div>
                </div>
                </div>
            </div>";
            
            return $returnedCode;
        }

        public static function GenerateAccountsViewListFooter($_retArray, $_currentPage) {
            $maxAccounts = $_retArray['count'];

            $maxPageNumber = ceil(($maxAccounts / 30));
            $returnedCode = "";
            $returnedCode .= "<div id='rightpane_footer'>";
            if ($_currentPage != 1) {
                $returnedCode .= "<img class='pagebutton1 viewaccounts_pageleft' src='img/left_arrow_gray.png'/>";
            }
            $returnedCode .= "<span style='font-size:16px'>$_currentPage</span>";
            if ($_currentPage < $maxPageNumber) {
                $returnedCode .= "<img class='pagebutton1 viewaccounts_pageright' src='img/right_arrow_gray.png'/>";
            }
            $returnedCode .= "</div>";

            return $returnedCode;
        }
    }
?>