<!doctype html>
<html>
	<head>
		<title>Печать ценников</title>
		<style>
			.l1_a{
				font-size: 16px;
					height: 75px;
					border-bottom: 1px solid #585858;
					padding: 5px 1px 0px 3px;
					overflow: hidden;
					margin-top: -9px;
			}

			table{
				table-layout: fixed;
				margin-top: 0mm;
				border-collapse: collapse;
			}

			td{
				height: 28.2mm;
				width: 48mm;
				border: 1px solid #bfbaba;
				page-break-inside: avoid;
			}

			.l1{
				width: 48mm !important;
				height: 26.5mm !important;
				font-size: 4.5mm;
				overflow: hidden;
			}
			.l2{
				margin-top: -3px;
			}
			.aid{
				float: left;
				font-size: 4mm;
				color: #808080;
				margin: 3px 0 0 2px;
			}
			.kost{
				font-size: 21px;
				float: right;
				margin-right: 2mm;
				font-weight: bold;
			}
			.l1_llc{
				font-size: 6px; 
				width:100%; 
				text-align:right; 
				margin-top:0px;
				margin-bottom: 5px;
			}
		</style>
	</head>
	<body>
		<table>
			<thead style="display: table-header-group"><?php
				$td=1; $i=1; $total=count($positions);
				foreach($positions as $item){
					if ($td==1) echo '<tr>'; ?>
						<td>
							<div class='l1'>
								<div class='l1_llc'>{{ $companyName }}</div>
								<div class='l1_a'>{{ $item['title'] }}</div>
								<div class='l2'>
									<span class='aid'>id: {{ $item['offer_id'] }}</span>
									<span class='kost'>{{ $item['price'] }} ₽</span>
								</div>
							</div>
						</td><?php
					if ($td==4 || $i==$total) { echo '</tr>'; $td=1; }
					else $td++;
					$i++; 
				}
			?></thead>
		</table>
	</body>
</html>