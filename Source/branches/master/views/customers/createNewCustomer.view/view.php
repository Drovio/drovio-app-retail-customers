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
importer::import("ENP", "Relations");
importer::import("RTL", "Relations");
importer::import("UI", "Apps");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");

// Import APP Packages
application::import("Main");
//#section_end#
//#section#[view]
use \APP\Main\viesManager;
use \UI\Apps\APPContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\popups\popup;
use \ENP\Relations\ePersonAddress;
use \RTL\Relations\customer;

$appContent = new APPContent($appID);
$actionFactory = $appContent->getActionFactory();

if (engine::isPost())
{
	// Check if something is empty
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Check contact name
	if (empty($_POST['fullname']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = $appContent->getLiteral("main.create", "lbl_fullname_placeholder");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Create new contact
	$customer = new customer();
	
	$name = $_POST['fullname'];
	$nameParts = explode(" ", $name);
	$firstname = $nameParts[0];
	unset($nameParts[0]);
	$lastname = implode(" ", $nameParts);
	$status = $customer->create($firstname, $lastname);
	
	// Get company info if not empty vat
	$companyVat = engine::getVar("company_vat");
	if (!empty($companyVat))
	{
		// Get company info from vies
		$companyInfo = viesManager::getCompanyInfo($companyVat);
		
		// Update company basic info
		$customer->updateCompanyInfo($isCompany = TRUE, $companyInfo['name'], $companyVat, "");
		
		// If there is an address, add it to the person
		if (!empty($companyInfo['address']))
		{
			$pAddress = new ePersonAddress($customer->getPersonID());
			$pAddress->create($typeID = 2, $companyInfo['address'], $postal_code = "", $city = "", $countryID = "");
		}
	}
	
	// If there is an error in creating the library, show it
	if (!$status)
	{
		$err_header = $appContent->getLiteral("customers.create", "lbl_create");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, DOM::create("span", "Error creating customer..."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE, $disposable = FALSE);
	
	// Add action to reload list
	$succFormNtf->addReportAction($type = "customers.list.reload");
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}

// Build the application view content
$appContent->build("", "createNewCustomerDialog", TRUE);

$formContainer = HTML::select(".createNewCustomerDialog .formContainer")->item(0);
// Build form
$form = new simpleForm("");
$imageForm = $form->build($action = "", $defaultButtons = FALSE)->engageApp("customers/createNewCustomer")->get();
DOM::append($formContainer, $imageForm);

// Customer name
$ph = $appContent->getLiteral("customers.create", "lbl_fullname_placeholder", array(), FALSE);
$input = $form->getInput($type = "text", $name = "fullname", $value = "", $class = "bginp", $autofocus = TRUE, $required = TRUE);
DOM::attr($input, "placeholder", $ph);
$form->append($input);

// Company VAT
$ph = $appContent->getLiteral("customers.create", "lbl_companyvat_placholder", array(), FALSE);
$input = $form->getInput($type = "text", $name = "company_vat", $value = "", $class = "bginp", $autofocus = FALSE, $required = FALSE);
DOM::attr($input, "placeholder", $ph);
$form->append($input);

$title = $appContent->getLiteral("customers.create", "lbl_create");
$create_btn = $form->getSubmitButton($title, $id = "btn_create", $name = "");
$form->append($create_btn);

// Create popup
$pp = new popup();
$pp->type($type = popup::TP_PERSISTENT, $toggle = FALSE);
$pp->background(TRUE);
$pp->build($appContent->get());

return $pp->getReport();
//#section_end#
?>