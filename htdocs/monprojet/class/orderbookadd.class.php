<?php


class Orderbookadd extends Orderbook
{

    /**
     *    	Return HTML code to output a photo
     *
     *    	@param	string		$modulepart			Key to define module concerned ('societe', 'userphoto', 'memberphoto')
     *     	@param  object		$object				Object containing data to retrieve file name
     * 		@param	int			$width				Width of photo
     * 		@param	int			$height				Height of photo (auto if 0)
     * 		@param	int			$caneditfield		Add edit fields
     * 		@param	string		$cssclass			CSS name to use on img for photo
     * 		@param	string		$imagesize		    'mini', 'small' or '' (original)
     *      @param  int         $addlinktofullsize  Add link to fullsize image
     *      @param  int         $cache              1=Accept to use image in cache
     * 	  	@return string    						HTML code to output photo
     */

    static function showphoto($imageview,$document,$contrat,$modulepart, $object, $projectstatic,$width=100, $height=0, $caneditfield=0, $cssclass='photowithmargin', $imagesize='', $addlinktofullsize=1, $cache=0,$docext='')
    {
    	global $conf,$langs;

    	$entity = (! empty($object->entity) ? $object->entity : $conf->entity);
    	$id = (! empty($object->id) ? $object->id : $object->rowid);

    	$ret='';$dir='';$file='';$originalfile='';$altfile='';$email='';
    	$id = (! empty($object->id) ? $object->id : $object->rowid);
    	$id = (! empty($task_time->id) ? $task_time->id : $task_time->rowid);

    	$dir=$conf->$modulepart->dir_output[$entity];
    	$dir=$conf->monprojet->multidir_output[$entity];
    	
    	$file = $projectstatic->ref.'/contrat/'.$contrat->ref.'/';
	  		//$dirfile= '/'.$projectstatic->ref.'/'.$object->ref.'/'.$id;
    	$originalfile = $projectstatic->ref.'/contrat/'.$contrat->ref.'/';
    	$origdocument = $document;
    	$info_fichero = pathinfo($document);
    	if (isset($info_fichero['extension']) && $info_fichero['extension']!=strtolower($info_fichero['extension']))
    		$document=$info_fichero['filename'].'.'.strtolower($info_fichero['extension']);

    	$object->photo = $document;
    	if (! empty($object->photo)) 
    	{
    		if ((string) $imagesize == 'mini') 
    			$file.=get_exdir($id, 2, 0, 0, $object, $modulepart).'thumbs/'.getImageFileNameForSize($object->photo, '_mini');
    		elseif ((string) $imagesize == 'small') 
    			$file.=get_exdir($id, 2, 0, 0, $object, $modulepart).getImageFileNameForSize($object->photo, '_small');
    		else 
    			$file.=get_exdir($id, 2, 0, 0, $object, $modulepart).'thumbs/'.$object->photo;
    		$originalfile.=get_exdir($id, 2, 0, 0, $object, $modulepart).$id.'/'.$origdocument;
    	}
    	if (! empty($conf->global->MAIN_OLD_IMAGE_LINKS)) $altfile=$object->id.".jpg";
        		// For backward compatibility
    	$email=$object->email;
        	//echo '<hr>file '.$file;
    	if ($dir)
    	{
    		$modulepart = 'monprojet';
    		if ($file && file_exists($dir."/".$file))
    		{
    			if ($addlinktofullsize) $ret.='<a href="'.DOL_URL_ROOT.'/monprojet/viewimage.php?modulepart='.$modulepart.'&entity='.$entity.'&file='.urlencode($originalfile).'&cache='.$cache.'" target="_blank">';
    			$ret.='<img alt="'.$docext.'" id="photologo'.(preg_replace('/[^a-z]/i','_',$file)).'" class="'.$cssclass.'" '.($width?' width="'.$width.'"':'').($height?' height="'.$height.'"':'').' src="'.DOL_URL_ROOT.'/monprojet/viewimage.php?modulepart='.$modulepart.'&entity='.$entity.'&file='.urlencode($file).'&cache='.$cache.'">';
    			if ($addlinktofullsize) $ret.='</a>';
    		}
    		else if ($altfile && file_exists($dir."/".$altfile))
    		{
    			if ($addlinktofullsize) $ret.='<a href="'.DOL_URL_ROOT.'/monprojet/viewimage.php?modulepart='.$modulepart.'&entity='.$entity.'&file='.urlencode($originalfile).'&cache='.$cache.'" target="_blank">';
    			$ret.='<img alt="Photo alt" id="photologo'.(preg_replace('/[^a-z]/i','_',$file)).'" class="'.$cssclass.'" '.($width?' width="'.$width.'"':'').($height?' height="'.$height.'"':'').' src="'.DOL_URL_ROOT.'/monprojet/viewimage.php?modulepart='.$modulepart.'&entity='.$entity.'&file='.urlencode($altfile).'&cache='.$cache.'">';
    			if ($addlinktofullsize) $ret.='</a>';
    		}
    		else
    		{
    			$nophoto='/public/theme/common/nophoto.png';
				if (in_array($modulepart,array('userphoto','contact')))	// For module that are "physical" users
				{
					$nophoto='/public/theme/common/user_anonymous.png';
					if ($object->gender == 'man') $nophoto='/public/theme/common/user_man.png';
					if ($object->gender == 'woman') $nophoto='/public/theme/common/user_woman.png';
				}

				if (! empty($conf->gravatar->enabled) && $email)
				{
	                /**
	                 * @see https://gravatar.com/site/implement/images/php/
	                 */
	                global $dolibarr_main_url_root;
	                $ret.='<!-- Put link to gravatar -->';
                    $ret.='<img class="photo'.$modulepart.($cssclass?' '.$cssclass:'').'" alt="Gravatar avatar" title="'.$email.' Gravatar avatar" border="0"'.($width?' width="'.$width.'"':'').($height?' height="'.$height.'"':'').' src="https://www.gravatar.com/avatar/'.dol_hash(strtolower(trim($email)),3).'?s='.$width.'&d='.urlencode(dol_buildpath($nophoto,2)).'">';	// gravatar need md5 hash
                }
                else
                {
                	$ret.='<img class="photo'.$modulepart.($cssclass?' '.$cssclass:'').'" alt="No photo" border="0"'.($width?' width="'.$width.'"':'').($height?' height="'.$height.'"':'').' src="'.DOL_URL_ROOT.$nophoto.'">';
                }
            }

            if ($caneditfield)
            {
            	if ($object->photo) $ret.="<br>\n";
            	$ret.='<table class="nobordernopadding hideonsmartphone">';
            	if ($object->photo) $ret.='<tr><td align="center"><input type="checkbox" class="flat photodelete" name="deletephoto" id="photodelete"> '.$langs->trans("Delete").'<br><br></td></tr>';
            	$ret.='<tr><td>'.$langs->trans("PhotoFile").'</td></tr>';
            	$ret.='<tr><td><input type="file" class="flat" name="photo" id="photoinput"></td></tr>';
            	$ret.='</table>';
            }

        }
        else dol_print_error('','Call of showphoto with wrong parameters');

        return $ret;
    }


