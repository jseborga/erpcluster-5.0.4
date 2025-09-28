<?php
if (!$error)
{
	foreach ($_POST['campo'] AS $j => $campo)
	{
		if (!$error)
		{
			$row = $data[$j];
			if (!empty($campo))
			{
				$obj->$campo = $data[$j];
			}
		}
	}
	$obj->entity = $conf->entity;
	$obj->fk_user_create = $user->id;
	$obj->fk_user_mod = $user->id;
	$obj->datec = $now;
	$obj->datem = $now;
	$obj->active = 1;
				//buscamos para no duplicar
	$res = $objTmp->fetch(0,$obj->ref);
	if ($res ==0)
	{
		$res = $obj->create($user,1);
		if ($res <=0)
		{
			$error++;
			setEventMessages($obj->error,$obj->errors,'errors');
		}
	}
	elseif($res >0)
	{
					//actualizamos , volvemos a armar
		foreach ($_POST['campo'] AS $j => $campo)
		{
			if (!$error)
			{
				$row = $data[$j];
				if (!empty($campo))
				{
					$objTmp->$campo = $data[$j];
				}
			}
		}
		$objTmp->fk_user_mod = $user->id;
		$objTmp->datem = $now;
		echo '<hr>'.$res = $objTmp->update($user);
		if ($res <=0)
		{
			$error++;
			setEventMessages($objTmp->error,$objTmp->errors,'errors');
		}
	}
}
if ($error)
	$action = 'create';
?>