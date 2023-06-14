<html>
<head>
<link rel="icon" href="data:,">
<link rel="stylesheet" type="text/css" href="styles/style.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script type="text/javascript" src="js/utils.js"></script>
<script type="text/javascript" src="js/portal.js"></script>
</head>

<body>
<?php
require("engine/engine.php");

echo("<div id='pagewrap_master'>");
    echo("<div id='pagewrap'>");
    echo("</div>");
    echo("
    <div id='rightslide'>
        <div class='rightslide_topbar'>
            <div class='rightslide_topbar_header'>Service <div class='color1'>Manager</div></div>
        </div>
        <div class='rightslide_content_wrapper'>
            <div class='rightslide_content'>
            </div>
            <div class='rightslide_footer'>
            <div class='button_type_2 btn_temp noflexgro' >Create and\rSchedule</div><div class='button_type_2 btn_temp noflexgro'>Create</div><div class='button_type_1 btn_temp noflexgro'>Discard</div>
            </div>
        </div>
    </div>");
echo("</div>");
echo("<div class='popup_darken'></div><div class='popup_wrapper'><div class='popup_content'>
</div>
</div>");

?>
</body>

</html>
