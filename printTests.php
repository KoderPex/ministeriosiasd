<script src="<?php echo $GLOBALS['VirtualDir'];?>assets/js/charts/sparkline/jquery.sparkline.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>assets/js/charts/sparkline/sparkline-init.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>assets/js/charts/easypiechart/jquery.easypiechart.min.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>assets/js/charts/easypiechart/easypiechart-init.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>assets/js/charts/flot/jquery.flot.min.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>assets/js/charts/flot/jquery.flot.resize.min.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>assets/js/charts/flot/jquery.flot.pie.min.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>assets/js/charts/flot/jquery.flot.tooltip.min.js"></script>
<script src="<?php echo $GLOBALS['VirtualDir'];?>assets/js/charts/flot/jquery.flot.orderBars.js"></script>
<div class="row">
	<div class="col-lg-2 col-sm-4 col-xs-6">
		<div class="databox databox-lg databox-vertical databox-inverted bg-white databox-shadowed">
			<div class="databox-top">
				<div class="databox-piechart">
					<div data-toggle="easypiechart" class="easyPieChart block-center" data-barcolor="#e75b8d" data-linecap="butt" data-percent="40" data-animate="500" data-linewidth="8" data-size="100" data-trackcolor="#eee" style="width: 100px; height: 100px; line-height: 100px;">
						<span class="white font-200"><i class="fa fa-tags pink"></i></span>
						<canvas width="200" height="200" style="width: 100px; height: 100px;"></canvas>
					</div>
				</div>
			</div>
			<div class="databox-bottom no-padding text-align-center">
				<span class="databox-number lightcarbon no-margin">11</span>
				<span class="databox-text lightcarbon no-margin">NEW TICKETS</span>

			</div>
		</div>
	</div>


	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="row" ng-controller="dashboard">
			<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12" ng-repeat="panel in panels">
				<div class="databox bg-white radius-bordered">
					<div class="databox-left {{downloads.leftBkTheme}}">
						<div class="databox-piechart">
							<div data-toggle="easypiechart" class="easyPieChart" data-barcolor="#fff" data-linecap="butt" data-percent="{{downloads.pc}}" data-animate="500" data-linewidth="3" data-size="47" data-trackcolor="rgba(255,255,255,0.1)"><span class="white font-90">{{downloads.pc}}%</span></div>
						</div>
					</div>
					<div class="databox-right">
						<span class="{{downloads.rightBkTheme}}">{{downloads.qt}}</span>
						<div class="databox-text darkgray">{{downloads.ds}}</div>
						<div class="{{downloads.rightDecorate}}">
							<i class="{{downloads.ico}}"></i>
						</div>
					</div>
				</div>
			</div>
			
		</div>
	</div>
</div>

<script src="<?php echo $GLOBALS['VirtualDir'];?>js/printTests.js"></script>