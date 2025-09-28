<?php
/* Copyright (C) 2007-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
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
 *      \file       clientes/clientes_card.php
 *      \ingroup    clientes
 *      \brief      This file is an example of a php page
 *                  Initialy built by build_class_from_table on 2017-06-19 16:52
 */

//if (! defined('NOREQUIREUSER'))  define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))    define('NOREQUIREDB','1');
//if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');
//if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');           // Do not check anti CSRF attack test
//if (! defined('NOSTYLECHECK'))   define('NOSTYLECHECK','1');          // Do not check style html tag into posted data
//if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1');        // Do not check anti POST attack test
//if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');         // If there is no need to load and show top and left menu
//if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');         // If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
//if (! defined("NOLOGIN"))        define("NOLOGIN",'1');               // If this page is public (can be called outside logged session)

// Change this following line to use the correct relative path (../, ../../, etc)
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include '../main.inc.php';                  // to work if your module directory is into dolibarr root htdocs directory
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';            // to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../dolibarr/htdocs/main.inc.php';     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");
// Change this following line to use the correct relative path from htdocs
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php');

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formorder.class.php';

require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';


require_once DOL_DOCUMENT_ROOT.'/core/lib/order.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

dol_include_once('/clientes/class/clientes.class.php');

// Load traductions files requiredby by page
$transAreaType = $langs->trans("Assistance");
$langs->load("assistance");
$langs->load("other");

// Get parameters
// obtenemos los datos de esta misma clase
$id         = GETPOST('id','int');
$action     = GETPOST('action','alpha');
$cancel     = GETPOST('cancel');
$confirm    = GETPOST('confirm');
$backtopage = GETPOST('backtopage');
$myparam    = GETPOST('myparam','alpha');
/* codLaiwett */
/* aqui recuepramos los valores mandados */

$pass = GETPOST('Password');

/* endCodLaiwett */




$search_entity=GETPOST('search_entity','int');
$search_nit=GETPOST('search_nit','int');
$search_nombrecompleto=GETPOST('search_nombrecompleto','alpha');
$search_empresa=GETPOST('search_empresa','alpha');
$search_status=GETPOST('search_status','int');



if (empty($action) && empty($id) && empty($ref)) $action='view';

// Protection if external user
if ($user->societe_id > 0)
{
    //accessforbidden();
}
//$result = restrictedArea($user, 'clientes', $id);


$object = new Clientes($db);
$extrafields = new ExtraFields($db);

// fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label($object->table_element);

// Load object
include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php';  // Must be include, not include_once  // Must be include, not include_once. Include fetch and fetch_thirdparty but not fetch_optionals

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('assistance'));


/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
    if ($cancel) 
    {
        if ($action != 'addlink')
        {
            $urltogo=$backtopage?$backtopage:dol_buildpath('/clientes/cliente/list.php',1);
            header("Location: ".$urltogo);
            exit;
        }       
        if ($id > 0 || ! empty($ref)) $ret = $object->fetch($id,$ref);
        $action='';
    }
    
    // Action to add record
    // Acccion de grabar  
    
    
    if ($action == 'add')
    {   


        if (GETPOST('cancel'))
        {
            $urltogo=$backtopage?$backtopage:dol_buildpath('/clientes/cliente/list.php',1);
            header("Location: ".$urltogo);
            exit;
        }

        $error=0;

        /* object_prop_getpost_prop */
        
        $object->entity=GETPOST('entity','int');
        $object->nit=GETPOST('nit','int');
        $object->nombrecompleto=GETPOST('nombrecompleto','alpha');
        $object->empresa=GETPOST('empresa','alpha');
        $object->status=GETPOST('status','int');

        

        if (empty($object->nit))
        {
            $error++;
            setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Nit")), null, 'errors');
        }

        if (! $error)
        {
            $result=$object->create($user);
            if ($result > 0)
            {
                // Creation OK
                /* codLaiwett */
                /* corregir el codigo $urltogo=$backtopage?$backtopage:dol_buildpath('/clientes/cliente/card.php?id='.$result,1); */
                /* endCodLaiwett */
                $urltogo=$backtopage?$backtopage:dol_buildpath('/clientes/cliente/card.php?id='.$result,1);
                header("Location: ".$urltogo);
                exit;
            }
            {
                // Creation KO
                if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
                else  setEventMessages($object->error, null, 'errors');
                $action='create';
            }
        }
        else
        {
            $action='create';
        }
    }

    // Action to update record
    if ($action == 'update')
    {
        $error=0;

        

        $object->entity=$conf->entity;
        $object->nit=GETPOST('nit','int');
        $object->nombrecompleto=GETPOST('nombrecompleto','alpha');
        $object->empresa=GETPOST('empresa','alpha');
        $object->status=GETPOST('status','int');

        

        if (empty($object->nit))
        {
            $error++;
            setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Nit")), null, 'errors');
        }

        if (! $error)
        {
            $result=$object->update($user);
            if ($result > 0)
            {
                $action='view';
            }
            else
            {
                // Creation KO
                if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
                else setEventMessages($object->error, null, 'errors');
                $action='edit';
            }
        }
        else
        {
            $action='edit';
        }
    }

    // Action to delete
    /* validar las acciones */
    /*if ($action == 'confirm_delete' && $confirm == 'yes' && $user->rights->clientes->level1->level2)*/
    if ($action == 'confirm_delete')
    {
        /* codLaiwett */
        //print_r($_POST);
        //print_r($_GET);
        print_r($_REQUEST);
        $ar = $_REQUEST;
        /*print( "el valor separador -> ". $ar['separator']. "</br>");
        print( "el valor number -> ". $ar['numberplaque']. "</br>");
        print( "el valor password -> ". $ar['Password']. "</br>");*/

        /* endCodLaiwett */

        exit;
    

        $result=$object->delete($user);
        if ($result > 0)
        {
            // Delete OK
            setEventMessages("RecordDeleted", null, 'warnings');
            header("Location: ".dol_buildpath('/clientes/cliente/list.php',1));
            exit;
        }
        else
        {
            if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
            else setEventMessages($object->error, null, 'errors');
        }
    }

}




