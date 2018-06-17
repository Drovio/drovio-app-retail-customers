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
importer::import("RTL", "Relations");
importer::import("UI", "Apps");
importer::import("UI", "Forms");

// Import APP Packages
//#section_end#
//#section#[view]
use \AEL\Literals\appLiteral;
use \ENP\Relations\ePerson;
use \RTL\Relations\customer;
use \UI\Apps\APPContent;
use \UI\Forms\templates\simpleForm;

// Create Application Content
$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "customersListViewContainer", TRUE);

// Get all customers
$teamCustomers = customer::getCustomers();
$customerContainer = HTML::select(".customersListView .clist.customers")->item(0);
$customerContacts = array();
foreach ($teamCustomers as $customerInfo)
{
	// Set customer as contact
	$customerID = $customerInfo['person_id'];
	$customerContacts[$customerID] = 1;
	
	// Create the list item
	$customerName = $customerInfo['firstname']." ".$customerInfo['lastname'];
	$listItem = getListItem($customerName, $info = array());
	DOM::append($customerContainer, $listItem);
	
	// Set action
	$attr = array();
	$attr['cid'] = $customerID;
	$attr['pid'] = $customerID;
	$actionFactory->setAction($listItem, "customers/customerCard", ".customersListView .detailsContainer .wbox.details", $attr, $loading = TRUE);
}

// Get all relation persons
$personRelations = ePerson::getPersons();
$contactsContainer = HTML::select(".customersListView .clist.contacts")->item(0);
foreach ($personRelations as $personInfo)
{
	$personID = $personInfo['id'];
	
	// Check if person was a customer and skip
	if ($customerContacts[$personID])
		continue;
	
	// Create the list item
	$personName = $personInfo['firstname']." ".$personInfo['lastname'];
	$info = array();
	$info['add_customer'] = 1;
	$info['person_id'] = $personID;
	$listItem = getListItem($personName, $info);
	DOM::append($contactsContainer, $listItem);
	
	// Set action
	$attr = array();
	$attr['cid'] = $personID;
	$attr['pid'] = $personID;
	$actionFactory->setAction($listItem, "persons/contactCard", ".customersListView .detailsContainer .wbox.details", $attr, $loading = TRUE);
}

// Return output
return $appContent->getReport();

function getListItem($itemName, $info = array())
{
	// Create person list item
	$listItem = DOM::create("div", "", "", "listItem");
	
	// Ico
	$ico = DOM::create("div", "", "", "ico");
	DOM::append($listItem, $ico);
	if (!empty($image))
	{
		$img = DOM::create("img");
		DOM::attr($img, "src", $image);
		DOM::append($ico, $img);
	}
	
	// Check to create form to add to customers
	if ($info['add_customer'])
	{
		$form = new simpleForm();
		$addForm = $form->build("", FALSE)->engageApp("persons/addCustomer")->get();
		HTML::addClass($addForm, "addcform");
		DOM::append($listItem, $addForm);
		
		// Person id
		$input = $form->getInput($type = "hidden", $name = "pid", $value = $info['person_id'], $class = "", $autofocus = FALSE, $required = FALSE);
		$form->append($input);
		
		// Submit
		$title = appLiteral::get("main.list", "btn_add_customer");
		$button = $form->getSubmitButton($title, $id = "btn_add_customer");
		$form->append($button);
	}
	
	// Name
	$name = DOM::create("div", $itemName, "", "name");
	DOM::append($listItem, $name);
	
	return $listItem;
}
//#section_end#
?>