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
$appContent->build("", "customerCardContainer", TRUE);

// Get customer id to show details for
$customerID = engine::getVar("cid");
$customer = new customer($customerID);
$customerInfo = $customer->info();

// Set name
$name = HTML::select(".customerCard .sidebar .name")->item(0);
if (!empty($customerInfo['middle_name']))
	$fullname = $customerInfo['firstname']." ".$customerInfo['middle_name']." ".$customerInfo['lastname'];
else
	$fullname = $customerInfo['firstname']." ".$customerInfo['lastname'];
HTML::innerHTML($name, $fullname);

// Contact info section
$detailsContainer = HTML::select(".customerCard .detailsContainer")->item(0);
$section = DOM::create("div", "", "", "section customer_info");
DOM::append($detailsContainer, $section);

// Load customer info
$attr = array();
$attr['cid'] = $customerID;
$viewContainer = $appContent->getAppViewContainer($viewName = "customers/customerInfo", $attr, $startup = FALSE, $containerID = "customerInfoViewContainer", $loading = FALSE, $preload = TRUE);
DOM::append($section, $viewContainer);

// Contact finances section
$detailsContainer = HTML::select(".customerCard .detailsContainer")->item(0);
$section = DOM::create("div", "", "", "section finances_info");
DOM::append($detailsContainer, $section);

// Load customer finances
$attr = array();
$attr['cid'] = $customerID;
$viewContainer = $appContent->getAppViewContainer($viewName = "customers/customerFinances", $attr, $startup = FALSE, $containerID = "financesInfoViewContainer", $loading = FALSE, $preload = TRUE);
DOM::append($section, $viewContainer);

// Person info section
$detailsContainer = HTML::select(".customerCard .detailsContainer")->item(0);
$section = DOM::create("div", "", "", "section person_info");
DOM::append($detailsContainer, $section);

// Load customer's connected person info
$attr = array();
$attr['cid'] = $customerID;
$attr['pid'] = $customerID;
$viewContainer = $appContent->getAppViewContainer($viewName = "persons/personInfo", $attr, $startup = FALSE, $containerID = "personInfoViewContainer", $loading = FALSE, $preload = TRUE);
DOM::append($section, $viewContainer);


// Delete relation button
$deleteButton = HTML::select(".customerCard .abutton.delete")->item(0);
$attr = array();
$attr['cid'] = $customerID;
$actionFactory->setAction($deleteButton, $viewName = "customers/deleteCustomer", $holder = "", $attr, $loading = TRUE);

// Action to switch to details view
$appContent->addReportAction($name = "listviewer.switchto.details");

// Return output
return $appContent->getReport();
//#section_end#
?>