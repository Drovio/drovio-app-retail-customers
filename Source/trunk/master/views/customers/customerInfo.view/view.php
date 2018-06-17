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
importer::import("RTL", "Relations");
importer::import("UI", "Apps");

// Import APP Packages
//#section_end#
//#section#[view]
use \RTL\Relations\customer;
use \UI\Apps\APPContent;

// Create Application Content
$appContent = new APPContent($appID);
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "customerInfoContainer", TRUE);
$viewContainer = HTML::select(".customerInfo .viewContainer")->item(0);

// Get customer id
$customerID = engine::getVar("cid");
$customer = new customer($customerID);
$customerInfo = $customer->info();

$infoRow = getCustomerInfoRow("info", $customerInfo['firstname']." ".$customerInfo['lastname']);
DOM::append($viewContainer, $infoRow);

// Occupation
if (!empty($customerInfo['occupation']))
{
	$infoRow = getCustomerInfoRow("occupation", $customerInfo['occupation']);
	DOM::append($viewContainer, $infoRow);
}

// Company information
if ($customerInfo['is_company'])
{
	// SSN
	if (!empty($customerInfo['company_name']))
	{
		$infoRow = getCustomerInfoRow("company", $customerInfo['company_name']);
		DOM::append($viewContainer, $infoRow);
	}
	
	// SSN
	if (!empty($customerInfo['tax_id']))
	{
		$attr = array();
		$attr['taxid'] = $customerInfo['tax_id'];
		$value = $appContent->getLiteral("customers.details", "lbl_taxid", $attr);
		$infoRow = getCustomerInfoRow("cinfo", $value);
		DOM::append($viewContainer, $infoRow);
	}
	
	// IRS
	if (!empty($customerInfo['irs']))
	{
		$attr = array();
		$attr['irs'] = $customerInfo['irs'];
		$value = $appContent->getLiteral("customers.details", "lbl_irs", $attr);
		$infoRow = getCustomerInfoRow("cinfo", $value);
		DOM::append($viewContainer, $infoRow);
	}
}

// Add action to edit button
$editButton = HTML::select(".customerInfo .edit")->item(0);
$attr = array();
$attr['cid'] = $customerID;
$actionFactory->setAction($editButton, $viewName = "customers/editCustomerInfo", $holder = ".customerInfoContainer .editFormContainer", $attr, $loading = TRUE);

// Return output
return $appContent->getReport();

function getCustomerInfoRow($type, $value)
{
	$infoRow = DOM::create("div", "", "", "infoRow");
	HTML::addClass($infoRow, $type);
	
	// Create ico
	$ico = DOM::create("div", "", "", "ico");
	DOM::append($infoRow, $ico);
	
	$value = DOM::create("div", $value, "", "ivalue");
	DOM::append($infoRow, $value);
	
	return $infoRow;
}
//#section_end#
?>