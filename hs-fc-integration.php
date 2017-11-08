<?php
/**
 * Plugin Name:  HS FormCraft Proxy
 * Plugin URI: https://github.com/tezrosolutions/hs-fc-integration
 * Description: This plugin integrates ContactForm7 with HubSpot Form API
 * Version: 1.0.0
 * Author: Muhammad Umair
 * Author URI: https://github.com/tezrosolutions/
 */


define('HS_PORTAL', 'HubSpot Portal ID Here');
define("GUID_PROPERTY_ANALYSIS_FORM", "HubSpot Form GUID");
define("GUID_CONTACT_US_FORM", "HubSpot Form GUID");

function _get_hs_context($page_title, $page_url) {
	if(isset($_COOKIE['hubspotutk'])) {
   		$hubspotutk = $_COOKIE['hubspotutk'];
	} else {
    		$hubspotutk = "";
	}
	$ip_addr = $_SERVER['REMOTE_ADDR'];
	$hs_context = array(
    	'hutk' => $hubspotutk,
    	'ipAddress' => $ip_addr,
    	'pageUrl' => $page_url,
    	'pageName' => $page_title
	);
	return $hs_context_json = json_encode($hs_context);
}
function _post_data_to_hs($data, $endpoint) {
	$ch = @curl_init();
	@curl_setopt($ch, CURLOPT_POST, true);
	@curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	@curl_setopt($ch, CURLOPT_URL, $endpoint);
	@curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    	'Content-Type: application/x-www-form-urlencoded'
    ));
	@curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch); 
	$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
	@curl_close($ch);
}


add_action('formcraft_before_save', 'submit_to_hubspot', 10, 4); 

function submit_to_hubspot($content, $meta, $raw_content, $integrations) {
	if($content['Form ID'] == 6) {

		$property_type = ($content['Property Type.value'])?$content['Property Type.value']:"";
		$is_the_property_vacant_ = ($content['.value'])?$content['.value']:"";
		$firstname = ($content['Your Name'])?$content['Your Name']:"";
		$email = ($content['Your Email'])?$content['Your Email']:"";
		$phone = ($content['Telephone'])?$content['Telephone']:"";
		$comments = ($content['Comments'])?$content['Comments']:"";
		
		$hs_context_json = _get_hs_context("Home", "https://www.mesaproperties.net");
		
		$str_post = "property_type=". urlencode($property_type)
		. "&is_the_property_vacant_=". urlencode($is_the_property_vacant_)
		. "&firstname=" . urlencode($firstname)
        . "&email=" . urlencode($email)
		. "&phone=" . urlencode($phone)
		. "&comments=" . urlencode($comments)
        . "&hs_context=" . urlencode($hs_context_json); 

		$endpoint = 'https://forms.hubspot.com/uploads/form/v2/' . HS_PORTAL . '/' . 				GUID_PROPERTY_ANALYSIS_FORM;
		_post_data_to_hs($str_post, $endpoint);

	}
	else if($content['Form ID'] == 11) {
              

      	$property_type = ($content['Property Type.value'])?$content['Property Type.value']:"";
		$is_the_property_vacant_ = ($content['.value'])?$content['.value']:"";
		$property_address = ($content['Address'])?$content['Address']:"";
        $city = ($content['City'])?$content['City']:"";
		$firstname = ($content['Your Name'])?$content['Your Name']:"";
        $email = ($content['Your Email'])?$content['Your Email']:"";
		$phone = ($content['Telephone'])?$content['Telephone']:"";
		$comments = ($content['Comments'])?$content['Comments']:"";
        
        $hs_context_json = _get_hs_context("Contact Us", "https://www.mesaproperties.net/contact-us");
		
		$str_post = "property_type=". urlencode($property_type)
		. "&is_the_property_vacant_=". urlencode($is_the_property_vacant_)
		. "&property_address=". urlencode($property_address)
		. "&city=". urlencode($city)
		. "&firstname=" . urlencode($firstname)
        . "&email=" . urlencode($email)
		. "&phone=" . urlencode($phone)
		. "&comments=" . urlencode($comments)
        . "&hs_context=" . urlencode($hs_context_json); 

		$endpoint = 'https://forms.hubspot.com/uploads/form/v2/' . HS_PORTAL . '/' . 				GUID_CONTACT_US_FORM;
		_post_data_to_hs($str_post, $endpoint);
	}
}
