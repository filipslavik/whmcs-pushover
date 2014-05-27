<?php
/**
 * WHMCS Pushover Notifications Hooks
 *
 * Use this addon to use the Pushover (pushover.net) API
 *
 * @package    WHMCS 5.2.1+
 * @author     Myles McNamara (get@smyl.es)
 * @copyright  Copyright (c) Myles McNamara 2013-2014
 * @license    GPL v3+
 * @version    1.0.1
 * @link       https://github.com/tripflex/whmcs-pushover
 */

if (!defined("WHMCS"))
	die("This file cannot be accessed directly");

define( 'PO_ROOT', dirname( __FILE__ ) );

require_once( PO_ROOT . '/functions.php');
require_once( PO_ROOT . '/inc/whmcse.php' );

function pushover_ticket_open($vars) {

	$ticketid = $vars['ticketid'];
	$userid = $vars['userid'];
	$deptid = $vars['deptid'];
	$deptname = $vars['deptname'];
	$subject = $vars['subject'];
	$message = $vars['message'];
	$priority = $vars['priority'];

	// Convert HTML entities (single/double quote) back to single or double quote
	$message = htmlspecialchars_decode($message, ENT_QUOTES);

	$pushover_userkey = po_get_userkey();
	if (!$pushover_userkey) return false;

	$po_ticket_url = po_get_admin_ticket_url($ticketid);
	
	$pushover_api_url = 'https://api.pushover.net/1/messages.json';
	$pushover_app_token = 'a7HcPjJeGmAtyG4e6tCYqyXk5wc5Xj';
	$pushover_title = '[Ticket ID: '.$ticketid.'] New Support Ticket Opened';
	$pushover_url = $po_ticket_url;
	$pushover_url_title = "Open Admin Area to View Ticket";
	$pushover_message = $message;

	$pushover_post_fields = array(
			  	'token' => $pushover_app_token,
			  	'user' => $pushover_userkey,
			  	'title' => $pushover_title,
			  	'message' => $pushover_message,
			  	'url' => $pushover_url,
			  	'url_title' => $pushover_url_title,
			  	'priority' => 1
			  	);

	// Convert to URL-Encoded string to post as application/x-www-form-urlencoded
	$pushover_encoded_post_fields = http_build_query($pushover_post_fields);

	$pushover_resp = curlCall($pushover_api_url, $pushover_encoded_post_fields, $pushover_options);

	$parsed_resp = json_decode($pushover_resp, TRUE);

	if ($parsed_resp['status'] != 1) {
		logModuleCall('pushover','pushover_server_error', $pushover_post_fields, $pushover_resp);
	}

	logModuleCall('pushover','hook_ticket_open', $pushover_post_fields, $pushover_resp);

}
function pushover_ticket_reply($vars) {

	$ticketid = $vars['ticketid'];
	$userid = $vars['userid'];
	$deptid = $vars['deptid'];
	$deptname = $vars['deptname'];
	$subject = $vars['subject'];
	$message = $vars['message'];
	$priority = $vars['priority'];

	// Convert HTML entities (single/double quote) back to single or double quote
	$message = htmlspecialchars_decode($message, ENT_QUOTES);

	$pushover_userkey = po_get_userkey();
	if (!$pushover_userkey) return false;

	$po_ticket_url = po_get_admin_ticket_url($ticketid);

	$pushover_api_url = 'https://api.pushover.net/1/messages.json';
	$pushover_app_token = 'a7HcPjJeGmAtyG4e6tCYqyXk5wc5Xj';
	$pushover_title = '[Ticket ID: '.$ticketid.'] New Support Ticket Response';
	$pushover_url = $po_ticket_url;
	$pushover_url_title = "Open Admin Area to View Ticket";
	$pushover_message = $message;

	$pushover_post_fields = array(
			  	'token' => $pushover_app_token,
			  	'user' => $pushover_userkey,
			  	'title' => $pushover_title,
			  	'message' => $pushover_message,
			  	'url' => $pushover_url,
			  	'url_title' => $pushover_url_title,
			  	'priority' => 1
			  	);

	// Convert to URL-Encoded string to post as application/x-www-form-urlencoded
	$pushover_encoded_post_fields = http_build_query($pushover_post_fields);

	$pushover_resp = curlCall($pushover_api_url, $pushover_encoded_post_fields, $pushover_options);

	$parsed_resp = json_decode($pushover_resp, TRUE);

	if ($parsed_resp['status'] != 1) {
		logModuleCall('pushover','pushover_server_error', $pushover_post_fields, $pushover_resp);
	}

	logModuleCall('pushover','hook_ticket_open', $pushover_post_fields, $pushover_resp);

}

function po_check_update() {

	return PO_WHMCSe::output_update('https://github.com/tripflex/whmcs-pushover/raw/master/release', '1.0.1', 'Pushover');
}

add_hook( "AdminHomepage", 1, "po_check_update" );

add_hook("TicketOpen",1,"pushover_ticket_open");
add_hook("TicketUserReply",1,"pushover_ticket_reply");