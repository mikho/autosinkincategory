<?php if (!defined('APPLICATION')) exit();

// Define the plugin:
$PluginInfo['AutosinkinCategory'] = array(
	'Name' => 'Autosink Threads',
   'Description' => 'Allows the administrators to set discussions posted in selected categories to sink automagically',
   'Version' => '1.0',
   'RequiredApplications' => array('Vanilla' => '2.1.11'),
   'RequiredTheme' => FALSE, 
   'RequiredPlugins' => FALSE,
   'HasLocale' => FALSE,
   'SettingsUrl' => '/dashboard/plugin/AutosinkinCategory',
   'SettingsPermission' => 'Garden.AdminUser.Only',
   'Author' => "mikho",
   'AuthorEmail' => 'mikho@lowendguide.com',
   'AuthorUrl' => 'http://www.lowendguide.com'
);

class AutosinkinCategoryPlugin extends Gdn_Plugin {

   /**
    * Plugin constructor
    *
    * This fires once per page load, during execution of bootstrap.php. It is a decent place to perform
    * one-time-per-page setup of the plugin object. Be careful not to put anything too strenuous in here
    * as it runs every page load and could slow down your forum.
    */
   public function __construct() {
      
   }
/**
    * From here, we can do whatever we like, including turning this plugin into a mini controller and
    * allowing us an easy way of creating a dashboard settings screen.
    *
    * @param $Sender Sending controller instance
    */
    public function PluginController_AutosinkinCategory_Create($Sender) {
      $Sender->AddSideMenu('plugin/AutosinkinCategory');
	  $Sender->permission($this->getPluginKey('SettingsPermission'));
      $Sender->setData('Title', ($this->getPluginKey('Name').' Settings'));
      $Sender->setData('Description',$this->getPluginKey('Description'));    
	  $categories = CategoryModel::categories();  
        // Most of the time you will not need root category.
      unset($categories[-1]);
       // The configuration module does everything you like to achieve automagically!
       $configurationModule = new ConfigurationModule($Sender);
       
	    $configurationModule->initialize(array(
           'AutosinkinCategory.Categories' => array(
               'Control' => 'CheckBoxList',
               'LabelCode' => 'Categories',
               'Items' => $categories,
               'Description' => $this->getPluginKey('Description'), 
               'Options' => array('ValueField' => 'CategoryID', 'TextField' => 'Name')
           )
       ));
	   // BAM!
       $configurationModule->renderAll(); 
	}

	public function DiscussionModel_BeforeSaveDiscussion_handler ($Sender) {
		// check if thread posted is in a category set to autosink.
		// Sink the discussion.
		// $arrayAutosinkinCategoryCategories = explode(",",C('AutosinkinCategory.Categories',""));
		$arrayAutosinkinCategoryCategories = C('AutosinkinCategory.Categories',"");
		$discussion =& $Sender->EventArguments['FormPostValues'];
		if (in_array($discussion["CategoryID"],$arrayAutosinkinCategoryCategories)) {
			$discussion['Sink'] = '1';
		} 
	}
	
/**
    * Plugin setup
    *
    * This method is fired only once, immediately after the plugin has been enabled in the /plugins/ screen, 
    * and is a great place to perform one-time setup tasks, such as database structure changes, 
    * addition/modification ofconfig file settings, filesystem changes, etc.
    */
   public function Setup() {
      // Set up the plugin's default values
   }
   /**
    * Plugin cleanup
    *
    * Method is fired only once, immediately before the plugin is disabled, perform cleanup tasks such as deletion of unsued files and folders.
    */
   public function OnDisable() {
      RemoveFromConfig('AutosinkinCategory.Categories');
   }
}

?>
