<?php 
function compaginarTabla($pagina=1, $total_pag=1, $total_datos=1, $cantidad=12, $inicio=1, $fin=1,$sufijo=''){
	$previo=$pagina-1;
	$next=$pagina+1;
	if($pagina==1){$previo=1;}
	if($total_pag==$pagina){$next=$pagina;}
	if($total_datos==0){$inicio=0;$total_pag=1; $next=1;$previo=1;}
		$paginacion = '<div class="d-flex justify-content-between align-items-center flex-wrap">
							<div class="d-flex flex-wrap py-2 mr-3">
								<a href="#" class="btn btn-icon btn-sm btn-light mr-2 my-1" onClick="verPagina'.$sufijo.'(\'1\')">
									<i class="ki ki-bold-double-arrow-back icon-xs"></i>
								</a>
								<a href="#" class="btn btn-icon btn-sm btn-light mr-2 my-1" onClick="verPagina'.$sufijo.'('.$previo.')">
									<i class="ki ki-bold-arrow-back icon-xs"></i>
								</a>
								<div class="d-flex align-items-center py-3">
									<span class="text-muted">
										Pág. <input id="txtNroPaginaFooter'.$sufijo.'" type="text" value="'.$pagina.'" class="form-control" onkeypress="return solo_numero(event)" onKeyUp="if(event.keyCode==\'13\'){verPagina'.$sufijo.'(this.value);}" size="1" style="height:18px; text-align:center; display: initial;width: 30px;line-height: normal; padding: 0px;" >de '.$total_pag.'&nbsp&nbsp; 
									</span>
								</div>
								<a href="#" class="btn btn-icon btn-sm btn-light mr-2 my-1" onClick="verPagina'.$sufijo.'('.$next.')">
									<i class="ki ki-bold-arrow-next icon-xs"></i>
								</a>
								<a href="#" class="btn btn-icon btn-sm btn-light mr-2 my-1" onClick="verPagina'.$sufijo.'('.$total_pag.')">
									<i class="ki ki-bold-double-arrow-next icon-xs"></i>
								</a>
							</div>
							<div class="d-flex align-items-center">
								<input value="'.$cantidad.'" name="cboCantidadBusqueda'.$sufijo.'" id="cboCantidadBusqueda'.$sufijo.'" onkeypress="return solo_numero(event)" onKeyUp="if(event.keyCode==\'13\'){verPagina'.$sufijo.'(\'1\');}" type="text" class="form-control" size="2" style="height:18px; text-align:center; display: initial;width: 30px;line-height: normal; padding: 0px;"> 
								<span class="text-muted">
								Filas | Mostrando '.$inicio.' al '.$fin.' de '.$total_datos.' Filas
								</span>
							</div>
						</div>';
	return $paginacion;
}
?>