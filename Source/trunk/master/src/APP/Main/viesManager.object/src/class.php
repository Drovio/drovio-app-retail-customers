<?php
//#section#[header]
// Namespace
namespace APP\Main;

require_once($_SERVER['DOCUMENT_ROOT'].'/_domainConfig.php');

// Use Important Headers
use \API\Platform\importer;
use \Exception;

// Check Platform Existance
if (!defined('_RB_PLATFORM_')) throw new Exception("Platform is not defined!");

// Import application loader
importer::import("AEL", "Platform", "application");
use \AEL\Platform\application;
//#section_end#
//#section#[class]
/**
 * @library	APP
 * @package	Main
 * 
 * @copyright	Copyright (C) 2015 RetailCustomers. All rights reserved.
 */

importer::import("AEL", "Resources", "DOMParser");

use \AEL\Resources\DOMParser;

/**
 * VIES Manager and Parser
 * 
 * Parses the VIES website and gets company information given the VAT and state code.
 * 
 * @version	0.1-1
 * @created	November 1, 2015, 1:03 (GMT)
 * @updated	November 1, 2015, 1:03 (GMT)
 */
class viesManager
{
	/**
	 * Get company information.
	 * 
	 * @param	integer	$vat
	 * 		The company vat number.
	 * 
	 * @param	string	$stateCode
	 * 		The state code.
	 * 
	 * @return	array
	 * 		An array including the 'name' and the 'address' of the company.
	 */
	public static function getCompanyInfo($vat, $stateCode = "EL")
	{
		// Add parameters
		$parameters = array();
		$parameters['memberStateCode'] = $stateCode;
		$parameters['number'] = $vat;
		$parameters['requesterMemberStateCode'] = $stateCode;
		$parameters['requesterNumber'] = $vat;
		
		$url = "http://ec.europa.eu/taxation_customs/vies/vatResponse.html?";
		foreach ($parameters as $name => $value)
			$url .= $name."=".$value."&";

		// Get the response page
		$responsePage = self::curl($url, $parameters);
		
		// Parse the page
		$parser = new DOMParser();
		$parser->loadContent($responsePage, $contentType = DOMParser::TYPE_HTML, $preserve = TRUE);
		
		// Initialize company info
		$companyInfo = array();
		
		// Get company info
		$companyInfo['name'] = trim($parser->evaluate("//table[@id='vatResponseFormTable']/tr[6]/td[2]")->item(0)->nodeValue);
		$companyInfo['address'] = trim($parser->evaluate("//table[@id='vatResponseFormTable']/tr[7]/td[2]")->item(0)->nodeValue);
		
		// Return company info
		return $companyInfo;
	}
	
	/**
	 * Make the cURL request to vies page.
	 * 
	 * @param	string	$url
	 * 		The url value.
	 * 
	 * @param	array	$parameters
	 * 		The post parameters.
	 * 
	 * @return	mixed
	 * 		The cURL response.
	 */
	private function curl($url, $parameters = array())
	{
		// Initialize cURL
		$curl = curl_init();

		// Set options
		$options = array();
		$options[CURLOPT_RETURNTRANSFER] = 1;
		$options[CURLOPT_URL] = $url;
		
		// Set post parameters
		$options[CURLOPT_POST] = 1;
		$options[CURLOPT_POSTFIELDS] = $parameters;
		
		// Set options array
		curl_setopt_array($curl, $options);
		
		// Execute and close url
		$response = curl_exec($curl);
		curl_close($curl);
		
		// Return response
		return $response;
	}
}
//#section_end#
?>