<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteCreatorForm{
	
	private static $isFormIncluded = false;	  //indicator that the form included once
	
	
	/**
	 * add conditions elementor control
	 */
	public static function getConditionsRepeaterSettings(){
		
		$settings = new UniteCreatorSettings();
		
		//--- operator
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		
		$arrOptions = array("And"=>"and","Or"=>"or");
		
		$settings->addSelect("operator", $arrOptions, __("Operator","unlimited-elements-for-elementor"),"and",$params);
		
		//--- field name
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		
		$settings->addTextBox("field_name","",__("Field Name","unlimited-elements-for-elementor"),$params);
		
		//--- condition
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		
		$arrOptions = array(
			"=" => "= (equal)",
			">" => "> (more)",
			">=" => ">= (more or equal)",
			"<" => "< (less)",
			"<=" => "<= (less or equal)",
			"!=" => "!= (not equal)");
		
		$arrOptions = array_flip($arrOptions);
		
		$settings->addSelect("condition", $arrOptions, __("Condition","unlimited-elements-for-elementor"),"=",$params);
		
		//--- value
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["label_block"] = true;
		
		$settings->addTextBox("field_value","",__("Field Value","unlimited-elements-for-elementor"),$params);
		
		
		return($settings);		
	}
	
	
	/**
	 * add form includes
	 */
	public function addFormIncludes(){
		
		//don't include inside editor
				
		if(self::$isFormIncluded == true)
			return(false);
		
		//include common scripts only once
		if(self::$isFormIncluded == false){
			
			$urlFormJS = GlobalsUC::$url_assets_libraries."form/uc_form.js";
			
			UniteProviderFunctionsUC::addAdminJQueryInclude();
			HelperUC::addScriptAbsoluteUrl_widget($urlFormJS, "uc_form");
			
		}
		
		
		self::$isFormIncluded = true;
		
		
	}
	
	/**
	 * get conditions data
	 * modify the data, add class and attributes
	 */
	public function getVisibilityConditionsParamsData($data, $visibilityParam){
		

		$name = UniteFunctionsUC::getVal($visibilityParam, "name");
		
		$arrValue = UniteFunctionsUC::getVal($visibilityParam, "value");
				
		if(empty($arrValue))
			return($data);
		
		$arrValue = UniteFunctionsUC::getVal($arrValue, "{$name}_conditions");
		
		if(empty($arrValue))
			return($data);
		
		$data["ucform_class"] = " ucform-has-conditions";
		
		return($data);
	}
	
	/**
	 * get the form values
	 */
	private function getFieldsData($arrContent, $arrFields){
		
		$arrOutput = array();
		
		foreach($arrFields as $arrField){
			
			//get field input
			
			$fieldID = UniteFunctionsUC::getVal($arrField, "id");
			$fieldValue = UniteFunctionsUC::getVal($arrField, "value");
						
			//get saved settings from layout
			$arrFieldSettings = HelperProviderCoreUC_EL::getAddonValuesWithDataFromContent($arrContent, $fieldID);
			
			//get values that we'll use in the form
			
			// note - not all the fields will have a name
			$name = UniteFunctionsUC::getVal($arrFieldSettings, "field_name");
			
			
			$title = UniteFunctionsUC::getVal($arrFieldSettings, "label");
			
			// you can take more settings valus if needed
			
			$arrFieldOutput = array();
			$arrFieldOutput["title"] = $title;
			$arrFieldOutput["name"] = $name;	
			$arrFieldOutput["value"] = $fieldValue;	
			
			$arrOutput[] = $arrFieldOutput;
		}
		
		
		return($arrOutput);
	}
	
	/**
	 * submit the form
	 */
	private function doSubmitActions($settings, $fields){
		
		
		dmp("submit the form");
		
		dmp($settings);
		
		dmp($fields);
		
		exit();
		
	}
	
	
	/**
	 * submit form
	 */
	public function submitFormFront(){
		
		$formData = UniteFunctionsUC::getGetVar("formdata",null,UniteFunctionsUC::SANITIZE_TEXT_FIELD);
		$formID = UniteFunctionsUC::getGetVar("formId",null,UniteFunctionsUC::SANITIZE_KEY);
		$layoutID = UniteFunctionsUC::getGetVar("postId",null,UniteFunctionsUC::SANITIZE_ID);
		
		UniteFunctionsUC::validateNotEmpty($formID,"form id");
		UniteFunctionsUC::validateNumeric($layoutID,"post id");
		
		if(empty($formData))
			UniteFunctionsUC::throwError("no form data found");

		$formData = stripcslashes($formData);
			
		$arrFields = UniteFunctionsUC::jsonDecode($formData);
		
		$arrContent = HelperProviderCoreUC_EL::getElementorContentByPostID($layoutID);
		
		if(empty($arrContent))
			UniteFunctionsUC::throwError("Elementor content not found");
					
		$addonForm = HelperProviderCoreUC_EL::getAddonWithDataFromContent($arrContent, $formID);
		
		//here can done some validation next...
				
		$arrFormSettings = $addonForm->getProcessedMainParamsValues();
		
		$arrFieldsData = $this->getFieldsData($arrContent, $arrFields);
		
		
		$this->doSubmitActions($arrFormSettings, $arrFieldsData);
				
		
	}
	
	
}