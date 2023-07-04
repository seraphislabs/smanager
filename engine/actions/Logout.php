<?php

trait ActionLogout {
    public static function Logout() {
        OpLog::Log("Action: Logout");
        echo("<script>history.pushState(null, null, '/index.php');</script>");
        $rdb = RDB::getInstance();
        if (isset($_SESSION['token']))
            $rdb->delete($_SESSION['token']);
		session_unset();
		session_destroy();
    }
}

?>