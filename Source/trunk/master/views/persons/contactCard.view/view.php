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
importer::import("ENP", "Relations");
importer::import("UI", "Apps");
importer::import("UI", "Forms");

// Import APP Packages
//#section_end#
//#section#[view]
use \AEL\Literals\appLiteral;
use \ENP\Relations\ePerson;
use \UI\Apps\APPContent;
use \UI\Forms\templates\simpleForm;

// Create Application Content
$appContent = new APPContent($appID);
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "personCardContainer", TRUE);

// Get person id to show detail for
$personID = engine::getVar("pid");
$ePerson = new ePerson($personID);
$personInfo = $ePerson->info();

// Set name
$name = HTML::select(".personCard .sidebar .name")->item(0);
if (!empty($personInfo['middle_name']))
	$fullname = $personInfo['firstname']." ".$personInfo['middle_name']." ".$personInfo['lastname'];
else
	$fullname = $personInfo['firstname']." ".$personInfo['lastname'];
HTML::innerHTML($name, $fullname);

// Person info section
$detailsContainer = HTML::select(".personCard .detailsContainer")->item(0);
$section = DOM::create("div", "", "", "section person_info");
DOM::append($detailsContainer, $section);

// Add customer button
$sidebar = HTML::select(".personCard .sidebar")->item(0);
$form = new simpleForm();
$addForm = $form->build("", FALSE)->engageApp("persons/addCustomer")->get();
HTML::addClass($addForm, "addcform");
DOM::append($sidebar, $addForm);

// Person id
$input = $form->getInput($type = "hidden", $name = "pid", $value = $personID, $class = "", $autofocus = FALSE, $required = FALSE);
$form->append($input);

// Submit
$title = appLiteral::get("main.list", "btn_add_customer");
$button = $form->getSubmitButton($title, $id = "btn_add_customer");
$form->append($button);

// Load person info
$attr = array();
$attr['cid'] = $personID;
$attr['pid'] = $personID;
$viewContainer = $appContent->getAppViewContainer($viewName = "persons/personInfo", $attr, $startup = FALSE, $containerID = "personInfoViewContainer", $loading = FALSE, $preload = TRUE);
DOM::append($section, $viewContainer);

// Action to switch to details view
$appContent->addReportAction($name = "listviewer.switchto.details");

// Return output
return $appContent->getReport();
//#section_end#
?>