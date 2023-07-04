<?php

class ListEmployees {
    public static function AsList($_dbInfo, $_retArray, $_shifts, $_roles) {
        $returnedCode = "";
        $employees = $_retArray;
        if (is_array($employees)) {
            foreach($employees as $employee) {
                $employeeName = $employee['firstname'] . ' ' . $employee['lastname'];
                $employeeRole = $employee['role'];
                $employeeShift = $employee['shift'];
                $employeeId = $employee['id'];

                $roleName = $_roles[$employeeRole]['name'];
                $shiftName = $_shifts[$employeeShift]['name'];

                $returnedCode .= <<<HTML
                <tr class='openemployeebutton' data-employeeid='$employeeId'>
                    <td>$employeeName</td>
                    <td>$roleName</td>
                    <td>$shiftName</td>
                </tr>
                HTML;
            }
        }

        $returnedCode .= <<<HTML
        <script>
            $('.openemployeebutton').click(function () { 
                $(this).hide();
                var eid = $(this).data('employeeid');
                var requestData = [
                {name: 'action', value: 'LoadPage'},
                {name: 'pageid', value: 'ViewEmployee'},
                {name: 'employeeid', value: eid}
                ];
                Action_LoadPage(requestData);
            });
        </script>
        HTML;

        return $returnedCode;
    }
}

?>