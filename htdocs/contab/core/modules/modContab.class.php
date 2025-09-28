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
 * 	\defgroup   produccion     Module Contable
 *  \brief      Example of a module descriptor.
 *				Such a file must be copied into htdocs/mymodulet/core/modules directory.
 *  \file       htdocs/contab/core/modules/modcontab.class.php
 *  \ingroup    Contable
 *  \brief      Description and activation file for module Contable
 */
include_once(DOL_DOCUMENT_ROOT ."/core/modules/DolibarrModules.class.php");

/**
 *  Description and activation class for module Produccion
 */
class modContab extends DolibarrModules
{
	/**
	 *   Constructor. Define names, constants, directories, boxes, permissions
	 *
	 *   @param      DoliDB		$db      Database handler
	 */
	function modContab($db)
	{
		global $langs,$conf;

		$this->db = $db;

		// Id for module (must be unique).
		// Use here a free id (See in Home -> System information -> Dolibarr for list of used modules id).
		$this->numero = 525000;
		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'contab';

		// Family can be 'crm','financial','hr','projects','products','ecm','technic','other'
		// It is used to group modules in module setup page
		$this->family = "Financiero";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i','',get_class($this));
		// Module description, used if translation string 'ModuleXXXDesc' not found (where XXX is value of numeric property 'numero' of module)
		$this->description = "Contabilidad, Plan de cuentas, Periodos contables, Puntos Asiento, Asientos Standard, Asientos contables ";
		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = '5.0.3.1';
		// Key used in llx_const table to save module status enabled/disabled (where MYMODULE is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Where to store the module in setup page (0=common,1=interface,2=others,3=very specific)
		$this->special = 0;
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		$this->picto='contab';
		$this->picto='contab@contab';

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
		$this->module_parts = array('hooks' => array('socid','formAddCost','createForm'));

		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/produccion/temp");
		//$this->dirs = array("/contab/chartofaccount","/contab/pointentry","/contab/standardseat","/contab/period");

		// Config pages. Put here list of php page, stored into produccion/admin directory, to use to setup module.
		$this->config_page_url = array("contab.php@contab");

		// Dependencies
		$this->depends = array();		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array();	// List of modules id to disable if this one is disabled
		$this->phpmin = array(5,0);					// Minimum version of PHP required by module
		$this->need_dolibarr_version = array(3,0);	// Minimum version of Dolibarr required by module
		$this->langfiles = array("contab@contab");

		// Constants
		// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
		// Example: $this->const=array(0=>array('MYMODULE_MYNEWCONST1','chaine','myvalue','This is a constant to add',1),
		//                             1=>array('MYMODULE_MYNEWCONST2','chaine','myvalue','This is another constant to add',0)
		// );
		$this->const = array();
		/*
		$this->const =
		  array(0=>array('CONTAB_ACCOUNT_CAPITAL','chaine','3101','Cuenta Capital Social',1),
			1=>array('CONTAB_PAYABLES_UNDEFINED','chaine','2502','Cuentas por Pagar no definidos',1),
			2=>array('CONTAB_RECEIVABLES_UNDEFINED','chaine','1415','Cuentas por Cobrar no definidos',1),
			3=>array('CONTAB_ACCOUNT_SALARY','chaine','5105','Cuenta Sueldos y Salarios',1),
			4=>array('CONTAB_TSE_EGRESO','chaine','10','Codigo asiento egreso',1),
			5=>array('CONTAB_TSE_INGRESO','chaine','20','Codigo asiento ingreso',1),
			6=>array('CONTAB_TSE_TRASPASO','chaine','30','Codigo asiento traspaso',1),
			7=>array('CONTAB_TSE_TYPENUMERIC','chaine','1','Forma numeracion asientos 1=Anual; 2=Mensual',1)

			);
		*/
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

		// Dictionnaries
	//if (! isset($conf->almacen->enabled)) $conf->almacen->enabled=0;
			$this->dictionnaries=array();
	// $this->dictionnaries=array(
	// 			   'langs'=>'contab@contab',
	// 			   'tabname'=>array(MAIN_DB_PREFIX."cs_seatstype"),
	// 			   'tablib'=>array("Typeseats"),
	// 			   'tabsql'=>array('SELECT a.rowid as rowid, a.entity, a.code, a.label, a.ref FROM '.MAIN_DB_PREFIX.'cs_seatstype as a'),
	// 			   'tabsqlsort'=>array("code ASC"),
	// 			   'tabfield'=>array("entity,code,label,ref"),
	// 			   'tabfieldvalue'=>array("entity,code,label,ref"),
	// 			   'tabfieldinsert'=>array("entity,code,label,ref"),
	// 			   'tabrowid'=>array("rowid"),
	// 			   'tabcond'=>array($conf->contab->enabled)
	// 			   );

		/* Example:
		if (! isset($conf->produccion->enabled)) $conf->produccion->enabled=0;	// This is to avoid warnings
		$this->dictionnaries=array(
			'langs'=>'produccion@produccion',
			'tabname'=>array(MAIN_DB_PREFIX."table1",MAIN_DB_PREFIX."table2",MAIN_DB_PREFIX."table3"),		// List of tables we want to see into dictonnary editor
			'tablib'=>array("Table1","Table2","Table3"),													// Label of tables
			'tabsql'=>array('SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table1 as f','SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table2 as f','SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table3 as f'),	// Request to select fields
			'tabsqlsort'=>array("label ASC","label ASC","label ASC"),																					// Sort order
			'tabfield'=>array("code,label","code,label","code,label"),																					// List of fields (result of select to show dictionnary)
			'tabfieldvalue'=>array("code,label","code,label","code,label"),																				// List of fields (list of fields to edit a record)
			'tabfieldinsert'=>array("code,label","code,label","code,label"),																			// List of fields (list of fields for insert)
			'tabrowid'=>array("rowid","rowid","rowid"),																									// Name of columns with primary key (try to always name it 'rowid')
			'tabcond'=>array($conf->produccion->enabled,$conf->produccion->enabled,$conf->produccion->enabled)												// Condition to show each dictionnary
		);
		*/

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
		$this->boxes[$r][1] = "box_contab.php";
		$r++;

		// Permissions
		$this->rights = array();		// Permission array used by this module
		$r=0;
		//$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Contab';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'lire';

		$r++;
		//$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Read chart account';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'account';
		$this->rights[$r][5] = 'read';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Create account plan';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'account';
		$this->rights[$r][5] = 'write';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Delete account plan';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'account';
		$this->rights[$r][5] = 'del';

		$r+=10;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Read period';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'leerperiod';

		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Create period  ';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'crearperiod';

		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Delete period';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'delperiod';

		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Abrir/Cerrar periodo';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'valperiod';

		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Close period';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'closeperiod';

		$r+=10;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Leer Documentos PDF';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'pdf';
		$this->rights[$r][5] = 'write';

		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Eliminar Documentos PDF';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'pdf';
		$this->rights[$r][5] = 'del';

		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Crear Tipos de Cambio';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'tc';
		$this->rights[$r][5] = 'crear';

		$r+=10;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Read standard seat';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'leerseatst';

		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Create standard seat';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'crearseatst';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Delete standard seat';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'delseatst';
		$r+=10;

		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Readseatsmanual';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'seatma';
		$this->rights[$r][5] = 'read';

		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Createseatsmanual';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'seatma';
		$this->rights[$r][5] = 'write';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Deleteseatsmanual';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'seatma';
		$this->rights[$r][5] = 'del';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Validateseatsmanual';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'seatma';
		$this->rights[$r][5] = 'val';

		$r+=10;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Readseats';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'seat';
		$this->rights[$r][5] = 'read';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Seatcreate';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'seat';
		$this->rights[$r][5] = 'write';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Seatdelete';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'seat';
		$this->rights[$r][5] = 'del';

		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Seatvalidate';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'seat';
		$this->rights[$r][5] = 'val';


		$r+=10;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Ver Reportes';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'leerrep';

		$r+=10;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Cuentas de Gasto';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'leerspending';

		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Crear Cuentas de Gasto';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'crearspending';

		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Borrar Cuentas de Gasto';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'delspending';

		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Validar Cuentas de Gasto';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'valspending';

		$r+=10;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Tipo transacciones';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'trans';
		$this->rights[$r][5] = 'read';

		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Crear tipo transacciones';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'trans';
		$this->rights[$r][5] = 'write';

		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Borrar tipo transacciones';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'trans';
		$this->rights[$r][5] = 'del';

		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Validar tipo transacciones';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'trans';
		$this->rights[$r][5] = 'val';

		$r+=10;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Visiones';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'vision';
		$this->rights[$r][5] = 'read';

		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Crear Visiones';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'vision';
		$this->rights[$r][5] = 'write';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Modificar Visiones';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'vision';
		$this->rights[$r][5] = 'mod';

		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Borrar Visiones';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'vision';
		$this->rights[$r][5] = 'del';

		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Borrar Visiones';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'vision';
		$this->rights[$r][5] = 'val';

		$r+=10;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Ver Estados Financieros';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'ef';
		$this->rights[$r][5] = 'read';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Eliminar PDF Estados Financieros';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'ef';
		$this->rights[$r][5] = 'del';


		$r=10;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Upload files';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'upload';
		$this->rights[$r][5] = 'read';

		// Main menu entries
		$this->menus = array();			// List of menus to add
		$r=0;
		$this->menu[$r]=array(	'fk_menu'=>0,
			'type'=>'top',
			'titre'=>'Contab',
			'mainmenu'=>'contab',
			'leftmenu'=>'0',
			'url'=>'/contab/index.php',
			'langs'=>'contab@contab',
			'position'=>100,
			'enabled'=>$conf->contab->enabled,
			'perms'=>'$user->rights->contab->lire',
			'target'=>'',
			'user'=>0);
		$r++;

		// Example to declare a Left Menu entry:
		$this->menu[$r]=array(	'fk_menu'=>'r=0',
			'type'=>'left',
			'titre'=>'Contab',
			'mainmenu'=>'contab',
			'url'=>'/contab/index.php',
			'langs'=>'contab@contab',
			'position'=>100,
			'enabled'=>'$conf->contab->enabled',
			'perms'=>'$user->rights->contab->lire',
			'target'=>'',
			'user'=>0);
		/*
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=1',
					'type'=>'left',
					'titre'=>'Money',
					'mainmenu'=>'contab',
					'leftmenu'=>'currency',
					'url'=>'/contab/currency/liste.php',
					'langs'=>'contab@contab',
					'position'=>100,
					'enabled'=>'$conf->contab->enabled',
					'perms'=>'$user->rights->contab->currency->leer',
					'target'=>'',
					'user'=>0);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=contab,fk_leftmenu=currency',
					'type'=>'left',
					'titre'=>'Create money',
					'mainmenu'=>'contab',
					'url'=>'/contab/currency/fiche.php?action=create',
					'langs'=>'contab@contab',
					'position'=>100,
					'enabled'=>'$conf->contab->enabled',
					'perms'=>'$user->rights->contab->currency->crear',
					'target'=>'',
					'user'=>0);

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=1',
					'type'=>'left',
					'titre'=>'Exchangerate',
					'mainmenu'=>'contab',
					'leftmenu'=>'exchange',
					'url'=>'/contab/exchangerate/liste.php',
					'langs'=>'contab@contab',
					'position'=>100,
					'enabled'=>'$conf->contab->enabled',
					'perms'=>'$user->rights->contab->currency->leer',
					'target'=>'',
					'user'=>0);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=contab,fk_leftmenu=exchange',
					'type'=>'left',
					'titre'=>'Createexchangerate',
					'mainmenu'=>'contab',
					'url'=>'/contab/exchangerate/fiche.php?action=create',
					'langs'=>'contab@contab',
					'position'=>100,
					'enabled'=>'$conf->contab->enabled',
					'perms'=>'$user->rights->contab->currency->crear',
					'target'=>'',
					'user'=>0);
		*/
					$r++;
					$this->menu[$r]=array(	'fk_menu'=>'r=1',
						'type'=>'left',
						'titre'=>'Periods',
						'mainmenu'=>'contab',
						'leftmenu'=>'period',
						'url'=>'/contab/period/list.php',
						'langs'=>'contab@contab',
						'position'=>100,
						'enabled'=>'$conf->contab->enabled',
						'perms'=>'$user->rights->contab->leerperiod',
						'target'=>'',
						'user'=>0);
					$r++;
					$this->menu[$r]=array(	'fk_menu'=>'r=1',
						'type'=>'left',
						'titre'=>'Typetransaction',
						'mainmenu'=>'contab',
						'leftmenu'=>'trans',
						'url'=>'/contab/transaction/list.php',
						'langs'=>'contab@contab',
						'position'=>100,
						'enabled'=>'$conf->contab->enabled',
						'perms'=>'$user->rights->contab->trans->read',
						'target'=>'',
						'user'=>0);

		//$r++;
		//$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=contab,fk_leftmenu=period',
		//			'type'=>'left',
		//			'titre'=>'Create period',
		//			'mainmenu'=>'contab',
		//			'url'=>'/contab/period/card.php?action=create',
		//			'langs'=>'contab@contab',
		//			'position'=>100,
		//			'enabled'=>'$conf->contab->enabled',
		//			'perms'=>'$user->rights->contab->crearperiod',
		//			'target'=>'',
		//			'user'=>0);
					$r++;
					$this->menu[$r]=array(	'fk_menu'=>'r=1',
						'type'=>'left',
						'titre'=>'Modelsaccount',
						'mainmenu'=>'contab',
						'leftmenu'=>'chartmod',
						'url'=>'/contab/accounts/accountmodel.php?id=31',
						'langs'=>'contab@contab',
						'position'=>100,
						'enabled'=>'$conf->contab->enabled',
						'perms'=>'$user->rights->contab->account->read',
						'target'=>'',
						'user'=>0);
					$r++;
					$this->menu[$r]=array(	'fk_menu'=>'r=1',
						'type'=>'left',
						'titre'=>'Chartofaccounts',
						'mainmenu'=>'contab',
						'leftmenu'=>'chart',
						'url'=>'/contab/accounts/account.php',
						'langs'=>'contab@contab',
						'position'=>100,
						'enabled'=>'$conf->contab->enabled',
						'perms'=>'$user->rights->contab->account->read',
						'target'=>'',
						'user'=>0);
					$r++;
					$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=contab,fk_leftmenu=chart',
						'type'=>'left',
						'titre'=>'Createchartaccount',
						'mainmenu'=>'contab',
						'url'=>'/contab/accounts/card.php?action=create',
						'langs'=>'contab@contab',
						'position'=>100,
						'enabled'=>'$conf->contab->enabled',
						'perms'=>'$user->rights->contab->account->write',
						'target'=>'',
						'user'=>0);

