<?php
require_once dirname(__FILE__) . '/core/Core.php';

use Tygh\Registry;
use Tygh\Database;

class Triggmine_Integrator_CS_Cart extends TriggMine_Core
{
	const VERSION = '3.0.23.5';
	private $_scriptFiles = array();
	private $_scripts = array();
	
	
	// Hold an instance of the class
	private static $instance;
	
	// The singleton method
	public static function singleton()
	{
		if (!isset(self::$instance)) {
			$class = __CLASS__;
			self::$instance = new $class;
		}
		return self::$instance;
	}
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function _deviceId() {
		return isset($_COOKIE['device_id']) && $_COOKIE['device_id'] ? $_COOKIE['device_id'] : "";
	}
	
	public function _deviceId1() {
		return isset($_COOKIE['device_id_1']) && $_COOKIE['device_id_1'] ? $_COOKIE['device_id_1'] : "";
	}
	
	/**
	 * Returns a name of your CMS / eCommerce platform.
	 *
	 * @return string Agent name.
	 */
	public function getAgent()
	{
		return PRODUCT_NAME;
	}
	
	/**
	 * Returns a version of your CMS / eCommerce platform.
	 *
	 * @return string Version.
	 */
	public function getAgentVersion()
	{
		return PRODUCT_VERSION;
	}
	
	/**
	 * Returns a value of the setting having given name.
	 *
	 * @param string $key Setting name.
	 *
	 * @return string Setting value.
	 */
	protected function _getSettingValue($key)
	{
		$setting_name = 'addons.triggmine.'.$key;
		return Registry::get($setting_name);
	}
	
	/**
	 * Returns a json string of orders
	 *
	 * @param $data
	 *
	 * @return string Json
	 */
	public function exportOrders($data)
	{
		// TODO: Implement exportOrders() method.
	}
	
	/**
	 * Adds &lt;script&gt; tag into the HTML.
	 * Modifies the URL depending on whether it is a plugin file or not.
	 *
	 * @param string $url          Relative or absolute URL of the JS file.
	 * @param bool   $isPluginFile Is it a part of plugin?
	 */
	 
	public function registerJavaScriptFile($url, $isPluginFile = true)
	{
		$this->_scriptFiles[] = $url;
	}
	
	/**
	 * Output all registered javascript to frontend
	 */
	public function outputJavaScript()
	{
		$result = '';
		foreach ($this->_scriptFiles as $scriptFile) {
			$result .= "<script type='text/javascript' src='$scriptFile'></script>" . PHP_EOL;
		}
		foreach ($this->_scripts as $script) {
			$result .= "<script type='text/javascript'>/* <![CDATA[ */ $script /* ]]> */</script>" . PHP_EOL;
		}
		return $result;
	}
	
	public function install()
	{
		$this->activate();
	}
	
	public function uninstall()
	{
		$this->deactivate();
	}
	
	/**
	 * Tells whether current request is AJAX one.
	 * AJAX doesn't equal to async.
	 *
	 * @return bool
	 */
	public function isAjaxRequest()
	{
		if (defined('AJAX_REQUEST')){
			return true;
		}
		return false;
	}
	
	/**
	 * Tells about JS support in the integrator.
	 *
	 * @return bool
	 */
	public function supportsJavaScript()
	{
		return true;
	}
	
	/**
	 * Adds JS into the HTML.
	 *
	 * @param string $script JS code.
	 */
	public function registerJavaScript($script)
	{
		$this->_scripts[] = $script;
	}
	
	/**
	 * Returns URL of the website.
	 */
	public function getSiteUrl()
	{
		return Registry::get('config.current_location');
	}
	
	/**
	 * Returns array with buyer info [BuyerEmail, FirstName, LastName].
	 */
	public function getBuyerInfo()
	{
		// TODO: Implement getBuyerInfo() method.
	}
	
	/**
	 * Tells whether current user is admin.
	 *
	 * @return bool Is user an administrator.
	 */
	protected function _isUserAdmin()
	{
		if ($_SESSION['auth']['user_type'] == 'A'){
			return true;
		}
		if($_SESSION['auth']['user_type'] == 'C') {
			return false;
		}
	}
	
	protected function _getUserDataFromDatabase($email)
	{
		$user_id = fn_is_user_exists(0, array('email' => $email));
		
		if(!$user_id){
			return true;
		}
		$user_info = fn_get_user_info($user_id);
		if(!empty($user_info))
		{
			$data = array(
				'BuyerRegEnd' => gmdate("Y-m-d H:i:s", $user_info['timestamp'])
			);
			return $data;
		}
		$data = array(
			'BuyerRegStart' => gmdate("Y-m-d H:i:s", time())
		);
		return $data;
	}
	
	protected function _fillShoppingCart($cartContent)
	{
		if (empty($cartContent['Items'])) {
			return true;
		}
		if (!empty($_SESSION['cart'])) {
			fn_clear_cart($_SESSION['cart']);
		}
		$cart_products = array();
		$cartItems = $cartContent['Items'];
		foreach($cartItems as $cartItem){
			$product_id = $cartItem['CartItemId'];
			$item['product_id'] = $product_id;
			$item['amount'] = $cartItem['Count'];
			$cart_products [$product_id]= $item;
		}
		if(!empty($cart_products)) {
			fn_add_product_to_cart($cart_products, $_SESSION['cart'], $_SESSION['auth'], false);
		}
	}
	
