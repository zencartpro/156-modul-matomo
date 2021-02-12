<?php  
/**
* matomo.php
* @copyright Copyright 2021 webchills (www.webchills.at)
* @based on piwikecommerce 2012 by Stephan Miller
* @copyright Copyright 2003-2021 Zen Cart Development Team
* @copyright Portions Copyright 2003 osCommerce
* @license https://www.zen-cart-pro.at/license/3_0.txt GNU General Public License V3.0
* @version $Id: matomo.php 2021-02-11 21:48:40Z webchills $
*/


function log_category($categories_id,$language_id) {
global $db;

$categories_query = "select categories_name from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = " . (int)$categories_id . " and language_id = " . (int)$language_id;
$categories = $db->Execute($categories_query);

if ($categories->RecordCount() > 0) {    
return '_paq.push([\'setEcommerceView\',productSku = false, productName = false, category = \''.str_replace(array('\'', '"'), '', $categories->fields['categories_name']).'\']);' . "\n";
}

} 

function log_product($products_id,$language_id) {
global $db;      

$products_query = "select p.products_id, p.products_model, pd.products_name, cd.categories_name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, ". TABLE_CATEGORIES_DESCRIPTION ." cd WHERE p.products_id = pd.products_id and p2c.categories_id = cd.categories_id and p.products_id = " . (int)$products_id . " and pd.language_id =".(int)$language_id." and cd.language_id =".(int)$language_id;
$products = $db->Execute($products_query);

if ($products->RecordCount() > 0) {
return '_paq.push([\'setEcommerceView\', \''.$products->fields['products_id'].'\', \''.str_replace(array('\'', '"'), '', $products->fields['products_name']).'\', \''.str_replace(array('\'', '"'), '', $products->fields['categories_name']).'\']);' . "\n";
}

}   

function log_cart($products,$total,$language_id) {
global $db;                 

for ($i=0, $n=sizeof($products); $i<$n; $i++) {

$categories_query = "select cd.categories_name from " . TABLE_CATEGORIES_DESCRIPTION ." cd, ". TABLE_PRODUCTS_TO_CATEGORIES . " p2c WHERE cd.categories_id = p2c.categories_id and p2c.products_id = " . (int)$products[$i]['id'] . " and cd.language_id =".(int)$language_id;

$categories = $db->Execute($categories_query); 
if (!is_null($products[$i]['model'])) {     
$string .= '_paq.push([\'addEcommerceItem\', \''.$products[$i]['model'].'\',\''.str_replace(array('\'', '"'), '', $products[$i]['name']).'\',\''.str_replace(array('\'', '"'), '', $categories->fields['categories_name']).'\',\''.format_price($products[$i]['final_price']).'\',\''.$products[$i]['quantity'].'\']);' . "\n";
}	else {
$string .= '_paq.push([\'addEcommerceItem\', \''.$products[$i]['id'].'\',\''.str_replace(array('\'', '"'), '', $products[$i]['name']).'\',\''.str_replace(array('\'', '"'), '', $categories->fields['categories_name']).'\',\''.format_price($products[$i]['final_price']).'\',\''.$products[$i]['quantity'].'\']);' . "\n";	
}
}

$string .= '_paq.push([\'trackEcommerceCartUpdate\',\''.format_price($total).'\']);' . "\n";

return $string;
} 

function log_order($insert_id,$order,$products,$language_id) {
global $db;      

foreach ($products as $p) {

if (!is_null($p['products_id'])) {    
$categories_query = "select cd.categories_name from " . TABLE_CATEGORIES_DESCRIPTION ." cd, ". TABLE_PRODUCTS_TO_CATEGORIES . " p2c WHERE cd.categories_id = p2c.categories_id and p2c.products_id = " . (int)$p['products_id'] . " and cd.language_id =".(int)$language_id;
$categories = $db->Execute($categories_query);

$order_product_query = "select products_id, products_model, products_tax, products_quantity, final_price from " . TABLE_ORDERS_PRODUCTS . " where orders_id = " . (int)$insert_id . " and products_id = " . (int)$p['products_id'];
$order_product = $db->Execute($order_product_query);
if (!is_null($products[$i]['model'])) { 
$string .= '_paq.push([\'addEcommerceItem\', \''.$order_product->fields['products_model'].'\',\''.str_replace(array('\'', '"'), '', $p['products_name']).'\',\''.str_replace(array('\'', '"'), '', $categories->fields['categories_name']).'\',\''.(float)$order_product->fields['final_price'].'\',\''.$order_product->fields['products_quantity'].'\']);' . "\n";
} else {
$string .= '_paq.push([\'addEcommerceItem\', \''.$order_product->fields['products_id'].'\',\''.str_replace(array('\'', '"'), '', $p['products_name']).'\',\''.str_replace(array('\'', '"'), '', $categories->fields['categories_name']).'\',\''.(float)$order_product->fields['final_price'].'\',\''.$order_product->fields['products_quantity'].'\']);' . "\n";
}
}

}

$subtotal_result = $db->Execute("SELECT ROUND(value, 2) subtotal FROM ". TABLE_ORDERS_TOTAL ." WHERE class='ot_subtotal' AND orders_id = ". $order->fields['orders_id']);
$subtotal = $subtotal_result->fields['subtotal'];
$shipping_result = $db->Execute("SELECT ROUND(value, 2) shipping FROM ". TABLE_ORDERS_TOTAL ." WHERE class='ot_shipping' AND orders_id = ". $order->fields['orders_id']);
if ($shipping_result->RecordCount() > 0) {
$shipping = $shipping_result->fields['shipping'];
} else {
$shipping = 0.00;
}
$discount_result = $db->Execute("SELECT ROUND(value, 2) discount FROM ". TABLE_ORDERS_TOTAL ." WHERE class='ot_coupon' AND orders_id = ". $order->fields['orders_id']);
if ($discount_result->RecordCount() > 0) {
$discount = $discount_result->fields['discount'];
} else {
$discount = 0.00;
}
$string .= '_paq.push([\'trackEcommerceOrder\',\''.$insert_id.'\',\''.format_price($order->fields['order_total']).'\',\''.format_price($subtotal).'\',\''.format_price($order->fields['order_tax']).'\',\''.format_price($shipping).'\',\''.format_price($discount).'\']);' . "\n";

return $string;	
} 


function format_price($price) {      
return number_format($price, 2, '.', '');
}

$FileName = str_replace(array('\'', '"'), '', $UserInput);

function log_custom_variable($index,$key,$value) {

return '_paq.push([\'setCustomVariable\',\''.$index.'\',\''.$key.'\',\''.$value.'","visit"]);' . "\n";
}