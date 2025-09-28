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
 * 	\defgroup   mymodule     Module MyModule
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
class modMonprojet extends DolibarrModules
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
		$this->numero = 601000;
		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'monprojet';

		// Family can be 'crm','financial','hr','projects','products','ecm','technic','other'
		// It is used to group modules in module setup page
		$this->family = "other";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i','',get_class($this));
		// Module description, used if translation string 'ModuleXXXDesc' not found (where XXX is value of numeric property 'numero' of module)
		$this->description = "Seguimiento a la ejecucion de tareas";
		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = '1.6.3.2';
		// Key used in llx_const table to save module status enabled/disabled (where MYMODULE is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Where to store the module in setup page (0=common,1=interface,2=others,3=very specific)
		$this->special = 0;
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		$this->picto='monprojet@monprojet';

		// Defined all module parts (triggers, login, substitutions, menus, css, etc...)
		// for default path (eg: /mymodule/core/xxxxx) (0=disable, 1=enable)
		// for specific path of parts (eg: /mymodule/core/modules/barcode)
		// for specific css file (eg: /mymodule/css/mymodule.css.php)
		$this->module_parts = array('triggers' => 1,
			'hooks' => array('doActions','formObjectOptions','projectlist','projectcard','contacttpl'),
			'js' => array('/monprojet/js/monprojet.js'),
			'css' => array('/monprojet/css/style.css','/monprojet/css/si.css','/monprojet/css/style.css','/monprojet/css/styleadd.css'),
			);

		// $this->module_parts = array(
		//                         	'triggers' => 0,
						// Set this to 1 if module has its own trigger directory (core/triggers)
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
		//$this->module_parts = array();

		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/mymodule/temp");
		$this->dirs = array();

		// Config pages. Put here list of php page, stored into mymodule/admin directory, to use to setup module.
		$this->config_page_url = array("monprojet.php@monprojet");

		// Dependencies
		$this->hidden = false;			// A condition to hide module
		$this->depends = array();		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array();	// List of modules id to disable if this one is disabled
		$this->conflictwith = array();	// List of modules id this module is in conflict with
		$this->phpmin = array(5,0);					// Minimum version of PHP required by module
		$this->need_dolibarr_version = array(3,0);	// Minimum version of Dolibarr required by module
		$this->langfiles = array("monprojet@monprojet");

		// Constants
		// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
		// Example: $this->const=array(0=>array('MYMODULE_MYNEWCONST1','chaine','myvalue','This is a constant to add',1),
		//                             1=>array('MYMODULE_MYNEWCONST2','chaine','myvalue','This is another constant to add',0, 'current', 1)
		// );
		$this->const = array(0=>array('MONPROJET_HIDE_INACTIVE_IN_COMBOBOX','chaine','1','Ocultar en el combobox los inactivos 0=No, 1=Si',1),
			1=>array('MONPROJET_USE_SEARCH_TO_SELECT','chaine','3','Utilizar buscador en el combobox por numero de caracteres: 1=1 caracter, 2=2 caracteres, 3=tres caracteres',1),
			2=>array('MONPROJET_USE_WITHPROJECT','chaine','0','Mostrar el proyecto cuando se ingresa a tareas 0=No, 1=Si',1),
			3=>array('MONPROJET_USE_SHORT','chaine','0','Ver unidad de medida 0=Nombre extendido, 1=Nombre corto',1),
			4=>array('MONPROJET_MESSAGE_SENDMAIL','chaine','0','Se enviara mensajes de correo, 0=No, 1=Si ',1),
			5=>array('MONPROJET_TAX_INCLUDED','chaine','0','El valor registrado incluye impuestos, 0=No, 1=Si ',1),
			6=>array('MONPROJET_CODE_CATEGORY_MATERIAL','chaine','0','Id de la categoria materiales. Si Proyectos esta relacionado a Presupuestos (budget), este valor se omite ',1),
			7=>array('MONPROJET_CODE_CATEGORY_WORKFORCE','chaine','0','Id de la categoria mano de obra. Si Proyectos esta relacionado a Presupuestos (budget), este valor se omite ',1),
			8=>array('MONPROJET_CODE_CATEGORY_MACHINERY','chaine','0','Id de la categoria equipos y maquinaria. Si Proyectos esta relacionado a Presupuestos (budget), este valor se omite ',1),
			9=>array('MONPROJET_CREATE_TASK_DEFAULT','chaine','Tarea1;Unidad1;Cantidad1|Tarea2;Unidad2;Cantidad2','Tareas a crearse por defecto en cualquier proyecto. Separe las tareas con |',1),
			);

		// Array to add new pages in new tabs
		// Example: $this->tabs = array('objecttype:+tabname1:Title1:mylangfile@mymodule:$user->rights->mymodule->read:/mymodule/mynewtab1.php?id=__ID__',  	// To add a new tab identified by code tabname1
		//                              'objecttype:+tabname2:Title2:mylangfile@mymodule:$user->rights->othermodule->read:/mymodule/mynewtab2.php?id=__ID__',  	// To add another new tab identified by code tabname2
		//                              'objecttype:-tabname:NU:conditiontoremove');                                                     						// To remove an existing tab identified by code tabname
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
		$this->tabs = array('task:-task_task',
			'task:+task_task:Task:monprojet@monprojet:$user->rights->monprojet->task->leer:/monprojet/task/task.php?id=__ID__&withproject=1',
			'task:-task_contact',
			'task:+task_contact:Contacting:monprojet@monprojet:$user->rights->monprojet->contact->leer:/monprojet/task/contact.php?id=__ID__&withproject=1',
			'task:-task_time',
			'task:+task_time:Progress:monprojet@monprojet:$user->rights->monprojet->timed->leer:/monprojet/task/time.php?id=__ID__&withproject=1',
			'task:+resource:Iuseresources:monprojet@monprojet:$user->rights->monprojet->con->leer:/monprojet/task/resource.php?id=__ID__&withproject=1',
					//'task:+task_eje:Ejecution:monprojet@monprojet:$user->rights->monprojet->eje->leer:/monprojet/task/ejecution.php?id=__ID__&withproject=1',
			'task:+Dependences:Dependences:monprojet@monprojet:$user->rights->monprojet->depe->leer:/monprojet/task/depends.php?id=__ID__&withproject=1',
			'task:+task_pay:Collections:monprojet@monprojet:$user->rights->monprojet->pay->leer:/monprojet/task/collection.php?id=__ID__&withproject=1',
			'task:+task_close:Close:monprojet@monprojet:$user->rights->monprojet->clo->leer:/monprojet/task/close.php?id=__ID__&withproject=1',
			'task:-task_document',
			'task:+task_documents:Documents:monprojet@monprojet:$user->rights->monprojet->doc->leer:/monprojet/task/document.php?id=__ID__&withproject=1',
			'task:-task_notes',
			'task:+task_comment:Note:monprojet@monprojet:$user->rights->monprojet->note->leer:/monprojet/task/note.php?id=__ID__&withproject=1',
			'task:+task_attach:Attachmenttype:monprojet@monprojet:$user->rights->monprojet->att->leer:/monprojet/task/attachment.php?id=__ID__&withproject=1',
			'project:-project',
			'project:+project:Project:monprojet@monprojet:$user->rights->projet->lire:/monprojet/card.php?id=__ID__',
			'project:-element',
			'project:+element:Summary:monprojet@monprojet:$user->rights->monprojet->gantt->leer:/monprojet/element.php?id=__ID__',
			'project:-contact',
			'project:+Contact:Contact:monprojet@monprojet:$user->rights->monprojet->proc->leer:/monprojet/contact.php?id=__ID__',
					//'project:+Budget:Budget:monprojet@monprojet:$user->rights->monprojet->bud->leer:/monprojet/budget.php?id=__ID__',
			'project:+Summary:Binnacle:monprojet@monprojet:$user->rights->monprojet->leer:/monprojet/summary.php?id=__ID__',
			'project:-gantt',
			'project:+Gantt:Gantt:monprojet@monprojet:$user->rights->monprojet->gantt->leer:/monprojet/ganttview.php?id=__ID__',
			'project:-tasks',
			'project:+taskss:Tasks:monprojet@monprojet:$user->rights->monprojet->task->leer:/monprojet/tasks.php?id=__ID__',
			'project:+resources:Resources:monprojet@monprojet:$user->rights->monprojet->con->leer:/monprojet/resources.php?id=__ID__',
			'project:+tasksrep:Report:monprojet@monprojet:$user->rights->monprojet->adv->leer:/monprojet/tasksrep.php?id=__ID__',
			'project:-notes',
			'project:+notess:Notes:monprojet@monprojet:$user->rights->monprojet->notep->leer:/monprojet/note.php?id=__ID__',
			'project:+contrat:Contrat:monprojet@monprojet:$user->rights->monprojet->cont->leer:/monprojet/contrat.php?id=__ID__',
			'project:+payment:Payments:monprojet@monprojet:$user->rights->monprojet->payp->leer:/monprojet/paiement.php?id=__ID__',
			'project:+collect:Collects:monprojet@monprojet:$user->rights->monprojet->payp->leer:/monprojet/collect.php?id=__ID__',
			'project:+docint:Docint:monprojet@monprojet:$user->rights->monprojet->docint->leer:/monprojet/docint.php?id=__ID__',
			'project:-document',
			'project:+docext:Documents:monprojet@monprojet:$user->rights->monprojet->docext->leer:/monprojet/docext.php?id=__ID__',
			);

	// $this->tabs = array('objecttype:+project:Project:monprojet@monprojet:$user->rights->monprojet->leer:/projet/fiche.php?id=__ID__'

	//			    );  	// To add a new tab identified by code
		// Dictionaries
		if (! isset($conf->monprojet->enabled))
		{
			$conf->monprojet=new stdClass();
			$conf->monprojet->enabled=0;
		}
		$this->dictionaries=array();
		$this->dictionaries=array(
			'langs'=>'monprojet@monprojet',
			'tabname'=>array(MAIN_DB_PREFIX."c_element_task",
				MAIN_DB_PREFIX."c_guarantees",
							   MAIN_DB_PREFIX."c_deductions",),		// List of tables we want to see into dictonnary editor
			'tablib'=>array("Attachmenttype",
				"Typeofguarantees",
							  "Typedeductions",),													// Label of tables
			'tabsql'=>array('SELECT f.rowid as rowid, f.entity, f.code, f.label AS label, f.active FROM '.MAIN_DB_PREFIX.'c_element_task as f',
				'SELECT f.rowid as rowid, f.code, f.label AS label, f.active FROM '.MAIN_DB_PREFIX.'c_guarantees as f',
							  'SELECT f.rowid as rowid, f.code, f.label AS label, f.sequence, f.active FROM '.MAIN_DB_PREFIX.'c_deductions as f',),	// Request to select fields
			'tabsqlsort'=>array("label ASC",
				"label ASC",
							  "sequence ASC",),																					// Sort order
			'tabfield'=>array("entity,code,label",
				"code,label",
							"code,label,sequence",),					// List of fields (result of select to show dictionary)
			'tabfieldvalue'=>array("entity,code,label",
				"code,label",
								 "code,label,sequence",),				// List of fields (list of fields to edit a record)
			'tabfieldinsert'=>array("entity,code,label",
				"code,label",
								  "code,label,sequence",),			// List of fields (list of fields for insert)
			'tabrowid'=>array("rowid",
				"rowid",
							"rowid",),					// Name of columns with primary key (try to always name it 'rowid')
			'tabcond'=>array($conf->monprojet->enabled,
				$conf->monprojet->enabled,
							   $conf->monprojet->enabled,)				// Condition to show each dictionary
			);

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

		// Boxes
		// Add here list of php file(s) stored in core/boxes that contains class to show a box.
		$this->boxes = array();			// List of boxes
		// Example:
	// $this->boxes=array(array(0=>array('file'=>'myboxa.php','note'=>'','enabledbydefaulton'=>'Home'),1=>array('file'=>'myboxb.php','note'=>''),2=>array('file'=>'myboxc.php','note'=>'')););
	// $this->boxes=array(
	// 		   array(0=>array('file'=>'box_monprojet.php','note'=>'myproject','enabledbydefaulton'=>'Home'),
	// 			 1=>array('file'=>'myboxb.php','note'=>''),
	// 			 2=>array('file'=>'myboxc.php','note'=>'')),);
		$r=0;
		$this->boxes[$r]['file']='box_monprojet.php@monprojet';
		$this->boxes[$r]['note']='Mis proyectos';
		$r++;
		$this->boxes[$r]['file']='box_montask.php@monprojet';
		$this->boxes[$r]['note']='Mis tareas';
		$r++;
		$this->boxes[$r]['file']='box_montaskadvance.php@monprojet';
		$this->boxes[$r]['note']='Mis tareas con avance';

		$this->boxes=array(
		    0=>array('file'=>'box_monprojet.php','note'=>'Mis propios proyectos','enabledbydefaulton'=>'Home'),
		    2=>array('file'=>'box_montask.php','note'=>'','enabledbydefaulton'=>'Home'),
		    3=>array('file'=>'box_montaskadvance.php','note'=>'')
		);


		// Permissions
		$this->rights = array();		// Permission array used by this module
		$r=0;

		// Add here list of permission defined by an id, a label, a boolean and two constant strings.
		// Example:
		// $this->rights[$r][0] = 2000; 				// Permission id (must not be already used)
		// $this->rights[$r][1] = 'Permision label';	// Permission label
		// $this->rights[$r][3] = 0; 					// Permission by default for new user (0/1)
		// $this->rights[$r][4] = 'level1';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		// $this->rights[$r][5] = 'level2';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		// $r++;


		// Main menu entries
		$this->menu = array();			// List of menus to add
		$r=0;
		$this->rights[$r][0] = 601101;
		$this->rights[$r][1] = 'Monitoreo proyectos';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'leer';

		$r++;
		$this->rights[$r][0] = 601102;
		$this->rights[$r][1] = 'Ver Dependientes';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'depe';
		$this->rights[$r][5] = 'leer';

		$r++;
		$this->rights[$r][0] = 601103;
		$this->rights[$r][1] = 'Adicionar Dependientes';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'depe';
		$this->rights[$r][5] = 'crear';

		$r++;
		$this->rights[$r][0] = 601104;
		$this->rights[$r][1] = 'Borrar Dependientes';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'depe';
		$this->rights[$r][5] = 'del';

		$r++;
		$this->rights[$r][0] = 601108;
		$this->rights[$r][1] = 'Ver notas proyectos';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'notep';
		$this->rights[$r][5] = 'leer';
		$r++;
		$this->rights[$r][0] = 601109;
		$this->rights[$r][1] = 'Enviar correo de notas proyectos a contactos';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'notep';
		$this->rights[$r][5] = 'send';

		$r++;
		$this->rights[$r][0] = 601231;
		$this->rights[$r][1] = 'Leer contactos del proyecto';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'proc';
		$this->rights[$r][5] = 'leer';
		$r++;
		$this->rights[$r][0] = 601232;
		$this->rights[$r][1] = 'Crear contactos del proyecto';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'proc';
		$this->rights[$r][5] = 'crear';
		$r++;
		$this->rights[$r][0] = 601233;
		$this->rights[$r][1] = 'Borrar contactos del proyecto';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'proc';
		$this->rights[$r][5] = 'del';

		$r++;
		$this->rights[$r][0] = 601235;
		$this->rights[$r][1] = 'Leer contactos de la tarea';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'prot';
		$this->rights[$r][5] = 'leer';
		$r++;
		$this->rights[$r][0] = 601236;
		$this->rights[$r][1] = 'Crear contactos de la tarea';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'prot';
		$this->rights[$r][5] = 'crear';
		$r++;
		$this->rights[$r][0] = 601237;
		$this->rights[$r][1] = 'Borrar contactos de la tarea';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'prot';
		$this->rights[$r][5] = 'del';

		//budget presupuesto
		$r++;
		$this->rights[$r][0] = 601211;
		$this->rights[$r][1] = 'View budget';
		$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'bud';
		$this->rights[$r][5] = 'leer';
		$r++;
		$this->rights[$r][0] = 601212;
		$this->rights[$r][1] = 'Create budget';
		$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'bud';
		$this->rights[$r][5] = 'crear';
		$r++;
		$this->rights[$r][0] = 601213;
		$this->rights[$r][1] = 'Modify budget';
		$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'bud';
		$this->rights[$r][5] = 'mod';
		$r++;
		$this->rights[$r][0] = 601214;
		$this->rights[$r][1] = 'Delete budget';
		$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'bud';
		$this->rights[$r][5] = 'del';
		$r++;
		$this->rights[$r][0] = 601215;
		$this->rights[$r][1] = 'Validate budget';
		$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'bud';
		$this->rights[$r][5] = 'val';
		$r++;
		$this->rights[$r][0] = 601216;
		$this->rights[$r][1] = 'Create item budget';
		$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'budi';
		$this->rights[$r][5] = 'crear';
		$r++;
		$this->rights[$r][0] = 601217;
		$this->rights[$r][1] = 'Modify item budget';
		$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'budi';
		$this->rights[$r][5] = 'mod';
		$r++;
		$this->rights[$r][0] = 601218;
		$this->rights[$r][1] = 'Delete item budget';
		$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'budi';
		$this->rights[$r][5] = 'del';
		$r++;
		$this->rights[$r][0] = 601219;
		$this->rights[$r][1] = 'Add/Mod item amount budget';
		$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'budi';
		$this->rights[$r][5] = 'addm';

		//registro de consumos
		$r++;
		$this->rights[$r][0] = 601221;
		$this->rights[$r][1] = 'View Resources';
		$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'con';
		$this->rights[$r][5] = 'leer';
		$r++;
		$this->rights[$r][0] = 601222;
		$this->rights[$r][1] = 'Create task resources';
		$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'con';
		$this->rights[$r][5] = 'crear';
		$r++;
		$this->rights[$r][0] = 601223;
		$this->rights[$r][1] = 'Modify task resources';
		$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'con';
		$this->rights[$r][5] = 'mod';
		$r++;
		$this->rights[$r][0] = 601224;
		$this->rights[$r][1] = 'Delete task resources';
		$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'con';
		$this->rights[$r][5] = 'del';
		$r++;
		$this->rights[$r][0] = 601225;
		$this->rights[$r][1] = 'Validate task resources';
		$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'con';
		$this->rights[$r][5] = 'val';

		//contratos
		$r++;
		$this->rights[$r][0] = 601135;
		$this->rights[$r][1] = 'Ver contratos del proyecto';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'cont';
		$this->rights[$r][5] = 'leer';
		$r++;
		$this->rights[$r][0] = 601136;
		$this->rights[$r][1] = 'Crear/Modificar garantias';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'cont';
		$this->rights[$r][5] = 'crear';
		$r++;
		$this->rights[$r][0] = 601137;
		$this->rights[$r][1] = 'Borrar garantias';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'cont';
		$this->rights[$r][5] = 'del';
		$r++;
		$this->rights[$r][0] = 601138;
		$this->rights[$r][1] = 'Crear/Modificar Deducciones al contrato';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'cont';
		$this->rights[$r][5] = 'ded';

		//Tareas avance
		$r++;
		$this->rights[$r][0] = 601111;
		$this->rights[$r][1] = 'Ver Avance tareas';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'timed';
		$this->rights[$r][5] = 'leer';
		$r++;
		$this->rights[$r][0] = 601112;
		$this->rights[$r][1] = 'Registrar Avance tareas';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'timed';
		$this->rights[$r][5] = 'crear';
		$r++;
		$this->rights[$r][0] = 601113;
		$this->rights[$r][1] = 'Borrar Avance tareas';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'timed';
		$this->rights[$r][5] = 'del';
		$r++;
		$this->rights[$r][0] = 601114;
		$this->rights[$r][1] = 'Modificar Avance tareas';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'timed';
		$this->rights[$r][5] = 'mod';

		//task contact
		$r++;
		$this->rights[$r][0] = 601116;
		$this->rights[$r][1] = 'Ver recursos tareas';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'contact';
		$this->rights[$r][5] = 'leer';
		$r++;
		$this->rights[$r][0] = 601117;
		$this->rights[$r][1] = 'Registrar recursos tareas';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'contact';
		$this->rights[$r][5] = 'crear';
		$r++;
		$this->rights[$r][0] = 601118;
		$this->rights[$r][1] = 'Registrar/Modificar fechas recursos tareas';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'contact';
		$this->rights[$r][5] = 'creard';
		$r++;
		$this->rights[$r][0] = 601119;
		$this->rights[$r][1] = 'Borrar recursos tareas';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'contact';
		$this->rights[$r][5] = 'del';
		$r++;
		$this->rights[$r][0] = 601120;
		$this->rights[$r][1] = 'Modificar recursos tareas';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'contact';
		$this->rights[$r][5] = 'mod';

		//task
		$r++;
		$this->rights[$r][0] = 601121;
		$this->rights[$r][1] = 'Leer Tareas';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'task';
		$this->rights[$r][5] = 'leer';
		$r++;
		$this->rights[$r][0] = 601122;
		$this->rights[$r][1] = 'Crear Tareas';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'task';
		$this->rights[$r][5] = 'crear';
		$r++;
		$this->rights[$r][0] = 601123;
		$this->rights[$r][1] = 'Modificar Tareas';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'task';
		$this->rights[$r][5] = 'mod';
		$r++;
		$this->rights[$r][0] = 601124;
		$this->rights[$r][1] = 'Borrar Tareas';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'task';
		$this->rights[$r][5] = 'del';
		$r++;
		$this->rights[$r][0] = 601125;
		$this->rights[$r][1] = 'Ver monto unitario Tarea';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'task';
		$this->rights[$r][5] = 'leerm';
		$r++;
		$this->rights[$r][0] = 601126;
		$this->rights[$r][1] = 'Registrar monto unitario Tarea';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'task';
		$this->rights[$r][5] = 'addm';
		$r++;
		$this->rights[$r][0] = 601127;
		$this->rights[$r][1] = 'Modificar monto unitario Tarea';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'task';
		$this->rights[$r][5] = 'modm';


		$r++;
		$this->rights[$r][0] = 601131;
		$this->rights[$r][1] = 'Ver Ejecucion tareas';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'eje';
		$this->rights[$r][5] = 'leer';
		$r++;
		$this->rights[$r][0] = 601132;
		$this->rights[$r][1] = 'Registrar Ejecucion tareas';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'eje';
		$this->rights[$r][5] = 'crear';
		$r++;
		$this->rights[$r][0] = 601133;
		$this->rights[$r][1] = 'Borrar Ejecucion tareas';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'eje';
		$this->rights[$r][5] = 'del';
		$r++;
		$this->rights[$r][0] = 601134;
		$this->rights[$r][1] = 'Modificar Ejecucion tareas';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'eje';
		$this->rights[$r][5] = 'mod';



		$r++;
		$this->rights[$r][0] = 601147;
		$this->rights[$r][1] = 'Ver Cierre Tareas';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'clo';
		$this->rights[$r][5] = 'leer';
		$r++;
		$this->rights[$r][0] = 601148;
		$this->rights[$r][1] = 'Registrar Cierre Tareas';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'clo';
		$this->rights[$r][5] = 'crear';


		// $r++;
		// $this->rights[$r][0] = 601131;
		// $this->rights[$r][1] = 'Registrar Ejecucion Tareas';
		// //$this->rights[$r][2] = 'a';
		// $this->rights[$r][3] = 0;
		// $this->rights[$r][4] = 'task';
		// $this->rights[$r][5] = 'modr';
		$r++;
		$this->rights[$r][0] = 601141;
		$this->rights[$r][1] = 'Ver Documentos en Tareas';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'doc';
		$this->rights[$r][5] = 'leer';
		$r++;
		$this->rights[$r][0] = 601142;
		$this->rights[$r][1] = 'Subir Documentos en Tareas';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'doc';
		$this->rights[$r][5] = 'crear';

		//notes
		$r++;
		$this->rights[$r][0] = 601151;
		$this->rights[$r][1] = 'Ver notas en Tareas';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'note';
		$this->rights[$r][5] = 'leer';
		$r++;
		$this->rights[$r][0] = 601152;
		$this->rights[$r][1] = 'Modificar notas publicas en Tareas';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'notepub';
		$this->rights[$r][5] = 'crear';
		$r++;
		$this->rights[$r][0] = 601153;
		$this->rights[$r][1] = 'Modificar notas privadas en Tareas (solo usuarios internos)';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'notepri';
		$this->rights[$r][5] = 'crear';

		//Elements
		$r++;
		$this->rights[$r][0] = 601161;
		$this->rights[$r][1] = 'Ver tipo de adjuntos en tareas';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'att';
		$this->rights[$r][5] = 'leer';
		$r++;
		$this->rights[$r][0] = 601162;
		$this->rights[$r][1] = 'Crear tipo de adjuntos en tareas';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'att';
		$this->rights[$r][5] = 'crear';
		$r++;
		$this->rights[$r][0] = 601163;
		$this->rights[$r][1] = 'Modificar tipo de adjuntos en tareas';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'att';
		$this->rights[$r][5] = 'mod';
		$r++;
		$this->rights[$r][0] = 601164;
		$this->rights[$r][1] = 'Borrar tipo de adjuntos en tareas';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'att';
		$this->rights[$r][5] = 'del';

		//permisos para subida de archivos
		$r++;
		$this->rights[$r][0] = 601171;
		$this->rights[$r][1] = 'Subir pdf, png, jpeg, jpg, bmp, gif, doc, docx, xls, xlsx';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'pho';
		$this->rights[$r][5] = 'up1';
		$r++;
		$this->rights[$r][0] = 601172;
		$this->rights[$r][1] = 'Subir pdf, png, jpeg, jpg, bmp, gif, doc, docx';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'pho';
		$this->rights[$r][5] = 'up2';
		$r++;
		$this->rights[$r][0] = 601173;
		$this->rights[$r][1] = 'Subir pdf, png, jpeg, jpg, bmp, gif';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'pho';
		$this->rights[$r][5] = 'up3';
		$r++;
		$this->rights[$r][0] = 601174;
		$this->rights[$r][1] = 'Subir pdf';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'pho';
		$this->rights[$r][5] = 'up4';
		$r++;
		$this->rights[$r][0] = 601175;
		$this->rights[$r][1] = 'Subir todo';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'pho';
		$this->rights[$r][5] = 'up5';

		//programados
		$r++;
		$this->rights[$r][0] = 601179;
		$this->rights[$r][1] = 'Ver tareas programadas';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'prog';
		$this->rights[$r][5] = 'leer';

		//validar avance para pago
		$r++;
		$this->rights[$r][0] = 601181;
		$this->rights[$r][1] = 'Ver Avance acumulado para cobro';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'pay';
		$this->rights[$r][5] = 'leer';

		$r++;
		$this->rights[$r][0] = 601182;
		$this->rights[$r][1] = 'Registrar avance para cobro por tarea';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'pay';
		$this->rights[$r][5] = 'crear';

		$r++;
		$this->rights[$r][0] = 601183;
		$this->rights[$r][1] = 'Validar avance para cobro por tarea';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'pay';
		$this->rights[$r][5] = 'val';
		$r++;
		$this->rights[$r][0] = 601184;
		$this->rights[$r][1] = 'Borrar avance para cobro por tarea';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'pay';
		$this->rights[$r][5] = 'del';

		//planillas de cobros
		$r++;
		$this->rights[$r][0] = 601185;
		$this->rights[$r][1] = 'Ver planillas de cobro';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'payp';
		$this->rights[$r][5] = 'leer';
		$r++;
		$this->rights[$r][0] = 601186;
		$this->rights[$r][1] = 'Aprobar avance para cobro por tarea';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'payp';
		$this->rights[$r][5] = 'app';
		$r++;
		$this->rights[$r][0] = 601187;
		$this->rights[$r][1] = 'Generar planilla de cobro';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'payp';
		$this->rights[$r][5] = 'pay';
		$r++;
		$this->rights[$r][0] = 601188;
		$this->rights[$r][1] = 'Modificar planilla de cobro';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'payp';
		$this->rights[$r][5] = 'mod';
		$r++;
		$this->rights[$r][0] = 601189;
		$this->rights[$r][1] = 'Aprobar planilla de cobro';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'payp';
		$this->rights[$r][5] = 'payapp';
		$r++;
		$this->rights[$r][0] = 601190;
		$this->rights[$r][1] = 'Facturar planilla de cobro';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'payp';
		$this->rights[$r][5] = 'fac';


		//gant
		$r++;
		$this->rights[$r][0] = 601191;
		$this->rights[$r][1] = 'Ver Diagrama de Gantt';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'gantt';
		$this->rights[$r][5] = 'leer';

		//documentos internos
		$r++;
		$this->rights[$r][0] = 601193;
		$this->rights[$r][1] = 'Ver Documentos internos proyectos';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'docint';
		$this->rights[$r][5] = 'leer';
		//documentos internos
		$r++;
		$this->rights[$r][0] = 601194;
		$this->rights[$r][1] = 'Ver Documentos externos proyectos';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'docext';
		$this->rights[$r][5] = 'leer';

		$r++;
		$this->rights[$r][0] = 601195;
		$this->rights[$r][1] = 'Exportar Planilla de pago';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'payp';
		$this->rights[$r][5] = 'rep';

		//avance fisico
		$r++;
		$this->rights[$r][0] = 601196;
		$this->rights[$r][1] = 'Ver Avance';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'adv';
		$this->rights[$r][5] = 'leer';
		//avance fisico
		$r++;
		$this->rights[$r][0] = 601197;
		$this->rights[$r][1] = 'Ver Avance fisico';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'advc';
		$this->rights[$r][5] = 'leer';
		//avance financiero
		$r++;
		$this->rights[$r][0] = 601198;
		$this->rights[$r][1] = 'Ver Avance financiero';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'advf';
		$this->rights[$r][5] = 'leer';

		$r++;
		$this->rights[$r][0] = 601199;
		$this->rights[$r][1] = 'Descargar Documentos subidos';
		$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'read';

		$r++;
		$this->rights[$r][0] = 601201;
		$this->rights[$r][1] = 'Libro de Ordenes';
		$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'book';
		$this->rights[$r][5] = 'leer';
		$r++;
		$this->rights[$r][0] = 601202;
		$this->rights[$r][1] = 'Libro de Ordenes crear';
		$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'book';
		$this->rights[$r][5] = 'crear';
		$r++;
		$this->rights[$r][0] = 601203;
		$this->rights[$r][1] = 'Libro de Ordenes modificar';
		$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'book';
		$this->rights[$r][5] = 'mod';
		$r++;
		$this->rights[$r][0] = 601204;
		$this->rights[$r][1] = 'Libro de Ordenes borrar';
		$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'book';
		$this->rights[$r][5] = 'del';
		$r++;
		$this->rights[$r][0] = 601205;
		$this->rights[$r][1] = 'Libro de Ordenes validar';
		$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'book';
		$this->rights[$r][5] = 'val';
		$r++;
		$this->rights[$r][0] = 601206;
		$this->rights[$r][1] = 'Borrar imagenes Libro de Ordenes';
		$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'bookimg';
		$this->rights[$r][5] = 'del';

		//planillas de pagos
		$r++;
		$this->rights[$r][0] = 601241;
		$this->rights[$r][1] = 'Ver planillas de pagos';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'paip';
		$this->rights[$r][5] = 'leer';
		$r++;
		$this->rights[$r][0] = 601242;
		$this->rights[$r][1] = 'Aprobar avance para pagos por tarea';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'paip';
		$this->rights[$r][5] = 'app';
		$r++;
		$this->rights[$r][0] = 601243;
		$this->rights[$r][1] = 'Generar planilla de pago';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'paip';
		$this->rights[$r][5] = 'pay';
		$r++;
		$this->rights[$r][0] = 601244;
		$this->rights[$r][1] = 'Modificar planilla de pago';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'paip';
		$this->rights[$r][5] = 'mod';
		$r++;
		$this->rights[$r][0] = 601245;
		$this->rights[$r][1] = 'Aprobar planilla de pago';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'paip';
		$this->rights[$r][5] = 'paiapp';
		$r++;
		$this->rights[$r][0] = 601246;
		$this->rights[$r][1] = 'Eliminar planilla de pago';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'paip';
		$this->rights[$r][5] = 'del';
		$r++;
		$this->rights[$r][0] = 601247;
		$this->rights[$r][1] = 'Generar factura planilla de pago';
		//$this->rights[$r][2] = 'a';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'paip';
		$this->rights[$r][5] = 'fac';


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

		// Main menu entries
		$this->menu = array();			// List of menus to add
		// $r=0;
		// $this->menu[$r]=array(	'fk_menu'=>0,
		// 			'type'=>'top',
		// 			'titre'=>'Monitoring projet',
		// 			'mainmenu'=>'monprojet',
		// 			'leftmenu'=>'0',
		// 			'url'=>'/monprojet/fiche.php',
		// 			'langs'=>'monprojet@monprojet',
		// 			'position'=>100,
		// 			'enabled'=>$conf->monprojet->enabled,
		// 			'perms'=>'$user->rights->monprojet->leer',
		// 			'target'=>'',
		// 			'user'=>0);

		//$r=1;

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

		$result=$this->_load_tables('/monprojet/sql/');

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

