<?php
/* Copyright (C) 2003      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis@dolibarr.fr>
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
 * 	\defgroup   produccion     Module Almacen
 *  \brief      Example of a module descriptor.
 *				Such a file must be copied into htdocs/mymodulet/core/modules directory.
 *  \file       htdocs/almacen/core/modules/modAlmacen.class.php
 *  \ingroup    amacen
 *  \brief      Description and activation file for module Almacen
 */
include_once(DOL_DOCUMENT_ROOT ."/core/modules/DolibarrModules.class.php");


/**
 *  Description and activation class for module Produccion
 */
class modAlmacen extends DolibarrModules
{
	/**
	 *   Constructor. Define names, constants, directories, boxes, permissions
	 *
	 *   @param      DoliDB		$db      Database handler
	 */
	function modAlmacen($db)
	{
		global $langs,$conf;

		$this->db = $db;

		// Id for module (must be unique).
		// Use here a free id (See in Home -> System information -> Dolibarr for list of used modules id).
		$this->numero = 521300;
		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'almacen';

		// Family can be 'crm','financial','hr','projects','products','ecm','technic','other'
		// It is used to group modules in module setup page
		$this->family = "products";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i','',get_class($this));
		// Module description, used if translation string 'ModuleXXXDesc' not found (where XXX is value of numeric property 'numero' of module)
		$this->description = "Almacenes, manejo de Locales, Relacion de Locales, Pedidos a Almacenes, Unidades de medida";
		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = '2.2.1';
		//modificación al 20180201
		// Key used in llx_const table to save module status enabled/disabled (where MYMODULE is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Where to store the module in setup page (0=common,1=interface,2=others,3=very specific)
		$this->special = 0;
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		$this->picto='almacen';
		$this->picto='almacen@almacen';

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
		$this->module_parts = array('triggers'=>1);

		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/produccion/temp");
		$this->dirs = array("/almacen/units","/almacen/local");

		// Config pages. Put here list of php page, stored into produccion/admin directory, to use to setup module.
		$this->config_page_url = array("almacen.php@almacen");

		// Dependencies
		$this->depends = array();		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array();	// List of modules id to disable if this one is disabled
		$this->phpmin = array(5,0);					// Minimum version of PHP required by module
		$this->need_dolibarr_version = array(3,0);	// Minimum version of Dolibarr required by module
		$this->langfiles = array("almacen@almacen");

		// Constants
		// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
		// Example: $this->const=array(0=>array('MYMODULE_MYNEWCONST1','chaine','myvalue','This is a constant to add',1),
		//                             1=>array('MYMODULE_MYNEWCONST2','chaine','myvalue','This is another constant to add',0)
		// );
		$this->const = array();

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
		$this->tabs = array(
			'stock:+Permission:Permission:almacen@almacen:$user->rights->almacen->entr->crear:/almacen/permission/permission.php?id=__ID__',
			'product:-stock',
			'product:+stock:Stock:productext@productext:$user->rights->almacen->stock->read:/product/stock/product.php?id=__ID__',
			'product:+Peps:StockPeps:productext@productext:$user->rights->almacen->stockpeps->read:/almacen/stock/productpeps.php?id=__ID__',
			);

		// Dictionnaries
	//if (! isset($conf->almacen->enabled)) $conf->almacen->enabled=0;
		/*
		$this->dictionnaries=array('langs'=>'almacen@almacen',
			'tabname'=>array(MAIN_DB_PREFIX.'c_type_mouvement',MAIN_DB_PREFIX.'c_type_entrepot'),
			'tablib'=>array('Typemouvement','Typeentrepot'),
			'tabsql'=>array('SELECT a.rowid as rowid, a.code, a.label, a.type, a.active FROM '.MAIN_DB_PREFIX.'c_type_mouvement AS a','SELECT a.rowid as rowid, a.code, a.label, a.active FROM '.MAIN_DB_PREFIX.'c_type_entrepot AS a'),
			'tabsqlsort'=>array('label ASC','label ASC'),
			'tabfield'=>array('code,label','code,label',),
			'tabfieldvalue'=>array('code,label,active','code,label,active'),
			'tabfieldinsert'=>array('code,label','code,label'),
			'tabrowid'=>array('rowid','rowid'),
			'tabcond'=>array($conf->almacen->enabled,$conf->almacen->enabled),
			);
			*/
		$this->dictionnaries=array('langs'=>'almacen@almacen',
			'tabname'=>array(MAIN_DB_PREFIX.'c_type_entrepot'),
			'tablib'=>array('Typeentrepot'),
			'tabsql'=>array('SELECT a.rowid as rowid, a.code, a.label, a.active FROM '.MAIN_DB_PREFIX.'c_type_entrepot AS a'),
			'tabsqlsort'=>array('label ASC'),
			'tabfield'=>array('code,label',),
			'tabfieldvalue'=>array('code,label,active'),
			'tabfieldinsert'=>array('code,label'),
			'tabrowid'=>array('rowid'),
			'tabcond'=>array($conf->almacen->enabled),
			);
		//$this->dictionnaries = array();
	// $this->dictionnaries=array(
	// 			   'langs'=>'almacen@almacen',
	// 			   'tabname'=>array(MAIN_DB_PREFIX."sol_almacen",MAIN_DB_PREFIX."sol_almacendet"),		// List of tables we want to see into dictonnary editor
	// 			   'tablib'=>array("almacen"),													// Label of tables
	// 			   'tabsql'=>array('SELECT a.rowid as rowid, a.ref, a.description, a.statut FROM '.MAIN_DB_PREFIX.'sol_almacen as a'),	// Request to select fields
	// 			   'tabsqlsort'=>array("ref ASC"),																					// Sort order
	// 			   'tabfield'=>array("ref,description"),																					// List of fields (result of select to show dictionnary)
	// 			   'tabfieldvalue'=>array("ref,description"),																				// List of fields (list of fields to edit a record)
	// 			   'tabfieldinsert'=>array("ref,sol_almacen"),																			// List of fields (list of fields for insert)
	// 			   'tabrowid'=>array("rowid"),																									// Name of columns with primary key (try to always name it 'rowid')
	// 			   'tabcond'=>array($conf->almacen->enabled)												// Condition to show each dictionnary
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
		$this->boxes[$r]['file']='box_alertmin.php@almacen';
		$this->boxes[$r]['note']='Productalertmin';


		// Permissions
		$this->rights = array();		// Permission array used by this module
		$r=0;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Almacenes';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'lirealm';

		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Leer Pedidos Almacen';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'pedido';
		$this->rights[$r][5] = 'read';

		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Crear Pedidos Almacen';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'pedido';
		$this->rights[$r][5] = 'write';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Validar Pedidos Almacen';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'pedido';
		$this->rights[$r][5] = 'val';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Aprobar Pedidos Almacen';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'pedido';
		$this->rights[$r][5] = 'app';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Aprobar todos los Pedidos Almacen';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'pedido';
		$this->rights[$r][5] = 'appall';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Rechazar Pedidos Almacen';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'pedido';
		$this->rights[$r][5] = 'rech';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Entregar Pedidos Almacen';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'pedido';
		$this->rights[$r][5] = 'ent';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Anular Pedidos Almacen';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'pedido';
		$this->rights[$r][5] = 'nul';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Eliminar Pedidos Almacen';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'pedido';
		$this->rights[$r][5] = 'del';

		$r+=8;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Crear documentos PDF Almacen';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'creardoc';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Borrar Documentos PDF Almacen';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'deldoc';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Descargar Documentos PDF Almacen';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'read';

		$r+=10;
		//$this->rights[$r][0] = $this->numero+$r;
		//$this->rights[$r][1] = 'Crear Entregas de Almacenes';
		//$this->rights[$r][2] = 'a';
		//$this->rights[$r][3] = 0;
		//$this->rights[$r][4] = 'crearentrega';

		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Leer Entregas de Almacenes';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'leerentrega';
		$r++;
		/*$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Entregar pedidos de Almacenes  ';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'entregaped';*/
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Borrar Entregas de Almacenes  ';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'supprimer';


		$r+=10;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Leer Locales';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'local';
		$this->rights[$r][5] = 'read';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Crear Locales';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'local';
		$this->rights[$r][5] = 'write';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Borrar Locales';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'local';
		$this->rights[$r][5] = 'del';


		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Leer Relacion Almacenes';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'leerlocal';

		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Crear Nueva Relacion Almacen';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'crearlocal';

		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Leer Traspasos entre Almacenes';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'leertransf';

		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Crear Traspasos entre Almacenes';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'transf';
		$this->rights[$r][5] = 'write';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Aprobar Traspasos entre almacenes';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'transf';
		$this->rights[$r][5] = 'app';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Crear Movimientos de salida';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'transfout';
		$this->rights[$r][5] = 'write';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Aprobar Movimientos de salida';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'transfout';
		$this->rights[$r][5] = 'app';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Crear Movimientos de entrada';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'transfin';
		$this->rights[$r][5] = 'write';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Aprobar Movimientos de entrada';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'transfin';
		$this->rights[$r][5] = 'app';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Modificar Movimientos de entrada';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'transfin';
		$this->rights[$r][5] = 'mod';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Eliminar traspasos o movimientos';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'transf';
		$this->rights[$r][5] = 'del';

		//$r++;
		//$this->rights[$r][0] = $this->numero+$r;
		//$this->rights[$r][1] = 'Crear Traspasos modificando fechas';
		//$this->rights[$r][2] = 'a';
		//$this->rights[$r][3] = 0;
		//$this->rights[$r][4] = 'transf';
		//$this->rights[$r][5] = 'datem';


		$r+=5;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Ver Consultas';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'inv';
		$this->rights[$r][5] = 'read';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Ver Kardex';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'inv';
		$this->rights[$r][5] = 'kard';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Ver Kardex valorado';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'inv';
		$this->rights[$r][5] = 'kardv';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Ver Inventarios';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'inv';
		$this->rights[$r][5] = 'inv';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Ver Inventario valorado';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'inv';
		$this->rights[$r][5] = 'invv';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Leer Movimientos de Almacen';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'inv';
		$this->rights[$r][5] = 'viewmov';

		$r+=9;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Usar Productos para venta';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'leersell';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Usar Productos para compra';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'leernosell';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Ver precios de productos';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'leerprice';

		$r+=10;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Asignar Usuarios a almacen';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'entr';
		$this->rights[$r][5] = 'crear';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Modificar Asignación Usuarios a almacen';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'entr';
		$this->rights[$r][5] = 'mod';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Borrar Usuarios de almacen';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'entr';
		$this->rights[$r][5] = 'del';

		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Descarga documentos';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'download';


		$r+=9;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Leer tipos de movimiento';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'type';
		$this->rights[$r][5] = 'lire';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Crear/Modificar tipos de movimiento';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'type';
		$this->rights[$r][5] = 'write';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Eliminar tipos de movimiento ';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'type';
		$this->rights[$r][5] = 'del';

		$r+=20;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Leer periodos';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'per';
		$this->rights[$r][5] = 'lire';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Crear/Modificar periodos';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'per';
		$this->rights[$r][5] = 'write';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Abrir/Cerrar periodo contable';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'per';
		$this->rights[$r][5] = 'act';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Cerrar periodo almacen';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'per';
		$this->rights[$r][5] = 'actal';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Eliminar periodos';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'per';
		$this->rights[$r][5] = 'del';

		$r+=10;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Reports';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'rep';
		$this->rights[$r][5] = 'read';

		$r+=10;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Export/Import';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'expimp';
		$this->rights[$r][5] = 'read';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Import wizard';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'expimp';
		$this->rights[$r][5] = 'imp';

		$r+=9;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Change gestion';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'gest';
		$this->rights[$r][5] = 'write';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Closeperiod';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'close';
		$this->rights[$r][5] = 'write';

		$r+=5;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Viewstockpeps';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'stockpeps';
		$this->rights[$r][5] = 'read';
		$r+=5;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Viewstockppp';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'stock';
		$this->rights[$r][5] = 'read';
		$r+=5;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Programationtransfer';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'program';
		$this->rights[$r][5] = 'read';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Createprogramationtransfer';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'program';
		$this->rights[$r][5] = 'write';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Validateprogramationtransfer';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'program';
		$this->rights[$r][5] = 'val';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Deleteprogramationtransfer';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'program';
		$this->rights[$r][5] = 'del';
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Generatetransfer';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'program';
		$this->rights[$r][5] = 'gen';

		/*
		$r++;
		$this->rights[$r][0] = $this->numero+$r;
		$this->rights[$r][1] = 'Export wizard';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'expimp';
		$this->rights[$r][5] = 'exp';
		*/
		// $r++;
		// $this->rights[$r][0] = 521370;
		// $this->rights[$r][1] = 'Leer Transferencias ';
		// //$this->rights[$r][2] = 'a';
		// $this->rights[$r][3] = 0;
		// $this->rights[$r][4] = 'leertransfer';
		// $r++;
		// $this->rights[$r][0] = 521371;
		// $this->rights[$r][1] = 'Crear Transferencias ';
		// //$this->rights[$r][2] = 'a';
		// $this->rights[$r][3] = 0;
		// $this->rights[$r][4] = 'creartransfer';


		// Add here list of permission defined by an id, a label, a boolean and two constant strings.
		// Example:
		// $this->rights[$r][0] = 2000; 				// Permission id (must not be already used)
		// $this->rights[$r][1] = 'Permision label';	// Permission label
		// $this->rights[$r][3] = 0; 					// Permission by default for new user (0/1)
		// $this->rights[$r][4] = 'level1';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		// $this->rights[$r][5] = 'level2';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		// $r++;


		// Main menu entries
		$this->menus = array();			// List of menus to add
		$r=0;
		$this->menu[$r]=array(	'fk_menu'=>0,
			'type'=>'top',
			'titre'=>'Almacenes',
			'mainmenu'=>'almacen',
			'leftmenu'=>'0',
			'url'=>'/almacen/index.php',
			'langs'=>'almacen@almacen',
			'position'=>100,
			'enabled'=>$conf->almacen->enabled,
			'perms'=>'$user->rights->almacen->lirealm',
			'target'=>'',
			'user'=>0);
		$r++;

		// // Example to declare a Left Menu entry:
		// $this->menu[$r]=array(	'fk_menu'=>'r=0',
		// 			'type'=>'left',
		// 			'titre'=>'Teachers',
		// 			'mainmenu'=>'almacen',
		// 			'leftmenu'=>'maestro',
		// 			'url'=>'/almacen/liste.php',
		// 			'langs'=>'almacen@almacen',
		// 			'position'=>100,
		// 			'enabled'=>'$conf->almacen->enabled',
		// 			'perms'=>'$user->rights->almacen->leerlocal',
		// 			'target'=>'',
		// 			'user'=>0);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=0',
			'type'=>'left',
			'titre'=>'Typemouvement',
			'mainmenu'=>'almacen',
			'leftmenu'=>'type',
			'url'=>'/almacen/typemouvement/list.php',
			'langs'=>'almacen@almacen',
			'position'=>100,
			'enabled'=>'$conf->almacen->enabled',
			'perms'=>'$user->rights->almacen->type->lire',
			'target'=>'',
			'user'=>0);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=0',
			'type'=>'left',
			'titre'=>'Periods',
			'mainmenu'=>'almacen',
			'leftmenu'=>'per',
			'url'=>'/almacen/period/list.php',
			'langs'=>'almacen@almacen',
			'position'=>100,
			'enabled'=>'$conf->almacen->enabled',
			'perms'=>'$user->rights->almacen->per->lire',
			'target'=>'',
			'user'=>0);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=0',
			'type'=>'left',
			'titre'=>'Warehouses',
			'mainmenu'=>'almacen',
			'leftmenu'=>'locales',
			'url'=>'/almacen/local/liste.php',
			'langs'=>'almacen@almacen',
			'position'=>100,
			'enabled'=>'$conf->almacen->enabled',
			'perms'=>'$user->rights->almacen->local->read',
			'target'=>'',
			'user'=>0);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=almacen,fk_leftmenu=locales',
			'type'=>'left',
			'titre'=>'Createwarehouse',
			'mainmenu'=>'almacen',
			'url'=>'/almacen/local/fiche.php?action=create',
			'langs'=>'almacen@almacen',
			'position'=>100,
			'enabled'=>'$conf->almacen->enabled',
			'perms'=>'$user->rights->almacen->local->write',
			'target'=>'',
			'user'=>0);

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
		// $this->menu[$r]=array(	'fk_menu'=>'r=0',
		// 			'type'=>'left',
		// 			'titre'=>'Movements',
		// 			'mainmenu'=>'almacen',
		// 			'leftmenu'=>'mouv',
		// 			'url'=>'/almacen/index.php',
		// 			'langs'=>'almacen@almacen',
		// 			'position'=>100,
		// 			'enabled'=>'$conf->almacen->enabled',
		// 			'perms'=>'$user->rights->almacen->leerpedido',
		// 			'target'=>'',
		// 			'user'=>0);

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=0',
			'type'=>'left',
			'titre'=>'Warehouseorders',
			'mainmenu'=>'almacen',
			'leftmenu'=> 'solicitud',
			'url'=>'/almacen/liste.php',
			'langs'=>'almacen@almacen',
			'position'=>100,
			'enabled'=>'$conf->almacen->enabled',
			'perms'=>'$user->rights->almacen->pedido->read',
			'target'=>'',
			'user'=>0);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=almacen,fk_leftmenu=solicitud',
			'type'=>'left',
			'titre'=>'Neworder',
			'mainmenu'=>'almacen',
			'url'=>'/almacen/fiche.php?action=create',
			'langs'=>'almacen@almacen',
			'position'=>100,
			'enabled'=>'$conf->almacen->enabled',
			'perms'=>'$user->rights->almacen->pedido->write',
			'target'=>'',
			'user'=>0);

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=0',
			'type'=>'left',
			'titre'=>'Transferencias',
			'mainmenu'=>'almacen',
			'leftmenu'=>'trans',
			'url'=>'/almacen/transferencia/liste.php?search_statut='.($user->rights->almacen->pedido->ent?6:1),
			'langs'=>'almacen@almacen',
			'position'=>100,
			'enabled'=>'$conf->almacen->enabled',
			'perms'=>'$user->rights->almacen->leertransf',
			'target'=>'',
			'user'=>0);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=almacen,fk_leftmenu=trans',
			'type'=>'left',
			'titre'=>'CreateNewTransfer',
			'mainmenu'=>'almacen',
			'url'=>'/almacen/transferencia/fiche.php?action=create',
			'langs'=>'almacen@almacen',
			'position'=>100,
			'enabled'=>'$conf->almacen->enabled',
			'perms'=>'$user->rights->almacen->transf->write',
			'target'=>'',
			'user'=>0);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=almacen,fk_leftmenu=trans',
			'type'=>'left',
			'titre'=>'CreateNewMovementEntry',
			'mainmenu'=>'almacen',
			'url'=>'/almacen/transferencia/entry.php?action=create',
			'langs'=>'almacen@almacen',
			'position'=>100,
			'enabled'=>'$conf->almacen->enabled',
			'perms'=>'$user->rights->almacen->transfin->write',
			'target'=>'',
			'user'=>0);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=almacen,fk_leftmenu=trans',
			'type'=>'left',
			'titre'=>'CreateNewMovementOut',
			'mainmenu'=>'almacen',
			'url'=>'/almacen/transferencia/out.php?action=create',
			'langs'=>'almacen@almacen',
			'position'=>100,
			'enabled'=>'$conf->almacen->enabled',
			'perms'=>'$user->rights->almacen->transfout->write',
			'target'=>'',
			'user'=>0);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=almacen,fk_leftmenu=trans',
			'type'=>'left',
			'titre'=>'Programationtransfer',
			'mainmenu'=>'almacen',
			'leftmenu'=>'transprog',
			'url'=>'/almacen/program/list.php',
			'langs'=>'almacen@almacen',
			'position'=>100,
			'enabled'=>'$conf->almacen->enabled',
			'perms'=>'$user->rights->almacen->program->read',
			'target'=>'',
			'user'=>0);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=almacen,fk_leftmenu=transprog',
			'type'=>'left',
			'titre'=>'Uploadprogramationtransfer',
			'mainmenu'=>'almacen',
			'leftmenu'=>'transprog',
			'url'=>'/almacen/program/upload.php?action=create',
			'langs'=>'almacen@almacen',
			'position'=>100,
			'enabled'=>'$conf->almacen->enabled',
			'perms'=>'$user->rights->almacen->program->write',
			'target'=>'',
			'user'=>0);

		$r++;
		//report
		$this->menu[$r]=array(	'fk_menu'=>'r=0',
			'type'=>'left',
			'titre'=>'Reports',
			'mainmenu'=>'almacen',
			'leftmenu'=>'rep',
			'url'=>'/almacen/index.php',
			'langs'=>'almacen@almacen',
			'position'=>100,
			'enabled'=>'$conf->almacen->enabled',
			'perms'=>'$user->rights->almacen->rep->read',
			'target'=>'',
			'user'=>0);

		/*
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'r=0',
			'type'=>'left',
			'titre'=>'Consultations',
			'mainmenu'=>'almacen',
			'leftmenu'=>'consulta',
			'url'=>'/almacen/inventario/index.php',
			'langs'=>'almacen@almacen',
			'position'=>100,
			'enabled'=>'$conf->almacen->enabled',
			'perms'=>'$user->rights->almacen->inv->read',
			'target'=>'',
			'user'=>0);
		*/

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=almacen,fk_leftmenu=rep',
			'type'=>'left',
			'titre'=>'Physicalinventory',
			'mainmenu'=>'almacen',
			'url'=>'/almacen/inventario/inventario.php',
			'langs'=>'almacen@almacen',
			'position'=>100,
			'enabled'=>'$conf->almacen->enabled',
			'perms'=>'$user->rights->almacen->inv->inv',
			'target'=>'',
			'user'=>0);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=almacen,fk_leftmenu=rep',
			'type'=>'left',
			'titre'=>'Valuedphysicalinventory',
			'mainmenu'=>'almacen',
			'url'=>'/almacen/inventario/inventario.php?yesnoprice=1',
			'langs'=>'almacen@almacen',
			'position'=>100,
			'enabled'=>'$conf->almacen->enabled',
			'perms'=>'$user->rights->almacen->inv->invv',
			'target'=>'',
			'user'=>0);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=almacen,fk_leftmenu=rep',
			'type'=>'left',
			'titre'=>'ValuedinventoryUFV',
			'mainmenu'=>'almacen',
			'url'=>'/almacen/inventario/inventarioufv.php',
			'langs'=>'almacen@almacen',
			'position'=>100,
			'enabled'=>'$conf->almacen->enabled',
			'perms'=>'$user->rights->almacen->inv->invv',
			'target'=>'',
			'user'=>0);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=almacen,fk_leftmenu=rep',
			'type'=>'left',
			'titre'=>'Physicalkardex',
			'mainmenu'=>'almacen',
			'url'=>'/almacen/inventario/kardex.php',
			'langs'=>'almacen@almacen',
			'position'=>100,
			'enabled'=>'$conf->almacen->enabled',
			'perms'=>'$user->rights->almacen->inv->kard',
			'target'=>'',
			'user'=>0);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=almacen,fk_leftmenu=rep',
			'type'=>'left',
			'titre'=>'Valuedphysicalkardex',
			'mainmenu'=>'almacen',
			'url'=>'/almacen/inventario/kardex.php?yesnoprice=1',
			'langs'=>'almacen@almacen',
			'position'=>100,
			'enabled'=>'$conf->almacen->enabled',
			'perms'=>'$user->rights->almacen->inv->kardv',
			'target'=>'',
			'user'=>0);

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=almacen,fk_leftmenu=rep',
			'type'=>'left',
			'titre'=>'Movimientos',
			'mainmenu'=>'almacen',
			'url'=>'/almacen/mouvement.php',
			'langs'=>'almacen@almacen',
			'position'=>100,
			'enabled'=>'$conf->almacen->enabled',
			'perms'=>'$user->rights->almacen->inv->viewmov',
			'target'=>'',
			'user'=>0);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=almacen,fk_leftmenu=rep',
			'type'=>'left',
			'titre'=>'OrderProcessingTimes',
			'mainmenu'=>'almacen',
			'url'=>'/almacen/report/timeprocess.php',
			'langs'=>'almacen@almacen',
			'position'=>100,
			'enabled'=>'$conf->almacen->enabled',
			'perms'=>'$user->rights->almacen->rep->read',
			'target'=>'',
			'user'=>0);
		/*
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=almacen,fk_leftmenu=rep',
			'type'=>'left',
			'titre'=>'Current balances',
			'mainmenu'=>'almacen',
			'url'=>'/almacen/report/balanceentrepot.php',
			'langs'=>'almacen@almacen',
			'position'=>100,
			'enabled'=>'$conf->almacen->enabled',
			'perms'=>'$user->rights->almacen->rep->read',
			'target'=>'',
			'user'=>0);
			*/

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=almacen,fk_leftmenu=rep',
			'type'=>'left',
			'titre'=>'Currentbalances',
			'mainmenu'=>'almacen',
			'url'=>'/almacen/report/balanceentrepot.php',
			'langs'=>'almacen@almacen',
			'position'=>100,
			'enabled'=>'$conf->almacen->enabled',
			'perms'=>'$user->rights->almacen->rep->read',
			'target'=>'',
			'user'=>0);

		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=almacen,fk_leftmenu=rep',
			'type'=>'left',
			'titre'=>'Balancemin',
			'mainmenu'=>'almacen',
			'url'=>'/almacen/report/balancemin.php',
			'langs'=>'almacen@almacen',
			'position'=>100,
			'enabled'=>'$conf->almacen->enabled',
			'perms'=>'$user->rights->almacen->rep->read',
			'target'=>'',
			'user'=>0);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=almacen,fk_leftmenu=rep',
			'type'=>'left',
			'titre'=>'Balancemaxmin',
			'mainmenu'=>'almacen',
			'url'=>'/almacen/report/balancemaxmin.php',
			'langs'=>'almacen@almacen',
			'position'=>100,
			'enabled'=>'$conf->almacen->enabled',
			'perms'=>'$user->rights->almacen->rep->read',
			'target'=>'',
			'user'=>0);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=almacen,fk_leftmenu=rep',
			'type'=>'left',
			'titre'=>'Rotation',
			'mainmenu'=>'almacen',
			'url'=>'/almacen/report/rotation.php',
			'langs'=>'almacen@almacen',
			'position'=>100,
			'enabled'=>'$conf->almacen->enabled',
			'perms'=>'$user->rights->almacen->rep->read',
			'target'=>'',
			'user'=>0);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=almacen,fk_leftmenu=rep',
			'type'=>'left',
			'titre'=>'Closedperiods',
			'mainmenu'=>'almacen',
			'url'=>'/almacen/report/closed.php',
			'langs'=>'almacen@almacen',
			'position'=>100,
			'enabled'=>'$conf->almacen->enabled',
			'perms'=>'$user->rights->almacen->rep->read',
			'target'=>'',
			'user'=>0);

		$r++;
		//import

		$this->menu[$r]=array(	'fk_menu'=>'r=0',
			'type'=>'left',
			'titre'=>'Importmovements',
			'mainmenu'=>'almacen',
			'leftmenu'=>'imp',
			'url'=>'/almacen/import/ficheimport.php',
			'langs'=>'almacen@almacen',
			'position'=>100,
			'enabled'=>'$conf->almacen->enabled',
			'perms'=>'$user->rights->almacen->expimp->imp',
			'target'=>'',
			'user'=>0);

		/*
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=almacen,fk_leftmenu=exp',
			'type'=>'left',
			'titre'=>'Export wizard',
			'mainmenu'=>'almacen',
			'url'=>'/almacen/expimp/export.php',
			'langs'=>'almacen@almacen',
			'position'=>100,
			'enabled'=>'$conf->almacen->enabled',
			'perms'=>'$user->rights->almacen->expimp->exp',
			'target'=>'',
			'user'=>0);
		$r++;
		$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=almacen,fk_leftmenu=exp',
			'type'=>'left',
			'titre'=>'Import wizard',
			'mainmenu'=>'almacen',
			'url'=>'/almacen/import/import.php?action=create',
			'langs'=>'almacen@almacen',
			'position'=>100,
			'enabled'=>'$conf->almacen->enabled',
			'perms'=>'$user->rights->almacen->expimp->imp',
			'target'=>'',
			'user'=>0);
		*/

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
		return $this->_load_tables('/almacen/sql/');
	}
}

?>
