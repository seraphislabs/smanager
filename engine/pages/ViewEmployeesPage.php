<?php
class PageViewEmployees {
    public static function Generate($_dbInfo, $_postData) {
        $canAddEmployee = DatabaseManager::CheckPermissions($_dbInfo, ['ce']);
        if (!DatabaseManager::CheckPermissions($_dbInfo, ['vel'])) {
            die("You do not have permission to view this page. Speak to your account manager to gain access.");
        }
        $returnedCode = "<script>history.pushState(null, null, '/index.php?page=ViewEmployees');</script>";
        $returnedCode .= <<<HTML
            <script type='text/javascript'>
                function OpenNewEmployeePage() {
                    var data = {};
                    var requestData = [
                        {name: 'action', value: 'LoadPopup'},
                        {name: 'buttonid', value : 'NewEmployee'},
                        {name: 'data', value : JSON.stringify(data)}
                    ];
                    Action_LoadPopup(xhrArray, requestData);
                }
                $('.btn_newemployeedialogue').click(function() {
                    OpenNewEmployeePage();
                });
            </script>
        HTML;

        $returnedCode .= <<<HTML
        <div id='rightpane_header'>
        <div class='listheaderbuttoncontainer'>
        HTML;

        if ($canAddEmployee) {
            $returnedCode .= <<<HTML
            <div class='listheaderbutton btn_newemployeedialogue'><img src='img/add_user_green.png' class='img_icon_small' style='margin-right:6px'/> New</div>
            HTML;
        }

        $returnedCode .= <<<HTML
        </div>
        </div><div id='rightpane_viewport'>
        <table class='table_employees'>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Shift</th>
                </tr>
            </thead>
            <tbody>
        HTML;

        $employees = DatabaseManager::GetAllEmployees($_dbInfo);
        $roles = DatabaseManager::GetAllEmployeeRoles($_dbInfo, true);
        $shifts = DatabaseManager::GetAllEmployeeShifts($_dbInfo, true);

        $employeeCode = ListEmployees::AsList($_dbInfo, $employees, $shifts, $roles);
        $returnedCode .= $employeeCode;
            
        $returnedCode .= <<<HTML
            </tbody>
        </table>
        </div>
        HTML;

        return $returnedCode;
    }
}
?>