<?php
//#section#[header]
// Use Important Headers
use \API\Platform\importer;
use \API\Platform\engine;
use \Exception;

// Check Platform Existance
if (!defined('_RB_PLATFORM_')) throw new Exception("Platform is not defined!");

// Import DOM, HTML
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");

use \UI\Html\DOM;
use \UI\Html\HTML;

// Import application for initialization
importer::import("AEL", "Platform", "application");
use \AEL\Platform\application;

// Increase application's view loading depth
application::incLoadingDepth();

// Set Application ID
$appID = 88;

// Init Application and Application literal
application::init(88);
// Secure Importer
importer::secure(TRUE);

// Import SDK Packages
importer::import("AEL", "Literals");
importer::import("RTL", "Relations");
importer::import("UI", "Apps");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");

// Import APP Packages
//#section_end#
//#section#[view]
use \AEL\Literals\appLiteral;
use \RTL\Relations\customer;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;

$customerID = engine::getVar("cid");
$customer = new customer($customerID);
if (engine::isPost())
{
	// Create form error Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Delete customer
	$status = $customer->remove();

	// If there is an error in creating the folder, show it
	if ($status !== TRUE)
	{
		$err_header = appLiteral::get("customers.details", "hd_deleteCustmer");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, DOM::create("span", "Error deleting relation."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE);
	
	// Add action to reload list
	$succFormNtf->addReportAction($type = "customers.list.reload", $value = "");
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}


// Build the frame
$frame = new dialogFrame();
$title = appLiteral::get("customers.details", "hd_deleteCustmer");
$frame->build($title, "", FALSE)->engageApp("customers/deleteCustomer");
$form = $frame->getFormFactory();

// Header
$customerInfo = $customer->info();
$attr = array();
$attr['cname'] = $customerInfo['firstname']." ".$customerInfo['lastname'];
$title = appLiteral::get("customers.details", "lbl_sureDeleteCustomer", $attr);
$hd = DOM::create("h3", $title);
$frame->append($hd);

$title = appLiteral::get("customers.details", "lbl_deleteCustomerNotice", $attr);
$hd = DOM::create("p", $title);
$frame->append($hd);

// Person id
$input = $form->getInput($type = "hidden", $name = "cid", $value = $customerID, $class = "", $autofocus = FALSE, $required = FALSE);
$frame->append($input);

// Return the report
return $frame->getFrame();
//#section_end#
?>