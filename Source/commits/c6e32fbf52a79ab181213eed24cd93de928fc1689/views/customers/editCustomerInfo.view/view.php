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
	// Update person information
	$customer = new customer($customerID);
	$customer->update($_POST['firstname'], $_POST['lastname'], $_POST['middlename'], $_POST['occupation']);
	
	// Update company information
	$isCompany = isset($_POST['is_company']);
	$customer->updateCompanyInfo($isCompany, $_POST['cname'], $_POST['taxid'], $_POST['irs']);
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE, $disposable = FALSE);
	
	// Add action to reload info
	$succFormNtf->addReportAction($type = "customerinfo.reload", $value = "");
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}

// Build the application view content
$appContent->build("", "editCustomerInfoContainer", TRUE);
$formContainer = HTML::select(".editCustomerInfo .formContainer")->item(0);

// Build form
$form = new simpleForm();
$editForm = $form->build()->engageApp("customers/editCustomerInfo")->get();
DOM::append($formContainer, $editForm);

$input = $form->getInput($type = "hidden", $name = "cid", $value = $customerID, $class = "", $autofocus = FALSE, $required = FALSE);
$form->append($input);


// Person basic info
$customer = new customer($customerID);
$customerInfo = $customer->info();

// Basic Info
$title = $appContent->getLiteral("customers.details.edit", "hd_basicInfo");
$group = getEditGroup($title, FALSE);
$form->append($group);

$title = $appContent->getLiteral("customers.details.edit", "lbl_firstname");
$ph = $appContent->getLiteral("customers.details.edit", "lbl_firstname", array(), FALSE);
$fRow = getSimpleFormRow($form, $title, $customerInfo['firstname'], $ph, $name = "firstname");
DOM::append($group, $fRow);

$title = $appContent->getLiteral("customers.details.edit", "lbl_middlename");
$ph = $appContent->getLiteral("customers.details.edit", "lbl_middlename", array(), FALSE);
$fRow = getSimpleFormRow($form, $title, $customerInfo['middle_name'], $ph, $name = "middlename");
DOM::append($group, $fRow);

$title = $appContent->getLiteral("customers.details.edit", "lbl_lastname");
$ph = $appContent->getLiteral("customers.details.edit", "lbl_lastname", array(), FALSE);
$fRow = getSimpleFormRow($form, $title, $customerInfo['lastname'], $ph, $name = "lastname");
DOM::append($group, $fRow);

$title = $appContent->getLiteral("customers.details.edit", "lbl_occupation");
$ph = $appContent->getLiteral("customers.details.edit", "lbl_occupation", array(), FALSE);
$fRow = getSimpleFormRow($form, $title, $customerInfo['occupation'], $ph, $name = "occupation");
DOM::append($group, $fRow);

// Company Info
$title = $appContent->getLiteral("customers.details.edit", "hd_companyInfo");
$group = getEditGroup($title, FALSE);
$form->append($group);

$title = $appContent->getLiteral("customers.details.edit", "lbl_isCompany");
$fRow = getSimpleFormRow($form, $title, $customerInfo['is_company'], $ph = "", $name = "is_company", $type = "checkbox");
DOM::append($group, $fRow);

$companyInfoContainer = DOM::create("div", "", "", "cInfoContainer");
DOM::append($group, $companyInfoContainer);
if ($customerInfo['is_company'])
	HTML::addClass($companyInfoContainer, "open");
	

$title = $appContent->getLiteral("customers.details.edit", "lbl_company_name");
$ph = $appContent->getLiteral("customers.details.edit", "lbl_company_name", array(), FALSE);
$fRow = getSimpleFormRow($form, $title, $customerInfo['company_name'], $ph, $name = "cname");
DOM::append($companyInfoContainer, $fRow);

$title = $appContent->getLiteral("customers.details.edit", "lbl_taxid");
$ph = $appContent->getLiteral("customers.details.edit", "lbl_taxid_ph", array(), FALSE);
$fRow = getSimpleFormRow($form, $title, $customerInfo['tax_id'], $ph, $name = "taxid");
DOM::append($companyInfoContainer, $fRow);

$title = $appContent->getLiteral("customers.details.edit", "lbl_irs");
$ph = $appContent->getLiteral("customers.details.edit", "lbl_irs_ph", array(), FALSE);
$fRow = getSimpleFormRow($form, $title, $customerInfo['irs'], $ph, $name = "irs");
DOM::append($companyInfoContainer, $fRow);


// Set action to switch to edit info
$appContent->addReportAction($type = "customerinfo.edit", $value = "");

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