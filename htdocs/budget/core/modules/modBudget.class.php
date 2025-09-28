<?php
/* Copyright (C) 2003      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@capnetworks.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * 	\defgroup   budget     Module BUDGET
 *  \brief      Example of a module descriptor.
 *				Such a file must be copied into htdocs/mymodule/core/modules directory.
 *  \file       htdocs/mymodule/core/modules/modMyModule.class.php
 *  \ingroup    mymodule
 *  \brief      Description and activation file for module MyModule
 */
include_once DOL_DOCUMENT_ROOT .'/core/modules/DolibarrModules.class.php';


/**
 *  Description and activation class for module MyModule
 */
class modBudget extends DolibarrModules
{
	/**
	 *   Constructor. Define names, constants, directories, boxes, permissions
	 *
	 *   @param      DoliDB		$db      Database handler
	 */
	function __construct($db)
	{
		global $langs,$conf;

		$this->db = $db;

		// Id for module (must be unique).
		// Use here a free id (See in Home -> System information -> Dolibarr for list of used modules id).
		$this->numero = 580000;
		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'budget';

		// Family can be 'crm','financial','hr','projects','products','ecm','technic','other'
		// It is used to group modules in module setup page
		$this->family = "other";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i','',get_class($this));
		// Module description, used if translation string 'ModuleXXXDesc' not found (where XXX is value of numeric property 'numero' of module)
		$this->description = "Budgetsproject";
		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		//modificado en fecha 19/04/2018 10:06
		$this->version = '1.1.3.4';
		//modificado en fecha 16/04/2018 10:06
		//$this->version = '1.1.2.2';
		// Key used in llx_const table to save module status enabled/disabled (where MYMODULE is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Where to store the module in setup page (0=common,1=interface,2=others,3=very specific)
		$this->special = 0;
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		$this->picto='budget@budget';


		// Defined all module parts (triggers, login, substitutions, menus, css, etc...)
		// for default path (eg: /mymodule/core/xxxxx) (0=disable, 1=enable)
		// for specific path of parts (eg: /mymodule/core/modules/barcode)
		// for specific css file (eg: /mymodule/css/mymodule.css.php)
		//$this->module_parts = array(
		//                        	'triggers' => 0,                                 	// Set this to 1 if module has its own trigger directory (core/triggers)
		//							'login' => 0,                                    	// Set this to 1 if module has its own login method directory (core/login)
		//							'substitutions' => 0,                            	// Set this to 1 if module has its own substitution function file (core/substitutions)
		//							'menus' => 0,                                    	// Set this to 1 if module has its own menus handler directory (core/menus)
		//							'theme' => 0,                                    	// Set this to 1 if module has its own theme directory (theme)
		//                        	'tpl' => 0,                                      	// Set this to 1 if module overwrite template dir (core/tpl)
		//							'barcode' => 0,                                  	// Set this to 1 if module has its own barcode directory (core/modules/barcode)
		//							'models' => 0,                                   	// Set this to 1 if module has its own models directory (core/modules/xxx)
		//							'css' => array('/mymodule/css/mymodule.css.php'),	// Set this to relative path of css file if module has its own css file
	 	//							'js' => array('/mymodule/js/mymodule.js'),          // Set this to relative path of js file if module must load a js on all pages
		//							'hooks' => array('hookcontext1','hookcontext2')  	// Set here all hooks context managed by module
		//							'dir' => array('output' => 'othermodulename'),      // To force the default directories names
		//							'workflow' => array('WORKFLOW_MODULE1_YOURACTIONTYPE_MODULE2'=>array('enabled'=>'! empty($conf->module1->enabled) && ! empty($conf->module2->enabled)', 'picto'=>'yourpicto@mymodule')) // Set here all workflow context managed by module
		//                        );
		$this->module_parts = array('triggers'=>1,'contactelement'=>1);
		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/mymodule/temp");
		$this->dirs = array();

		// Config pages. Put here list of php page, stored into mymodule/admin directory, to use to setup module.
		$this->config_page_url = array("budget.php@budget");

		// Dependencies
		$this->hidden = false;			// A condition to hide module
		$this->depends = array();		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array();	// List of modules id to disable if this one is disabled
		$this->conflictwith = array();	// List of modules id this module is in conflict with
		$this->phpmin = array(5,0);					// Minimum version of PHP required by module
		$this->need_dolibarr_version = array(3,0);	// Minimum version of Dolibarr required by module
		$this->langfiles = array("budget@budget");

		// Constants
		// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
		// Example: $this->const=array(0=>array('MYMODULE_MYNEWCONST1','chaine','myvalue','This is a constant to add',1),
		//                             1=>array('MYMODULE_MYNEWCONST2','chaine','myvalue','This is another constant to add',0, 'current', 1)
		// );
		$this->const = array(0=>array('BUDGET_WORKFORCE_DEF','chaine','','Id de categoria que clasifique Mano de Obra',1),
			1=>array('BUDGET_MATERIALS_DEF','chaine','','Id de categoria que clasifique Materiales',1),
			2=>array('BUDGET_MACHINERY_DEF','chaine','','Id de categoria que clasifique Maquinaria y equipo',1),
			);
		$this->const = array(
			0=>array('BUDGET_CODE_ITEM_DEF','chaine','','Id de catgoria que clasifique ITEMS ',1),
			1=>array('ITEMS_USE_SEARCH_TO_SELECT','chaine','1','Buscador de items en combo',1),
			2=>array('BUDGET_DEFAULT_PREFIX_TASK','chaine','TA','Prefijo por defecto para numeración tareas',1),
			3=>array('BUDGET_DEFAULT_NCHARACTER_TASK','chaine','TA','Numero de digitos para numeración tareas',1),
			);

		// Array to add new pages in new tabs
		// Example: $this->tabs = array('objecttype:+tabname1:Title1:mylangfile@mymodule:$user->rights->mymodule->read:/mymodule/mynewtab1.php?id=__ID__',  					// To add a new tab identified by code tabname1
        //                              'objecttype:+tabname2:SUBSTITUTION_Title2:mylangfile@mymodule:$user->rights->othermodule->read:/mymodule/mynewtab2.php?id=__ID__',  	// To add another new tab identified by code tabname2. Label will be result of calling all substitution functions on 'Title2' key.
        //                              'objecttype:-tabname:NU:conditiontoremove');                                                     										// To remove an existing tab identified by code tabname
		// where objecttype can be
		// 'categories_x'	  to add a tab in category view (replace 'x' by type of category (0=product, 1=supplier, 2=customer, 3=member)
		// 'contact'          to add a tab in contact view
		// 'contract'         to add a tab in contract view
		// 'group'            to add a tab in group view
		// 'intervention'     to add a tab in intervention view
		// 'invoice'          to add a tab in customer invoice view
		// 'invoice_supplier' to add a tab in supplier invoice view
		// 'member'           to add a tab in fundation member view
		// 'opensurveypoll'	  to add a tab in opensurvey poll view
		// 'order'            to add a tab in customer order view
		// 'order_supplier'   to add a tab in supplier order view
		// 'payment'		  to add a tab in payment view
		// 'payment_supplier' to add a tab in supplier payment view
		// 'product'          to add a tab in product view
		// 'propal'           to add a tab in propal view
		// 'project'          to add a tab in project view
		// 'stock'            to add a tab in stock view
		// 'thirdparty'       to add a tab in third party view
		// 'user'             to add a tab in user view
		$this->tabs = array();
		$this->tabs = array(
			'product:+fixedasset:Fixedasset:@budget:$user->rights->budget->asset->read:/budget/productasset/card.php?id=__ID__',
			);
        // Dictionaries
		if (! isset($conf->budget->enabled))
		{
			$conf->budget=new stdClass();
			$conf->budget->enabled=0;
		}
		$this->dictionaries=array();
        /* Example:
        if (! isset($conf->mymodule->enabled)) $conf->mymodule->enabled=0;	// This is to avoid warnings
        $this->dictionaries=array(
            'langs'=>'mylangfile@mymodule',
            'tabname'=>array(MAIN_DB_PREFIX."table1",MAIN_DB_PREFIX."table2",MAIN_DB_PREFIX."table3"),		// List of tables we want to see into dictonnary editor
            'tablib'=>array("Table1","Table2","Table3"),													// Label of tables
            'tabsql'=>array('SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table1 as f','SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table2 as f','SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table3 as f'),	// Request to select fields
            'tabsqlsort'=>array("label ASC","label ASC","label ASC"),																					// Sort order
            'tabfield'=>array("code,label","code,label","code,label"),																					// List of fields (result of select to show dictionary)
            'tabfieldvalue'=>array("code,label","code,label","code,label"),																				// List of fields (list of fields to edit a record)
            'tabfieldinsert'=>array("code,label","code,label","code,label"),																			// List of fields (list of fields for insert)
            'tabrowid'=>array("rowid","rowid","rowid"),																									// Name of columns with primary key (try to always name it 'rowid')
            'tabcond'=>array($conf->mymodule->enabled,$conf->mymodule->enabled,$conf->mymodule->enabled)												// Condition to show each dictionary
        );
        */
        $this->dictionaries = array(
        	'langs' => 'budget',
        	'tabname' => array(MAIN_DB_PREFIX."c_resources_human",MAIN_DB_PREFIX."c_parameter_equipment",MAIN_DB_PREFIX."c_type_engine",MAIN_DB_PREFIX."c_type_parameter",),
        	'tablib' => array("Resourceshuman","Parametersequipment","Typeengine","Typeparameter",),
        	'tabsql' => array("SELECT f.rowid AS rowid, f.code, f.label, f.active FROM ".MAIN_DB_PREFIX."c_resources_human AS f","SELECT f.rowid AS rowid, f.code, f.label, f.active FROM ".MAIN_DB_PREFIX."c_parameter_equipment AS f","SELECT f.rowid AS rowid, f.entity,f.code, f.label, f.active FROM ".MAIN_DB_PREFIX."c_type_engine AS f","SELECT f.rowid AS rowid, f.entity, f.code, f.label, f.active FROM ".MAIN_DB_PREFIX."c_type_parameter AS f",
        		),
        	'tabsqlsort' => array("label ASC","label ASC","label ASC","label ASC",),
        	'tabfield' => array("code,label","code,label","entity,code,label","entity,code,label",),
        	'tabfieldvalue' => array("code,label","code,label","entity,code,label","entity,code,label",),
        	'tabfieldinsert'=>array("code,label","code,label","entity,code,label","entity,code,label",),
        	'tabrowid'=>array("rowid","rowid","rowid","rowid",),
        	'tabcond'=>array($conf->budget->enabled,$conf->budget->enabled,$conf->budget->enabled,$conf->budget->enabled,),
        	);
        // Boxes
		// Add here list of php file(s) stored in core/boxes that contains class to show a box.
        $this->boxes = array();			// List of boxes
		// Example:
		//$this->boxes=array(array(0=>array('file'=>'myboxa.php','note'=>'','enabledbydefaulton'=>'Home'),1=>array('file'=>'myboxb.php','note'=>''),2=>array('file'=>'myboxc.php','note'=>'')););

		// Permissions
		$this->rights = array();		// Permission array used by this module
		$r=0;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Budget';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'read';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Budgetwrite';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'write';

		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Read all budget';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'all';
		$this->rights[$r][5] = 'lire';

		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Read teacher';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'tea';
		$this->rights[$r][5] = 'leer';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Create teacher';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'tea';
		$this->rights[$r][5] = 'crear';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Modify teacher';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'tea';
		$this->rights[$r][5] = 'mod';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Delete teacher';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'tea';
		$this->rights[$r][5] = 'del';

		//paramater
		$r+=9;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'ReadParameters';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'par';
		$this->rights[$r][5] = 'read';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'CreateParameters';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'par';
		$this->rights[$r][5] = 'write';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'ModifyParameters';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'par';
		$this->rights[$r][5] = 'mod';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'DeleteParameters';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'par';
		$this->rights[$r][5] = 'del';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'ActivateParameters';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'par';
		$this->rights[$r][5] = 'val';



		//supplies / suministros materiales productos
		$r+=10;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Supplies';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'sup';
		$this->rights[$r][5] = 'leer';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'CreateSupplies';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'sup';
		$this->rights[$r][5] = 'crear';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'ModifySupplies';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'sup';
		$this->rights[$r][5] = 'mod';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'DeleteSupplies';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'sup';
		$this->rights[$r][5] = 'del';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'UploadSupplies';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'sup';
		$this->rights[$r][5] = 'up';

		$r+=9;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Readbudget';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'bud';
		$this->rights[$r][5] = 'read';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Readallbudget';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'bud';
		$this->rights[$r][5] = 'readall';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Createbudget';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'bud';
		$this->rights[$r][5] = 'write';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Deletebudget';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'bud';
		$this->rights[$r][5] = 'del';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Validatebudget';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'bud';
		$this->rights[$r][5] = 'val';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Approvebudget';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'bud';
		$this->rights[$r][5] = 'app';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Uploadbudget';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'bud';
		$this->rights[$r][5] = 'up';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Createdatabasemother';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'bud';
		$this->rights[$r][5] = 'writem';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Clonebudget';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'bud';
		$this->rights[$r][5] = 'clone';



		$r+=9;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Createreportpdf';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'pdf';
		$this->rights[$r][5] = 'creer';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Deletereportpdf';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'pdf';
		$this->rights[$r][5] = 'del';


		//ver precios presupuesto
		$r+=5;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Readbudgetprices';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'bpri';
		$this->rights[$r][5] = 'leer';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Createbudgetprices';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'bpri';
		$this->rights[$r][5] = 'crear';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Modifybudgetprices';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'bpri';
		$this->rights[$r][5] = 'mod';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Deletebudgetprices';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'bpri';
		$this->rights[$r][5] = 'del';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Validatebudgetprices';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'bpri';
		$this->rights[$r][5] = 'val';

		//contact budget
		$r+=10;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Readcontactbudget';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'budc';
		$this->rights[$r][5] = 'lire';

		$r+=5;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Readmodules_itemsbudget';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'budi';
		$this->rights[$r][5] = 'leer';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Compareversionsbudget';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'budi';
		$this->rights[$r][5] = 'com';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Createmodulesbudget';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'budm';
		$this->rights[$r][5] = 'crear';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Modifymodulesbudget';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'budm';
		$this->rights[$r][5] = 'mod';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Deletemodulesbudget';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'budm';
		$this->rights[$r][5] = 'del';

		$r+=3;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Readitemsbudget';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'budi';
		$this->rights[$r][5] = 'leer';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Createitemsbudget';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'budi';
		$this->rights[$r][5] = 'crear';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Cloneitemsbudget';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'budi';
		$this->rights[$r][5] = 'clon';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Modifyitemsbudget';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'budi';
		$this->rights[$r][5] = 'mod';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Deleteitemsbudget';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'budi';
		$this->rights[$r][5] = 'del';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Validateitemsbudget';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'budi';
		$this->rights[$r][5] = 'val';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Uploaditemsbudget';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'budi';
		$this->rights[$r][5] = 'up';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Exportitemsbudget';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'budi';
		$this->rights[$r][5] = 'exp';

		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Createitemsbudgetproductivity';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'budi';
		$this->rights[$r][5] = 'prod';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Createcontactitemsbudget';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'budic';
		$this->rights[$r][5] = 'crear';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Reportitemsbudget';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'budi';
		$this->rights[$r][5] = 'rep';

		$r+=6;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Createresourcebudget';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'budr';
		$this->rights[$r][5] = 'crear';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Modifyresourcebudget';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'budr';
		$this->rights[$r][5] = 'mod';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Deleteresourcebudget';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'budr';
		$this->rights[$r][5] = 'del';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Validateresourcebudget';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'budr';
		$this->rights[$r][5] = 'val';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Uploadresourcebudget';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'budr';
		$this->rights[$r][5] = 'up';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Changepricesbyfactor';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'budr';
		$this->rights[$r][5] = 'fact';

		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Readdatabaseproduct';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'budr';
		$this->rights[$r][5] = 'prod';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Modifyresourcebudgetperformance';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'budr';
		$this->rights[$r][5] = 'writerend';

		//items
		$r+=6;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Items';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'ite';
		$this->rights[$r][5] = 'leer';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Createitems';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'ite';
		$this->rights[$r][5] = 'crear';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Cloneitems';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'ite';
		$this->rights[$r][5] = 'clone';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'NewItemsversion';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'ite';
		$this->rights[$r][5] = 'version';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Validateitems';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'ite';
		$this->rights[$r][5] = 'val';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'DeleteItems';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'ite';
		$this->rights[$r][5] = 'del';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Uploaditems';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'ite';
		$this->rights[$r][5] = 'upload';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Exportitems';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'ite';
		$this->rights[$r][5] = 'exp';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Changefactorproduction';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'ite';
		$this->rights[$r][5] = 'fact';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'CreateItemsproduct';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'ite';
		$this->rights[$r][5] = 'writepro';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'DeleteItemsproduct';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'ite';
		$this->rights[$r][5] = 'delpro';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'CreateItemsproduction';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'ite';
		$this->rights[$r][5] = 'writerend';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Updateresourceprices';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'ite';
		$this->rights[$r][5] = 'updateres';


		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'ViewPriceItems';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'pri';
		$this->rights[$r][5] = 'leer';

		//typeitems
		$r+=5;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'ReadTypeitems';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'typ';
		$this->rights[$r][5] = 'leer';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'CreateTypeitems';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'typ';
		$this->rights[$r][5] = 'crear';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'ModifyTypeitems';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'typ';
		$this->rights[$r][5] = 'mod';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'DeleteTypeitems';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'typ';
		$this->rights[$r][5] = 'del';


		$r+=10;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Readformula';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'form';
		$this->rights[$r][5] = 'read';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Createformula';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'form';
		$this->rights[$r][5] = 'write';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Modifyformula';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'form';
		$this->rights[$r][5] = 'mod';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Deleteformula';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'form';
		$this->rights[$r][5] = 'del';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Validateformula';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'form';
		$this->rights[$r][5] = 'val';


		$r+=10;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Readcalendaritems';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'cale';
		$this->rights[$r][5] = 'lire';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Createcalendaritems';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'cale';
		$this->rights[$r][5] = 'crear';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Modifycalendaritems';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'cale';
		$this->rights[$r][5] = 'mod';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Deletecalendaritems';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'cale';
		$this->rights[$r][5] = 'del';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Validatecalendaritems';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'cale';
		$this->rights[$r][5] = 'val';

		$r+=10;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Productfixedasset';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'asset';
		$this->rights[$r][5] = 'read';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Createproductfixedasset';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'asset';
		$this->rights[$r][5] = 'write';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Deleteproductfixedasset';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'asset';
		$this->rights[$r][5] = 'del';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Validateproductfixedasset';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'asset';
		$this->rights[$r][5] = 'val';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Uploadfileproductfixedasset';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'asset';
		$this->rights[$r][5] = 'upload';

		$r+=10;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Variables';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'var';
		$this->rights[$r][5] = 'read';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Createvariables';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'var';
		$this->rights[$r][5] = 'write';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Deletevariables';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'var';
		$this->rights[$r][5] = 'del';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Validatevariables';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'var';
		$this->rights[$r][5] = 'val';

		// Add here list of permission defined by an id, a label, a boolean and two constant strings.
		// Example:
		// $this->rights[$r][0] = $this->numero + $r;	// Permission id (must not be already used)
		// $this->rights[$r][1] = 'Permision label';	// Permission label
		// $this->rights[$r][3] = 1; 					// Permission by default for new user (0/1)
		// $this->rights[$r][4] = 'level1';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		// $this->rights[$r][5] = 'level2';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		// $r++;


		// Main menu entries
		$this->menu = array();			// List of menus to add
		$r=0;
		$this->menu[$r]=array(	'fk_menu'=>0,
			'type'=>'top',
			'titre'=>'Budget',
			'mainmenu'=>'budget',
			'leftmenu'=>'0',
			'url'=>'/budget/index.php',
			'langs'=>'budget@budget',
			'position'=>100,
			'enabled'=>$conf->budget->enabled,
			'perms'=>'$user->rights->budget->read',
			'target'=>'',
			'user'=>0);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=0',
			'type'=>'left',
			'titre'=>'Teacher/Parameters',
			'mainmenu'=>'budget',
			'leftmenu'=>'param',
			'url'=>'/budget/index.php',
			'langs'=>'budget@budget',
			'position'=>100,
			'enabled'=>'$conf->budget->enabled',
			'perms'=>'$user->rights->budget->par->read',
			'target'=>'',
			'user'=>0);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=budget,fk_leftmenu=calc',
			'type'=>'left',
			'titre'=>'Newparameter',
			'mainmenu'=>'budget',
			'url'=>'/budget/calculation/card.php?action=create',
			'langs'=>'budget',
			'position'=>100,
			'enabled'=>'$conf->budget->enabled',
			'perms'=>'$user->rights->budget->par->write',
			'target'=>'',
			'user'=>0);

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=1',
			'type'=>'left',
			'titre'=>'Calendars',
			'mainmenu'=>'budget',
			'leftmenu'=>'cale',
			'url'=>'/budget/calendar/list.php',
			'langs'=>'budget@budget',
			'position'=>100,
			'enabled'=>'$conf->budget->enabled',
			'perms'=>'$user->rights->budget->tea->leer',
			'target'=>'',
			'user'=>0);

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=1',
			'type'=>'left',
			'titre'=>'Calculationparameters',
			'mainmenu'=>'budget',
			'leftmenu'=>'calc',
			'url'=>'/budget/calculation/list.php',
			'langs'=>'budget@budget',
			'position'=>100,
			'enabled'=>'$conf->budget->enabled',
			'perms'=>'$user->rights->budget->par->read',
			'target'=>'',
			'user'=>0);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=1',
			'type'=>'left',
			'titre'=>'Incidents',
			'mainmenu'=>'budget',
			'leftmenu'=>'calc',
			'url'=>'/budget/incidents/list.php',
			'langs'=>'budget@budget',
			'position'=>100,
			'enabled'=>'$conf->budget->enabled',
			'perms'=>'$user->rights->budget->tea->leer',
			'target'=>'',
			'user'=>0);


		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=1',
			'type'=>'left',
			'titre'=>'Structuretype',
			'mainmenu'=>'budget',
			'leftmenu'=>'str',
			'url'=>'/budget/typestructure/list.php',
			'langs'=>'budget@budget',
			'position'=>100,
			'enabled'=>'$conf->budget->enabled',
			'perms'=>'$user->rights->budget->par->read',
			'target'=>'',
			'user'=>0);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=budget,fk_leftmenu=str',
			'type'=>'left',
			'titre'=>'Newstructuretype',
			'mainmenu'=>'budget',
			'url'=>'/budget/typestructure/card.php?action=create',
			'langs'=>'budget',
			'position'=>100,
			'enabled'=>'$conf->budget->enabled',
			'perms'=>'$user->rights->budget->par->write',
			'target'=>'',
			'user'=>0);
		//type items
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=1',
			'type'=>'left',
			'titre'=>'Typeitem',
			'mainmenu'=>'budget',
			'leftmenu'=>'str',
			'url'=>'/budget/ctypeitem/list.php',
			'langs'=>'budget@budget',
			'position'=>100,
			'enabled'=>'$conf->budget->enabled',
			'perms'=>'$user->rights->budget->typ->leer',
			'target'=>'',
			'user'=>0);
		//items
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=1',
			'type'=>'left',
			'titre'=>'Items',
			'mainmenu'=>'budget',
			'leftmenu'=>'str',
			'url'=>'/budget/items/list.php',
			'langs'=>'budget@budget',
			'position'=>100,
			'enabled'=>'$conf->budget->enabled',
			'perms'=>'$user->rights->budget->ite->leer',
			'target'=>'',
			'user'=>0);
		//resources
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=1',
			'type'=>'left',
			'titre'=>'Resources',
			'mainmenu'=>'budget',
			'leftmenu'=>'str',
			'url'=>'/budget/resources/list.php',
			'langs'=>'budget@budget',
			'position'=>100,
			'enabled'=>'$conf->budget->enabled',
			'perms'=>'$user->rights->budget->ite->leer',
			'target'=>'',
			'user'=>0);

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=0',
			'type'=>'left',
			'titre'=>'Formulas',
			'mainmenu'=>'budget',
			'leftmenu'=>'form',
			'url'=>'/budget/formula/list.php',
			'langs'=>'budget',
			'position'=>100,
			'enabled'=>'$conf->budget->enabled',
			'perms'=>'$user->rights->budget->form->read',
			'target'=>'',
			'user'=>0);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=budget,fk_leftmenu=form',
			'type'=>'left',
			'titre'=>'Createnew',
			'mainmenu'=>'budget',
			'url'=>'/budget/formula/card.php?action=create',
			'langs'=>'budget',
			'position'=>100,
			'enabled'=>'$conf->budget->enabled',
			'perms'=>'$user->rights->budget->form->write',
			'target'=>'',
			'user'=>0);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=0',
			'type'=>'left',
			'titre'=>'Variables',
			'mainmenu'=>'budget',
			'leftmenu'=>'var',
			'url'=>'/budget/variables/list.php',
			'langs'=>'budget',
			'position'=>100,
			'enabled'=>'$conf->budget->enabled',
			'perms'=>'$user->rights->budget->var->read',
			'target'=>'',
			'user'=>0);



		//budget
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=0',
			'type'=>'left',
			'titre'=>'Budget',
			'mainmenu'=>'budget',
			'leftmenu'=>'bud',
			'url'=>'/budget/budget/list.php',
			'langs'=>'budget',
			'position'=>100,
			'enabled'=>'$conf->budget->enabled',
			'perms'=>'$user->rights->budget->read',
			'target'=>'',
			'user'=>0);
		//budget create
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=budget,fk_leftmenu=bud',
			'type'=>'left',
			'titre'=>'Createnew',
			'mainmenu'=>'budget',
			'url'=>'/budget/budget/card.php?action=create',
			'langs'=>'budget',
			'position'=>100,
			'enabled'=>'$conf->budget->enabled',
			'perms'=>'$user->rights->budget->bud->write',
			'target'=>'',
			'user'=>0);

		// $r++;
		// $this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=price,fk_leftmenu=item',
		// 			'type'=>'left',
		// 			'titre'=>'Items',
		// 			'mainmenu'=>'price',
		// 			'leftmenu'=>'item',
		// 			'url'=>'/budget/items/items.php',
		// 			'langs'=>'budget@budget',
		// 			'position'=>100,
		// 			'enabled'=>'$conf->budget->enabled',
		// 			'perms'=>'$user->rights->budget->ite->leer',
		// 			'target'=>'',
		// 			'user'=>0);

		// Add here entries to declare new menus
		//
		// Example to declare a new Top Menu entry and its Left menu entry:
		// $this->menu[$r]=array(	'fk_menu'=>0,			                // Put 0 if this is a top menu
		//							'type'=>'top',			                // This is a Top menu entry
		//							'titre'=>'MyModule top menu',
		//							'mainmenu'=>'mymodule',
		//							'leftmenu'=>'mymodule',
		//							'url'=>'/mymodule/pagetop.php',
		//							'langs'=>'mylangfile@mymodule',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
		//							'position'=>100,
		//							'enabled'=>'$conf->mymodule->enabled',	// Define condition to show or hide menu entry. Use '$conf->mymodule->enabled' if entry must be visible if module is enabled.
		//							'perms'=>'1',			                // Use 'perms'=>'$user->rights->mymodule->level1->level2' if you want your menu with a permission rules
		//							'target'=>'',
		//							'user'=>2);				                // 0=Menu for internal users, 1=external users, 2=both
		// $r++;
		//
		// Example to declare a Left Menu entry into an existing Top menu entry:
		// $this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=xxx',		    // Use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
		//							'type'=>'left',			                // This is a Left menu entry
		//							'titre'=>'MyModule left menu',
		//							'mainmenu'=>'xxx',
		//							'leftmenu'=>'mymodule',
		//							'url'=>'/mymodule/pagelevel2.php',
		//							'langs'=>'mylangfile@mymodule',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
		//							'position'=>100,
		//							'enabled'=>'$conf->mymodule->enabled',  // Define condition to show or hide menu entry. Use '$conf->mymodule->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
		//							'perms'=>'1',			                // Use 'perms'=>'$user->rights->mymodule->level1->level2' if you want your menu with a permission rules
		//							'target'=>'',
		//							'user'=>2);				                // 0=Menu for internal users, 1=external users, 2=both
		// $r++;


		// Exports
		$r=1;

		// Example:
		// $this->export_code[$r]=$this->rights_class.'_'.$r;
		// $this->export_label[$r]='CustomersInvoicesAndInvoiceLines';	// Translation key (used only if key ExportDataset_xxx_z not found)
        // $this->export_enabled[$r]='1';                               // Condition to show export in list (ie: '$user->id==3'). Set to 1 to always show when module is enabled.
		// $this->export_permission[$r]=array(array("facture","facture","export"));
		// $this->export_fields_array[$r]=array('s.rowid'=>"IdCompany",'s.nom'=>'CompanyName','s.address'=>'Address','s.zip'=>'Zip','s.town'=>'Town','s.fk_pays'=>'Country','s.phone'=>'Phone','s.siren'=>'ProfId1','s.siret'=>'ProfId2','s.ape'=>'ProfId3','s.idprof4'=>'ProfId4','s.code_compta'=>'CustomerAccountancyCode','s.code_compta_fournisseur'=>'SupplierAccountancyCode','f.rowid'=>"InvoiceId",'f.facnumber'=>"InvoiceRef",'f.datec'=>"InvoiceDateCreation",'f.datef'=>"DateInvoice",'f.total'=>"TotalHT",'f.total_ttc'=>"TotalTTC",'f.tva'=>"TotalVAT",'f.paye'=>"InvoicePaid",'f.fk_statut'=>'InvoiceStatus','f.note'=>"InvoiceNote",'fd.rowid'=>'LineId','fd.description'=>"LineDescription",'fd.price'=>"LineUnitPrice",'fd.tva_tx'=>"LineVATRate",'fd.qty'=>"LineQty",'fd.total_ht'=>"LineTotalHT",'fd.total_tva'=>"LineTotalTVA",'fd.total_ttc'=>"LineTotalTTC",'fd.date_start'=>"DateStart",'fd.date_end'=>"DateEnd",'fd.fk_product'=>'ProductId','p.ref'=>'ProductRef');
		// $this->export_entities_array[$r]=array('s.rowid'=>"company",'s.nom'=>'company','s.address'=>'company','s.zip'=>'company','s.town'=>'company','s.fk_pays'=>'company','s.phone'=>'company','s.siren'=>'company','s.siret'=>'company','s.ape'=>'company','s.idprof4'=>'company','s.code_compta'=>'company','s.code_compta_fournisseur'=>'company','f.rowid'=>"invoice",'f.facnumber'=>"invoice",'f.datec'=>"invoice",'f.datef'=>"invoice",'f.total'=>"invoice",'f.total_ttc'=>"invoice",'f.tva'=>"invoice",'f.paye'=>"invoice",'f.fk_statut'=>'invoice','f.note'=>"invoice",'fd.rowid'=>'invoice_line','fd.description'=>"invoice_line",'fd.price'=>"invoice_line",'fd.total_ht'=>"invoice_line",'fd.total_tva'=>"invoice_line",'fd.total_ttc'=>"invoice_line",'fd.tva_tx'=>"invoice_line",'fd.qty'=>"invoice_line",'fd.date_start'=>"invoice_line",'fd.date_end'=>"invoice_line",'fd.fk_product'=>'product','p.ref'=>'product');
		// $this->export_sql_start[$r]='SELECT DISTINCT ';
		// $this->export_sql_end[$r]  =' FROM ('.MAIN_DB_PREFIX.'facture as f, '.MAIN_DB_PREFIX.'facturedet as fd, '.MAIN_DB_PREFIX.'societe as s)';
		// $this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'product as p on (fd.fk_product = p.rowid)';
		// $this->export_sql_end[$r] .=' WHERE f.fk_soc = s.rowid AND f.rowid = fd.fk_facture';
		// $this->export_sql_order[$r] .=' ORDER BY s.nom';
		// $r++;
	}

	/**
	 *		Function called when module is enabled.
	 *		The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
	 *		It also creates data directories
	 *
     *      @param      string	$options    Options when enabling module ('', 'noboxes')
	 *      @return     int             	1 if OK, 0 if KO
	 */
	function init($options='')
	{
		$sql = array();

		$result=$this->_load_tables('/budget/sql/');

		return $this->_init($sql, $options);
	}

	/**
	 *		Function called when module is disabled.
	 *      Remove from database constants, boxes and permissions from Dolibarr database.
	 *		Data directories are not deleted
	 *
     *      @param      string	$options    Options when enabling module ('', 'noboxes')
	 *      @return     int             	1 if OK, 0 if KO
	 */
	function remove($options='')
	{
		$sql = array();

		return $this->_remove($sql, $options);
	}

}
