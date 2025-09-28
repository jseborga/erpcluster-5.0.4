<?php
if ($amount > 0)
{
	require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';

	$accountfrom=new Account($db);
	$accountfrom->fetch($fk_account_from);

	$accountto=new Account($db);
	$accountto->fetch($fk_account_dest);
	if ($fk_account_dest != $fk_account_from)
	{
		$error=0;
		$bank_line_id_from=0;
		$bank_line_id_to=0;
		$result=0;

		// By default, electronic transfert from bank to bank
		$typefrom='PRE';
		$typeto='VIR';
		if (!empty($fk_type))
		{
							// This is transfert of change
			$typefrom=$fk_type;
			$typeto=$fk_type;
		}
		if ($accountto->courant == 2)
		{
			$typeto='LIQ';
		}
		if ($accountfrom->courant == 2)
		{
			$typefrom='LIQ';
		}
		if ($aTypeop['ou'])
		{
			if (! $error) $bank_line_id_from = $accountfrom->addline($dateo, $typefrom, $label, -1*price2num($amount), $numdoc, $cat1, $user);
			if (! ($bank_line_id_from > 0))
			{
				$error++;
				setEventMessages($langs->trans('Error, al crear el movimiento '),null,'errors');
			}
		}
		if ($aTypeop['in'])
		{
			//cambiamos de id al usuario
			$fk_user_id_from = $user->id;
			if (! $error) $bank_line_id_to = $accountto->addline($dateo, $typeto, $label, price2num($amount), $numdoc, $cat1, $user);
			if (! ($bank_line_id_to > 0))
			{
				$error++;
				setEventMessages($langs->trans('Error, al crear el movimiento 2'),null,'errors');
			}
		}
		if ($aTypeop['ou'])
		{
			if (! $error) $result=$accountfrom->add_url_line($bank_line_id_from, $bank_line_id_to, DOL_URL_ROOT.'/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert');
			if (! ($result > 0))
			{
				$error++;
				setEventMessages($langs->trans('Error, al crear el movimiento 11 '),null,'errors');
			}
		}
		if ($aTypeop['in'])
		{
			if (! $error) $result=$accountto->add_url_line($bank_line_id_to, $bank_line_id_from, DOL_URL_ROOT.'/compta/bank/ligne.php?rowid=', '(banktransfert)', 'banktransfert');
			if (! ($result > 0))
			{
				$error++;
				setEventMessages($langs->trans('Error, al crear el movimiento 22'),null,'errors');
			}
		}
	}
	else
	{
		$error++;
		setEventMessages(null, $langs->trans("ErrorFromToAccountsMustDiffers"), 'errors');
		$action = 'close';
	}
}