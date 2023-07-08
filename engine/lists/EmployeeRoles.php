<?php

class ListEmployeeRoles {

 public static function AsSelect() {
  $returnedCode = "";

  $retVar = DatabaseManager::GetAllEmployeeRoles(false);

  foreach($retVar as $role) {
      $roleName = $role['name'];
      $roleID = $role['id'];
      $returnedCode .= <<<HTML
      <option value='$roleID'>$roleName</option>
      HTML;
  }

  return $returnedCode;
  }

  public static function AsList($_retArray) {
    $count = 0;
    $returnedCode = "";
    $_roles = $_retArray;
    if (is_array($_roles)) {
        foreach($_roles as $role) {
            $count++;
            $color = "#E0DFE5";

            if ($count%2 == 0) {
                $color = "#FAFAFA";
            }

            $roleName = $role['name'];
            $roleId = $role['id'];
            $returnedCode .= <<<HTML
            <div class='formsection_line_leftjustify edit_role_button' data-roleid='$roleId'>
                <img src='img/edit_green.png' style='width:20px;'/>$roleName
            </div>
            HTML;
        }
    }

    $returnedCode .= <<<HTML
        <script>
            $(".edit_role_button").click(function() {
                    var roleid = $(this).data('roleid');
                    var data = {};
                    data['roleid'] = roleid;
                    var requestData = [
                        {name: 'action', value: 'LoadPopup'},
                        {name: 'pageid', value: 'NewRole'},
                        {name: 'data', value: JSON.stringify(data)}
                    ];
                    Action_LoadPopup(requestData);
                });
        </script>
    HTML;

    return $returnedCode;
}
}

?>