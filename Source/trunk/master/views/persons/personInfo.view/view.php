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
importer::import("UI", "Apps");

// Import APP Packages
//#section_end#
//#section#[view]
use \ENP\Relations\ePerson;
use \ENP\Relations\ePersonAddress;
use \ENP\Relations\ePersonMail;
use \ENP\Relations\ePersonPhone;
use \UI\Apps\APPContent;

// Create Application Content
$appContent = new APPContent($appID);
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "personInfoContainer", TRUE);
$viewContainer = HTML::select(".personInfo")->item(0);

// Get person id to show detail for
$personID = engine::getVar("pid");
$person = new ePerson($personID);
$personInfo = $person->info();

$infoRow = getPersonInfoRow("info", $personInfo['firstname']." ".$personInfo['lastname']);
DOM::append($viewContainer, $infoRow);

// List all phones
$pPhoneManager = new ePersonPhone($personID);
$phones = $pPhoneManager->getAllPhones();
foreach ($phones as $phoneInfo)
{
	$infoRow = getPersonInfoRow("phone", $phoneInfo['phone']);
	DOM::append($viewContainer, $infoRow);
}

// List all mails
$pMailManager = new ePersonMail($personID);
$mail = $pMailManager->getAllMail();
foreach ($mail as $mailInfo)
{
	$infoRow = getPersonInfoRow("mail", $mailInfo['mail']);
	DOM::append($viewContainer, $infoRow);
}

// List all addresses
$pAddressManager = new ePersonAddress($personID);
$addresses = $pAddressManager->getAllAddresses();
foreach ($addresses as $addressInfo)
{
	$value = $addressInfo['address'].", ".$addressInfo['postal_code'].", ".$addressInfo['city'];
	$infoRow = getPersonInfoRow("address", $value);
	DOM::append($viewContainer, $infoRow);
}

// Birthday
if (!empty($personInfo['date_of_birth']))
{
	$infoRow = getPersonInfoRow("birthday", $personInfo['date_of_birth']);
	DOM::append($viewContainer, $infoRow);
}

// Notes
if (!empty($personInfo['notes']))
{
	$infoRow = getPersonInfoRow("notes", $personInfo['notes']);
	DOM::append($viewContainer, $infoRow);
}

// Add action to edit button
$editButton = HTML::select(".contactInfo .edit")->item(0);
$attr = array();
$attr['pid'] = $personID;
$actionFactory->setAction($editButton, $viewName = "details/editContactInfo", $holder = ".contactInfoContainer .editFormContainer", $attr, $loading = TRUE);

// Action to switch to details view
$appContent->addReportAction($name = "listviewer.switchto.details");

// Return output
return $appContent->getReport();

function getPersonInfoRow($type, $value)
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