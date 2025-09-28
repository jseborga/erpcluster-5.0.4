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
 * 	\defgroup   mant     Module Mantenimiento
 *  \brief      Example of a module descriptor.
 *				Such a file must be copied into htdocs/mant/core/modules directory.
 *  \file       htdocs/mant/core/modules/modMant.class.php
 *  \ingroup    mant
 *  \brief      Description and activation file for module Mant
 */
include_once DOL_DOCUMENT_ROOT .'/core/modules/DolibarrModules.class.php';


/**
 *  Description and activation class for module Mant
 */
class modMant extends DolibarrModules
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
		$this->numero = 501000;
		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'mant';

		// Family can be 'crm','financial','hr','projects','products','ecm','technic','other'
		// It is used to group modules in module setup page
		$this->family = "other";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i','',get_class($this));
		// Module description, used if translation string 'ModuleXXXDesc' not found (where XXX is value of numeric property 'numero' of module)
		$this->description = "Mantenimiento de Infraestructura";
		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = '5.0.1.4';
		// Key used in llx_const table to save module status enabled/disabled (where MYMODULE is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Where to store the module in setup page (0=common,1=interface,2=others,3=very specific)
		$this->special = 0;
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		$this->picto='mant';
		$this->picto='mant@mant';

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
		$this->module_parts = array();

		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/mymodule/temp");
		$this->dirs = array("/mant/temp");

		// Config pages. Put here list of php page, stored into mymodule/admin directory, to use to setup module.
		$this->config_page_url = array("mant.php@mant");

		// Dependencies
		$this->hidden = false;			// A condition to hide module
		$this->depends = array();		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array();	// List of modules id to disable if this one is disabled
		$this->conflictwith = array();	// List of modules id this module is in conflict with
		$this->phpmin = array(5,0);					// Minimum version of PHP required by module
		$this->need_dolibarr_version = array(3,0);	// Minimum version of Dolibarr required by module
		$this->langfiles = array("mant@mant");

		// Constants
		// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
		// Example: $this->const=array(0=>array('MYMODULE_MYNEWCONST1','chaine','myvalue','This is a constant to add',1),
		//                             1=>array('MYMODULE_MYNEWCONST2','chaine','myvalue','This is another constant to add',0, 'current', 1)
		// );
		$this->const = array(0=>array('MANT_EXTENSION_MAIL_DEFAULT','chaine','','Correo predeterminado a controlar para nuevos tickect',1),
				     1=>array('MANT_USE_EXTENSION_MAIL_COMPANY','chaine','0','Utilizar extension del Correo de la empresa: 0=No; 1=Si',1),
				     2=>array('MANT_SUBPERMCATEGORY_FOR_DOCUMENTS','chaine','image','Constante para autorizar vista de imagenes del modulo',1),
				     3=>array('MANT_URL','chaine','http://localhost','Direccion de la pagina local',1),
				     4=>array('MANT_EQUIPMENT_ADDON','chaine','mod_mant_numbertwo','Modelo de numeracion para equipos',1),
				     5=>array('MANT_DEFAULT_PROPERTY','chaine','1','Codigo referencial del inmueble por defecto para seleccion',1),
				     5=>array('MANT_SEND_EMAIL','chaine','0','Envio de correo por defecto 0=No: 1=Si',1),
				     6=>array('MEMBER_USE_SEARCH_TO_SELECT','chaine','3','Usar buscador de miembros desde: 1=caracter, 2=dos caracteres, 3=tres caracteres',1),

				     );

		// Array to add new pages in new tabs
		// Example: $this->tabs = array('objecttype:+tabname1:Title1:mylangfile@mymodule:$user->rights->mymodule->read:/mymodule/mynewtab1.php?id=__ID__',  	// To add a new tab identified by code tabname1
        //                              'objecttype:+tabname2:Title2:mylangfile@mymodule:$user->rights->othermodule->read:/mymodule/mynewtab2.php?id=__ID__',  	// To add another new tab identified by code tabname2
        //                              'objecttype:-tabname':NU:conditiontoremove);                                                     						// To remove an existing tab identified by code tabname
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
        $this->tabs = array(
			'assets:+Maintenance:Maintenance:@mant:$user->rights->mant->jobs->mant:/mant/jobs/mant.php?id=__ID__',
		);

        // Dictionnaries
	    if (! isset($conf->mant->enabled))
	      {
        	$conf->mant=new stdClass();
        	$conf->mant->enabled=0;
	      }
	    $this->dictionnaries=
	      array(
		    'langs' => 'mant@mant',
		    'tabname' => array(MAIN_DB_PREFIX."c_especiality",
				       MAIN_DB_PREFIX."c_typemant",
				       MAIN_DB_PREFIX."c_type_jobs",
				       MAIN_DB_PREFIX."c_inspection_book",
				       MAIN_DB_PREFIX."c_type_campo",
				       MAIN_DB_PREFIX."c_frequency",
				       MAIN_DB_PREFIX."c_working_class",
				       MAIN_DB_PREFIX."m_type_repair",),
		    'tablib' => array("Especialityarea",
				      "Typeofmaintenance",
				      "typejobs",
				      "Inspectionbook",
				      "Typecampo",
				      "Frequency",
				      "Workingclass",
				      'Typerepair'),
		    'tabsql' => array("SELECT f.rowid AS rowid, f.code, f.label, f.active FROM ".MAIN_DB_PREFIX."c_especiality AS f",
				      "SELECT f.rowid AS rowid, f.entity, f.code, f.label, f.active FROM ".MAIN_DB_PREFIX."c_typemant AS f",
				      "SELECT f.rowid AS rowid, f.code, f.label, f.active FROM ".MAIN_DB_PREFIX."c_type_jobs AS f",
				      "SELECT f.rowid AS rowid, f.code, f.label, f.active FROM ".MAIN_DB_PREFIX."c_inspection_book AS f",
				      "SELECT f.rowid AS rowid, f.code, f.label, f.active FROM ".MAIN_DB_PREFIX."c_type_campo AS f",
				      "SELECT f.rowid AS rowid, f.code, f.label, f.active FROM ".MAIN_DB_PREFIX."c_frequency AS f",
				      "SELECT f.rowid AS rowid, f.code, f.label, f.active FROM ".MAIN_DB_PREFIX."c_working_class AS f",
				      "SELECT f.entity, f.rowid AS rowid, f.ref, f.label, f.active FROM ".MAIN_DB_PREFIX."m_type_repair AS f"),
		    'tabsqlsort' => array("label ASC",
					  "label ASC",
					  "label ASC",
					  "label ASC",
					  "label ASC",
					  "label ASC",
					  "label ASC",
					  "label ASC",),
		    'tabfield' => array("code,label",
					"entity,code,label",
					"code,label",
					"code,label",
					"code,label",
					"code,label",
					"code,label",
					"entity,ref,label",),
		    'tabfieldvalue' => array("code,label",
					     "entity,code,label",
					     "code,label",
					     "code,label",
					     "code,label",
					     "code,label",
					     "code,label",
					     "entity,ref,label",),
		    'tabfieldinsert'=>array("code,label",
					    "entity,code,label",
					    "code,label",
					    "code,label",
					    "code,label",
					    "code,label",
					    "code,label",
					    "entity,ref,label",),
		    'tabrowid'=>array("rowid",
				      "rowid",
				      "rowid",
				      "rowid",
				      "rowid",
				      "rowid",
				      "rowid",
				      "rowid",),
		    'tabcond'=>array($conf->mant->enabled,
				     $conf->mant->enabled,
				     $conf->mant->enabled,
				     $conf->mant->enabled,
				     $conf->mant->enabled,
				     $conf->mant->enabled,
				     $conf->mant->enabled,
				     $conf->mant->enabled,)
		    );
        /* Example:
        if (! isset($conf->mymodule->enabled)) $conf->mymodule->enabled=0;	// This is to avoid warnings
        $this->dictionnaries=array(
            'langs'=>'mylangfile@mymodule',
            'tabname'=>array(MAIN_DB_PREFIX."table1",MAIN_DB_PREFIX."table2",MAIN_DB_PREFIX."table3"),		// List of tables we want to see into dictonnary editor
            'tablib'=>array("Table1","Table2","Table3"),													// Label of tables
            'tabsql'=>array('SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table1 as f','SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table2 as f','SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table3 as f'),	// Request to select fields
            'tabsqlsort'=>array("label ASC","label ASC","label ASC"),																					// Sort order
            'tabfield'=>array("code,label","code,label","code,label"),																					// List of fields (result of select to show dictionnary)
            'tabfieldvalue'=>array("code,label","code,label","code,label"),																				// List of fields (list of fields to edit a record)
            'tabfieldinsert'=>array("code,label","code,label","code,label"),																			// List of fields (list of fields for insert)
            'tabrowid'=>array("rowid","rowid","rowid"),																									// Name of columns with primary key (try to always name it 'rowid')
            'tabcond'=>array($conf->mymodule->enabled,$conf->mymodule->enabled,$conf->mymodule->enabled)												// Condition to show each dictionnary
        );
        */

        // Boxes
		// Add here list of php file(s) stored in core/boxes that contains class to show a box.
        $this->boxes = array();			// List of boxes
		// Example:
		//$this->boxes=array(array(0=>array('file'=>'myboxa.php','note'=>'','enabledbydefaulton'=>'Home'),1=>array('file'=>'myboxb.php','note'=>''),2=>array('file'=>'myboxc.php','note'=>'')););

		// Permissions
		$this->rights = array();		// Permission array used by this module
		$r=0;
		$this->rights[$r][0] = 501001;
		$this->rights[$r][1] = 'Mantenimiento';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'read';

		$r++;
		$this->rights[$r][0] = 501015;
		$this->rights[$r][1] = 'Leer Maestros';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'teacher';
		$this->rights[$r][5] = 'leer';

		$r++;
		$this->rights[$r][0] = 501016;
		$this->rights[$r][1] = 'Crear Maestros';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'teacher';
		$this->rights[$r][5] = 'crear';
		$r++;
		$this->rights[$r][0] = 501017;
		$this->rights[$r][1] = 'Borrar Maestros';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'teacher';
		$this->rights[$r][5] = 'del';
		$r++;
		$this->rights[$r][0] = 501018;
		$this->rights[$r][1] = 'Validar Maestros';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'teacher';
		$this->rights[$r][5] = 'val';
		$r++;
		$this->rights[$r][0] = 501019;
		$this->rights[$r][1] = 'Actualizar información activos';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'update';

		$r++;
		$this->rights[$r][0] = 501021;
		$this->rights[$r][1] = 'Leer Tickets Trabajo';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'tick';
		$this->rights[$r][5] = 'leer';
		$r++;
		$this->rights[$r][0] = 501022;
		$this->rights[$r][1] = 'Crear Tickets Trabajo';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'tick';
		$this->rights[$r][5] = 'crear';
		$r++;
		$this->rights[$r][0] = 501023;
		$this->rights[$r][1] = 'Borrar Tickets Trabajo';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'tick';
		$this->rights[$r][5] = 'del';
		$r++;
		$this->rights[$r][0] = 501024;
		$this->rights[$r][1] = 'Rechazar Ticket de Trabajo';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'tick';
		$this->rights[$r][5] = 'rech';
		$r++;
		$this->rights[$r][0] = 501025;
		$this->rights[$r][1] = 'Validar Ticket de Trabajo';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'tick';
		$this->rights[$r][5] = 'val';
		$r++;
		$this->rights[$r][0] = 501026;
		$this->rights[$r][1] = 'Leer todos los Ticket de Trabajo';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'tick';
		$this->rights[$r][5] = 'selus';
		$r++;
		$this->rights[$r][0] = 501027;
		$this->rights[$r][1] = 'Asignar Ticket de Trabajo';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'tick';
		$this->rights[$r][5] = 'ass';
		$r++;
		$this->rights[$r][0] = 501028;
		$this->rights[$r][1] = 'Asignar Ticket a tecnicos';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'tick';
		$this->rights[$r][5] = 'asst';
		$r++;
		$this->rights[$r][0] = 501029;
		$this->rights[$r][1] = 'Programar Ticket';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'tick';
		$this->rights[$r][5] = 'prog';

		$r++;
		$this->rights[$r][0] = 501031;
		$this->rights[$r][1] = 'Leer Ordenes de Trabajo';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'jobs';
		$this->rights[$r][5] = 'leer';

		$r++;
		$this->rights[$r][0] = 501032;
		$this->rights[$r][1] = 'Crear Ordenes de Trabajo';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'jobs';
		$this->rights[$r][5] = 'crear';

		$r++;
		$this->rights[$r][0] = 501033;
		$this->rights[$r][1] = 'Validar Ordenes de Trabajo';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'jobs';
		$this->rights[$r][5] = 'val';

		$r++;
		$this->rights[$r][0] = 501034;
		$this->rights[$r][1] = 'Borrar Ordenes de Trabajo';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'jobs';
		$this->rights[$r][5] = 'del';

		$r++;
		$this->rights[$r][0] = 501035;
		$this->rights[$r][1] = 'Rechazar Ordenes de Trabajo';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'jobs';
		$this->rights[$r][5] = 'rech';

		$r++;
		$this->rights[$r][0] = 501036;
		$this->rights[$r][1] = 'Asignar Ordenes de Trabajo';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'jobs';
		$this->rights[$r][5] = 'assignjobs';

		$r++;
		$this->rights[$r][0] = 501037;
		$this->rights[$r][1] = 'Iniciar Ordenes de Trabajo';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'jobs';
		$this->rights[$r][5] = 'upjobs';

		$r++;
		$this->rights[$r][0] = 501038;
		$this->rights[$r][1] = 'Registrar trabajo realizado';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'jobs';
		$this->rights[$r][5] = 'regjobs';
		$r++;
		$this->rights[$r][0] = 501039;
		$this->rights[$r][1] = 'Modificar trabajo realizado';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'jobs';
		$this->rights[$r][5] = 'modjobs';
		$r++;
		$this->rights[$r][0] = 501041;
		$this->rights[$r][1] = 'Eliminar trabajo realizado';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'jobs';
		$this->rights[$r][5] = 'deljobs';

		$r++;
		$this->rights[$r][0] = 501042;
		$this->rights[$r][1] = 'Leer Todas las Ordenes de Trabajo';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'jobs';
		$this->rights[$r][5] = 'leerall';


		$r++;
		$this->rights[$r][0] = 501045;
		$this->rights[$r][1] = 'Abrir Ordenes de Trabajo';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'jobs';
		$this->rights[$r][5] = 'openwork';

		$r++;
		$this->rights[$r][0] = 501046;
		$this->rights[$r][1] = 'Ver mantenimiento por activo';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'jobs';
		$this->rights[$r][5] = 'mant';

		$r++;
		$this->rights[$r][0] = 501047;
		$this->rights[$r][1] = 'Ver imagenes';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'image';
		$this->rights[$r][5] = 'read';

		$r++;
		$this->rights[$r][0] = 501048;
		$this->rights[$r][1] = 'Rechazar Ordenes de Trabajo Asignados';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'jobs';
		$this->rights[$r][5] = 'rechasig';
		$r++;
		$this->rights[$r][0] = 501049;
		$this->rights[$r][1] = 'Cerrar Ordenes de Trabajo';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'jobs';
		$this->rights[$r][5] = 'close';

		$r++;
		$this->rights[$r][0] = 501051;
		$this->rights[$r][1] = 'Leer Programacion Mantenimiento';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'prog';
		$this->rights[$r][5] = 'leer';

		$r++;
		$this->rights[$r][0] = 501052;
		$this->rights[$r][1] = 'Crear Programacion Mantenimiento';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'prog';
		$this->rights[$r][5] = 'crear';
		$r++;
		$this->rights[$r][0] = 501053;
		$this->rights[$r][1] = 'Modificar Programacion Mantenimiento';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'prog';
		$this->rights[$r][5] = 'mod';
		$r++;
		$this->rights[$r][0] = 501054;
		$this->rights[$r][1] = 'Borrar Programacion Mantenimiento';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'prog';
		$this->rights[$r][5] = 'del';
		$r++;
		$this->rights[$r][0] = 501055;
		$this->rights[$r][1] = 'Validar Programacion Mantenimiento';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'prog';
		$this->rights[$r][5] = 'val';

		$r++;
		$this->rights[$r][0] = 501061;
		$this->rights[$r][1] = 'Ver bienes';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'equ';
		$this->rights[$r][5] = 'read';
		$r++;
		$this->rights[$r][0] = 501062;
		$this->rights[$r][1] = 'Crear/Modificar bienes';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'equ';
		$this->rights[$r][5] = 'write';
		$r++;
		$this->rights[$r][0] = 501063;
		$this->rights[$r][1] = 'Borrar bienes';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'equ';
		$this->rights[$r][5] = 'del';

		$r++;
		$this->rights[$r][0] = 501071;
		$this->rights[$r][1] = 'Ver grupos de bienes';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'group';
		$this->rights[$r][5] = 'read';
		$r++;
		$this->rights[$r][0] = 501072;
		$this->rights[$r][1] = 'Crear/Modificar grupos de bienes';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'group';
		$this->rights[$r][5] = 'write';
		$r++;
		$this->rights[$r][0] = 501073;
		$this->rights[$r][1] = 'Borrar grupos de bienes';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'group';
		$this->rights[$r][5] = 'del';


		$r++;
		$this->rights[$r][0] = 501081;
		$this->rights[$r][1] = 'Reportes Mantenimiento';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'rep';
		$this->rights[$r][5] = 'leer';

		$r+=10;
		$this->rights[$r][0] = 501091;
		$this->rights[$r][1] = 'Subir documentos adjuntos';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'up';
		$this->rights[$r][5] = 'write';
		$r++;
		$this->rights[$r][0] = 501092;
		$this->rights[$r][1] = 'Eliminar documentos adjuntos';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'up';
		$this->rights[$r][5] = 'del';

		// Add here list of permission defined by an id, a label, a boolean and two constant strings.
		// Example:
		// $this->rights[$r][0] = 2000; 				// Permission id (must not be already used)
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
					'titre'=>'Mant',
					'mainmenu'=>'mant',
					'leftmenu'=>'0',
					'url'=>'/mant/index.php',
					'langs'=>'mant@mant',
					'position'=>100,
					'enabled'=>$conf->mant->enabled,
					'perms'=>'$user->rights->mant->read',
					'target'=>'',
					'user'=>2);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=0',
					'type'=>'left',
					'titre'=>'Teachers',
					'mainmenu'=>'mant',
					'leftmenu'=>'teacher',
					'url'=>'/mant/index.php',
					'langs'=>'mant@mant',
					'position'=>100,
					'enabled'=>'$conf->mant->enabled',
					'perms'=>'$user->rights->mant->teacher->leer',
					'target'=>'',
					'user'=>0);
		// $r++;
		// $this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=mant,fk_leftmenu=teacher',
		// 			'type'=>'left',
		// 			'titre'=>'View departament',
		// 			'mainmenu'=>'mant',
		// 			'leftmenu'=>'departament',
		// 			'url'=>'/mant/departament/liste.php',
		// 			'langs'=>'mant@mant',
		// 			'position'=>100,
		// 			'enabled'=>'$conf->mant->enabled',
		// 			'perms'=>'$user->rights->mant->teacher->leer',
		// 			'target'=>'',
		// 			'user'=>0);

		// $r++;
		// $this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=mant,fk_leftmenu=departament',
		// 			'type'=>'left',
		// 			'titre'=>'Create departament',
		// 			'mainmenu'=>'mant',
		// 			'url'=>'/mant/departament/fiche.php?action=create',
		// 			'langs'=>'mant@mant',
		// 			'position'=>100,
		// 			'enabled'=>'$conf->mant->enabled',
		// 			'perms'=>'$user->rights->mant->teacher->crear',
		// 			'target'=>'',
		// 			'user'=>0);

		// $r++;
		// $this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=mant,fk_leftmenu=teacher',
		// 			'type'=>'left',
		// 			'titre'=>'View charge',
		// 			'mainmenu'=>'mant',
		// 			'leftmenu'=>'charge',
		// 			'url'=>'/mant/charge/liste.php',
		// 			'langs'=>'mant@mant',
		// 			'position'=>100,
		// 			'enabled'=>'$conf->mant->enabled',
		// 			'perms'=>'$user->rights->mant->teacher->leer',
		// 			'target'=>'',
		// 			'user'=>0);

		// $r++;
		// $this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=mant,fk_leftmenu=charge',
		// 			'type'=>'left',
		// 			'titre'=>'Create charge',
		// 			'mainmenu'=>'mant',
		// 			'url'=>'/mant/charge/fiche.php?action=create',
		// 			'langs'=>'mant@mant',
		// 			'position'=>100,
		// 			'enabled'=>'$conf->mant->enabled',
		// 			'perms'=>'$user->rights->mant->teacher->crear',
		// 			'target'=>'',
		// 			'user'=>0);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=mant,fk_leftmenu=teacher',
					'type'=>'left',
					'titre'=>'View groups',
					'mainmenu'=>'mant',
					'leftmenu'=>'groups',
					'url'=>'/mant/groups/list.php',
					'langs'=>'mant@mant',
					'position'=>100,
					'enabled'=>$conf->mant->enabled,
					'perms'=>$user->rights->mant->group->read,
					'target'=>'',
					'user'=>0);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=mant,fk_leftmenu=teacher',
					'type'=>'left',
					'titre'=>'View Equipment',
					'mainmenu'=>'mant',
					'leftmenu'=>'equipment',
					'url'=>'/mant/equipment/list.php',
					'langs'=>'mant@mant',
					'position'=>100,
					'enabled'=>$conf->mant->enabled,
					'perms'=>$user->rights->mant->equ->read,
					'target'=>'',
					'user'=>0);

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=mant,fk_leftmenu=equipment',
					'type'=>'left',
					'titre'=>'Create equipment',
					'mainmenu'=>'mant',
					'url'=>'/mant/equipment/card.php?action=create',
					'langs'=>'mant@mant',
					'position'=>100,
					'enabled'=>$conf->mant->enabled,
					'perms'=>$user->rights->mant->equ->write,
					'target'=>'',
					'user'=>0);

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=mant,fk_leftmenu=teacher',
					'type'=>'left',
					'titre'=>'Typerepair',
					'mainmenu'=>'mant',
					'url'=>'/mant/typerepair/list.php',
					'langs'=>'mant@mant',
					'position'=>100,
					'enabled'=>'$conf->mant->enabled',
					'perms'=>'$user->rights->mant->teacher->leer',
					'target'=>'',
					'user'=>0);
		/*
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=mant,fk_leftmenu=teacher',
					'type'=>'left',
					'titre'=>'View inspection book',
					'mainmenu'=>'mant',
					'url'=>'/mant/inspectionbook/liste.php',
					'langs'=>'mant@mant',
					'position'=>100,
					'enabled'=>'$conf->mant->enabled',
					'perms'=>'$user->rights->mant->teacher->crear',
					'target'=>'',
					'user'=>0);

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=mant,fk_leftmenu=teacher',
					'type'=>'left',
					'titre'=>'View wcts',
					'mainmenu'=>'mant',
					'leftmenu'=>'mwcts',
					'url'=>'/mant/mwcts/liste.php',
					'langs'=>'mant@mant',
					'position'=>100,
					'enabled'=>'$conf->mant->enabled',
					'perms'=>'$user->rights->mant->teacher->leer',
					'target'=>'',
					'user'=>0);

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=mant,fk_leftmenu=mwcts',
					'type'=>'left',
					'titre'=>'Create wcts',
					'mainmenu'=>'mant',
					'url'=>'/mant/mwcts/liste.php?action=create',
					'langs'=>'mant@mant',
					'position'=>100,
					'enabled'=>'$conf->mant->enabled',
					'perms'=>'$user->rights->mant->teacher->crear',
					'target'=>'',
					'user'=>0);
		*/
		//TICKET
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=0',
					'type'=>'left',
					'titre'=>'Ticket work',
					'mainmenu'=>'mant',
					'leftmenu'=>'works',
					'url'=>'/mant/request/list.php',
					'langs'=>'mant@mant',
					'position'=>100,
					'enabled'=>'$conf->mant->enabled',
					'perms'=>'$user->rights->mant->tick->leer',
					'target'=>'',
					'user'=>2);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=mant,fk_leftmenu=works',
					'type'=>'left',
					'titre'=>'Create ticket work',
					'mainmenu'=>'mant',
					'url'=>'/mant/request/card.php?action=create',
					'langs'=>'mant@mant',
					'position'=>100,
					'enabled'=>'$conf->mant->enabled',
					'perms'=>'$user->rights->mant->tick->crear',
					'target'=>'',
					'user'=>2);

		// $r++;
		// $this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=mant,fk_leftmenu=works',
		// 			'type'=>'left',
		// 			'titre'=>'Create ticket work II',
		// 			'mainmenu'=>'mant',
		// 			'url'=>'/mant/request/ficheemail.php?action=create',
		// 			'langs'=>'mant@mant',
		// 			'position'=>100,
		// 			'enabled'=>'$conf->mant->enabled',
		// 			'perms'=>'$user->rights->mant->tick->crear',
		// 			'target'=>'_blank',
		// 			'user'=>0);

		//JOBS
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=0',
					'type'=>'left',
					'titre'=>'Jobs',
					'mainmenu'=>'mant',
					'leftmenu'=>'jobs',
					'url'=>'/mant/jobs/list.php',
					'langs'=>'mant@mant',
					'position'=>100,
					'enabled'=>'$conf->mant->enabled',
					'perms'=>'$user->rights->mant->jobs->leer',
					'target'=>'',
					'user'=>2);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=mant,fk_leftmenu=jobs',
					'type'=>'left',
					'titre'=>'Createjobs',
					'mainmenu'=>'mant',
					'url'=>'/mant/jobs/card.php?action=create',
					'langs'=>'mant@mant',
					'position'=>100,
					'enabled'=>'$conf->mant->enabled',
					'perms'=>'$user->rights->mant->jobs->crear',
					'target'=>'',
					'user'=>0);

		//PROGRAMMING
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=0',
					'type'=>'left',
					'titre'=>'Programming',
					'mainmenu'=>'mant',
					'leftmenu'=>'prog',
					'url'=>'/mant/program/liste.php',
					'langs'=>'mant@mant',
					'position'=>100,
					'enabled'=>'$conf->mant->enabled',
					'perms'=>'$user->rights->mant->prog->leer',
					'target'=>'',
					'user'=>2);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=mant,fk_leftmenu=prog',
					'type'=>'left',
					'titre'=>'Createprogramming',
					'mainmenu'=>'mant',
					'url'=>'/mant/program/fiche.php?action=create',
					'langs'=>'mant@mant',
					'position'=>100,
					'enabled'=>'$conf->mant->enabled',
					'perms'=>'$user->rights->mant->prog->crear',
					'target'=>'',
					'user'=>2);

		//REPORTS
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=0',
					'type'=>'left',
					'titre'=>'Reports',
					'mainmenu'=>'mant',
					'leftmenu'=>'rep',
					'url'=>'/mant/report/index.php',
					'langs'=>'mant@mant',
					'position'=>100,
					'enabled'=>'$conf->mant->enabled',
					'perms'=>'$user->rights->mant->rep->leer',
					'target'=>'',
					'user'=>2);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=mant,fk_leftmenu=rep',
					'type'=>'left',
					'titre'=>'Ordenes de trabajo',
					'mainmenu'=>'mant',
					'url'=>'/mant/report/fiche.php?action=create',
					'langs'=>'mant@mant',
					'position'=>100,
					'enabled'=>'$conf->mant->enabled',
					'perms'=>'$user->rights->mant->rep->leer',
					'target'=>'',
					'user'=>2);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=mant,fk_leftmenu=rep',
					'type'=>'left',
					'titre'=>'OT por Uso de recursos',
					'mainmenu'=>'mant',
					'url'=>'/mant/report/otresources.php?action=create',
					'langs'=>'mant@mant',
					'position'=>100,
					'enabled'=>'$conf->mant->enabled',
					'perms'=>'$user->rights->mant->rep->leer',
					'target'=>'',
					'user'=>2);
		/*
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=mant,fk_leftmenu=rep',
					'type'=>'left',
					'titre'=>'Resumen OT',
					'mainmenu'=>'mant',
					'url'=>'/mant/report/ficheres.php?action=create',
					'langs'=>'mant@mant',
					'position'=>100,
					'enabled'=>'$conf->mant->enabled',
					'perms'=>'$user->rights->mant->rep->leer',
					'target'=>'',
					'user'=>2);
		*/
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=mant,fk_leftmenu=rep',
					'type'=>'left',
					'titre'=>'OT por equipos',
					'mainmenu'=>'mant',
					'url'=>'/mant/report/equipmentjob.php',
					'langs'=>'mant@mant',
					'position'=>100,
					'enabled'=>'$conf->mant->enabled',
					'perms'=>'$user->rights->mant->rep->leer',
					'target'=>'',
					'user'=>2);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=mant,fk_leftmenu=rep',
					'type'=>'left',
					'titre'=>'Uso de recursos',
					'mainmenu'=>'mant',
					'url'=>'/mant/report/resourcejob.php',
					'langs'=>'mant@mant',
					'position'=>100,
					'enabled'=>'$conf->mant->enabled',
					'perms'=>'$user->rights->mant->rep->leer',
					'target'=>'',
					'user'=>2);


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

		$result=$this->_load_tables('/mant/sql/');

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

?>