<?php

if (!defined('BOOTSTRAP')) { die('Access denied'); }

fn_register_hooks(
    'login_user_post',
    'save_cart',
    'place_order',
    'update_addon_status_pre',
    'url_post',
    'get_product_details_layout_post'
);