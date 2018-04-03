<script src="<?php echo $GLOBALS['VirtualDir'];?>assets/js/datatable/jquery.dataTables.min.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>assets/js/datatable/ZeroClipboard.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>assets/js/datatable/dataTables.tableTools.min.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>assets/js/datatable/dataTables.bootstrap.min.js"></script>
<?php
@require_once("rules/testes.php");
?>
<div class="row">
	<?php 
	$testes = fVerificaTestes( $_SESSION['PESSOA']['id'] );

	//SE EXISTE TESTE DE MINISTERIOS PENDENTE
	if ( $testes["minis"]["nr_rsp"] > 0 ):
	?>
	<div class="col-xs-12 col-md-12">
		<a id="btnFinishMinisterios" href="javascript:void(0);" class="btn btn-labeled btn-palegreen">
			<i class="btn-label glyphicon glyphicon-floppy-saved"></i>Finalizar e arquivar meu Teste de Minist&eacute;rios
		</a>
		<div class="widget">
			<div class="widget-body">
				<table class="table table-condensed table-hover compact cell-border" id="simpledatatable">
					<thead class="bordered-darkorange">
						<tr>
							<th>D&ecirc; sua nota de 1 a 10, apenas para o(s) minist&eacute;rio(s) de seu interesse conforme as &aacute;reas abaixo:</th>
						</tr>
					</thead>
					<tbody/>
				</table>
			</div>
		</div>
	</div>				
	<?php
	endif;
	
	$ultimoResultado = false;
	//EXIBE RESULTADOS
	foreach (fExistHistorico( $_SESSION['PESSOA']['id'], 'M' ) as $result):
		?>
		<div class="col-xs-12 col-md-12">
			<div class="well with-header">
				<div class="header bg-blue">
					<?php
						if (!$ultimoResultado):
							$ultimoResultado = true;
							echo "<span class=\"btn btn-primary\">&Uacute;LTIMO RESULTADO</span>&nbsp;";
						endif;
					?>
					Conclu&iacute;do&nbsp;em:&nbsp;<?php echo strftime("%d/%m/%Y", strtotime($result['dh_conclusao']));?>
					<span class="pull-right" style="cursor:pointer" name="printResult" id-teste="<?php echo $result['id'];?>"><i class="fa fa-print fa-2x"></i></span>
				</div>
				<table class="table table-hover">
					<thead class="bordered-darkorange">
						<tr>
							<th>Ordem</th>
							<th>Minist&eacute;rio</th>
							<th>Nota</th>
						</tr>
					</thead>
					<tbody>
					<?php
					$ordem = 0;
					foreach (fQueryResult($result['id']) as $rsitem):
						?>
						<tr>
						<td><?php echo ++$ordem;?>&ordm;</td>
						<td><?php echo utf8_encode($rsitem['ds_item']);?></td>
						<td><?php echo $rsitem['nr_item'];?></td>
						</tr>
						<?php				
					endforeach;
					?>
					</tbody>
				</table>
			</div>
		</div>
		<?php
	endforeach;
	?>
</div>
<script src="<?php echo $GLOBALS['VirtualDir'];?>js/dashboard_testeministerios.js<?php echo "?".microtime();?>"></script>