/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('','MyPageName','');

$form=new Form($db);


// Put here content of your page

// Example : Adding jquery code
print '<script type="text/javascript" language="javascript">
jQuery(document).ready(function() {
    function init_myfunc()
    {
        jQuery("#myid").removeAttr(\'disabled\');
        jQuery("#myid").attr(\'disabled\',\'disabled\');
    }
    init_myfunc();
    jQuery("#mybutton").click(function() {
        init_myfunc();
    });
});
</script>';


// Part to create
if ($action == 'create')
{
    print load_fiche_titre($langs->trans("NewMyModule"));

    print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
    print '<input type="hidden" name="action" value="add">';
    print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

    dol_fiche_head();

    print '<table class="border centpercent">'."\n";
    // print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
    // 
    print '<tr><td class="fieldrequired">'.$langs->trans("Fieldentity").'</td><td><input class="flat" type="text" name="entity" value="'.GETPOST('entity').'"></td></tr>';
    print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnit").'</td><td><input class="flat" type="text" name="nit" value="'.GETPOST('nit').'"></td></tr>';
    print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnombrecompleto").'</td><td><input class="flat" type="text" name="nombrecompleto" value="'.GETPOST('nombrecompleto').'"></td></tr>';
    print '<tr><td class="fieldrequired">'.$langs->trans("Fieldempresa").'</td><td><input class="flat" type="text" name="empresa" value="'.GETPOST('empresa').'"></td></tr>';
    print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td><input class="flat" type="text" name="status" value="'.GETPOST('status').'"></td></tr>';
    /* codLaiwett */
    /* Aqui aumentaremos las etiquetas que necesitemos */
    
    //Radio
    print '<tr><td class="fieldrequired">'.'Tipo de Empresa'.'</td><td><input class="flat" type="radio" name="sexo" value="'.GETPOST('tipoempresa').'"><label>Privada<label></td></tr>';
    print '<tr><td class="fieldrequired"></td><td><input class="flat" type="radio" name="sexo" checked value="'.GETPOST('tipoempresa').'"><label>Publica<label></td></tr>';

    //Checkbox's
    print '<tr><td class="fieldrequired">'.'Rubro'.'</td><td><input class="flat" type="checkbox" name="rubro" value="'.GETPOST('contrataciones').' "><label>Contrataciones<label></td></tr>';
    print '<tr><td class="fieldrequired">'.''.'</td><td><input class="flat" type="checkbox" name="rubro" value="'.GETPOST('comercio').' "><label>Comercio<label></td></tr>';
    print '<tr><td class="fieldrequired">'.''.'</td><td><input class="flat" type="checkbox" name="rubro" value="'.GETPOST('exportacion').' "><label>Exportacion<label></td></tr>';

    //Fecha

    print '<tr><td class="fieldrequired">'.'Fecha de Aniversario'.'</td><td><input class="flat" type="date" name="fecha" value="'. date("Y-m-d") .'</td></tr>';


    /* endCodLaiwett */

    print '</table>'."\n";

    dol_fiche_end();

    print '<div class="center">
    <input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp;
    <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

    print '</form>';
}



// Part to edit record
if (($id || $ref) && $action == 'edit')
{
    print load_fiche_titre($langs->trans("MyModule"));
    
    print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
    print '<input type="hidden" name="action" value="update">';
    print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
    print '<input type="text" name="id" value="'.$object->id.'">';

        
    dol_fiche_head();

    print '<table class="border centpercent">'."\n";
    // print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
    // 
    print '<tr><td class="fieldrequired">'.$langs->trans("Fieldentity").'</td><td><input class="flat" type="text" name="entity" value="'.$object->entity.'"></td></tr>';
    print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnit").'</td><td><input class="flat" type="text" name="nit" value="'.$object->nit.'"></td></tr>';
    print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnombrecompleto").'</td><td><input class="flat" type="text" name="nombrecompleto" value="'.$object->nombrecompleto.'"></td></tr>';
    print '<tr><td class="fieldrequired">'.$langs->trans("Fieldempresa").'</td><td><input class="flat" type="text" name="empresa" value="'.$object->empresa.'"></td></tr>';
    print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td><input class="flat" type="text" name="status" value="'.$object->status.'"></td></tr>';

    print '</table>';
    
    dol_fiche_end();

    print '<div class="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
    print ' &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
    print '</div>';

    print '</form>';
}



// Part to show record
if ($object->id > 0 && (empty($action) || ($action != 'edit' && $action != 'create')))
{
    $res = $object->fetch_optionals($object->id, $extralabels);

    /* codLaiwett*/
    /* borrar o comentar esta basura que no se usa para editar si no para poner mas pestanas a la vista*/
    // --> $head = commande_prepare_head($object);
    /* endCodLaiwett */
    dol_fiche_head($head, 'order', $langs->trans("CustomerOrder"), 0, 'order');

    print load_fiche_titre($langs->trans("clientes"));
    
    dol_fiche_head();
    
    /* codLaiwett */
    /* investigar sobre el formquestion */
    /* para declarar una etiqueta de html se le debera mandar en un array */
    /* array(tipo de etiqueta, un label(texto), tamanio de letra, el name , el value, el placeholder, ) */
    /* Nota. Obviamente respetando las caracteristicas de un array */

    $formquestion = array(
                     array('type'=>'text','label'=>$langs->trans('Number plaque'),'size'=>40,'name'=>'numberplaque','value'=>'','                       placeholder'=>$langs->trans('Registre los numeros de plaquetas separado por un caracter único')),
                     array('type'=>'text','label'=>$langs->trans('Separator'),'size'=>5,'name'=>'separator','value'=>'','placeholder'=>$langs->trans('Separator del texto')
                          ),
                     array('type' =>'password', 'label' => 'Introdusca la contrasenia', 'size' => 10, 'name' => 'Password', 'value'=>'', 'placeholder' => '' ),
                     array('type' =>'select', 'label' => 'Intro una opcion','name' =>'Ocupacion', 'values' => array(1=>'Portero',2=>'Saltenero'),'default' => 0),
                     array('type' => 'radio','label'=>'Sexo', 'name'=>'sexo', 'values'=>array(1=>'Masculino',2=>'Femenino',3=>'otro')),

                     array('type'=>'checkbox', 'label'=>'Moneda', 'name'=>'moneda', 'value'=>'Bolivianos', 'valorLabel'=>'Bolivianos'),
                     array('type'=>'checkbox', 'label'=>'', 'name'=>'moneda', 'value'=>'false', 'valorLabel'=>'Dolares'),
                     array('type'=>'checkbox', 'label'=>'', 'name'=>'moneda', 'value'=>'false', 'valorLabel'=>'Euros'),

                     array('type'=>'date', 'label'=>'Fecha de Nacimiento')                    
                        );

    /* tanto en el codigo como la libreria */
    if ($action == 'delete') {
        $formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, 
                                          $langs->trans('DeleteMyOjbect'),
                                          $langs->trans('ConfirmDeleteMyObject'),
                                          'confirm_delete',
                                          $formquestion ,
                                          0,
                                          2);
        print $formconfirm;
    }
    
    print '<table class="border centpercent">'."\n";
    // print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td>'.$object->label.'</td></tr>';
    // 
    print '<tr><td class="fieldrequired">'.$langs->trans("Fieldentity").'</td><td>'.$object->entity.'</td></tr>';
    print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnit").'</td><td>'.$object->nit.'</td></tr>';
    print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnombrecompleto").'</td><td>'.$object->nombrecompleto.'</td></tr>';
    print '<tr><td class="fieldrequired">'.$langs->trans("Fieldempresa").'</td><td>'.$object->empresa.'</td></tr>';
    print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td>'.$object->status.'</td></tr>';

    print '</table>';
    
    dol_fiche_end();


    // Buttons
    print '<div class="tabsAction">'."\n";
    $parameters=array();
    $reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
    if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

    if (empty($reshook))
    {
        if ($user->rights->clientes->read)
        {
            print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
        }

        /****** codLaiwett *****/
        /* Agrgamos los botones y sus respectivos privilegios */

        if ($user->rights->clientes->delete)
        {
            print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
        }

        if ($user->rights->clientes->atras)
        {
            print '<div class="inline-block divButAction"><a class="butAction" href="'.DOL_URL_ROOT.'/clientes/cliente/list.php">'.$langs->trans('Atras').'</a></div>'."\n";
        }
        /***** endCodLaiwett *****/

    }
    print '</div>'."\n";


    // Example 2 : Adding links to objects
    // Show links to link elements
    //$linktoelem = $form->showLinkToObjectBlock($object, null, array('clientes'));
    //$somethingshown = $form->showLinkedObjectBlock($object, $linktoelem);

}


// End of page
llxFooter();
$db->close();