	/**
	 * Returns absolute URL to the shopping cart page.
	 *
	 * @return string Shopping cart URL.
	 */
	public function getCartUrl()
	{
		$host = Registry::get('config.current_location');
		$link = $host.'/index.php?dispatch=checkout.cart';
		return $link;
	}
	
	public function _onBuyerLoggedIn($user)
	{
		$userInfo = array();
		if(!empty($user['email'])){
			$userInfo['BuyerEmail'] = $user['email'];
		}
		if(!empty($user['firstname'])){
			$userInfo['FirstName'] = $user['firstname'];
		}
		if(!empty($user['lastname'])){
			$userInfo['LastName'] = $user['lastname'];
		}
		if(!empty($user['birthday'])){
			$userInfo['BuyerBirthday'] = $user['birthday'];
		}
		if(!empty($user['phone'])){
			$userInfo['PhoneNumber'] = $user['phone'];
		}
		$this->logInBuyer($userInfo);
	}
	
	public function _onBuyerLoggedOut()
	{
		$this->logOutBuyer();
	}
	
	public function _onCartFullUpdate($items)
	{
		$this->updateCartFull($items);
	}
	
	public function _onCartPurchased($data)
	{
		$this->purchaseCart($data);
	}
	
    public function isBot()
	{
	   preg_match('/bot|curl|spider|google|baidu|facebook|yandex|bing|aol|duckduckgo|teoma|yahoo|twitter^$/i', $_SERVER['HTTP_USER_AGENT'], $matches);
	
	   return (empty($matches)) ? false : true;
	}
	
	public function SoftChek($status = 0)
	{

        $res = array(
            'dateCreated'       => date('Y-m-d\TH:i:s'),
            'diagnosticType'    => 'InstallPlugin',
            'description'       => $this->getVersion(),
            'status'            => $status
        );
        
		return $res;
	}
	
    public function onDiagnosticInformationUpdated($data, $url, $token)
    {
        return $this->apiClient($data, 'control/api/plugin/onDiagnosticInformationUpdated', $url, $token);
    }
    
    public function PageInit($product_id)
    {
    	
    	
    	$item		= fn_get_product_data($product_id, $_SESSION['auth']);
    	$categories	= $item['category_ids'];
    	$deviceId	= $this->_deviceId();
    	$deviceId1	= $this->_deviceId1();
    	
        $product = array (
            "product_id"            => $item['product_id'],
            "product_name"          => $item['product'],
            "product_desc"          => $item['full_description'],
            "product_sku"           => $item['product_code'],
            "product_image"         => $item['main_pair']['detailed']['image_path'],
            "product_url"           => (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
            "product_qty"           => 1,
            "product_price"         => $item['base_price'],
            "product_total_val"     => $item['base_price'],
            "product_categories"    => array()
        );
        
        $user_id = $_SESSION['auth']['user_id'];
        
        if(!empty($user_id)) {
        	
        	$user_data = fn_get_user_info($user_id, false);
        	
	        $customer = array(
	            "device_id"             => $deviceId,
	            "device_id_1"           => $deviceId1,
	            "customer_id"           => $user_data['user_id'],
	            "customer_first_name"   => $user_data['firstname'],
	            "customer_last_name"    => $user_data['lastname'],
	            "customer_email"        => $user_data['email'],
	            "customer_date_created" => ""
	        );      	
        }
        else { 
        	
	        $customer = array(
	            "device_id"             => $deviceId,
	            "device_id_1"           => $deviceId1,
	            "customer_id"           => "",
	            "customer_first_name"   => "",
	            "customer_last_name"    => "",
	            "customer_email"        => "",
	            "customer_date_created" => ""
	        );
        }
        
        $products  = array($product);
    
        $data = array(
          "user_agent"      => $_SERVER['HTTP_USER_AGENT'] ?: null,
          "customer"        => $customer,
          "products"        => $products
        );
		
		return $data;
    }
    
    public function onPageInit($data)
    {
        return $this->apiClient($data, 'api/events/navigation');
    }
    
    public function getCustomerLoginData($user_id)
    {
    	if($user_id) {
    		
	    	$user_data	= fn_get_user_info($user_id, false);
	    	$deviceId	= $this->_deviceId();
	    	$deviceId1	= $this->_deviceId1();
	    	
	    	// $this->localResponseLog($user_data);
    	
	        $customer = array(
	            "device_id"             => $deviceId,
	            "device_id_1"           => $deviceId1,
	            "customer_id"           => $user_data['user_id'],
	            "customer_first_name"   => $user_data['firstname'],
	            "customer_last_name"    => $user_data['lastname'],
	            "customer_email"        => $user_data['email'],
	            "customer_date_created" => ""
	        );
	        
	        return $customer;
    	}
    }
    
    public function sendLoginData($data)
    {
        return $this->apiClient($data, 'api/events/prospect/login');
    }
    
    public function sendLogoutData($data)
    {
        return $this->apiClient($data, 'api/events/prospect/logout');
    }

    public function sendRegisterData($data)
    {
        return $this->apiClient($data, 'api/events/prospect/registration');
    }
}