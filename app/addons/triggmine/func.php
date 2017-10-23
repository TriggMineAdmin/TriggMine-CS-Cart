<?php

require_once 'lib/integrator.php';

use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

/**
 * Run triggmine integrator singleton on every page
 * event - on_page_loaded
 *
 * @return string
 */
function triggmine_on_page_loaded()
{
    $integrator = Triggmine_Integrator_CS_Cart::singleton();
    $integrator->onPageLoaded();
    return $integrator->outputJavaScript();
}

/**
 * Prepare and sends products data to update buyer info.
 * event - login_user_post
 *
 * @return bool
 */
function fn_triggmine_login_user_post($user_id, $cu_id, $udata, $auth, $condition, $result)
{
    if($_SESSION['auth']['user_id'] == 0) {
        $integrator = Triggmine_Integrator_CS_Cart::singleton();
        $integrator->_onBuyerLoggedOut();
    }elseif($_SESSION['auth']['user_id'] !== 0) {
        $integrator = Triggmine_Integrator_CS_Cart::singleton();
        $integrator->_onBuyerLoggedIn($udata);
        if (isset($_SESSION['cart']['products'])) {
            $products = $_SESSION['cart']['products'];
            $items = array();
            foreach ($products as $product) {
                $item = array();
                $product_id = $product['product_id'];
                $item['CartItemId'] = $product_id;
                $item['Title'] = $product['product'];
                $item['Count'] = $product['amount'];
                $item['Price'] = $product['price'];
                $product_data = fn_get_product_data($product_id, $_SESSION['auth']);
                if (isset($product_data['meta_description'])) {
                    $item['Description'] = $product_data['meta_description'];
                }
                if(isset($product['main_pair']['detailed']['image_path'])){
                    $ImageUrl = $product['main_pair']['detailed']['image_path'];
                }else{
                    $ImageInfo = fn_get_cart_product_icon($product['product_id']);
                    $ImageUrl = $ImageInfo['detailed']['http_image_path'];
                }
                if (isset($ImageUrl)) {
                    $item['ImageUrl'] = $ImageUrl;
                }
                //TODO add ThumbnailUrl
                /*if (isset($item['ThumbnailUrl'])) {
                    $ImageThumbnailUrl = path to ThumbnailUrl;
                }*/
                $items['Items'][] = $item;
            }
            $integrator->_onCartFullUpdate($items);
            return true;
        }
    } else {
        return false;
    }
}
/**
 * Prepare and sends products data to update buyer info.
 * event - save_cart
 *
 * @return bool
 */
function fn_triggmine_save_cart($cart, $user_id, $type)
{
    if (isset($cart['products'])) {
        $products = $cart['products'];
        $items = array();
        foreach ($products as $product) {
            $item = array();
            $ImageInfo = fn_get_cart_product_icon($product['product_id']);
            $item['CartItemId'] = $product['product_id'];
            $item['Title'] = $product['product'];
            $item['Count'] = $product['amount'];
            $item['Price'] = $product['price'];
            $product_data = fn_get_product_data($product['product_id'], $_SESSION['auth']);
            if (isset($product_data['meta_description'])) {
                $item['Description'] = $product_data['meta_description'];
            }
            if (isset($ImageInfo['detailed'])) {
                $ImageUrl = $ImageInfo['detailed']['http_image_path'];
                $item['ImageUrl'] = $ImageUrl;
            }
            //TODO add ThumbnailUrl
            /*if (isset($item['ThumbnailUrl'])) {
                $ImageThumbnailUrl = path to ThumbnailUrl;
            }*/
            $items['Items'][] = $item;
        }
        $integrator = Triggmine_Integrator_CS_Cart::singleton();
        $integrator->_onCartFullUpdate($items);
        return true;
    } else {
        return false;
    }
}
/**
 * Prepare and sends user data if user placed order.
 * event - place_order
 *
 * @return bool
 */
function fn_triggmine_place_order($order_id, $action, $order_status, $cart, $auth)
{
    if($order_id) {
        $userInfo = array();
        $user_id = $auth['user_id'];
        $u_data = fn_get_user_info($user_id, false);
        if (!empty($u_data['email'])) {
            $userInfo['BuyerEmail'] = $u_data['email'];
        }
        if (!empty($u_data['firstname'])) {
            $userInfo['FirstName'] = $u_data['firstname'];
        }
        if (!empty($u_data['lastname'])) {
            $userInfo['LastName'] = $u_data['lastname'];
        }
        if (!empty($u_data['birthday'])) {
            $userInfo['BuyerBirthday'] = $u_data['birthday'];
        }
        if (!empty($u_data['phone'])) {
            $userInfo['PhoneNumber'] = $u_data['phone'];
        }
        $integrator = Triggmine_Integrator_CS_Cart::singleton();
        $integrator->_onCartPurchased($userInfo);
        return true;
    } else {
        return false;
    }
}