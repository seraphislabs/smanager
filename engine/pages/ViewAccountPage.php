<?php
class PageViewAccount {
    public static function Generate($_dbInfo, $_accountid) {
        $returnedCode = "";
        // Permission Check
        if (!DatabaseManager::CheckPermissions($_dbInfo, ['va'])) {
            die("You do not have permission to view this page. Speak to your account manager to gain access.");
        }

        $accountInfo = DatabaseManager::GetAccount($_dbInfo, $_accountid);

        if (!is_array($accountInfo)) {
            die();
        }

        if (count($accountInfo) <= 0) {
            die();
        }

        $primaryContactInfo = DatabaseManager::GetContact($_dbInfo, $accountInfo['primarycontactid']);
        $serviceLocations = DatabaseManager::GetAllLocationsByAccount($_dbInfo, $_accountid);

        $locationsListings = ListLocations::AsList($_dbInfo, $serviceLocations);

        $returnedCode .= <<<HTML
        <div class ='display_container'>
            <div class='display_header'>
                <span class='textcolor_green'>Account:</span> &nbsp; $accountInfo[name]
            </div>
            <div class='display_row'>
                <div class='display_section'>
                    <div class='display_section_header'>
                        Account Details 
                    </div>
                    <div class='display_section_content'>
                        <table class='table_accountdetails'>
                            <tbody>
                                <tr>
                                    <td style='justify-content:right;'>
                                        <span class='textcolor_green'>Account Name</span>
                                    </td>
                                    <td>
                                        $accountInfo[name]
                                    </td>
                                </tr>
                                <tr>
                                    <td style='justify-content:right;'>
                                        <span class='textcolor_green'>Account Type</span>
                                    </td>
                                    <td>
                                        $accountInfo[type]
                                    </td>
                                </tr>
                                <tr>
                                    <td style='justify-content:right;'>
                                        <span class='textcolor_green'>Address</div>
                                    </td>
                                    <td>
                                        $accountInfo[street1] $accountInfo[street2]
                                        $accountInfo[city], $accountInfo[state] $accountInfo[zipcode]
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class='display_section'>
                    <div class='display_section_header'>
                        Primary Contact
                    </div>
                    <div class='display_section_content'>
                        <table class='table_accountdetails'>
                            <tbody>
                                <tr>
                                    <td style='justify-content:right;'>
                                        <span class='textcolor_green'>Name</span>
                                    </td>
                                    <td>
                                        $primaryContactInfo[firstname] $primaryContactInfo[lastname]
                                    </td>
                                </tr>
                                <tr>
                                    <td style='justify-content:right;'>
                                        <span class='textcolor_green'>Email</span>
                                    </td>
                                    <td>
                                        $primaryContactInfo[email]
                                    </td>
                                </tr>
                                <tr>
                                    <td style='justify-content:right;'>
                                        <span class='textcolor_green'>Primary Phone</span>
                                    </td>
                                    <td>
                                        $primaryContactInfo[primaryphone]
                                    </td>
                                </tr>
                                <tr>
                                    <td style='justify-content:right;'>
                                        <span class='textcolor_green'>Secondary Phone</span>
                                    </td>
                                    <td>
                                        $primaryContactInfo[secondaryphone]
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class='display_row'>
                <div class='display_section'>
                    <div class='display_section_header'>
                        Service Locations
                    </div>
                    <div class='display_section_content'>
                    <table class='table_accountlocationdetails'>
                        <thead>
                            <tr>
                                <th>Location Name</th>
                                <th>Address</th>
                                <th>Contact Name</th>
                                <th>Contact Phone</th>
                            </tr>
                        </thead>
                        <tbody>
                            $locationsListings
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        HTML;

        return $returnedCode;
    }
}
?>