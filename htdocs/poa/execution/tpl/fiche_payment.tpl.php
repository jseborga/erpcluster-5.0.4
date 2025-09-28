<?php
//recibiendo valores
$idp = GETPOST('idp');
//addpreventive
$display ='none';
if (isset($modal) && $modal == 'fichepay')
{
	print '<script type="text/javascript">
	$(window).load(function(){
		$("#fichepreventive").modal("show");
	});
</script>';
}
//$display = 'block';
print '<div id="'.$tagid.'" class="modal fade in" tabindex="-1" role="dialog" style="display: '.$display.'; margin-top:0px;" data-width="760;" aria-hidden="false">';
print '<div class="poa-modal">';
print '<div class="modal">';
print '<div class="modal-dialog modal-lg">';
print '<div class="modal-content">';

print '<div class="modal-header" style="background:#fff; color:#000; !important">';
print '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
print '<h4 class="modal-title">'.$langs->trans("Payment").': '.$objcont->array_options['options_ref_contrato'].': '.$objsoc->nom;
print '</div>';

print '<div class="modal-body" style="overflow-y: auto; color:#000; !important">';
if ($objact->fk_prev > 0 )
{

	//$aPrev[$object->id] = $object->gestion;
	if ($action == 'create' && $user->rights->poa->deve->crear)
	{

		$saldo = 0;
		foreach ((array) $aPreve AS $fk_poaprev => $gest)
		{
			$objcompr = new Poapartidacom($db);
			if ($objcompr->get_sum_pcp2($fk_poaprev,$i))
			{
				//total comprometido
				$totalcomp = $objcompr->total;
				$aTotalcomp[$gest] = $objcompr->aTotal[$gest];
				//array de comprom
				$aObjcomp[$gest] = $objcompr;
			}
			$objdeveng = new Poapartidadev($db);
			$lAdvance = false;
			if ($advance>0) $lAdvance = true;
			if ($objdeveng->get_sum_pcp2($fk_poaprev,$i,$lAdvance))
			{
				//total devengado
				$totaldev += $objdeveng->total;
				$aTotaldev[$gest]+= $objdeveng->aTotal[$gest];
				$aObjdev[$gest] = $objdeveng;
			}
		}
		$saldo = price2num($aTotalcomp[$gestion] - $aTotaldev[$gestion],'MT');
		if (price2num($saldo,'MT') > 0)
		{
			include_once DOL_DOCUMENT_ROOT.'/poa/process/tpl/addpayment.tpl.php';
		}
		else
		{
			if (price2num($saldo,'MT') == 0)
			{
				$objproc->fetch($idProcess);
				//actualizamos el estado del proceso a pagado
				if ($objproc->statut == 1 && $objproc->id == $idProcess)
				{
					$objproc->statut = 2;
					$objproc->update($user);
				}
			}
		}
	}
}
print '</div>'; //modal-body
print '</div>'; //modal-content
print '</div>'; //modal-dialog
print '</div>'; //modal modal-source
print '</div>'; //poa_modal
print '</div>'; //modal fade
?>