					$r++;
					$this->menu[$r]=array(	'fk_menu'=>'r=1',
						'type'=>'left',
						'titre'=>'Defaultaccounts',
						'mainmenu'=>'contab',
						'leftmenu'=>'default',
						'url'=>'/contab/admin/defaultaccounts.php',
						'langs'=>'contab@contab',
						'position'=>100,
						'enabled'=>'$conf->contab->enabled',
						'perms'=>'$user->rights->contab->account->write',
						'target'=>'',
						'user'=>0);
					/*
					$r++;
					$this->menu[$r]=array(	'fk_menu'=>'r=1',
						'type'=>'left',
						'titre'=>'Spendingaccounts',
						'mainmenu'=>'contab',
						'leftmenu'=>'spending',
						'url'=>'/contab/spending/liste.php',
						'langs'=>'contab@contab',
						'position'=>100,
						'enabled'=>'$conf->contab->enabled',
						'perms'=>'$user->rights->contab->leerspending',
						'target'=>'',
						'user'=>0);

					$r++;
					$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=contab,fk_leftmenu=spending',
						'type'=>'left',
						'titre'=>'Createspendingaccounts',
						'mainmenu'=>'contab',
						'url'=>'/contab/spending/fiche.php?action=create',
						'langs'=>'contab@contab',
						'position'=>100,
						'enabled'=>'$conf->contab->enabled',
						'perms'=>'$user->rights->contab->crearspending',
						'target'=>'',
						'user'=>0);
					*/
					$r++;
					$this->menu[$r]=array(	'fk_menu'=>'r=1',
						'type'=>'left',
						'titre'=>'Vision',
						'mainmenu'=>'contab',
						'leftmenu'=>'vision',
						'url'=>'/contab/vision/list.php',
						'langs'=>'contab@contab',
						'position'=>100,
						'enabled'=>'$conf->contab->enabled',
						'perms'=>'$user->rights->contab->vision->read',
						'target'=>'',
						'user'=>0);
					$r++;
					$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=contab,fk_leftmenu=vision',
						'type'=>'left',
						'titre'=>'Createvision',
						'mainmenu'=>'contab',
						'url'=>'/contab/vision/fiche.php?action=create',
						'langs'=>'contab@contab',
						'position'=>100,
						'enabled'=>'$conf->contab->enabled',
						'perms'=>'$user->rights->contab->vision->write',
						'target'=>'',
						'user'=>0);

		// $r++;
		// $this->menu[$r]=array(	'fk_menu'=>'r=1',
		// 			'type'=>'left',
		// 			'titre'=>'Entry points',
		// 			'mainmenu'=>'contab',
		// 			'leftmenu'=>'points',
		// 			'url'=>'/contab/pointentry/liste.php',
		// 			'langs'=>'contab@contab',
		// 			'position'=>100,
		// 			'enabled'=>'$conf->contab->enabled',
		// 			'perms'=>'$user->rights->contab->leerpoint',
		// 			'target'=>'',
		// 			'user'=>0);
		// $r++;
		// $this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=contab,fk_leftmenu=points',
		// 			'type'=>'left',
		// 			'titre'=>'Create entry points',
		// 			'mainmenu'=>'contab',
		// 			'url'=>'/contab/pointentry/fiche.php?action=create',
		// 			'langs'=>'contab@contab',
		// 			'position'=>100,
		// 			'enabled'=>'$conf->contab->enabled',
		// 			'perms'=>'$user->rights->contab->crearpoint',
		// 			'target'=>'',
		// 			'user'=>0);
		// $r++;
		// $this->menu[$r]=array(	'fk_menu'=>'r=1',
		// 			'type'=>'left',
		// 			'titre'=>'Standard seat',
		// 			'mainmenu'=>'contab',
		// 			'leftmenu'=>'sseat',
		// 			'url'=>'/contab/standardseat/liste.php',
		// 			'langs'=>'contab@contab',
		// 			'position'=>100,
		// 			'enabled'=>'$conf->contab->enabled',
		// 			'perms'=>'$user->rights->contab->leerseatst',
		// 			'target'=>'',
		// 			'user'=>0);
		// $r++;
		// $this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=contab,fk_leftmenu=sseat',
		// 			'type'=>'left',
		// 			'titre'=>'Create standard seat',
		// 			'mainmenu'=>'contab',
		// 			'url'=>'/contab/standardseat/fiche.php?action=create',
		// 			'langs'=>'contab@contab',
		// 			'position'=>100,
		// 			'enabled'=>'$conf->contab->enabled',
		// 			'perms'=>'$user->rights->contab->crearseatst',
		// 			'target'=>'',
		// 			'user'=>0);

					$r++;
					$this->menu[$r]=array(	'fk_menu'=>'r=0',
						'type'=>'left',
						'titre'=>'Seats',
						'mainmenu'=>'contab',
						'leftmenu'=>'seats',
						'url'=>'/contab/seats/list.php',
						'langs'=>'contab@contab',
						'position'=>100,
						'enabled'=>'$conf->contab->enabled',
						'perms'=>'$user->rights->contab->seat->read',
						'target'=>'',
						'user'=>0);

					$r++;
					$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=contab,fk_leftmenu=seats',
						'type'=>'left',
						'titre'=>'Createseatmanual',
						'mainmenu'=>'contab',
						'url'=>'/contab/seats/fiche.php?action=create',
						'langs'=>'contab@contab',
						'position'=>100,
						'enabled'=>'$conf->contab->enabled',
						'perms'=>'$user->rights->contab->seatma->write',
						'target'=>'',
						'user'=>0);

					$r++;
					$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=contab,fk_leftmenu=seats',
						'type'=>'left',
						'titre'=>'Createseatssales',
						'mainmenu'=>'contab',
						'url'=>'/contab/seatssales/fiche.php?action=create',
						'langs'=>'contab@contab',
						'position'=>100,
						'enabled'=>'$conf->contab->enabled',
						'perms'=>'$user->rights->contab->seat->write',
						'target'=>'',
						'user'=>0);

					$r++;
					$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=contab,fk_leftmenu=seats',
						'type'=>'left',
						'titre'=>'Createseatspurchases',
						'mainmenu'=>'contab',
						'url'=>'/contab/seatspurchases/fiche.php?action=create',
						'langs'=>'contab@contab',
						'position'=>100,
						'enabled'=>'$conf->contab->enabled',
						'perms'=>'$user->rights->contab->seat->write',
						'target'=>'',
						'user'=>0);

					$r++;
					$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=contab,fk_leftmenu=seats',
						'type'=>'left',
						'titre'=>'Createseatsbank',
						'mainmenu'=>'contab',
						'url'=>'/contab/seatsbank/fiche.php?action=create',
						'langs'=>'contab@contab',
						'position'=>100,
						'enabled'=>'$conf->contab->enabled',
						'perms'=>'$user->rights->contab->seat->write',
						'target'=>'',
						'user'=>0);

					$r++;
					$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=contab,fk_leftmenu=seats',
						'type'=>'left',
						'titre'=>'Createsalaryseats',
						'mainmenu'=>'contab',
						'url'=>'/contab/seatssalary/fiche.php?action=create',
						'langs'=>'contab@contab',
						'position'=>100,
						'enabled'=>'$conf->contab->enabled',
						'perms'=>'$user->rights->contab->seat->write',
						'target'=>'',
						'user'=>0);

					$r++;
					$this->menu[$r]=array(	'fk_menu'=>'r=0',
						'type'=>'left',
						'titre'=>'Reports',
						'mainmenu'=>'contab',
						'leftmenu'=>'report',
						'url'=>'/contab/report/index.php',
						'langs'=>'contab@contab',
						'position'=>100,
						'enabled'=>'$conf->contab->enabled',
						'perms'=>'$user->rights->contab->leerrep',
						'target'=>'',
						'user'=>0);

					$r++;
					$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=contab,fk_leftmenu=report',
						'type'=>'left',
						'titre'=>'Financialstatements',
						'mainmenu'=>'contab',
						'leftmenu'=>'fs',
						'url'=>'/contab/report/fs.php',
						'langs'=>'contab@contab',
						'position'=>100,
						'enabled'=>'$conf->contab->enabled',
						'perms'=>'$user->rights->contab->leerrep',
						'target'=>'',
						'user'=>0);
					$r++;
					$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=contab,fk_leftmenu=fs',
						'type'=>'left',
						'titre'=>'Trialbal_amountsandbalances',
						'mainmenu'=>'contab',
						'url'=>'/contab/report/bc.php?action=create',
						'langs'=>'contab@contab',
						'position'=>100,
						'enabled'=>'$conf->contab->enabled',
						'perms'=>'$user->rights->contab->leerrep',
						'target'=>'',
						'user'=>0);
					$r++;
					$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=contab,fk_leftmenu=fs',
						'type'=>'left',
						'titre'=>'Incomestatement',
						'mainmenu'=>'contab',
						'url'=>'/contab/report/er.php?action=create&rep=er',
						'langs'=>'contab@contab',
						'position'=>100,
						'enabled'=>'$conf->contab->enabled',
						'perms'=>'$user->rights->contab->leerrep',
						'target'=>'',
						'user'=>0);
					$r++;
					$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=contab,fk_leftmenu=fs',
						'type'=>'left',
						'titre'=>'Generalbalancesheet',
						'mainmenu'=>'contab',
						'url'=>'/contab/report/er.php?action=create&rep=bg',
						'langs'=>'contab@contab',
						'position'=>100,
						'enabled'=>'$conf->contab->enabled',
						'perms'=>'$user->rights->contab->leerrep',
						'target'=>'',
						'user'=>0);

					$r++;
					$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=contab,fk_leftmenu=report',
						'type'=>'left',
						'titre'=>'Books',
						'mainmenu'=>'contab',
						'leftmenu'=>'book',
						'url'=>'/contab/report/book.php',
						'langs'=>'contab@contab',
						'position'=>100,
						'enabled'=>'$conf->contab->enabled',
						'perms'=>'$user->rights->contab->leerrep',
						'target'=>'',
						'user'=>0);
					$r++;
					$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=contab,fk_leftmenu=book',
						'type'=>'left',
						'titre'=>'Ledger',
						'mainmenu'=>'contab',
						'url'=>'/contab/report/ledger.php?action=create',
						'langs'=>'contab@contab',
						'position'=>100,
						'enabled'=>'$conf->contab->enabled',
						'perms'=>'$user->rights->contab->leerrep',
						'target'=>'',
						'user'=>0);

					$r++;
					$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=contab,fk_leftmenu=book',
						'type'=>'left',
						'titre'=>'Journal',
						'mainmenu'=>'contab',
						'url'=>'/contab/report/journal.php?action=create',
						'langs'=>'contab@contab',
						'position'=>100,
						'enabled'=>'$conf->contab->enabled',
						'perms'=>'$user->rights->contab->leerrep',
						'target'=>'',
						'user'=>0);
					$r++;
					$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=contab,fk_leftmenu=report',
						'type'=>'left',
						'titre'=>'Grossprofitability',
						'mainmenu'=>'contab',
						'leftmenu'=>'book',
						'url'=>'/contab/report/sales.php',
						'langs'=>'contab@contab',
						'position'=>100,
						'enabled'=>'$conf->contab->enabled',
						'perms'=>'$user->rights->contab->leerrep',
						'target'=>'',
						'user'=>0);
					$r++;
					$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=contab,fk_leftmenu=report',
						'type'=>'left',
						'titre'=>'Expensesbyarea',
						'mainmenu'=>'contab',
						'leftmenu'=>'book',
						'url'=>'/contab/report/salesarea.php',
						'langs'=>'contab@contab',
						'position'=>100,
						'enabled'=>'$conf->contab->enabled',
						'perms'=>'$user->rights->contab->leerrep',
						'target'=>'',
						'user'=>0);

					$r++;
					$this->menu[$r]=array(	'fk_menu'=>'r=0',
						'type'=>'left',
						'titre'=>'Uploadfile',
						'mainmenu'=>'contab',
						'leftmenu'=>'report',
						'url'=>'/contab/upload/card.php?action=create',
						'langs'=>'contab@contab',
						'position'=>100,
						'enabled'=>'$conf->contab->enabled',
						'perms'=>'$user->rights->contab->upload->read',
						'target'=>'',
						'user'=>0);


					// $r++;
		// $this->menu[$r]=array(	'fk_menu'=>'r=1',
		// 			'type'=>'left',
		// 			'titre'=>'Crear Nueva Solicitud',
		// 			'mainmenu'=>'almacen',
		// 			'url'=>'/almacen/fiche.php?action=create',
		// 			'langs'=>'almacen@almacen',
		// 			'position'=>100,
		// 			'enabled'=>'$conf->almacen->enabled',
		// 			'perms'=>'$user->rights->almacen->crearpedido',
		// 			'target'=>'',
		// 			'user'=>0);
		// $r++;
		// $this->menu[$r]=array(	'fk_menu'=>'r=1',
		// 			'type'=>'left',
		// 			'titre'=>'Locales',
		// 			'mainmenu'=>'almacen',
		// 			'leftmenu'=>'locales',
		// 			'url'=>'/almacen/local/liste.php',
		// 			'langs'=>'almacen@almacen',
		// 			'position'=>100,
		// 			'enabled'=>'$conf->almacen->enabled',
		// 			'perms'=>'$user->rights->almacen->leerlocal',
		// 			'target'=>'',
		// 			'user'=>2);
		// $r++;
		// $this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=almacen,fk_leftmenu=locales',
		// 			'type'=>'left',
		// 			'titre'=>'Listar Locales',
		// 			'mainmenu'=>'almacen',
		// 			'url'=>'/almacen/local/liste.php',
		// 			'langs'=>'almacen@almacen',
		// 			'position'=>100,
		// 			'enabled'=>'$conf->almacen->enabled',
		// 			'perms'=>'$user->rights->almacen->leerlocal',
		// 			'target'=>'',
		// 			'user'=>2);
		// $r++;
		// $this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=almacen,fk_leftmenu=locales',
		// 			'type'=>'left',
		// 			'titre'=>'Crear Nuevo Local',
		// 			'mainmenu'=>'almacen',
		// 			'url'=>'/almacen/local/fiche.php?action=create',
		// 			'langs'=>'almacen@almacen',
		// 			'position'=>100,
		// 			'enabled'=>'$conf->almacen->enabled',
		// 			'perms'=>'$user->rights->almacen->crearlocal',
		// 			'target'=>'',
		// 			'user'=>2);
		// $r++;
		// $this->menu[$r]=array(	'fk_menu'=>'r=1',
		// 			'type'=>'left',
		// 			'titre'=>'Materiales',
		// 			'mainmenu'=>'almacen',
		// 			'leftmenu'=>'listproduct',
		// 			'url'=>'/almacen/productlist/liste.php',
		// 			'langs'=>'almacen@almacen',
		// 			'position'=>100,
		// 			'enabled'=>'$conf->almacen->enabled',
		// 			'perms'=>'$user->rights->almacen->leerlistproduct',
		// 			'target'=>'',
		// 			'user'=>2);
		// $r++;
		// $this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=almacen,fk_leftmenu=listproduct',
		// 			'type'=>'left',
		// 			'titre'=>'Lista Materiales',
		// 			'mainmenu'=>'almacen',
		// 			'url'=>'/almacen/productlist/liste.php',
		// 			'langs'=>'almacen@almacen',
		// 			'position'=>100,
		// 			'enabled'=>'$conf->almacen->enabled',
		// 			'perms'=>'$user->rights->almacen->leerlistproduct',
		// 			'target'=>'',
		// 			'user'=>2);
		// $r++;
		// $this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=almacen,fk_leftmenu=listproduct',
		// 			'type'=>'left',
		// 			'titre'=>'Crear Lista Material',
		// 			'mainmenu'=>'almacen',
		// 			'url'=>'/almacen/productlist/fiche.php?action=create',
		// 			'langs'=>'almacen@almacen',
		// 			'position'=>100,
		// 			'enabled'=>'$conf->almacen->enabled',
		// 			'perms'=>'$user->rights->almacen->crearlistproduct',
		// 			'target'=>'',
		// 			'user'=>2);
		// $r++;
		// $this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=almacen,fk_leftmenu=listproduct',
		// 			'type'=>'left',
		// 			'titre'=>'Lista Productos Alternativos',
		// 			'mainmenu'=>'almacen',
		// 			'url'=>'/almacen/productalternative/liste.php',
		// 			'langs'=>'almacen@almacen',
		// 			'position'=>100,
		// 			'enabled'=>'$conf->almacen->enabled',
		// 			'perms'=>'$user->rights->almacen->leerlistproductalt',
		// 			'target'=>'',
		// 			'user'=>2);
		// $r++;
		// $this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=almacen,fk_leftmenu=listproduct',
		// 			'type'=>'left',
		// 			'titre'=>'Crear Producto Alternativo',
		// 			'mainmenu'=>'almacen',
		// 			'url'=>'/almacen/productalternative/fiche.php?action=create',
		// 			'langs'=>'almacen@almacen',
		// 			'position'=>100,
		// 			'enabled'=>'$conf->almacen->enabled',
		// 			'perms'=>'$user->rights->almacen->crearlistproductalt',
		// 			'target'=>'',
		// 			'user'=>2);

		// $r++;
		// $this->menu[$r]=array(	'fk_menu'=>'r=1',
		// 			'type'=>'left',
		// 			'titre'=>'Unidades',
		// 			'mainmenu'=>'almacen',
		// 			'leftmenu'=>'unidades',
		// 			'url'=>'/almacen/units/liste.php',
		// 			'langs'=>'almacen@almacen',
		// 			'position'=>100,
		// 			'enabled'=>'$conf->almacen->enabled',
		// 			'perms'=>'$user->rights->almacen->leerunidad',
		// 			'target'=>'',
		// 			'user'=>0);
		// $r++;
		// $this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=almacen,fk_leftmenu=unidades',
		// 			'type'=>'left',
		// 			'titre'=>'Listar Unidades',
		// 			'mainmenu'=>'almacen',
		// 			'url'=>'/almacen/units/liste.php',
		// 			'langs'=>'almacen@almacen',
		// 			'position'=>100,
		// 			'enabled'=>'$conf->almacen->enabled',
		// 			'perms'=>'$user->rights->almacen->leerunidad',
		// 			'target'=>'',
		// 			'user'=>2);
		// $r++;
		// $this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=almacen,fk_leftmenu=unidades',
		// 			'type'=>'left',
		// 			'titre'=>'Crear Nueva Unidad',
		// 			'mainmenu'=>'almacen',
		// 			'url'=>'/almacen/units/fiche.php?action=create',
		// 			'langs'=>'almacen@almacen',
		// 			'position'=>100,
		// 			'enabled'=>'$conf->almacen->enabled',
		// 			'perms'=>'$user->rights->almacen->crearunidad',
		// 			'target'=>'',
		// 			'user'=>2);
		// $r++;
		// $this->menu[$r]=array(	'fk_menu'=>'r=1',
		// 			'type'=>'left',
		// 			'titre'=>'Transfers',
		// 			'mainmenu'=>'almacen',
		// 			'leftmenu'=>'transferencia',
		// 			'url'=>'/almacen/transferencia/liste.php',
		// 			'langs'=>'almacen@almacen',
		// 			'position'=>100,
		// 			'enabled'=>'$conf->almacen->enabled',
		// 			'perms'=>'$user->rights->almacen->leertransferencia',
		// 			'target'=>'',
		// 			'user'=>2);
		// $r++;
		// $this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=almacen,fk_leftmenu=transferencia',
		// 			'type'=>'left',
		// 			'titre'=>'CreateNewTransfer',
		// 			'mainmenu'=>'almacen',
		// 			'url'=>'/almacen/transferencia/fiche.php?action=create',
		// 			'langs'=>'almacen@almacen',
		// 			'position'=>100,
		// 			'enabled'=>'$conf->almacen->enabled',
		// 			'perms'=>'$user->rights->almacen->creartransferencia',
		// 			'target'=>'',
		// 			'user'=>2);
		// $r++;
		// $this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=almacen,fk_leftmenu=transferencia',
		// 			'type'=>'left',
		// 			'titre'=>'CreateNewMovementEntry',
		// 			'mainmenu'=>'almacen',
		// 			'url'=>'/almacen/transferencia/entry.php?action=create',
		// 			'langs'=>'almacen@almacen',
		// 			'position'=>100,
		// 			'enabled'=>'$conf->almacen->enabled',
		// 			'perms'=>'$user->rights->almacen->creartransferencia',
		// 			'target'=>'',
		// 			'user'=>2);
		// $r++;
		// $this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=almacen,fk_leftmenu=transferencia',
		// 			'type'=>'left',
		// 			'titre'=>'CreateNewMovementOut',
		// 			'mainmenu'=>'almacen',
		// 			'url'=>'/almacen/transferencia/out.php?action=create',
		// 			'langs'=>'almacen@almacen',
		// 			'position'=>100,
		// 			'enabled'=>'$conf->almacen->enabled',
		// 			'perms'=>'$user->rights->almacen->creartransferencia',
		// 			'target'=>'',
		// 			'user'=>2);

		// $r++;
		// $this->menu[$r]=array(	'fk_menu'=>'r=1',
		// 			'type'=>'left',
		// 			'titre'=>'Inventarios',
		// 			'mainmenu'=>'almacen',
		// 			'leftmenu'=>'inventario',
		// 			'url'=>'/almacen/inventario/inventario.php',
		// 			'langs'=>'almacen@almacen',
		// 			'position'=>100,
		// 			'enabled'=>'$conf->almacen->enabled',
		// 			'perms'=>'$user->rights->almacen->crearunidad',
		// 			'target'=>'',
		// 			'user'=>2);
		// $r++;
		// $this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=almacen,fk_leftmenu=inventario',
		// 			'type'=>'left',
		// 			'titre'=>'Kardex',
		// 			'mainmenu'=>'almacen',
		// 			'url'=>'/almacen/inventario/kardex.php',
		// 			'langs'=>'almacen@almacen',
		// 			'position'=>100,
		// 			'enabled'=>'$conf->almacen->enabled',
		// 			'perms'=>'$user->rights->almacen->crearunidad',
		// 			'target'=>'',
		// 			'user'=>2);
					$r++;





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
		return $this->_load_tables('/contab/sql/');
	}
}

?>