	/**
	 *    	Return HTML code to output a photo
	 *
	 *    	@param	string		$modulepart		Key to define module concerned ('societe', 'userphoto', 'memberphoto')
	 *     	@param  Object		$object			Object containing data to retrieve file name
	 * 		@param	int			$width			Width of photo
	 * 	  	@return string    					HTML code to output photo
	*/
	function showphotox($imageview,$task_time,$document,$object,$projectstatic,$width=100,$docext='')
	{
		global $conf;
		$modulepart = 'project_task';
		$entity = (! empty($projectstatic->entity) ? $projectstatic->entity : $conf->entity);
		$id = (! empty($task_time->id) ? $task_time->id : $task_time->rowid);

		$ret='';$dir='';$file='';$altfile='';$email='';
		if ($imageview == 'ini')
		{
			$dir=$conf->projet->multidir_output[$entity];
			$dir.= '/'.$projectstatic->ref.'/'.$object->ref.'/';
			$dirfile = $projectstatic->ref.'/'.$object->ref.'/';
			$info_fichero = pathinfo($document);
			if (isset($info_fichero['extension']) && $info_fichero['extension']!=strtolower($info_fichero['extension']))
				$file=$info_fichero['filename'].'.'.strtolower($info_fichero['extension']);
			else
				$file= $document;
			$file=preg_replace('/(\.png|\.gif|\.jpg|\.jpeg|\.bmp)/i','_small\\1',$file);
			if ($id) $file=$id.'/images/thumbs/'.$file;
			$namephoto = 'photoini';
		}
		if ($imageview == 'doc')
		{
			$dir=$conf->projet->multidir_output[$entity];
			$dir.= '/'.$projectstatic->ref.'/'.$object->ref.'/';
			$dirfile = $projectstatic->ref.'/'.$object->ref.'/';
			$info_fichero = pathinfo($document);
			if (isset($info_fichero['extension']) && $info_fichero['extension']!=strtolower($info_fichero['extension']))
				$file=$info_fichero['filename'].'.'.strtolower($info_fichero['extension']);
			else
				$file= $document;
			//$file=preg_replace('/(\.png|\.gif|\.jpg|\.jpeg|\.bmp)/i','_small\\1',$file);
			if ($id) $file=$id.'/'.$file;
			$namephoto = ($docext?$docext:$imageview);
		}
		if ($imageview == 'fin')
		{
			$dir=$conf->projet->multidir_output[$entity];
			$dir.= '/'.$projectstatic->ref.'/'.$object->ref.'/';
			$dirfile = $projectstatic->ref.'/'.$object->ref.'/';
			$info_fichero = pathinfo($document);
			if (isset($info_fichero['extension']) && $info_fichero['extension']!=strtolower($info_fichero['extension']))
				$file=$info_fichero['filename'].'.'.strtolower($info_fichero['extension']);
			else
				$file= $document;

			$file=preg_replace('/(\.png|\.gif|\.jpg|\.jpeg|\.bmp)/i','_small\\1',$file);
	  		//echo '<hr>file '.$file;
			if ($id) $file=$id.'/thumbs/'.$file;
	  		//echo '<hr>files '.$file;

			$namephoto = 'photofin';
		}
	 //echo '<hr>'.$file;
	//echo '<hr>exit '.file_exists($dir."/".$file);
		if ($dir)
		{
			$cache='0';
			if ($file && file_exists($dir.$file))
			{
				$dirfile.= $file;
				// TODO Link to large image
				$ret.='<a href="'.DOL_URL_ROOT.'/viewimage.php?modulepart='.$modulepart.'&entity='.$entity.'&file='.urlencode($dirfile).'&cache='.$cache.'">';
				$ret.='<img alt="'.$namephoto.'" id="photologo'.(preg_replace('/[^a-z]/i','_',$dirfile)).'" class="photologo" border="0" width="'.$width.'" src="'.DOL_URL_ROOT.'/viewimage.php?modulepart='.$modulepart.'&entity='.$entity.'&file='.urlencode($dirfile).'&cache='.$cache.'">';
				$ret.='</a>';
			}
			elseif ($altfile && file_exists($dir."/".$altfile))
			{
				$ret.='<a href="'.DOL_URL_ROOT.'/viewimage.php?modulepart='.$modulepart.'&entity='.$entity.'&file='.urlencode($file).'&cache='.$cache.'">';
				$ret.='<img alt="Photo alt" id="photologo'.(preg_replace('/[^a-z]/i','_',$file)).'" class="photologo" border="0" width="'.$width.'" src="'.DOL_URL_ROOT.'/viewimage.php?modulepart='.$modulepart.'&entity='.$entity.'&file='.urlencode($altfile).'&cache='.$cache.'">';
				$ret.='</a>';
			}
			else
			{
				if (! empty($conf->gravatar->enabled) && $email)
				{
					global $dolibarr_main_url_root;
					$ret.='<!-- Put link to gravatar -->';
					$ret.='<img alt="Photo found on Gravatar" title="Photo Gravatar.com - email '.$email.'" border="0" width="'.$width.'" src="http://www.gravatar.com/avatar/'.dol_hash($email).'?s='.$width.'&d='.urlencode(dol_buildpath('/theme/common/nophoto.jpg',2)).'">';
				}
				else
				{
					$ret.='<img alt="No photo" border="0" width="'.$width.'" src="'.DOL_URL_ROOT.'/theme/common/nophoto.jpg">';
				}
			}
		}
		else dol_print_error('','Call of showphoto with wrong parameters');

		return $ret;
	}

}
?>