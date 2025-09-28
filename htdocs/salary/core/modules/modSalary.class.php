<?php
/* Copyright (C) 2013-2013 Ramiro Queso        <ramiro@ubuntu-bo.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
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
 * 	\defgroup   gestion personal     Module Salary
 *  \brief      Example of a module descriptor.
 *				Such a file must be copied into htdocs/mymodulet/core/modules directory.
 *  \file       htdocs/salary/core/modules/modsalary.class.php
 *  \ingroup    Gestion personal
 *  \brief      Description and activation file for module Salary
 */
include_once(DOL_DOCUMENT_ROOT ."/core/modules/DolibarrModules.class.php");


/**
 *  Description and activation class for module Produccion
 */
class modSalary extends DolibarrModules
{
	/**
	 *   Constructor. Define names, constants, directories, boxes, permissions
	 *
	 *   @param      DoliDB		$db      Database handler
	 */
	function modSalary($db)
	{
        global $langs,$conf;

        $this->db = $db;

		// Id for module (must be unique).
		// Use here a free id (See in Home -> System information -> Dolibarr for list of used modules id).
		$this->numero = 20500;
		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'salary';

		// Family can be 'crm','financial','hr','projects','products','ecm','technic','other'
		// It is used to group modules in module setup page
		$this->family = "Salary";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i','',get_class($this));
		// Module description, used if translation string 'ModuleXXXDesc' not found (where XXX is value of numeric property 'numero' of module)
		$this->description = "Salarios, Cargos, Departamentos, Aportaciones, Planilla de Sueldos, Boletas de Pago ";
		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = '1.1';
		// Key used in llx_const table to save module status enabled/disabled (where MYMODULE is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Where to store the module in setup page (0=common,1=interface,2=others,3=very specific)
		$this->special = 0;
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		$this->picto='salary';
		$this->picto='salary@salary';

		// Defined all module parts (triggers, login, substitutions, menus, css, etc...)
		// for default path (eg: /produccion/core/xxxxx) (0=disable, 1=enable)
		// for specific path of parts (eg: /produccion/core/modules/barcode)
		// for specific css file (eg: /produccion/css/produccion.css.php)
		//$this->module_parts = array(
		//                        	'triggers' => 0,                                 // Set this to 1 if module has its own trigger directory
		//							'login' => 0,                                    // Set this to 1 if module has its own login method directory
		//							'substitutions' => 0,                            // Set this to 1 if module has its own substitution function file
		//							'menus' => 0,                                    // Set this to 1 if module has its own menus handler directory
		//							'barcode' => 0,                                  // Set this to 1 if module has its own barcode directory
		//							'models' => 0,                                   // Set this to 1 if module has its own models directory
		//							'css' => '/produccion/css/produccion.css.php',       // Set this to relative path of css if module has its own css file
		//							'hooks' => array('hookcontext1','hookcontext2')  // Set here all hooks context managed by module
		//							'workflow' => array('order' => array('WORKFLOW_ORDER_AUTOCREATE_INVOICE')) // Set here all workflow context managed by module
		//                        );
		$this->module_parts = array();

		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/produccion/temp");
		$this->dirs = array("/salary/charge","/salary/departament","/salary/salary","/salary/period");

		// Config pages. Put here list of php page, stored into produccion/admin directory, to use to setup module.
		$this->config_page_url = array("salary.php@salary");

		// Dependencies
		$this->depends = array();		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array();	// List of modules id to disable if this one is disabled
		$this->phpmin = array(5,0);					// Minimum version of PHP required by module
		$this->need_dolibarr_version = array(3,0);	// Minimum version of Dolibarr required by module
		$this->langfiles = array("salary@salary");

		// Constants
		// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
		// Example: $this->const=array(0=>array('MYMODULE_MYNEWCONST1','chaine','myvalue','This is a constant to add',1),
		//                             1=>array('MYMODULE_MYNEWCONST2','chaine','myvalue','This is another constant to add',0)
		// );
		$this->const =
		  array(
			0=>array('SALARY_BASIC_AMOUNT','chaine','1200','Salario basico del pais',$conf->entity),
			1=>array('SALARY_NRO_DIAS_LABORAL','chaine','30','Numero de dias laborales para calculo',$conf->entity),
			2=>array('SALARY_NRO_BASIC_BONO_ANT','chaine','3','Numero de salarios basicos para bono antiguedad',$conf->entity),
			3=>array('SALARY_CONCEPT_LIQUID_PAYMENT','chaine','510','Codigo de concepto Liquido Pagable (separado por comas) Ejm 401 o 401,402,403',$conf->entity),
			4=>array('SALARY_CODE_TABLE_GENERIC_SOLIDARY_CONTRIBUTION','chaine','006','Codigo Tabla Generica para Aporte Solidario',$conf->entity),
			5=>array('SALARY_CONCEPT_BASE_SOLIDARY_CONTRIBUTION','chaine','706','Codigo Concepto Base Aporte Solidario',$conf->entity),
			6=>array('SALARY_CONCEPT_BALANCE_RC_IVA','chaine','104','Codigo Concepto Saldo RC-IVA',$conf->entity)

			);

		// Array to add new pages in new tabs
		// Example: $this->tabs = array('objecttype:+tabname1:Title1:langfile@produccion:$user->rights->produccion->read:/produccion/mynewtab1.php?id=__ID__',  // To add a new tab identified by code tabname1
		//$this->tabs = array('product:+tabname1:Units:@almacen:$user->rights->almacen->crearpedido:/almacen/units/fiche.php?id=__ID__');                                                     // To remove an existing tab identified by code tabname
		// where objecttype can be
		// 'thirdparty'       to add a tab in third party view
		// 'intervention'     to add a tab in intervention view
		// 'order_supplier'   to add a tab in supplier order view
		// 'invoice_supplier' to add a tab in supplier invoice view
		// 'invoice'          to add a tab in customer invoice view
		// 'order'            to add a tab in customer order view
		// 'product'          to add a tab in product view
		// 'stock'            to add a tab in stock view
		// 'propal'           to add a tab in propal view
		// 'member'           to add a tab in fundation member view
		// 'contract'         to add a tab in contract view
		// 'user'             to add a tab in user view
		// 'group'            to add a tab in group view
		// 'contact'          to add a tab in contact view
		// 'categories_x'	  to add a tab in category view (replace 'x' by type of category (0=product, 1=supplier, 2=customer, 3=member)
        $this->tabs = array();
	$this->tabs = array('member:+tabname1:Membersdetail:@salary:$user->rights->salary->leeruser:/salary/puser/fiche.php?rowid=__ID__',
			    'member:+tabname2:Contractdetail:@salary:$user->rights->salary->leeruser:/salary/contract/liste.php?rowid=__ID__',
			    'member:+tabname3:Salary:@salary:$user->rights->salary->forms->lire:/salary/forms/liste.php?rowid=__ID__',     // Para añadir una pestaña en el objeto de tipo objecttype, pestaña identificada por el id tabname1
			    );

        // Dictionnaries
	//if (! isset($conf->almacen->enabled)) $conf->almacen->enabled=0;
	$this->dictionnaries=array();

	// $this->dictionnaries=array(
	// 			   'langs'=>'salary@salary',
	// 			   'tabname'=>array(MAIN_DB_PREFIX."p_civility"),		// List of tables we want to see into dictonnary editor
	// 			   'tablib'=>array("salary"),													// Label of tables
	// 			   'tabsql'=>array('SELECT a.rowid as rowid, a.code, a.label, a.active FROM '.MAIN_DB_PREFIX.'p_civility as a'),	// Request to select fields
	// 			   'tabsqlsort'=>array("code ASC"),																					// Sort order
	// 			   'tabfield'=>array("code,label,active"),																					// List of fields (result of select to show dictionnary)
	// 			   'tabfieldvalue'=>array("code,label,active"),																				// List of fields (list of fields to edit a record)
	// 			   'tabfieldinsert'=>array("code,label,active"),																			// List of fields (list of fields for insert)
	// 			   'tabrowid'=>array("rowid"),																									// Name of columns with primary key (try to always name it 'rowid')
	// 			   'tabcond'=>array($conf->salary->enabled)												// Condition to show each dictionnary
	// 			   );

        //Example:
        //if (! isset($conf->salary->enabled)) $conf->salary->enabled=0;	// This is to avoid warnings
        $this->dictionnaries=array(
            'langs'=>'salary@salary',
            'tabname'=>array(MAIN_DB_PREFIX."p_civility",MAIN_DB_PREFIX."p_blood_type"),		// List of tables we want to see into dictonnary editor
            'tablib'=>array("Civility","Blood"),													// Label of tables
            'tabsql'=>array('SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'p_civility as f','SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'p_blood_type as f'),	// Request to select fields
            'tabsqlsort'=>array("label ASC","label ASC"),																					// Sort order
            'tabfield'=>array("code,label","code,label"),																					// List of fields (result of select to show dictionnary)
            'tabfieldvalue'=>array("code,label","code,label"),																				// List of fields (list of fields to edit a record)
            'tabfieldinsert'=>array("code,label","code,label"),																			// List of fields (list of fields for insert)
            'tabrowid'=>array("rowid","rowid"),																									// Name of columns with primary key (try to always name it 'rowid')
            'tabcond'=>array($conf->salary->enabled,$conf->salary->enabled)												// Condition to show each dictionnary
        );


        // Boxes
		// Add here list of php file(s) stored in core/boxes that contains class to show a box.
        $this->boxes = array();			// List of boxes
		$r=0;
		// Example:
		/*
		$this->boxes[$r][1] = "myboxa.php";
		$r++;
		$this->boxes[$r][1] = "myboxb.php";
		$r++;
		*/
		// Permissions
		$this->rights = array();		// Permission array used by this module
		$r=0;
		//$r++;
		$this->rights[$r][0] = 20501;
		$this->rights[$r][1] = 'Planilla de Sueldos';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'lire';

		$r++;
		$this->rights[$r][0] = 20508;
		$this->rights[$r][1] = 'Parametros de Calculo';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'parameter';
		$r++;
		$this->rights[$r][0] = 20509;
		$this->rights[$r][1] = 'Parametros de Planilla';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'payroll';

		$r++;
		$this->rights[$r][0] = 20510;
		$this->rights[$r][1] = 'Leer Departamentos';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'dpto';
		$this->rights[$r][5] = 'lire';

		$r++;
		$this->rights[$r][0] = 20511;
		$this->rights[$r][1] = 'Crear Departamentos';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'dpto';
		$this->rights[$r][5] = 'creer';

		$r++;
		$this->rights[$r][0] = 20512;
		$this->rights[$r][1] = 'Borrar Departamentos';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'dpto';
		$this->rights[$r][5] = 'del';

		$r++;
		$this->rights[$r][0] = 20520;
		$this->rights[$r][1] = 'Leer Cargos';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'charge';
		$this->rights[$r][5] = 'lire';

		$r++;
		$this->rights[$r][0] = 20521;
		$this->rights[$r][1] = 'Crear Cargos';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'charge';
		$this->rights[$r][5] = 'creer';

		$r++;
		$this->rights[$r][0] = 20522;
		$this->rights[$r][1] = 'Borrar Cargos';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'charge';
		$this->rights[$r][5] = 'del';

		$r++;
		$this->rights[$r][0] = 20790;
		$this->rights[$r][1] = 'Liste regional';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'region';
		$this->rights[$r][5] = 'lire';

		$r++;
		$this->rights[$r][0] = 20791;
		$this->rights[$r][1] = 'Create regional';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'region';
		$this->rights[$r][5] = 'creer';

		$r++;
		$this->rights[$r][0] = 20792;
		$this->rights[$r][1] = 'Validate regional';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'region';
		$this->rights[$r][5] = 'val';

		$r++;
		$this->rights[$r][0] = 20793;
		$this->rights[$r][1] = 'Delete regional';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'region';
		$this->rights[$r][5] = 'del';

		$r++;
		$this->rights[$r][0] = 20525;
		$this->rights[$r][1] = 'Leer Periodos';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'period';
		$this->rights[$r][5] = 'lire';

		$r++;
		$this->rights[$r][0] = 20526;
		$this->rights[$r][1] = 'Crear Periodos';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'period';
		$this->rights[$r][5] = 'creer';

		$r++;
		$this->rights[$r][0] = 20527;
		$this->rights[$r][1] = 'Borrar Periodos';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'period';
		$this->rights[$r][5] = 'del';

		$r++;
		$this->rights[$r][0] = 20528;
		$this->rights[$r][1] = 'Validar Periodos';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'period';
		$this->rights[$r][5] = 'val';

		$r++;
		$this->rights[$r][0] = 20530;
		$this->rights[$r][1] = 'Read salary charge';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'leersacharge';

		$r++;
		$this->rights[$r][0] = 20531;
		$this->rights[$r][1] = 'Create salary charge';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'crearsacharge';

		$r++;
		$this->rights[$r][0] = 20532;
		$this->rights[$r][1] = 'Delete salary charge';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'delsacharge';

		$r++;
		$this->rights[$r][0] = 20541;
		$this->rights[$r][1] = 'Read user salary';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'leeruser';

		$r++;
		$this->rights[$r][0] = 20542;
		$this->rights[$r][1] = 'Create user salary';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'crearuser';

		$r++;
		$this->rights[$r][0] = 20543;
		$this->rights[$r][1] = 'Delete user salary';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'deluser';

		$r++;
		$this->rights[$r][0] = 20550;
		$this->rights[$r][1] = 'Liste type fol';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'typefol';
		$this->rights[$r][5] = 'lire';

		$r++;
		$this->rights[$r][0] = 20551;
		$this->rights[$r][1] = 'Create type fol';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'typefol';
		$this->rights[$r][5] = 'creer';

		$r++;
		$this->rights[$r][0] = 20552;
		$this->rights[$r][1] = 'Delete type fol';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'typefol';
		$this->rights[$r][5] = 'del';

		$r++;
		$this->rights[$r][0] = 20560;
		$this->rights[$r][1] = 'Liste concept';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'concept';
		$this->rights[$r][5] = 'lire';

		$r++;
		$this->rights[$r][0] = 20561;
		$this->rights[$r][1] = 'Create concept';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'concept';
		$this->rights[$r][5] = 'creer';

		$r++;
		$this->rights[$r][0] = 20562;
		$this->rights[$r][1] = 'Delete concept';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'concept';
		$this->rights[$r][5] = 'del';

		$r++;
		$this->rights[$r][0] = 20570;
		$this->rights[$r][1] = 'Liste generic';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'generic';
		$this->rights[$r][5] = 'lire';

		$r++;
		$this->rights[$r][0] = 20571;
		$this->rights[$r][1] = 'Create generic';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'generic';
		$this->rights[$r][5] = 'creer';

		$r++;
		$this->rights[$r][0] = 20572;
		$this->rights[$r][1] = 'Delete generic';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'generic';
		$this->rights[$r][5] = 'del';

		$r++;
		$this->rights[$r][0] = 20573;
		$this->rights[$r][1] = 'Validate generic';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'generic';
		$this->rights[$r][5] = 'val';


		$r++;
		$this->rights[$r][0] = 20580;
		$this->rights[$r][1] = 'Liste formula';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'formula';
		$this->rights[$r][5] = 'lire';

		$r++;
		$this->rights[$r][0] = 20581;
		$this->rights[$r][1] = 'Create formula';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'formula';
		$this->rights[$r][5] = 'creer';

		$r++;
		$this->rights[$r][0] = 20582;
		$this->rights[$r][1] = 'Delete formula';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'formula';
		$this->rights[$r][5] = 'del';

		$r++;
		$this->rights[$r][0] = 20583;
		$this->rights[$r][1] = 'Validate formula';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'formula';
		$this->rights[$r][5] = 'val';

		$r++;
		$this->rights[$r][0] = 20590;
		$this->rights[$r][1] = 'Liste bonus';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'bonus';
		$this->rights[$r][5] = 'lire';

		$r++;
		$this->rights[$r][0] = 20591;
		$this->rights[$r][1] = 'Create bonus';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'bonus';
		$this->rights[$r][5] = 'creer';

		$r++;
		$this->rights[$r][0] = 20592;
		$this->rights[$r][1] = 'Delete bonus';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'bonus';
		$this->rights[$r][5] = 'del';

		$r++;
		$this->rights[$r][0] = 20593;
		$this->rights[$r][1] = 'Validate bonus';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'bonus';
		$this->rights[$r][5] = 'val';

		$r++;
		$this->rights[$r][0] = 20610;
		$this->rights[$r][1] = 'Liste proces';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'proces';
		$this->rights[$r][5] = 'lire';

		$r++;
		$this->rights[$r][0] = 20611;
		$this->rights[$r][1] = 'Create proces';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'proces';
		$this->rights[$r][5] = 'creer';

		$r++;
		$this->rights[$r][0] = 20612;
		$this->rights[$r][1] = 'Delete proces';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'proces';
		$this->rights[$r][5] = 'del';

		$r++;
		$this->rights[$r][0] = 20613;
		$this->rights[$r][1] = 'Validate proces';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'proces';
		$this->rights[$r][5] = 'val';


		$r++;
		$this->rights[$r][0] = 20700;
		$this->rights[$r][1] = 'Liste forms';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'forms';
		$this->rights[$r][4] = 'lire';

		$r++;
		$this->rights[$r][0] = 20710;
		$this->rights[$r][1] = 'Upload archive';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'uparchive';

		$r++;
		$this->rights[$r][0] = 20720;
		$this->rights[$r][1] = 'Liste cost center';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'cc';
		$this->rights[$r][5] = 'lire';

		$r++;
		$this->rights[$r][0] = 20721;
		$this->rights[$r][1] = 'Create cost center';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'cc';
		$this->rights[$r][5] = 'creer';

		$r++;
		$this->rights[$r][0] = 20722;
		$this->rights[$r][1] = 'Delete cost center';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'cc';
		$this->rights[$r][5] = 'del';

		$r++;
		$this->rights[$r][0] = 20723;
		$this->rights[$r][1] = 'Validate cost center';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'cc';
		$this->rights[$r][5] = 'val';

		$r++;
		$this->rights[$r][0] = 20750;
		$this->rights[$r][1] = 'Leer Report';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'leerreport';

		$r++;
		$this->rights[$r][0] = 20751;
		$this->rights[$r][1] = 'Genera Sould salary';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'generasal';

		$r++;
		$this->rights[$r][0] = 20752;
		$this->rights[$r][1] = 'Valid Sould salary';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'validsal';

		$r++;
		$this->rights[$r][0] = 20762;
		$this->rights[$r][1] = 'Sould salary';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'crearrsal';

		$r++;
		$this->rights[$r][0] = 20764;
		$this->rights[$r][1] = 'Boleta salary';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'crearbsal';
		$r++;
		$this->rights[$r][0] = 20765;
		$this->rights[$r][1] = 'Delete Boleta salary';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'delrbsal';

		$r++;
		$this->rights[$r][0] = 20770;
		$this->rights[$r][1] = 'Liste member';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'member';
		$this->rights[$r][5] = 'lire';

		$r++;
		$this->rights[$r][0] = 20771;
		$this->rights[$r][1] = 'Create member';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'member';
		$this->rights[$r][5] = 'creer';
		$r++;
		$this->rights[$r][0] = 20772;
		$this->rights[$r][1] = 'Delete member';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'member';
		$this->rights[$r][5] = 'del';
		$r++;
		$this->rights[$r][0] = 20773;
		$this->rights[$r][1] = 'Validate member';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'member';
		$this->rights[$r][5] = 'val';


		$r++;
		$this->rights[$r][0] = 20780;
		$this->rights[$r][1] = 'Liste contract';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'contract';
		$this->rights[$r][5] = 'lire';

		$r++;
		$this->rights[$r][0] = 20781;
		$this->rights[$r][1] = 'Create contract';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'contract';
		$this->rights[$r][5] = 'creer';

		$r++;
		$this->rights[$r][0] = 20782;
		$this->rights[$r][1] = 'Validate contract';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'contract';
		$this->rights[$r][5] = 'val';

		$r++;
		$this->rights[$r][0] = 20783;
		$this->rights[$r][1] = 'Delete contract';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'contract';
		$this->rights[$r][5] = 'del';


		$r++;
		$this->rights[$r][0] = 20795;
		$this->rights[$r][1] = 'Lista aprobadores planilla';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'salapr';
		$this->rights[$r][5] = 'lire';

		$r++;
		$this->rights[$r][0] = 20796;
		$this->rights[$r][1] = 'Crear aprobadores';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'salapr';
		$this->rights[$r][5] = 'creer';

		$r++;
		$this->rights[$r][0] = 20797;
		$this->rights[$r][1] = 'Validar aprobadores';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'salapr';
		$this->rights[$r][5] = 'val';

		$r++;
		$this->rights[$r][0] = 20798;
		$this->rights[$r][1] = 'Borrar aprobadores';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'salapr';
		$this->rights[$r][5] = 'del';

		$r++;
		$this->rights[$r][0] = 20800;
		$this->rights[$r][1] = 'Payments';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'pay';
		$this->rights[$r][5] = 'lire';

		$r++;
		$this->rights[$r][0] = 20801;
		$this->rights[$r][1] = 'Create payment';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'pay';
		$this->rights[$r][5] = 'creer';

		$r++;
		$this->rights[$r][0] = 20802;
		$this->rights[$r][1] = 'Delete payment';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'pay';
		$this->rights[$r][5] = 'del';

		$r++;
		$this->rights[$r][0] = 20810;
		$this->rights[$r][1] = 'Salary seats';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'seat';
		$this->rights[$r][5] = 'lire';

		$r++;
		$this->rights[$r][0] = 20811;
		$this->rights[$r][1] = 'Create Salary seats';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'seat';
		$this->rights[$r][5] = 'creer';

		// Main menu entries
		$this->menus = array();			// List of menus to add
		$r=0;
		$this->menu[$r]=array(	'fk_menu'=>0,
					'type'=>'top',
					'titre'=>'Salary',
					'mainmenu'=>'salary',
					'leftmenu'=>'0',
					'url'=>'/salary/index.php',
					'langs'=>'salary@salary',
					'position'=>100,
					'enabled'=>$conf->salary->enabled,
					'perms'=>'$user->rights->salary->lire',
					'target'=>'',
					'user'=>0);
		$r++;

		// Example to declare a Left Menu entry:
		$this->menu[$r]=array(	'fk_menu'=>'r=0',
					'type'=>'left',
					'titre'=>'Teachers',
					'mainmenu'=>'salary',
					'leftmenu' =>'teacher',
					'url'=>'/salary/index.php',
					'langs'=>'salary@salary',
					'position'=>100,
					'enabled'=>'$conf->salary->enabled',
					'perms'=>'$user->rights->salary->lire',
					'target'=>'',
					'user'=>0);
		/*
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=1',
					'type'=>'left',
					'titre'=>'Liste departament',
					'mainmenu'=>'salary',
					'leftmenu'=>'teacher',
					'url'=>'/salary/departament/liste.php',
					'langs'=>'salary@salary',
					'position'=>100,
					'enabled'=>'$conf->salary->enabled',
					'perms'=>'$user->rights->salary->dpto->lire',
					'target'=>'',
					'user'=>0);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=salary,fk_leftmenu=teacher',
					'type'=>'left',
					'titre'=>'New departament',
					'mainmenu'=>'salary',
					'url'=>'/salary/departament/fiche.php?action=create',
					'langs'=>'salary@salary',
					'position'=>100,
					'enabled'=>'$conf->salary->enabled',
					'perms'=>'$user->rights->salary->dpto->creer',
					'target'=>'',
					'user'=>0);

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=1',
					'type'=>'left',
					'titre'=>'Liste charges',
					'mainmenu'=>'salary',
					'leftmenu'=>'charge',
					'url'=>'/salary/charge/liste.php',
					'langs'=>'salary@salary',
					'position'=>100,
					'enabled'=>'$conf->salary->enabled',
					'perms'=>'$user->rights->salary->charge->lire',
					'target'=>'',
					'user'=>0);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=salary,fk_leftmenu=charge',
					'type'=>'left',
					'titre'=>'Create charge',
					'mainmenu'=>'salary',
					'url'=>'/salary/charge/fiche.php?action=create',
					'langs'=>'salary@salary',
					'position'=>100,
					'enabled'=>'$conf->salary->enabled',
					'perms'=>'$user->rights->salary->charge->creer',
					'target'=>'',
					'user'=>0);
		*/
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=1',
					'type'=>'left',
					'titre'=>'Liste regional',
					'mainmenu'=>'salary',
					'leftmenu' => 'regional',
					'url'=>'/salary/regional/liste.php',
					'langs'=>'salary@salary',
					'position'=>100,
					'enabled'=>'$conf->salary->enabled',
					'perms'=>'$user->rights->salary->region->lire',
					'target'=>'',
					'user'=>0);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=salary,fk_leftmenu=regional',
					'type'=>'left',
					'titre'=>'Create regional',
					'mainmenu'=>'salary',
					'url'=>'/salary/regional/fiche.php?action=create',
					'langs'=>'salary@salary',
					'position'=>100,
					'enabled'=>'$conf->salary->enabled',
					'perms'=>'$user->rights->salary->region->creer',
					'target'=>'',
					'user'=>0);

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=1',
					'type'=>'left',
					'titre'=>'Liste cost center',
					'mainmenu'=>'salary',
					'leftmenu' => 'cost',
					'url'=>'/salary/costcenter/liste.php',
					'langs'=>'salary@salary',
					'position'=>100,
					'enabled'=>'$conf->salary->enabled',
					'perms'=>'$user->rights->salary->cc->lire',
					'target'=>'',
					'user'=>0);

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=salary,fk_leftmenu=cost',
					'type'=>'left',
					'titre'=>'Create cost center',
					'mainmenu'=>'salary',
					'url'=>'/salary/costcenter/fiche.php?action=create',
					'langs'=>'salary@salary',
					'position'=>100,
					'enabled'=>'$conf->salary->enabled',
					'perms'=>'$user->rights->salary->cc->creer',
					'target'=>'',
					'user'=>0);


		/* $r++; */
		/* $this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=salary,fk_leftmenu=teacher', */
		/* 			'type'=>'left', */
		/* 			'titre'=>'Liste salary charges', */
		/* 			'mainmenu'=>'salary', */
		/* 			'url'=>'/salary/salarycharge/liste.php', */
		/* 			'langs'=>'salary@salary', */
		/* 			'position'=>100, */
		/* 			'enabled'=>'$conf->salary->enabled', */
		/* 			'perms'=>'$user->rights->salary->leersacharge', */
		/* 			'target'=>'', */
		/* 			'user'=>0); */
		/* $r++; */
		/* $this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=salary,fk_leftmenu=teacher', */
		/* 			'type'=>'left', */
		/* 			'titre'=>'Create salary charge', */
		/* 			'mainmenu'=>'salary', */
		/* 			'url'=>'/salary/salarycharge/fiche.php?action=create', */
		/* 			'langs'=>'salary@salary', */
		/* 			'position'=>100, */
		/* 			'enabled'=>'$conf->salary->enabled', */
		/* 			'perms'=>'$user->rights->salary->crearsacharge', */
		/* 			'target'=>'', */
		/* 			'user'=>0); */

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=0',
					'type'=>'left',
					'titre'=>'Calculation parameters',
					'mainmenu'=>'salary',
					'leftmenu'=>'cparam',
					'url'=>'/salary/typefol/index.php',
					'langs'=>'salary@salary',
					'position'=>100,
					'enabled'=>'$conf->salary->enabled',
					'perms'=>'$user->rights->salary->parameter',
					'target'=>'',
					'user'=>0);


		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=salary,fk_leftmenu=cparam',
					'type'=>'left',
					'titre'=>'Liste calculation procedure',
					'mainmenu'=>'salary',
					'leftmenu' => 'procedim',
					'url'=>'/salary/typefol/liste.php',
					'langs'=>'salary@salary',
					'position'=>100,
					'enabled'=>'$conf->salary->enabled',
					'perms'=>'$user->rights->salary->typefol->lire',
					'target'=>'',
					'user'=>0);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=salary,fk_leftmenu=procedim',
					'type'=>'left',
					'titre'=>'Create calculation procedure',
					'mainmenu'=>'salary',
					'url'=>'/salary/typefol/fiche.php?action=create',
					'langs'=>'salary@salary',
					'position'=>100,
					'enabled'=>'$conf->salary->enabled',
					'perms'=>'$user->rights->salary->typefol->creer',
					'target'=>'',
					'user'=>0);


		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=salary,fk_leftmenu=cparam',
					'type'=>'left',
					'titre'=>'Liste proces',
					'mainmenu'=>'salary',
					'leftmenu' => 'proces',
					'url'=>'/salary/proces/liste.php',
					'langs'=>'salary@salary',
					'position'=>100,
					'enabled'=>'$conf->salary->enabled',
					'perms'=>'$user->rights->salary->proces->lire',
					'target'=>'',
					'user'=>0);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=salary,fk_leftmenu=proces',
					'type'=>'left',
					'titre'=>'Create proces',
					'mainmenu'=>'salary',
					'url'=>'/salary/proces/fiche.php?action=create',
					'langs'=>'salary@salary',
					'position'=>100,
					'enabled'=>'$conf->salary->enabled',
					'perms'=>'$user->rights->salary->proces->creer',
					'target'=>'',
					'user'=>0);


		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=salary,fk_leftmenu=cparam',
					'type'=>'left',
					'titre'=>'Liste period',
					'mainmenu'=>'salary',
					'leftmenu' => 'period',
					'url'=>'/salary/period/liste.php',
					'langs'=>'salary@salary',
					'position'=>100,
					'enabled'=>'$conf->salary->enabled',
					'perms'=>'$user->rights->salary->period->lire',
					'target'=>'',
					'user'=>0);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=salary,fk_leftmenu=period',
					'type'=>'left',
					'titre'=>'Create period',
					'mainmenu'=>'salary',
					'url'=>'/salary/period/fiche.php?action=create',
					'langs'=>'salary@salary',
					'position'=>100,
					'enabled'=>'$conf->salary->enabled',
					'perms'=>'$user->rights->salary->period->creer',
					'target'=>'',
					'user'=>0);

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=salary,fk_leftmenu=cparam',
					'type'=>'left',
					'titre'=>'Liste formulas',
					'mainmenu'=>'salary',
					'leftmenu' => 'formula',
					'url'=>'/salary/formula/liste.php',
					'langs'=>'salary@salary',
					'position'=>100,
					'enabled'=>'$conf->salary->enabled',
					'perms'=>'$user->rights->salary->formula->lire',
					'target'=>'',
					'user'=>0);

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=salary,fk_leftmenu=formula',
					'type'=>'left',
					'titre'=>'Create formula',
					'mainmenu'=>'salary',
					'url'=>'/salary/formula/fiche.php?action=create',
					'langs'=>'salary@salary',
					'position'=>100,
					'enabled'=>'$conf->salary->enabled',
					'perms'=>'$user->rights->salary->formula->creer',
					'target'=>'',
					'user'=>0);

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=0',
					'type'=>'left',
					'titre'=>'Payroll parameters',
					'mainmenu'=>'salary',
					'leftmenu'=>'payroll',
					'url'=>'/salary/concept/index.php',
					'langs'=>'salary@salary',
					'position'=>100,
					'enabled'=>'$conf->salary->enabled',
					'perms'=>'$user->rights->salary->payroll',
					'target'=>'',
					'user'=>0);

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=salary,fk_leftmenu=payroll',
					'type'=>'left',
					'titre'=>'Liste concept',
					'mainmenu'=>'salary',
					'leftmenu'=>'concept',
					'url'=>'/salary/concept/liste.php',
					'langs'=>'salary@salary',
					'position'=>100,
					'enabled'=>'$conf->salary->enabled',
					'perms'=>'$user->rights->salary->concept->lire',
					'target'=>'',
					'user'=>0);

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=salary,fk_leftmenu=concept',
					'type'=>'left',
					'titre'=>'Create concept',
					'mainmenu'=>'salary',
					'url'=>'/salary/concept/fiche.php?action=create',
					'langs'=>'salary@salary',
					'position'=>100,
					'enabled'=>'$conf->salary->enabled',
					'perms'=>'$user->rights->salary->concept->creer',
					'target'=>'',
					'user'=>0);

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=salary,fk_leftmenu=payroll',
					'type'=>'left',
					'titre'=>'Liste generic table',
					'mainmenu'=>'salary',
					'leftmenu' => 'generic',
					'url'=>'/salary/generic/liste.php',
					'langs'=>'salary@salary',
					'position'=>100,
					'enabled'=>'$conf->salary->enabled',
					'perms'=>'$user->rights->salary->generic->lire',
					'target'=>'',
					'user'=>0);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=salary,fk_leftmenu=generic',
					'type'=>'left',
					'titre'=>'Create generic table',
					'mainmenu'=>'salary',
					'url'=>'/salary/generic/fiche.php?action=create',
					'langs'=>'salary@salary',
					'position'=>100,
					'enabled'=>'$conf->salary->enabled',
					'perms'=>'$user->rights->salary->generic->creer',
					'target'=>'',
					'user'=>0);

		// $r++;
		// $this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=salary,fk_leftmenu=teacher',
		// 			'type'=>'left',
		// 			'titre'=>'Liste generic field',
		// 			'mainmenu'=>'salary',
		// 			'url'=>'/salary/generic/listefield.php',
		// 			'langs'=>'salary@salary',
		// 			'position'=>100,
		// 			'enabled'=>'$conf->salary->enabled',
		// 			'perms'=>'$user->rights->salary->leergeneric',
		// 			'target'=>'',
		// 			'user'=>0);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=salary,fk_leftmenu=generic',
					'type'=>'left',
					'titre'=>'Create generic field',
					'mainmenu'=>'salary',
					'url'=>'/salary/generic/fichefield.php?action=create',
					'langs'=>'salary@salary',
					'position'=>100,
					'enabled'=>'$conf->salary->enabled',
					'perms'=>'$user->rights->salary->generic->creer',
					'target'=>'',
					'user'=>0);

		//upload

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=0',
					'type'=>'left',
					'titre'=>'Upload archive',
					'mainmenu'=>'salary',
					'url'=>'/salary/upload/fiche.php?action=create',
					'langs'=>'salary@salary',
					'position'=>100,
					'enabled'=>'$conf->salary->enabled',
					'perms'=>'$user->rights->salary->uparchive',
					'target'=>'',
					'user'=>0);

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=0',
					'type'=>'left',
					'titre'=>'Processed payroll',
					'mainmenu'=>'salary',
					'leftmenu'=>'Proces',
					'url'=>'/salary/genera/planilla.php?action=create',
					'langs'=>'salary@salary',
					'position'=>100,
					'enabled'=>'$conf->salary->enabled',
					'perms'=>'$user->rights->salary->generasal',
					'target'=>'',
					'user'=>0);

		// $r++;
		// $this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=salary,fk_leftmenu=Proces',
		// 			'type'=>'left',
		// 			'titre'=>'Genera Salary sould',
		// 			'mainmenu'=>'salary',
		// 			'url'=>'/salary/genera/planilla.php?action=create',
		// 			'langs'=>'salary@salary',
		// 			'position'=>100,
		// 			'enabled'=>'$conf->salary->enabled',
		// 			'perms'=>'$user->rights->salary->generasal',
		// 			'target'=>'',
		// 			'user'=>0);

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=0',
					'type'=>'left',
					'titre'=>'Report',
					'mainmenu'=>'salary',
					'leftmenu'=>'Report',
					'url'=>'/salary/report/index.php',
					'langs'=>'salary@salary',
					'position'=>100,
					'enabled'=>'$conf->salary->enabled',
					'perms'=>'$user->rights->salary->leerreport',
					'target'=>'',
					'user'=>0);


		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=salary,fk_leftmenu=Report',
					'type'=>'left',
					'titre'=>'Sould salary',
					'mainmenu'=>'salary',
					'url'=>'/salary/report/rplanillacof.php?action=create',
					'langs'=>'salary@salary',
					'position'=>100,
					'enabled'=>'$conf->salary->enabled',
					'perms'=>'$user->rights->salary->crearrsal',
					'target'=>'',
					'user'=>0);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=salary,fk_leftmenu=Report',
					'type'=>'left',
					'titre'=>'Employer contribution',
					'mainmenu'=>'salary',
					'url'=>'/salary/report/rap.php?action=create',
					'langs'=>'salary@salary',
					'position'=>100,
					'enabled'=>'$conf->salary->enabled',
					'perms'=>'$user->rights->salary->crearrsal',
					'target'=>'',
					'user'=>0);

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=salary,fk_leftmenu=Report',
					'type'=>'left',
					'titre'=>'Boleta Salary',
					'mainmenu'=>'salary',
					'url'=>'/salary/report/rboleta.php?action=create',
					'langs'=>'salary@salary',
					'position'=>100,
					'enabled'=>'$conf->salary->enabled',
					'perms'=>'$user->rights->salary->crearbsal',
					'target'=>'',
					'user'=>0);

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=0',
					'type'=>'left',
					'titre'=>'Members',
					'mainmenu'=>'salary',
					'leftmenu'=>'Member',
					'url'=>'/salary/user/index.php',
					'langs'=>'salary@salary',
					'position'=>100,
					'enabled'=>'$conf->salary->enabled',
					'perms'=>'$user->rights->salary->member->lire',
					'target'=>'',
					'user'=>0);

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=salary,fk_leftmenu=Member',
					'type'=>'left',
					'titre'=>'Liste member',
					'mainmenu'=>'salary',
					'url'=>'/salary/user/liste.php',
					'langs'=>'salary@salary',
					'position'=>100,
					'enabled'=>'$conf->salary->enabled',
					'perms'=>'$user->rights->salary->member->lire',
					'target'=>'',
					'user'=>0);

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=salary,fk_leftmenu=Member',
					'type'=>'left',
					'titre'=>'Create member',
					'mainmenu'=>'salary',
					'url'=>'/salary/user/fiche.php?action=create',
					'langs'=>'salary@salary',
					'position'=>100,
					'enabled'=>'$conf->salary->enabled',
					'perms'=>'$user->rights->salary->member->creer',
					'target'=>'',
					'user'=>0);

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=0',
					'type'=>'left',
					'titre'=>'Approvers payroll',
					'mainmenu'=>'salary',
					'leftmenu'=>'apayroll',
					'url'=>'/salary/approver/liste.php',
					'langs'=>'salary@salary',
					'position'=>100,
					'enabled'=>'$conf->salary->enabled',
					'perms'=>'$user->rights->salary->salapr->lire',
					'target'=>'',
					'user'=>0);

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=salary,fk_leftmenu=apayroll',
					'type'=>'left',
					'titre'=>'Newapprover',
					'mainmenu'=>'salary',
					'url'=>'/salary/approver/fiche.php?action=create',
					'langs'=>'salary@salary',
					'position'=>100,
					'enabled'=>'$conf->salary->enabled',
					'perms'=>'$user->rights->salary->salapr->creer',
					'target'=>'',
					'user'=>0);

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=0',
					'type'=>'left',
					'titre'=>'Payments',
					'mainmenu'=>'salary',
					'leftmenu'=>'pay',
					'url'=>'/salary/payment/liste.php',
					'langs'=>'salary@salary',
					'position'=>100,
					'enabled'=>'$conf->salary->enabled',
					'perms'=>'$user->rights->salary->pay->lire',
					'target'=>'',
					'user'=>0);

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=salary,fk_leftmenu=pay',
					'type'=>'left',
					'titre'=>'Newpayment',
					'mainmenu'=>'salary',
					'url'=>'/salary/payment/fiche.php?action=create',
					'langs'=>'salary@salary',
					'position'=>100,
					'enabled'=>'$conf->salary->enabled',
					'perms'=>'$user->rights->salary->pay->creer',
					'target'=>'',
					'user'=>0);

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=0',
					'type'=>'left',
					'titre'=>'Seats',
					'mainmenu'=>'salary',
					'leftmenu'=>'seats',
					'url'=>'/contab/seatssalary/index.php',
					'langs'=>'salary@salary',
					'position'=>100,
					'enabled'=>'$conf->salary->enabled',
					'perms'=>'$user->rights->salary->seat->lire',
					'target'=>'',
					'user'=>0);

		// $r++;
		// $this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=salary,fk_leftmenu=seats',
		// 			'type'=>'left',
		// 			'titre'=>'Create salary seats',
		// 			'mainmenu'=>'salary',
		// 			'url'=>'/contab/seatssalary/fiche.php?action=create',
		// 			'langs'=>'salary@salary',
		// 			'position'=>100,
		// 			'enabled'=>'$conf->salary->enabled',
		// 			'perms'=>'$user->rights->salary->crearseat',
		// 			'target'=>'',
		// 			'user'=>0);

		// Add here entries to declare new menus
		//
		// Example to declare a new Top Menu entry and its Left menu entry:
		// $this->menu[$r]=array(	'fk_menu'=>0,			                // Put 0 if this is a top menu
		//							'type'=>'top',			                // This is a Top menu entry
		//							'titre'=>'Produccion top menu',
		//							'mainmenu'=>'produccion',
		//							'leftmenu'=>'produccion',
		//							'url'=>'/produccion/pagetop.php',
		//							'langs'=>'mylangfile',	                // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
		//							'position'=>100,
		//							'enabled'=>'$conf->produccion->enabled',	// Define condition to show or hide menu entry. Use '$conf->produccion->enabled' if entry must be visible if module is enabled.
		//							'perms'=>'1',			                // Use 'perms'=>'$user->rights->produccion->level1->level2' if you want your menu with a permission rules
		//							'target'=>'',
		//							'user'=>2);				                // 0=Menu for internal users, 1=external users, 2=both
		// $r++;
		// $this->menu[$r]=array(	'fk_menu'=>'r=0',		                // Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
		//							'type'=>'left',			                // This is a Left menu entry
		//							'titre'=>'Produccion left menu',
		//							'mainmenu'=>'produccion',
		//							'leftmenu'=>'produccion',
		//							'url'=>'/produccion/pagelevel1.php',
		//							'langs'=>'mylangfile',	                // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
		//							'position'=>100,
		//							'enabled'=>'$conf->produccion->enabled',	// Define condition to show or hide menu entry. Use '$conf->produccion->enabled' if entry must be visible if module is enabled.
		//							'perms'=>'1',			                // Use 'perms'=>'$user->rights->produccion->level1->level2' if you want your menu with a permission rules
		//							'target'=>'',
		//							'user'=>2);				                // 0=Menu for internal users, 1=external users, 2=both
		// $r++;
		//
		// Example to declare a Left Menu entry into an existing Top menu entry:
		// $this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=mainmenucode',	// Use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy'
		//							'type'=>'left',			                // This is a Left menu entry
		//							'titre'=>'Produccion left menu',
		//							'mainmenu'=>'mainmenucode',
		//							'leftmenu'=>'produccion',
		//							'url'=>'/produccion/pagelevel2.php',
		//							'langs'=>'mylangfile',	                // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
		//							'position'=>100,
		//							'enabled'=>'$conf->produccion->enabled',  // Define condition to show or hide menu entry. Use '$conf->produccion->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
		//							'perms'=>'1',			                // Use 'perms'=>'$user->rights->produccion->level1->level2' if you want your menu with a permission rules
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
		// $this->export_fields_array[$r]=array('s.rowid'=>"IdCompany",'s.nom'=>'CompanyName','s.address'=>'Address','s.cp'=>'Zip','s.ville'=>'Town','s.fk_pays'=>'Country','s.tel'=>'Phone','s.siren'=>'ProfId1','s.siret'=>'ProfId2','s.ape'=>'ProfId3','s.idprof4'=>'ProfId4','s.code_compta'=>'CustomerAccountancyCode','s.code_compta_fournisseur'=>'SupplierAccountancyCode','f.rowid'=>"InvoiceId",'f.facnumber'=>"InvoiceRef",'f.datec'=>"InvoiceDateCreation",'f.datef'=>"DateInvoice",'f.total'=>"TotalHT",'f.total_ttc'=>"TotalTTC",'f.tva'=>"TotalVAT",'f.paye'=>"InvoicePaid",'f.fk_statut'=>'InvoiceStatus','f.note'=>"InvoiceNote",'fd.rowid'=>'LineId','fd.description'=>"LineDescription",'fd.price'=>"LineUnitPrice",'fd.tva_tx'=>"LineVATRate",'fd.qty'=>"LineQty",'fd.total_ht'=>"LineTotalHT",'fd.total_tva'=>"LineTotalTVA",'fd.total_ttc'=>"LineTotalTTC",'fd.date_start'=>"DateStart",'fd.date_end'=>"DateEnd",'fd.fk_product'=>'ProductId','p.ref'=>'ProductRef');
		// $this->export_entities_array[$r]=array('s.rowid'=>"company",'s.nom'=>'company','s.address'=>'company','s.cp'=>'company','s.ville'=>'company','s.fk_pays'=>'company','s.tel'=>'company','s.siren'=>'company','s.siret'=>'company','s.ape'=>'company','s.idprof4'=>'company','s.code_compta'=>'company','s.code_compta_fournisseur'=>'company','f.rowid'=>"invoice",'f.facnumber'=>"invoice",'f.datec'=>"invoice",'f.datef'=>"invoice",'f.total'=>"invoice",'f.total_ttc'=>"invoice",'f.tva'=>"invoice",'f.paye'=>"invoice",'f.fk_statut'=>'invoice','f.note'=>"invoice",'fd.rowid'=>'invoice_line','fd.description'=>"invoice_line",'fd.price'=>"invoice_line",'fd.total_ht'=>"invoice_line",'fd.total_tva'=>"invoice_line",'fd.total_ttc'=>"invoice_line",'fd.tva_tx'=>"invoice_line",'fd.qty'=>"invoice_line",'fd.date_start'=>"invoice_line",'fd.date_end'=>"invoice_line",'fd.fk_product'=>'product','p.ref'=>'product');
		// $this->export_sql_start[$r]='SELECT DISTINCT ';
		// $this->export_sql_end[$r]  =' FROM ('.MAIN_DB_PREFIX.'facture as f, '.MAIN_DB_PREFIX.'facturedet as fd, '.MAIN_DB_PREFIX.'societe as s)';
		// $this->export_sql_end[$r] .=' LEFT JOIN '.MAIN_DB_PREFIX.'product as p on (fd.fk_product = p.rowid)';
		// $this->export_sql_end[$r] .=' WHERE f.fk_soc = s.rowid AND f.rowid = fd.fk_facture';
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

		$result=$this->load_tables();

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


	/**
	 *		Create tables, keys and data required by module
	 * 		Files llx_table1.sql, llx_table1.key.sql llx_data.sql with create table, create keys
	 * 		and create data commands must be stored in directory /produccion/sql/
	 *		This function is called by this->init
	 *
	 * 		@return		int		<=0 if KO, >0 if OK
	 */
	function load_tables()
	{
		return $this->_load_tables('/salary/sql/');
	}
}




?>