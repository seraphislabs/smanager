<?php
$pagesDir = __DIR__ . '/database';
$pages = scandir($pagesDir);
foreach ($pages as $page) {
    if (pathinfo($page, PATHINFO_EXTENSION) === 'php') {
        require_once $pagesDir . '/' . $page;
    }
}

class DatabaseManager
{
    use DatabaseAccounts;
    use DatabaseEmployees;
    use DatabaseEmployeeRoles;
    use DatabaseEmployeeShifts;
    use DatabaseLocations;
    use DatabaseHolidaySchedules;
    use DatabaseContacts;
    use DatabaseMaster;
}