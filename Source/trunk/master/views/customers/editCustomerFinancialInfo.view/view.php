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
importer::import("UI", "Forms");

// Import APP Packages
//#section_end#
//#section#[view]
use \RTL\Relations\customer;
use \UI\Apps\APPContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;

// Create Application Content
$appContent = new APPContent($appID);
$actionFactory = $appContent->getActionFactory();

// Get customer id to edit
$customerID = engine::getVar("cid");
if (engine::isPost())
{
	// Update customer balance
	$customer = new customer($customerID);
	$customer->updateBalance($_POST['cbalance']);
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE, $disposable = FALSE);
	
	// Add action to reload info
	$succFormNtf->addReportAction($type = "customerfinances.reload", $value = "");
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}

// Build the application view content
$appContent->build("", "editCustomerFinancialInfoContainer", TRUE);
$formContainer = HTML::select(".editCustomerFinancialInfo .formContainer")->item(0);

// Build form
$form = new simpleForm();
$editForm = $form->build()->engageApp("customers/editCustomerFinancialInfo")->get();
DOM::append($formContainer, $editForm);

$input = $form->getInput($type = "hidden", $name = "cid", $value = $customerID, $class = "", $autofocus = FALSE, $required = FALSE);
$form->append($input);


// Person basic info
$customer = new customer($customerID);
$customerInfo = $customer->info();

// Basic Info
$title = $appContent->getLiteral("customers.finances.edit", "hd_financialInfo");
$group = getEditGroup($title, FALSE);
$form->append($group);

$title = $appContent->getLiteral("customers.finances.edit", "lbl_balance");
$ph = $appContent->getLiteral("customers.finances.edit", "lbl_balance", array(), FALSE);
$fRow = getSimpleFormRow($form, $title, $customerInfo['balance'], $ph, $name = "cbalance");
DOM::append($group, $fRow);


// Set action to switch to edit info
$appContent->addReportAction($type = "customerfinances.edit", $value = "");

// Return output
return $appContent->getReport();

function getEditGroup($title, $newButton = TRUE)
{
	$group = DOM::create("div", "", "", "editGroup");
	
	// Add new button
	if ($newButton)
	{
		$create_new = DOM::create("div", "", "", "ico create_new");
		DOM::append($group, $create_new);
	}
	
	// Header
	$hd = DOM::create("h3", $title, "", "ghd");
	DOM::append($group, $hd);
	
	return $group;
}

function getSimpleFormRow($form, $labelTitle, $valueValue, $ph, $name, $inputType = "text")
{
	// Create a new row
	$fRow = DOM::create("div", "", "", "frow");
	
	$input = $form->getInput($type = $inputType, $name, $value = $valueValue, $class = "finput", $autofocus = FALSE, $required = FALSE);
	DOM::attr($input, "placeholder", $ph);
	if (($inputType == "radio" || $inputType == "checkbox") && $valueValue == 1)
		DOM::attr($input, "checked", "checked");
	$inputID = DOM::attr($input, "id");
	$label = $form->getLabel($labelTitle, $for = $inputID, $class = "flabel");
	
	// Append to frow
	DOM::append($fRow, $label);
	DOM::append($fRow, $input);
	
	return $fRow;
}
//#section_end#
?>