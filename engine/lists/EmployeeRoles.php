<?php

class ListEmployeeRoles {

 public static function AsSelect($_dbInfo) {
  $returnedCode = "";

  $retVar = DatabaseManager::GetAllEmployeeRoles($_dbInfo, false);

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
                    $('.popup_darken').fadeIn(500);
                    $('.popup_wrapper').fadeIn(500); 
                    $('.popup_scrollable').fadeIn(300);
                    SetLoadingIcon('.popup_scrollable');
                    var requestData = [
                        {name: 'action', value: 'GenerateNewRolePage'},
                        {name: 'roleid', value: roleid}
                    ];
                    CancelAllAjaxCalls();
                    AjaxCall(xhrArray, requestData, function(status, response) {
                        if (status) {
                            $('.popup_content').html(response).fadeIn(300);
                        }
                    });
                });
        </script>
    HTML;

    return $returnedCode;
}
}

?>