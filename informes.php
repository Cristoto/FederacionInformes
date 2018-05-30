<?php

	namespace FederacionInformes;
	use FederacionInformes\php\Excel;
	use FederacionInformes\php\Consultas;
	use FederacionInformes\php\PDF;
	
	require_once __DIR__ . '/vendor/autoload.php';
	
	if (isset($_POST['subidaFile'])){
		//Mime types for validations
		$mimes = array('application/vnd.ms-excel','text/plain','text/csv','text/tsv');

		$excel = new Excel($_FILES['files']);
		$excel->uploadFile($mimes, $_SERVER);
		$excel->loadDataIntoBD();
		Excel::deleteFiles();
	}
	$smb = new Consultas();
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<title>Generador de informes</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" href="Assets/images/favicon_pdf.ico">

	<link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
	<link rel="stylesheet" type="text/css" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="node_modules/font-awesome/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="node_modules/animate.css/animate.min.css">
	<link rel="stylesheet" type="text/css" href="node_modules/hamburgers/dist/hamburgers.min.css">
	<link rel="stylesheet" type="text/css" href="node_modules/select2/dist/css/select2.min.css">
	<link rel="stylesheet" type="text/css" href="Assets/css/util.css">
	<link rel="stylesheet" type="text/css" href="Assets/css/main.css">
</head>
<body>
	<form enctype="multipart/form-data" action="#" method="POST">
		<div class="bg-contact3">
			<div class="container-contact3">
				<div class="wrap-contact3">
					<form class="contact3-form validate-form">
						<span class="contact3-form-title">
							Generar informes
						</span>

						<div class="wrap-contact3-form-radio">
							<span class="title-radio">
								¿Bloqueo de puntos?
							</span>
							<div class="contact3-form-radio m-r-42">
								<input class="input-radio3" id="radio1" type="radio" name="bloqueo" value="S">
								<label class="label-radio3" for="radio1">
									Si
								</label>
							</div>
						
							<div class="contact3-form-radio">
								<input class="input-radio3" id="radio2" type="radio" name="bloqueo" value="N" checked="checked">
								<label class="label-radio3" for="radio2">
									No
								</label>
							</div>
						</div>

						<div class="wrap-input3 validate-input">
							<input class="input3" type="number" name="cantidadParticipantes" 
								   placeholder="Cantidad participantes que puntuan" 
								   oninvalid="this.setCustomValidity('Especifique cuantos participantes puntuan.')"
								   oninput="setCustomValidity('')"
								   required>
							<span class="focus-input3"></span>
						</div>

						<div class="wrap-input3 validate-input">
							<input class="input3" type="number" name="puntInicial" 
							       placeholder="Puntuación inical" 
								   oninvalid="this.setCustomValidity('Indique que cantidad de puntos obtiene el primer competidor.')"
								   oninput="setCustomValidity('')"
								   required>
							<span class="focus-input3"></span>
						</div>

						<div class="wrap-input3 validate-input">
							<input class="input3" type="number" name="difPuntos" 
							       placeholder="Diferencias entre puestos" 
								   oninvalid="this.setCustomValidity('Especifique la diferencia de puntos entre puestos.')"
								   oninput="setCustomValidity('')"
								   required>
							<span class="focus-input3"></span>
						</div>

						<div class="wrap-input3 input3-select" style="display:block" required>
							<div>
								<select class="selection-2" name="temporada">
								<?php									 
									$temporadas = $smb -> getTemporadas();									
									foreach ($temporadas as $temporada) {
										$year = date('Y', strtotime($temporada["fechaCompeticion"]));
										echo "<option value=\"" . $temporada["fechaCompeticion"] . "\">" . $year . "</option>";
									}
								?>
								</select>
							</div>
							<span class="focus-input3"></span>
						</div>
						<div class="container-contact3-form-btn">
							<input name="generar" type="submit" class="contact3-form-btn" value="Generar"/>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div id="dropDownSelect1"></div>
	</form>

	<?php
		if(isset($_POST["generar"])){
			$usaBloqueo = $_POST["bloqueo"];
			$cantParticipantes = $_POST["cantidadParticipantes"];
			$puntInicial = $_POST["puntInicial"];
			$difPuntos = $_POST["difPuntos"];
			$temporada = $_POST["temporada"];

			//$p = $smb->informeCategoria('Absoluto');
			//var_dump($smb->asignarPuntos($usaBloqueo, $cantParticipantes, $puntInicial, $difPuntos, $temporada));

			//createPDF('Categoría absoluto', [], $p);
			PDF::createPDF('Todos los competidores', [], $smb->getAllCompetidores());
			$smb->deleteAll();
			$smb->closeConnection();
		}
	?>
	
	<script src="node_modules/jquery/dist/jquery.min.js"></script>
	<script src="node_modules/popper.js/dist/popper.min.js"></script>
	<script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
	<script src="node_modules/select2/dist/js/select2.min.js"></script>
	<script>
		$(".selection-2").select2({
			minimumResultsForSearch: 20,
			dropdownParent: $('#dropDownSelect1')
		});
	</script>
	<script src="Assets/js/main.js"></script>

	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-23581568-13"></script>
	<script>
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag('js', new Date());

	gtag('config', 'UA-23581568-13');
	</script>

</body>
</html>