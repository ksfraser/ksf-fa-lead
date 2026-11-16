<?php
declare(strict_types=1);

$_GET['view'] = 'leads';

if (isset($_SERVER['PHP_SELF'])) {
    $_SERVER['PHP_SELF'] = preg_replace('#/pages/leads\.php$#', '/index.php', $_SERVER['PHP_SELF']);
}
if (isset($_SERVER['SCRIPT_NAME'])) {
    $_SERVER['SCRIPT_NAME'] = preg_replace('#/pages/leads\.php$#', '/index.php', $_SERVER['SCRIPT_NAME']);
}
if (isset($_SERVER['REQUEST_URI'])) {
    $_SERVER['REQUEST_URI'] = preg_replace(
        '#/pages/leads\.php(\?|$)#',
        '/index.php$1',
        $_SERVER['REQUEST_URI']
    );
}

chdir(__DIR__ . '/..');

require __DIR__ . '/../index.php';
