<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <title>Tímový projekt</title>
  </head>
  <body>
	<div class="container">

		<h1 class="display-4 mt-4 text-center mb-5">Zoznam meraní</h1>
		<table class="table">
		  <thead>
			<tr>
			  <th scope="col">#</th>
			  <th scope="col">Dátum</th>
			  <th scope="col">Čas</th>
			  <th scope="col">Frekvencia</th>
			  <th scole="col"></th>
			</tr>
		  </thead>
		  <tbody>
		  <?php
			$files = scandir('uploads');
			//print_r($files);
			for($i = 2; $i < sizeof($files);$i++){
					//echo '<a href = "chart.php?file='.$files[$i].'">'.$files[$i].'</a><br>';
					$pieces = explode(" ", $files[$i]);
					echo '<tr> <th scope="row">'.($i-1).'</th> <td>'.$pieces[0].'</td><td>'.$pieces[1].'</td><td>'.explode(".",$pieces[2])[0].'</td><td><a class="btn btn-secondary" href="chart.php?file='.$files[$i].'">otvoriť</a><br></td>';

			}
           ?>
		  </tbody>
		</table>
	</div>
	
	
	
	
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
  </body>
</html>
