<?php
    $pagesDir = __DIR__ . '/pages';
    $pages = scandir($pagesDir);
    foreach ($pages as $page) {
        if (pathinfo($page, PATHINFO_EXTENSION) === 'php') {
            require_once $pagesDir . '/' . $page;
        }
    }

    $pagesDir = __DIR__ . '/popups';
    $pages = scandir($pagesDir);
    foreach ($pages as $page) {
        if (pathinfo($page, PATHINFO_EXTENSION) === 'php') {
            require_once $pagesDir . '/' . $page;
        }
    }

    $pagesDir = __DIR__ . '/menus';
    $pages = scandir($pagesDir);
    foreach ($pages as $page) {
        if (pathinfo($page, PATHINFO_EXTENSION) === 'php') {
            require_once $pagesDir . '/' . $page;
        }
    }
?>