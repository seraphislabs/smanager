<?php

trait ActionLogout {
    public static function Logout() {
        echo("<script>history.pushState(null, null, '/index.php');</script>");
		session_unset();
		session_destroy();
    }
}

?>