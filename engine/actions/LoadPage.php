<?php

trait ActionLoadPage {
    public static function LoadPage($_dbInfo, $_pageid, $_data) {
		$returnedCode = "";
		switch ($_pageid) {
			case "Accounts":
				$returnedCode .= "<script>history.pushState(null, null, '/index.php?page=$_pageid');</script>";
				$returnedCode .= PageViewAccounts::Generate($_dbInfo);
                break;
			case "Dashboard":
				$returnedCode .= "<script>history.pushState(null, null, '/index.php?page=$_pageid');</script>";
				break;
			case "Employees":
				$returnedCode .= "<script>history.pushState(null, null, '/index.php?page=$_pageid');</script>";
				$returnedCode .= PageViewEmployees::Generate($_dbInfo);
				break;
			case "WorkOrders";
				$returnedCode .= "<script>history.pushState(null, null, '/index.php?page=$_pageid');</script>";
				$returnedCode .= PageViewWorkOrders::Generate($_dbInfo);
				break;
			case "EmployeeSettings";
				$returnedCode .= "<script>history.pushState(null, null, '/index.php?page=$_pageid');</script>";
				$returnedCode .= PageEmployeeSettings::Generate($_dbInfo);
				break;
			case "ScheduleSettings";
			    $returnedCode .= "<script>history.pushState(null, null, '/index.php?page=$_pageid');</script>";
				$returnedCode .= PageScheduleSettings::Generate($_dbInfo);
			    break;
			case "Invoices":
				$returnedCode .= "<script>history.pushState(null, null, '/index.php?page=$_pageid');</script>";
				$returnedCode .= PageViewInvoices::Generate($_dbInfo);
				break;
            case "ViewAccount":
                $_accountid = $_data['accountid'];
                $returnedCode .= "<script>history.pushState(null, null, '/index.php?page=ViewAccount&accountid=$_accountid');</script>";
                $returnedCode .= PageViewAccount::Generate($_dbInfo, $_accountid);
                break;
            case "ViewEmployee":
                $_employeeid = $_data['employeeid'];
                $returnedCode .= "<script>history.pushState(null, null, '/index.php?page=ViewAccount&employeeid=$_employeeid');</script>";
                $returnedCode .= PageViewEmployee::Generate($_dbInfo, $_employeeid);
                break;
		}
		return $returnedCode;
	}
}

?>