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
function fn_triggmine_on_page_loaded()
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
// function fn_triggmine_login_user_post($user_id, $cu_id, $udata, $auth, $condition, $result)
// {
//     if($_SESSION['auth']['user_id'] == 0) {
//         $integrator = Triggmine_Integrator_CS_Cart::singleton();
//         $integrator->_onBuyerLoggedOut();
//     }elseif($_SESSION['auth']['user_id'] !== 0) {
//         $integrator = Triggmine_Integrator_CS_Cart::singleton();
//         $integrator->_onBuyerLoggedIn($udata);
//         if (isset($_SESSION['cart']['products'])) {
//             $products = $_SESSION['cart']['products'];
//             $items = array();
//             foreach ($products as $product) {
//                 $item = array();
//                 $product_id = $product['product_id'];
//                 $item['CartItemId'] = $product_id;
//                 $item['Title'] = $product['product'];
//                 $item['Count'] = $product['amount'];
//                 $item['Price'] = $product['price'];
//                 $product_data = fn_get_product_data($product_id, $_SESSION['auth']);
//                 if (isset($product_data['meta_description'])) {
//                     $item['Description'] = $product_data['meta_description'];
//                 }
//                 if(isset($product['main_pair']['detailed']['image_path'])){
//                     $ImageUrl = $product['main_pair']['detailed']['image_path'];
//                 }else{
//                     $ImageInfo = fn_get_cart_product_icon($product['product_id']);
//                     $ImageUrl = $ImageInfo['detailed']['http_image_path'];
//                 }
//                 if (isset($ImageUrl)) {
//                     $item['ImageUrl'] = $ImageUrl;
//                 }
//                 /*TODO add ThumbnailUrl
//                 if (isset($item['ThumbnailUrl'])) {
//                     $ImageThumbnailUrl = path to ThumbnailUrl;
//                 }*/
//                 $items['Items'][] = $item;
//             }
//             $integrator->_onCartFullUpdate($items);
//             return true;
//         }
//     } else {
//         return false;
//     }
// }
/**
 * Prepare and sends products data to update buyer info.
 * event - save_cart
 *
 * @return bool
 */
// function fn_triggmine_save_cart($cart, $user_id, $type)
// {
    // $integrator = Triggmine_Integrator_CS_Cart::singleton();
    // $integrator->localResponseLog($cart);
    // $integrator->localResponseLog($user_id);
    // $integrator->localResponseLog($type);
    
        
/*    if (isset($cart['products'])) {
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
            // TODO add ThumbnailUrl
            // if (isset($item['ThumbnailUrl'])) {
            //     $ImageThumbnailUrl = path to ThumbnailUrl;
            // }
            $items['Items'][] = $item;
        }
        $integrator = Triggmine_Integrator_CS_Cart::singleton();
        $integrator->_onCartFullUpdate($items);
        return true;
    } else {
        return false;
    }*/
// }

/**
 * Prepare and sends products data to update buyer info.
 * event - save_cart
 *
 * @return bool
 */
function fn_triggmine_save_cart_content_post($cart, $user_id, $type, $user_type)
{
    $integrator = Triggmine_Integrator_CS_Cart::singleton();
    // $integrator->localResponseLog($cart);
    
    $data = $integrator->getCartData($cart);
    $res = $integrator->sendCart($data);
    $integrator->localResponseLog($data, $res);
}

/**
 * Prepare and sends user data if user placed order.
 * event - place_order
 *
 * @return bool
 */
function fn_triggmine_place_order($order_id, $action, $order_status, $cart, $auth)
{
    
    $integrator = Triggmine_Integrator_CS_Cart::singleton();
    // $integrator->localResponseLog($order_id);
    // $integrator->localResponseLog($action);
    // $integrator->localResponseLog($order_status);
    // $integrator->localResponseLog($cart);
    // $integrator->localResponseLog($auth);

    $integrator = Triggmine_Integrator_CS_Cart::singleton();
    
    $data = $integrator->getOrderData($order_id, $order_status, $cart);
    $res = $integrator->onConvertCartToOrder($data);
    // $integrator->localResponseLog($data, $res);  
    
/*    if($order_id) {
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
    }*/
}
/**
 * Hook is executed before changing add-on status (i.e. before add-on enabling or disabling).
 * event - update_addon_status_pre
 *
 * @return bool
 */
function fn_triggmine_update_addon_status_pre($addon, $status, $show_notification, $on_install, $allow_unmanaged, $old_status, $scheme)
{
    $integrator = Triggmine_Integrator_CS_Cart::singleton();
    
    // $integrator->localResponseLog(array($addon, $status, $show_notification, $on_install, $allow_unmanaged, $old_status, $scheme));
    
    return false;
}

function fn_triggmine_url_post($_url, $area, $url, $protocol)
{

    if(isset($_POST['addon']) && $_POST['addon'] === "triggmine") {
        
        $post = array_values($_POST['addon_data']['options']);
        
        $settings = array(
                'StatusEnableTriggmine'         => $post[0],
                'ApiUrl'                        => $post[1],
                'ApiKey'                        => $post[2],
                'StatusEnableOrderExport'       => $post[3],
                'OrderExportDateFrom'           => $post[4],
                'OrderExportDateTo'             => $post[5],
                'StatusEnableCustomerExport'    => $post[6],
                'CustomerExportDateFrom'        => $post[7],
                'CustomerExportDateTo'          => $post[8]
            );
        
        $integrator = Triggmine_Integrator_CS_Cart::singleton();

        $data = $integrator->SoftChek($post[0]);
        $res = $integrator->onDiagnosticInformationUpdated($data, $post[1], $post[2]);
        // $integrator->localResponseLog($data, $res);
        
        return true;
    }
    else {
        
        return false;  
    }
    
}

function fn_triggmine_get_product_details_layout_post($result, $product_id)
{
    
    // $product_data = fn_get_product_data($product_id, $_SESSION['auth']);
    
    $integrator = Triggmine_Integrator_CS_Cart::singleton();
    
    $data = $integrator->PageInit($product_id);
    $res = $integrator->onPageInit($data);
    // $integrator->localResponseLog($data, $res);
}

/**
 * Modifies the result after login user
 * event - login_user_post
 *
 * @return bool
 */
function fn_triggmine_login_user_post($user_id, $cu_id, $udata, $auth, $condition, $result)
{

    if($user_id && $auth['last_login']) { // Sign In User 
        
        $integrator = Triggmine_Integrator_CS_Cart::singleton();
        
        $data = $integrator->getCustomerLoginData($user_id);
        $res = $integrator->sendLoginData($data);
        // $integrator->localResponseLog($data, $res);
        
        return true;
    }
    elseif($user_id) { // Registration New User
 
        $integrator = Triggmine_Integrator_CS_Cart::singleton();
        
        $data = $integrator->getCustomerLoginData($user_id);
        $res = $integrator->sendRegisterData($data);
        // $integrator->localResponseLog($data, $res);
        
        return true;       
    }
    else {
        return false;
    }
}

/**
 * Allows to perform any actions after user logout.
 * event - user_logout_after
 *
 * @return bool
 */
function fn_triggmine_user_logout_after($auth)
{   
    if($auth['user_id']) {
        
        $integrator = Triggmine_Integrator_CS_Cart::singleton();
        
        $data = $integrator->getCustomerLoginData($auth['user_id']);
        $res = $integrator->sendLogoutData($data);
        // $integrator->localResponseLog($data, $res);
        
        return true;
    }
    else {
        return false;
    }
}