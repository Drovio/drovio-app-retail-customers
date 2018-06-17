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
importer::import("RTL", "Invoices");
importer::import("RTL", "Relations");
importer::import("UI", "Apps");

// Import APP Packages
//#section_end#
//#section#[view]
use \RTL\Invoices\invoice;
use \RTL\Relations\customer;
use \UI\Apps\APPContent;

// Create Application Content
$appContent = new APPContent($appID);
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "customerFinancesContainer", TRUE);
$viewContainer = HTML::select(".customerFinances")->item(0);

// Get customer id
$customerID = engine::getVar("cid");
$customer = new customer($customerID);
$customerInfo = $customer->info();

// Balance
$balance = $customerInfo['balance'];
$balance = (empty($balance) ? 0 : $balance);
$infoRow = getCustomerInfoRow("balance", number_format($balance, 2)." €");
DOM::append($viewContainer, $infoRow);

// Invoice container
$invoiceContainer = DOM::create("div", "", "", "invoiceContainer");
DOM::append($viewContainer, $invoiceContainer);

// Load invoices button
$title = $appContent->getLiteral("customers.finances", "lbl_loadInvoices");
$invoicesButton = DOM::create("div", $title, "", "invbutton");
DOM::append($invoiceContainer, $invoicesButton);
// Set action
$attr = array();
$attr['cid'] = $customerID;
$actionFactory->setAction($invoicesButton, "customers/invoices/invoiceList", ".invoiceContainer", $attr);

// Return output
return $appContent->getReport();

function geFinancesInfoRow($type, $value)
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