<!DOCTYPE HTML>
<html>
	<head>  
		<meta charset="utf-8">
		<script type="text/javascript" src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>

		<script type="text/javascript">
			function clear_storage(){
				localStorage.clear();
				location.reload();	
			}
			
			window.onload = function () {
	  
				// Check browser support
				if (typeof(Storage) == "undefined") 
					alert("Sorry, your browser does not support Web Storage...");
	  
				// ak existuju v local storage niake body
				if (localStorage.getItem("table") !== null){
					document.getElementById("values").innerHTML = localStorage.getItem("table");
				}
		
				var yy = [];
				
				<?php  
				
					$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
					$file_name = htmlspecialchars($_GET["file"]);
					$delta = explode(".",explode(" ",$file_name)[2])[0];
					
					//nacitanie dat zo suboru , Y-suradnice
					$file = file_get_contents("uploads/".$file_name,"r") or die("Unable to open file!");
					$json = json_decode($file);
					$data= array();
					foreach ($json as $key => $value) {
						array_push($data,$value);
					}
					
					// cas urcuje od ktorej sekundy sa graf vykresli a dlzka urcije kolko sekund sa vykresli
					if(isset($_POST['time'])){
						$cas = $_POST['time'];
					}else{
						$cas = 0;
					}
					if(isset($_POST['seconds'])){
						$dlzka = $_POST['seconds'];
					}else{
						$dlzka = 10;
					}
					
				?>
	
				var file_name = <?php echo json_encode($file_name); ?>;
				
				//ak sa nacita novy subor tak sa local storage premaze
				if(localStorage.getItem("file_name") == null)
					localStorage.setItem("file_name",file_name);
				else{
					if(localStorage.getItem("file_name").localeCompare(file_name) != 0)
						clear_storage();	
				}
	
				//presun Y dat z php do js
				yy = (<?php echo json_encode($data); ?>);
				var y =[];
				for(var j = 0;j<yy.length;j++){
					y.push(yy[j].value);
				}
				
				var delta;
				var deltaphp = <?php echo json_encode($delta);?>;
				//console.log(deltaphp);
				if(deltaphp.localeCompare('100Hz') == 0)
					delta = 0.01;
				else if(deltaphp.localeCompare('125Hz') == 0)
					delta = 0.008;
				else if(deltaphp.localeCompare('250Hz') == 0)
					delta = 0.004;
				else if(deltaphp.localeCompare('500Hz') == 0)
					delta = 0.002;
				else if(deltaphp.localeCompare('1000Hz') == 0)
					delta = 0.001;
				
				console.log(delta);
	
				// dlzka zaznamu v sekundach
				var max = ((y.length) * delta);
				document.getElementById("dlzkazaznamu").innerHTML += "Dĺžka záznamu je: "+ max+ " s";
				var time = <?php echo $cas;?>;
				var dlzka = <?php echo $dlzka;?>;

				// vyber bodov v urcenom intervale
				y_interval = y.slice(((time/delta)),((time+dlzka)/delta)+2);

	
				var points = [];
				var marked;
				var clicked;
				if (localStorage.getItem("clicked") !== null){
					clicked = JSON.parse(localStorage.getItem("clicked"));
				}else
					clicked = [];
				
				if (localStorage.getItem("marked") !== null){
					marked = JSON.parse(localStorage.getItem("marked"));
				}else
					marked = [];
				
				if (localStorage.getItem("marked_label") !== null){
					marked_label = JSON.parse(localStorage.getItem("marked_label"));
				}else
					marked_label = [];
				
				
				var label;
				if (localStorage.getItem("label") !== null){
					label = JSON.parse(localStorage.getItem("label"));
				}else
					label = 0;
				
				
				function getLabel(){
					return label;
				}
				function incLabel(){
					label = label +1;
					return label;
				}
	
	
				for(var i = 0 ; i < y_interval.length ; i++ ){
					// vypocet X hodnot podla frekvencie
					x = Math.round(delta*i*10000)/10000+ time;

					// vykleslenie oznacenych bodov po obnoveni stranky a vypisanie hodnot do tabulky
				  if (marked.includes(x) == true){
					mark_index = marked.indexOf(x);
					mark_label = marked_label[mark_index];
					points.push({x: x , y: parseInt(y_interval[i])/1000 ,indexLabel:mark_label,indexLabelFontSize:26,indexLabelFontWeight:"bolder",color : "Red",markerSize:15,markerBorderColor : "#000000",markerBorderThickness :2,markerType:"circle" , click: function(e){
						if(clicked.length == 0){
							document.getElementById("table_values").innerHTML += "<tr><td id='c_"+getLabel()+"'>"+incLabel()+"</td><td id='x1_"+getLabel()+"'>" + e.dataPoint.x + "s</td><td id='x2_"+getLabel()+"'></td><td id='diff_x_"+getLabel()+"'></td><td id='y1_"+getLabel()+"'>" + e.dataPoint.y + "mV</td><td id='y2_"+getLabel()+"'></td><td id='diff_y_"+getLabel()+"'></td></tr>";
							e.dataPoint.indexLabel=""+getLabel()+" - 1";
							marked_label.push(""+getLabel()+" - 1");
						}
						else if(clicked.length == 1){
							document.getElementById("x2_"+getLabel()).innerHTML += e.dataPoint.x + "s";
							document.getElementById("y2_"+getLabel()).innerHTML += e.dataPoint.y + "mV";
							e.dataPoint.indexLabel=""+getLabel()+" - 2";
							marked_label.push(""+getLabel()+" - 2");
						}
						e.dataPoint.indexLabelFontSize=26;
						e.dataPoint.indexLabelFontWeight="bolder";
						e.dataPoint.color = "Red";
						e.dataPoint.markerSize=15;
						e.dataPoint.markerBorderColor = "#000000";
						e.dataPoint.markerBorderThickness =2;
						e.dataPoint.markerType="circle";
						clicked.push({x: e.dataPoint.x , y: e.dataPoint.y});
						marked.push(e.dataPoint.x);
						
						if(clicked.length == 2){
							document.getElementById("diff_x_"+getLabel()).innerHTML += Math.round(Math.abs(clicked[1].x - clicked[0].x)*1000)/1000 + "s";
							document.getElementById("diff_y_"+getLabel()).innerHTML += Math.round(Math.abs(clicked[1].y - clicked[0].y)*1000)/1000 + "mV";
							clicked = [];
						}
						var point_table = document.getElementById("values").innerHTML;
						localStorage.setItem("table",point_table);
						localStorage.setItem("clicked",JSON.stringify(clicked));
						localStorage.setItem("marked",JSON.stringify(marked));
						localStorage.setItem("marked_label",JSON.stringify(marked_label));
						localStorage.setItem("label",JSON.stringify(getLabel()));
						chart.render();
					}});
				  }else{
					  points.push({x: x , y: parseInt(y_interval[i])/1000 , click: function(e){
						if(clicked.length == 0){
							document.getElementById("table_values").innerHTML += "<tr><td id='c_"+getLabel()+"'>"+incLabel()+"</td><td id='x1_"+getLabel()+"'>" + e.dataPoint.x + "s</td><td id='x2_"+getLabel()+"'></td><td id='diff_x_"+getLabel()+"'></td><td id='y1_"+getLabel()+"'>" + e.dataPoint.y + "mV</td><td id='y2_"+getLabel()+"'></td><td id='diff_y_"+getLabel()+"'></td></tr>";
							e.dataPoint.indexLabel=""+getLabel()+" - 1";
							marked_label.push(""+getLabel()+" - 1");
						}
						else if(clicked.length == 1){
							document.getElementById("x2_"+getLabel()).innerHTML += e.dataPoint.x + "s";
							document.getElementById("y2_"+getLabel()).innerHTML += e.dataPoint.y + "mV";
							e.dataPoint.indexLabel=""+getLabel()+" - 2";
							marked_label.push(""+getLabel()+" - 2");
						}
						e.dataPoint.indexLabelFontSize=26;
						e.dataPoint.indexLabelFontWeight="bolder";
						e.dataPoint.color = "Red";
						e.dataPoint.markerSize=15;
						e.dataPoint.markerBorderColor = "#000000";
						e.dataPoint.markerBorderThickness =2;
						e.dataPoint.markerType="circle";
						clicked.push({x: e.dataPoint.x , y: e.dataPoint.y});
						marked.push(e.dataPoint.x);

						if(clicked.length == 2){
							document.getElementById("diff_x_"+getLabel()).innerHTML += Math.round(Math.abs(clicked[1].x - clicked[0].x)*1000)/1000 + "s";
							document.getElementById("diff_y_"+getLabel()).innerHTML += Math.round(Math.abs(clicked[1].y - clicked[0].y)*1000)/1000 + "mV";
							clicked = [];
						}
						var point_table = document.getElementById("values").innerHTML;
						localStorage.setItem("table",point_table);
						localStorage.setItem("clicked",JSON.stringify(clicked));
						localStorage.setItem("marked",JSON.stringify(marked));
						localStorage.setItem("marked_label",JSON.stringify(marked_label));
						localStorage.setItem("label",JSON.stringify(getLabel()));
						chart.render();
					}});
				  }
				}

				// parametre grafu
				var chart = new CanvasJS.Chart("chartContainer", {
					backgroundColor: "transparent",
					zoomEnabled: true,
					axisX:{
						includeZero: true,
						viewportMinimum:time,
						viewportMaximum:time + (5.12),   //5.12
						crosshair:{
							enabled: true,
							snapToDataPoint: true,
							valueFormatString: "####0.###"
						},
						suffix: "s",
						
					},
					axisY:{
						gridThickness:-1,
						minimum: 0,
						maximum: 5,
						//suffix: "mV",
					},
					data: [{
						type: "spline",
						lineThickness: 2,			
						dataPoints: points,
						lineColor: "black",
						
					}]
				});

				chart.lineCap = "round";
				chart.lineJoin = "round";

				chart.render();

				var parentElement = document.getElementsByClassName("canvasjs-chart-toolbar");
				var childElement = document.getElementsByTagName("button");
				if(childElement[0].getAttribute("state") === "pan"){
				  childElement[0].click();
				}

			} 
  	 
  
		</script>
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

        <title>Tímový projekt</title>
		<style>
			.canvasjs-chart-toolbar,.canvasjs-chart-credit {
				display: none !important;
			} 
			table,th,td,tr {
				border: 1px solid black;
			}
			table tr:nth-child(even) {
				background-color: #eee;
			}
			table tr:nth-child(odd) {
				background-color: #fff;
			}
			table th {
				color: white;
				background-color: black;
			}
			td,th{
				padding: 20px;
			}
			th{
				border-color: white;
			}
		</style>

    </head>
    <body>
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-4 m-auto text-center">
                    <h1 class="display-4">EKG záznam z dátumu: <?php
                        $pieces = explode(" ", $file_name);
                        echo $pieces[0];
                    ?></h1>
                </div>
            </div>

            <div class="row">

            </div>



            <div id="chartContainer" style="height: 400px; width: 1904px; background-image: url(style/paper.png); background-repeat: repeat-x;background-size: contain;">

            </div>
            <div class="row">
                <div class="col-5 offset-1">
                    <div id="form">
                        <div id="dlzkazaznamu"></div>
                        <form action = "<?php echo json_encode($actual_link);?>" method = "post">
                            <div class="row mt-1">
                                <div class="col">
                                    <div class="form-inline">
                                        <label>Počet sekúnd: <input type="number" class="form-control ml-1" name="seconds" placeholder="10" required></label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-1">
                                <div class="col">
                                    <div class="form-inline">
                                        <label>Od sekundy:&nbsp;&nbsp; <input type="number" class="form-control ml-1" name="time" placeholder="0" required> </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-2 offset-2">
                                    <button type="submit" class="btn btn-secondary">Potvrdiť</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                 <div class="col-5">
                     <div class="row">
                         <div class="col">
                             <div id ="values" style="display: inline-block;float: left;">								 
                                 <table id = "table_values" style="display: inline-block;float: left;text-align:center;">
									 <tr><th></th><th colspan = 2>Čas (s)</th><th>&Delta;s</th><th colspan = 2>Amplitúda (mV)</th><th>&Delta;mV</th></tr>
                                     <tr><th>č.</th><th>x1</th><th>x2</th><th>Rozdiel x</th><th>y1</th><th>y2</th><th>Rozdiel y</th></tr>
                                 </table>
                                 
                             </div>
                         </div>
                     </div>

                     <div class="row mt-3">
                         <div class="col-2 offset-4">
                             <button class="btn btn-danger mt-3 m-auto" onclick = "clear_storage()">Vymazať</button>
                         </div>
                     </div>

                 </div>
            </div>

        </div>

		
		<?php  echo $data[0]; ?>
		
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>


    </body>
</html>