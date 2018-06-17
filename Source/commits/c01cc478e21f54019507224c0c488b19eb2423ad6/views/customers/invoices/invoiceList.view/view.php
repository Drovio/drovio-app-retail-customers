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
use \UI\Apps\APPContent;
use \RTL\Invoices\invoice;
use \RTL\Relations\customer;

// Create Application Content
$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "customerInvoiceList");

// Get customer id
$customerID = engine::getVar("cid");
$customer = new customer($customerID);

// Get all invoice types
$invoiceTypes = invoice::getInvoiceTypes($compact = TRUE);

// List all history invoices
$invoices = $customer->getCustomerInvoices();
foreach ($invoices as $invoiceInfo)
{
	// Create invoice row
	$ivrow = DOM::create("div", "", "", "ivrow");
	$appContent->append($ivrow);
	
	// Get prices
	$attr = array();
	$attr['total_price'] = number_format($invoiceInfo['total_price'] + $invoiceInfo['total_tax'], 2);
	$attr['price_no_tax'] = number_format($invoiceInfo['total_price'], 2);
	$title = $appContent->getLiteral("customers.finances", "lbl_invoicePrice", $attr);
	$ivf = DOM::create("div", $title, "", "ivf ivprice");
	DOM::append($ivrow, $ivf);
	
	// Invoice type
	$ivf = DOM::create("div", $invoiceTypes[$invoiceInfo['type_id']], "", "ivf type");
	DOM::append($ivrow, $ivf);
	
	
	// Invoice payment and balance
	$attr = array();
	$attr['total_payments'] = number_format($invoiceInfo['total_payments'], 2);
	$attr['balance'] = number_format($invoiceInfo['total_price'] + $invoiceInfo['total_tax'] - $invoiceInfo['total_payments'], 2);
	$title = $appContent->getLiteral("customers.finances", "lbl_invoiceBalance", $attr);
	$ivf = DOM::create("div", $title, "", "ivf ivbalance");
	DOM::append($ivrow, $ivf);
	
	// Invoice date
	$date = $invoiceInfo['date_created'];
	if (empty($date))
		$date = date("d M, Y, H:i", $invoiceInfo['time_created']);
	$ivf = DOM::create("div", $date, "", "ivf ivdate");
	DOM::append($ivrow, $ivf);
}

if (empty($invoices))
{
	$title = $appContent->getLiteral("customers.finances", "lbl_noInvoices");
	$hd = DOM::create("h2", $title, "", "hd");
	$appContent->append($hd);
}

// Return output
return $appContent->getReport();
//#section_end#
?>