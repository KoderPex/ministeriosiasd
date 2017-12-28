<script src="<?php echo $GLOBALS['VirtualDir'];?>assets/js/datatable/jquery.dataTables.min.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>assets/js/datatable/ZeroClipboard.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>assets/js/datatable/dataTables.bootstrap.min.js"></script>
<div class="row">
	<div class="col-xs-12 col-md-12">
	<?php fDataFilters( 
		array( 
			"filterTo" => "#giftDatatable",
			"filters" => 
				array( 
					array( "value" => "D", "label" => "Dom" ),
					array( "value" => "NI", "label" => "Nota igual" ),
					array( "value" => "NA", "label" => "Nota maior", "unique" => true ),
					array( "value" => "NE", "label" => "Nota menor", "unique" => true )
				)
		) 
	);?>
	</div>
	<div class="col-xs-12 col-md-12">
		<div class="widget">
			<div class="widget-header bordered-bottom bordered-yellow">
				<div class="widget-buttons">
                	<a href="#" data-toggle="maximize">
                    	<i class="fa fa-expand"></i>
					</a>
					<a href="#" data-toggle="collapse">
						<i class="fa fa-minus"></i>
					</a>
					<a href="#" data-toggle="dispose">
						<i class="fa fa-times"></i>
					</a>
				</div>
			</div>
			<div class="widget-body">
				<table class="table table-bordered table-hover table-striped dataTable" id="giftDatatable" role="grid">
					<thead class="bordered-darkorange">
						<tr role="row">
							<th>Nome</th>
							<th>Dom</th>
							<th>Nota</th>
						</tr>
					</thead>
					<tbody/>
				</table>
			</div>	
		</div>
	</div>
</div>
<script src="<?php echo $GLOBALS['VirtualDir'];?>js/searchGift.js<?php echo "?".microtime();?>"></script>