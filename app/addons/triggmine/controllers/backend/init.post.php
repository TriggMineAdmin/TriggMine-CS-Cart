<?php

use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    return;
}

if (isset($triggmine_scripts))
{
    $triggmine_scripts = fn_triggmine_on_page_loaded();
    Tygh::$app['view']->assign('triggmine_scripts', $triggmine_scripts);
}