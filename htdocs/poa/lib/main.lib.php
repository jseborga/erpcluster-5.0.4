<?php

/**
 * Ouput html header of a page.
 * This code is also duplicated into security2.lib.php::dol_loginfunction
 *
 * @param   string  $head           Optionnal head lines
 * @param   string  $title          HTML title
 * @param   int     $disablejs      More content into html header
 * @param   int     $disablehead    More content into html header
 * @param   array   $arrayofjs      Array of complementary js files
 * @param   array   $arrayofcss     Array of complementary css files
 * @return  void
 */
function top_htmlheadv($head, $title='', $disablejs=0, $disablehead=0, $arrayofjs='', $arrayofcss='')
{
	global $user, $conf, $langs, $db;

	top_httphead();

	if (empty($conf->css)) $conf->css = '/theme/eldy/style.css.php';    // If not defined, eldy by default

	//print '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
	//print '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd">';
	//print '<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
	//print '<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
	print '<!DOCTYPE HTML>';
	print "\n";
	if (! empty($conf->global->MAIN_USE_CACHE_MANIFEST)) print '<html manifest="'.DOL_URL_ROOT.'/cache.manifest">'."\n";
	else print '<html>'."\n";
	//print '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">'."\n";
	if (empty($disablehead))
	{
		print "<head>\n";
		if (GETPOST('dol_basehref')) print '<base href="'.dol_escape_htmltag(GETPOST('dol_basehref')).'">'."\n";
		// Displays meta
		print '<meta http-equiv="X-UA-Compatible" content="IE=edge">';
		print '<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">';
		print '<meta name="robots" content="noindex,nofollow">'."\n";      // Evite indexation par robots
		print '<meta name="author" content="Dolibarr Development Team">'."\n";
		$favicon=dol_buildpath('/theme/'.$conf->theme.'/img/favicon.ico',1);
		if (! empty($conf->global->MAIN_FAVICON_URL)) $favicon=$conf->global->MAIN_FAVICON_URL;
		print '<link rel="shortcut icon" type="image/x-icon" href="'.$favicon.'"/>'."\n";
		if (empty($conf->global->MAIN_OPTIMIZEFORTEXTBROWSER)) print '<link rel="top" title="'.$langs->trans("Home").'" href="'.(DOL_URL_ROOT?DOL_URL_ROOT:'/').'">'."\n";
		if (empty($conf->global->MAIN_OPTIMIZEFORTEXTBROWSER)) print '<link rel="copyright" title="GNU General Public License" href="http://www.gnu.org/copyleft/gpl.html#SEC1">'."\n";
		if (empty($conf->global->MAIN_OPTIMIZEFORTEXTBROWSER)) print '<link rel="author" title="Dolibarr Development Team" href="http://www.dolibarr.org">'."\n";


		// Displays title
		$appli='Dolibarr';
		if (!empty($conf->global->MAIN_APPLICATION_TITLE)) $appli=$conf->global->MAIN_APPLICATION_TITLE;

		if ($title) print '<title>'.dol_htmlentities($appli.' - '.$title).'</title>';
		else print "<title>".dol_htmlentities($appli)."</title>";
		print "\n";

		$ext='';
		print '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">';
		//print '<!-- Ionicons -->';
		//print '<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">';
		//print '<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css" />';

		if (! empty($conf->dol_use_jmobile)) $ext='version='.urlencode(DOL_VERSION);

		if (! defined('DISABLE_JQUERY') && ! $disablejs && $conf->use_javascript_ajax)
		{
			print '<!-- Includes CSS for JQuery (Ajax library) -->'."\n";
			$jquerytheme = 'smoothness';
			if (!empty($conf->global->MAIN_USE_JQUERY_THEME)) $jquerytheme = $conf->global->MAIN_USE_JQUERY_THEME;
			if (constant('JS_JQUERY_UI')) print '<link rel="stylesheet" type="text/css" href="'.JS_JQUERY_UI.'jquery-ui.min.css'.($ext?'?'.$ext:'').'" />'."\n";  // JQuery
			else print '<link rel="stylesheet" type="text/css" href="'.DOL_URL_ROOT.'/includes/jquery/css/'.$jquerytheme.'/jquery-ui-latest.custom.css'.($ext?'?'.$ext:'').'" />'."\n";    // JQuery
			print '<link rel="stylesheet" type="text/css" href="'.DOL_URL_ROOT.'/includes/jquery/plugins/tiptip/tipTip.css'.($ext?'?'.$ext:'').'" />'."\n";                           // Tooltip
			///rqc print '<link rel="stylesheet" type="text/css" href="'.DOL_URL_ROOT.'/includes/jquery/plugins/jnotify/jquery.jnotify-alt.min.css'.($ext?'?'.$ext:'').'" />'."\n";          // JNotify
			/*if (! empty($conf->global->MAIN_USE_JQUERY_FILEUPLOAD) || (defined('REQUIRE_JQUERY_FILEUPLOAD') && constant('REQUIRE_JQUERY_FILEUPLOAD')))     // jQuery fileupload
			{
				print '<link rel="stylesheet" type="text/css" href="'.DOL_URL_ROOT.'/includes/jquery/plugins/fileupload/css/jquery.fileupload-ui.css'.($ext?'?'.$ext:'').'" />'."\n";
			}*/
			if (! empty($conf->global->MAIN_USE_JQUERY_DATATABLES) || (defined('REQUIRE_JQUERY_DATATABLES') && constant('REQUIRE_JQUERY_DATATABLES')))     // jQuery datatables
			{
				//print '<link rel="stylesheet" type="text/css" href="'.DOL_URL_ROOT.'/includes/jquery/plugins/datatables/css/jquery.dataTables.css'.($ext?'?'.$ext:'').'" />'."\n";
				print '<link rel="stylesheet" type="text/css" href="'.DOL_URL_ROOT.'/includes/jquery/plugins/datatables/css/jquery.dataTables_jui.css'.($ext?'?'.$ext:'').'" />'."\n";
				print '<link rel="stylesheet" type="text/css" href="'.DOL_URL_ROOT.'/includes/jquery/plugins/datatables/extras/ColReorder/css/ColReorder.css'.($ext?'?'.$ext:'').'" />'."\n";
				print '<link rel="stylesheet" type="text/css" href="'.DOL_URL_ROOT.'/includes/jquery/plugins/datatables/extras/ColVis/css/ColVis.css'.($ext?'?'.$ext:'').'" />'."\n";
				//print '<link rel="stylesheet" type="text/css" href="'.DOL_URL_ROOT.'/includes/jquery/plugins/datatables/extras/ColVis/css/ColVisAlt.css'.($ext?'?'.$ext:'').'" />'."\n";
				print '<link rel="stylesheet" type="text/css" href="'.DOL_URL_ROOT.'/includes/jquery/plugins/datatables/extras/TableTools/css/TableTools.css'.($ext?'?'.$ext:'').'" />'."\n";
			}
			if (! empty($conf->global->MAIN_USE_JQUERY_MULTISELECT) || (defined('REQUIRE_JQUERY_MULTISELECT') && constant('REQUIRE_JQUERY_MULTISELECT')))     // jQuery multiselect
			{
				print '<link rel="stylesheet" type="text/css" href="'.DOL_URL_ROOT.'/includes/jquery/plugins/multiselect/css/ui.multiselect.css'.($ext?'?'.$ext:'').'" />'."\n";
			}
			// jQuery Timepicker
			if (! empty($conf->global->MAIN_USE_JQUERY_TIMEPICKER) || defined('REQUIRE_JQUERY_TIMEPICKER'))
			{
				print '<link rel="stylesheet" type="text/css" href="'.DOL_URL_ROOT.'/includes/jquery/plugins/timepicker/jquery-ui-timepicker-addon.css'.($ext?'?'.$ext:'').'" />'."\n";
			}
			// jQuery jMobile
			if (! empty($conf->global->MAIN_USE_JQUERY_JMOBILE) || defined('REQUIRE_JQUERY_JMOBILE') || ! empty($conf->dol_use_jmobile))
			{
				print '<link rel="stylesheet" type="text/css" href="'.DOL_URL_ROOT.'/includes/jquery/plugins/mobile/jquery.mobile-latest.min.css'.($ext?'?'.$ext:'').'" />'."\n";
			}

		}

		print '<!-- Includes CSS for Dolibarr theme -->'."\n";
		// Output style sheets (optioncss='print' or ''). Note: $conf->css looks like '/theme/eldy/style.css.php'
		//$themepath=dol_buildpath((empty($conf->global->MAIN_FORCETHEMEDIR)?'':$conf->global->MAIN_FORCETHEMEDIR).$conf->css,1);
		$themepath=dol_buildpath($conf->css,1);
		$themesubdir='';
		if (! empty($conf->modules_parts['theme'])) // This slow down
		{
			foreach($conf->modules_parts['theme'] as $reldir)
			{
				if (file_exists(dol_buildpath($reldir.$conf->css, 0)))
				{
					$themepath=dol_buildpath($reldir.$conf->css, 1);
					$themesubdir=$reldir;
					break;
				}
			}
		}
		$themeparam='?lang='.$langs->defaultlang.'&amp;theme='.$conf->theme.(GETPOST('optioncss')?'&amp;optioncss='.GETPOST('optioncss','alpha',1):'').'&amp;userid='.$user->id.'&amp;entity='.$conf->entity;
		$themeparam.=($ext?'&amp;'.$ext:'');
		if (! empty($_SESSION['dol_resetcache'])) $themeparam.='&amp;dol_resetcache='.$_SESSION['dol_resetcache'];
		if (GETPOST('dol_hide_topmenu'))           { $themeparam.='&amp;dol_hide_topmenu='.GETPOST('dol_hide_topmenu','int'); }
		if (GETPOST('dol_hide_leftmenu'))          { $themeparam.='&amp;dol_hide_leftmenu='.GETPOST('dol_hide_leftmenu','int'); }
		if (GETPOST('dol_optimize_smallscreen'))   { $themeparam.='&amp;dol_optimize_smallscreen='.GETPOST('dol_optimize_smallscreen','int'); }
		if (GETPOST('dol_no_mouse_hover'))         { $themeparam.='&amp;dol_no_mouse_hover='.GETPOST('dol_no_mouse_hover','int'); }
		if (GETPOST('dol_use_jmobile'))            { $themeparam.='&amp;dol_use_jmobile='.GETPOST('dol_use_jmobile','int'); $conf->dol_use_jmobile=GETPOST('dol_use_jmobile','int'); }
		//print 'themepath='.$themepath.' themeparam='.$themeparam;exit;
		print '<link rel="stylesheet" type="text/css" title="default" href="'.$themepath.$themeparam.'">'."\n";

		// CSS forced by modules (relative url starting with /)
		if (! empty($conf->modules_parts['css']))
		{
			$arraycss=(array) $conf->modules_parts['css'];
			foreach($arraycss as $modcss => $filescss)
			{
				$filescss=(array) $filescss;    // To be sure filecss is an array
				foreach($filescss as $cssfile)
				{
					if (empty($cssfile)) dol_syslog("Warning: module ".$modcss." declared a css path file into its descriptor that is empty.", LOG_WARNING);
					// cssfile is a relative path
					print '<!-- Includes CSS added by module '.$modcss. ' -->'."\n".'<link rel="stylesheet" type="text/css" title="default" href="'.dol_buildpath($cssfile,1);
					// We add params only if page is not static, because some web server setup does not return content type text/css if url has parameters, so browser cache is not used.
					if (!preg_match('/\.css$/i',$cssfile)) print $themeparam;
					print '">'."\n";
				}
			}
		}
		// CSS forced by page in top_htmlhead call (relative url starting with /)
		if (is_array($arrayofcss))
		{
			foreach($arrayofcss as $cssfile)
			{
				print '<!-- Includes CSS added by page -->'."\n".'<link rel="stylesheet" type="text/css" title="default" href="'.dol_buildpath($cssfile,1);
				// We add params only if page is not static, because some web server setup does not return content type text/css if url has parameters and browser cache is not used.
				if (!preg_match('/\.css$/i',$cssfile)) print $themeparam;
				print '">'."\n";
			}
		}

		// Output standard javascript links
		if (! defined('DISABLE_JQUERY') && ! $disablejs && ! empty($conf->use_javascript_ajax))
		{
			// JQuery. Must be before other includes
			print '<!-- Includes JS for JQuery -->'."\n";
			if (constant('JS_JQUERY')) print '<script type="text/javascript" src="'.JS_JQUERY.'jquery.min.js'.($ext?'?'.$ext:'').'"></script>'."\n";
			else print '<script type="text/javascript" src="'.DOL_URL_ROOT.'/includes/jquery/js/jquery-latest.min.js'.($ext?'?'.$ext:'').'"></script>'."\n";
			if (constant('JS_JQUERY_UI')) print '<script type="text/javascript" src="'.JS_JQUERY_UI.'jquery-ui.min.js'.($ext?'?'.$ext:'').'"></script>'."\n";
			else print '<script type="text/javascript" src="'.DOL_URL_ROOT.'/includes/jquery/js/jquery-ui-latest.custom.min.js'.($ext?'?'.$ext:'').'"></script>'."\n";
			//print '<script type="text/javascript" src="'.DOL_URL_ROOT.'/includes/jquery/plugins/tablednd/jquery.tablednd.0.6.min.js'.($ext?'?'.$ext:'').'"></script>'."\n";
			//print '<script type="text/javascript" src="'.DOL_URL_ROOT.'/includes/jquery/plugins/tiptip/jquery.tipTip.min.js'.($ext?'?'.$ext:'').'"></script>'."\n";
			// jQuery Layout
			if (empty($conf->dol_use_jmobile) && ! empty($conf->global->MAIN_MENU_USE_JQUERY_LAYOUT) || defined('REQUIRE_JQUERY_LAYOUT'))
			{
				print '<script type="text/javascript" src="'.DOL_URL_ROOT.'/includes/jquery/plugins/layout/jquery.layout-latest.js'.($ext?'?'.$ext:'').'"></script>'."\n";
			}
			// jQuery jnotify
			if (empty($conf->global->MAIN_DISABLE_JQUERY_JNOTIFY) && ! defined('DISABLE_JQUERY_JNOTIFY'))
			{
				print '<script type="text/javascript" src="'.DOL_URL_ROOT.'/includes/jquery/plugins/jnotify/jquery.jnotify.min.js'.($ext?'?'.$ext:'').'"></script>'."\n";
				print '<script type="text/javascript" src="'.DOL_URL_ROOT.'/core/js/jnotify.js'.($ext?'?'.$ext:'').'"></script>'."\n";
			}
			// jQuery blockUI
			if (! empty($conf->global->MAIN_USE_JQUERY_BLOCKUI) || defined('REQUIRE_JQUERY_BLOCKUI'))
			{
				print '<script type="text/javascript" src="'.DOL_URL_ROOT.'/includes/jquery/plugins/blockUI/jquery.blockUI.js'.($ext?'?'.$ext:'').'"></script>'."\n";
				print '<script type="text/javascript">'."\n";
				print 'var indicatorBlockUI = \''.DOL_URL_ROOT."/theme/".$conf->theme."/img/working2.gif".'\';'."\n";
				print '</script>'."\n";
				print '<script type="text/javascript" src="'.DOL_URL_ROOT.'/core/js/blockUI.js'.($ext?'?'.$ext:'').'"></script>'."\n";
			}
			// Flot
			if (empty($conf->global->MAIN_DISABLE_JQUERY_FLOT))
			{
				if (constant('JS_JQUERY_FLOT'))
				{
					print '<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="/javascript/excanvas/excanvas.min.js'.($ext?'?'.$ext:'').'"></script><![endif]-->'."\n";
					print '<script type="text/javascript" src="'.JS_JQUERY_FLOT.'jquery.flot.js'.($ext?'?'.$ext:'').'"></script>'."\n";
					print '<script type="text/javascript" src="'.JS_JQUERY_FLOT.'jquery.flot.pie.js'.($ext?'?'.$ext:'').'"></script>'."\n";
					print '<script type="text/javascript" src="'.JS_JQUERY_FLOT.'jquery.flot.stack.js'.($ext?'?'.$ext:'').'"></script>'."\n";
				}
				else
				{
					print '<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="'.DOL_URL_ROOT.'/includes/jquery/plugins/flot/excanvas.min.js'.($ext?'?'.$ext:'').'"></script><![endif]-->'."\n";
					print '<script type="text/javascript" src="'.DOL_URL_ROOT.'/includes/jquery/plugins/flot/jquery.flot.min.js'.($ext?'?'.$ext:'').'"></script>'."\n";
					print '<script type="text/javascript" src="'.DOL_URL_ROOT.'/includes/jquery/plugins/flot/jquery.flot.pie.min.js'.($ext?'?'.$ext:'').'"></script>'."\n";
					print '<script type="text/javascript" src="'.DOL_URL_ROOT.'/includes/jquery/plugins/flot/jquery.flot.stack.min.js'.($ext?'?'.$ext:'').'"></script>'."\n";
				}
			}
			// jQuery jeditable
			if (! empty($conf->global->MAIN_USE_JQUERY_JEDITABLE))
			{
				print '<script type="text/javascript" src="'.DOL_URL_ROOT.'/includes/jquery/plugins/jeditable/jquery.jeditable.min.js'.($ext?'?'.$ext:'').'"></script>'."\n";
				print '<script type="text/javascript" src="'.DOL_URL_ROOT.'/includes/jquery/plugins/jeditable/jquery.jeditable.ui-datepicker.js'.($ext?'?'.$ext:'').'"></script>'."\n";
				print '<script type="text/javascript" src="'.DOL_URL_ROOT.'/includes/jquery/plugins/jeditable/jquery.jeditable.ui-autocomplete.js'.($ext?'?'.$ext:'').'"></script>'."\n";
				print '<script type="text/javascript">'."\n";
				print 'var urlSaveInPlace = \''.DOL_URL_ROOT.'/core/ajax/saveinplace.php\';'."\n";
				print 'var urlLoadInPlace = \''.DOL_URL_ROOT.'/core/ajax/loadinplace.php\';'."\n";
				print 'var tooltipInPlace = \''.$langs->transnoentities('ClickToEdit').'\';'."\n";
				print 'var placeholderInPlace = \''.$langs->trans('ClickToEdit').'\';'."\n";
				print 'var cancelInPlace = \''.$langs->trans('Cancel').'\';'."\n";
				print 'var submitInPlace = \''.$langs->trans('Ok').'\';'."\n";
				print 'var indicatorInPlace = \'<img src="'.DOL_URL_ROOT."/theme/".$conf->theme."/img/working.gif".'">\';'."\n";
				print '</script>'."\n";
				print '<script type="text/javascript" src="'.DOL_URL_ROOT.'/core/js/editinplace.js'.($ext?'?'.$ext:'').'"></script>'."\n";
				print '<script type="text/javascript" src="'.DOL_URL_ROOT.'/includes/jquery/plugins/jeditable/jquery.jeditable.ckeditor.js'.($ext?'?'.$ext:'').'"></script>'."\n";
			}
			// jQuery File Upload
			/*
			if (! empty($conf->global->MAIN_USE_JQUERY_FILEUPLOAD) || (defined('REQUIRE_JQUERY_FILEUPLOAD') && constant('REQUIRE_JQUERY_FILEUPLOAD')))
			{
				print '<script type="text/javascript" src="'.DOL_URL_ROOT.'/includes/jquery/plugins/template/tmpl.min'.$ext.'"></script>'."\n";
				print '<script type="text/javascript" src="'.DOL_URL_ROOT.'/includes/jquery/plugins/fileupload/js/jquery.iframe-transport'.$ext.'"></script>'."\n";
				print '<script type="text/javascript" src="'.DOL_URL_ROOT.'/includes/jquery/plugins/fileupload/js/jquery.fileupload'.$ext.'"></script>'."\n";
				print '<script type="text/javascript" src="'.DOL_URL_ROOT.'/includes/jquery/plugins/fileupload/js/jquery.fileupload-fp'.$ext.'"></script>'."\n";
				print '<script type="text/javascript" src="'.DOL_URL_ROOT.'/includes/jquery/plugins/fileupload/js/jquery.fileupload-ui'.$ext.'"></script>'."\n";
				print '<script type="text/javascript" src="'.DOL_URL_ROOT.'/includes/jquery/plugins/fileupload/js/jquery.fileupload-jui'.$ext.'"></script>'."\n";
				print '<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE8+ -->'."\n";
				print '<!--[if gte IE 8]><script type="text/javascript" src="'.DOL_URL_ROOT.'/includes/jquery/plugins/fileupload/js/cors/jquery.xdr-transport'.$ext.'"></script><![endif]-->'."\n";
			}*/
			// jQuery DataTables
			if (! empty($conf->global->MAIN_USE_JQUERY_DATATABLES) || (defined('REQUIRE_JQUERY_DATATABLES') && constant('REQUIRE_JQUERY_DATATABLES')))
			{
				print '<script type="text/javascript" src="'.DOL_URL_ROOT.'/includes/jquery/plugins/datatables/js/jquery.dataTables.min.js'.($ext?'?'.$ext:'').'"></script>'."\n";
				print '<script type="text/javascript" src="'.DOL_URL_ROOT.'/includes/jquery/plugins/datatables/extras/ColReorder/js/ColReorder.min.js'.($ext?'?'.$ext:'').'"></script>'."\n";
				print '<script type="text/javascript" src="'.DOL_URL_ROOT.'/includes/jquery/plugins/datatables/extras/ColVis/js/ColVis.min.js'.($ext?'?'.$ext:'').'"></script>'."\n";
				print '<script type="text/javascript" src="'.DOL_URL_ROOT.'/includes/jquery/plugins/datatables/extras/TableTools/js/TableTools.min.js'.($ext?'?'.$ext:'').'"></script>'."\n";
			}
			// jQuery Multiselect
			if (! empty($conf->global->MAIN_USE_JQUERY_MULTISELECT) || (defined('REQUIRE_JQUERY_MULTISELECT') && constant('REQUIRE_JQUERY_MULTISELECT')))
			{
				//print '<script type="text/javascript" src="'.DOL_URL_ROOT.'/includes/jquery/plugins/multiselect/js/ui.multiselect.js'.($ext?'?'.$ext:'').'"></script>'."\n";
			}
			// jQuery Timepicker
			if (! empty($conf->global->MAIN_USE_JQUERY_TIMEPICKER) || defined('REQUIRE_JQUERY_TIMEPICKER'))
			{
				print '<script type="text/javascript" src="'.DOL_URL_ROOT.'/includes/jquery/plugins/timepicker/jquery-ui-timepicker-addon.js'.($ext?'?'.$ext:'').'"></script>'."\n";
				print '<script type="text/javascript" src="'.DOL_URL_ROOT.'/core/js/timepicker.js.php?lang='.$langs->defaultlang.($ext?'&amp;'.$ext:'').'"></script>'."\n";
			}
			// jQuery jMobile
			if (! empty($conf->global->MAIN_USE_JQUERY_JMOBILE) || defined('REQUIRE_JQUERY_JMOBILE') || (! empty($conf->dol_use_jmobile) && $conf->dol_use_jmobile > 0))
			{
				// We must force not using ajax because cache of jquery does not load js of other pages.
				// This also increase seriously speed onto mobile device where complex js code is very slow and memory very low.
				// Note: dol_use_jmobile=1 use jmobile without ajax, dol_use_jmobile=2 use jmobile with ajax
				if (empty($conf->dol_use_jmobile) || ($conf->dol_use_jmobile != 2 && $conf->dol_use_jmobile != 3))
				{
					print '<script type="text/javascript">
					$(document).bind("mobileinit", function(){
						$.extend(  $.mobile , {
							autoInitializePage : true,  /* We need this to run jmobile */
							/* loadingMessage : \'xxxxx\', */
							touchOverflowEnabled : true,
							defaultPageTransition : \'none\',
							defaultDialogTransition : \'none\',
							ajaxEnabled : false         /* old param was ajaxFormsEnabled and ajaxLinksEnabled */
						});
					});
				</script>';
			}
			if (empty($conf->dol_use_jmobile) || $conf->dol_use_jmobile != 3) print '<script type="text/javascript" src="'.DOL_URL_ROOT.'/includes/jquery/plugins/mobile/jquery.mobile-latest.min.js'.($ext?'?'.$ext:'').'"></script>'."\n";
		}
	}

	if (! $disablejs && ! empty($conf->use_javascript_ajax))
	{
			// CKEditor
		if (! empty($conf->fckeditor->enabled) && (empty($conf->global->FCKEDITOR_EDITORNAME) || $conf->global->FCKEDITOR_EDITORNAME == 'ckeditor'))
		{
			print '<!-- Includes JS for CKEditor -->'."\n";
			$pathckeditor=DOL_URL_ROOT.'/includes/ckeditor/';
			$jsckeditor='ckeditor.js';
				if (constant('JS_CKEDITOR'))    // To use external ckeditor 4 js lib
				{
					$pathckeditor=constant('JS_CKEDITOR');
				}
				print '<script type="text/javascript">';
				print 'var CKEDITOR_BASEPATH = \''.$pathckeditor.'\';'."\n";
				print 'var ckeditorConfig = \''.dol_buildpath($themesubdir.'/theme/'.$conf->theme.'/ckeditor/config.js',1).'\';'."\n";      // $themesubdir='' in standard usage
				print 'var ckeditorFilebrowserBrowseUrl = \''.DOL_URL_ROOT.'/core/filemanagerdol/browser/default/browser.php?Connector='.DOL_URL_ROOT.'/core/filemanagerdol/connectors/php/connector.php\';'."\n";
				print 'var ckeditorFilebrowserImageBrowseUrl = \''.DOL_URL_ROOT.'/core/filemanagerdol/browser/default/browser.php?Type=Image&Connector='.DOL_URL_ROOT.'/core/filemanagerdol/connectors/php/connector.php\';'."\n";
				print '</script>'."\n";
				print '<script type="text/javascript" src="'.$pathckeditor.$jsckeditor.($ext?'?'.$ext:'').'"></script>'."\n";
			}

			// Global js function
			print '<!-- Includes JS of Dolibarr -->'."\n";
			print '<script type="text/javascript" src="'.DOL_URL_ROOT.'/core/js/lib_head.js'.($ext?'?'.$ext:'').'"></script>'."\n";

			// Add datepicker default options
			//print '<script type="text/javascript" src="'.DOL_URL_ROOT.'/core/js/datepicker.js.php?lang='.$langs->defaultlang.($ext?'&amp;'.$ext:'').'"></script>'."\n";

			// JS forced by modules (relative url starting with /)
			if (! empty($conf->modules_parts['js']))        // $conf->modules_parts['js'] is array('module'=>array('file1','file2'))
			{
				$arrayjs=(array) $conf->modules_parts['js'];
				foreach($arrayjs as $modjs => $filesjs)
				{
					$filesjs=(array) $filesjs;  // To be sure filejs is an array
					foreach($filesjs as $jsfile)
					{
						// jsfile is a relative path
						print '<!-- Include JS added by module '.$modjs. '-->'."\n".'<script type="text/javascript" src="'.dol_buildpath($jsfile,1).'"></script>'."\n";
					}
				}
			}
			// JS forced by page in top_htmlhead (relative url starting with /)
			if (is_array($arrayofjs))
			{
				print '<!-- Includes JS added by page -->'."\n";
				foreach($arrayofjs as $jsfile)
				{
					if (preg_match('/^http/i',$jsfile))
					{
						print '<script type="text/javascript" src="'.$jsfile.'"></script>'."\n";
					}
					else
					{
						if (! preg_match('/^\//',$jsfile)) $jsfile='/'.$jsfile; // For backward compatibility
						print '<script type="text/javascript" src="'.dol_buildpath($jsfile,1).'"></script>'."\n";
					}
				}
			}
		}
		if ($conf->global->POA_SCRIPT)
		{
			include DOL_DOCUMENT_ROOT.'/poa/js/scriptpoa.js.php';
		}

		if (! empty($head)) print $head."\n";
		if (! empty($conf->global->MAIN_HTML_HEADER)) print $conf->global->MAIN_HTML_HEADER."\n";

		print "</head>\n\n";
	}

	$conf->headerdone=1;    // To tell header was output
}

?>