<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Generador de informes</title>
  <link rel="icon" href="Assets/images/favicon_excel.ico">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
  <link rel='stylesheet prefetch' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'>
  <link rel='stylesheet prefetch' href='https://fonts.googleapis.com/css?family=Roboto:100,300'>
  <link rel="stylesheet" href="Assets/css/style.css">
</head>
<body>
  <form enctype="multipart/form-data" action="informes.php" method="POST">
    <div class="upload">
      <div class="upload-files">
        <header>
          <p>
            <i class="fa fa-cloud-upload" aria-hidden="true"></i>
            <span class="up">Subir</span>
            <span class="load">fichero</span>
          </p>
        </header>
        <div class="body" id="drop">
          <i class="fa fa-file-excel-o pointer-none" aria-hidden="true"></i>
          <p class="pointer-none">
            <b>Arrastre y suelte </b> fichero aquí
            <br /> o
            <a href="" id="triggerFile">inspeccionar</a> para subir el archivo</p>
          <input type="file" multiple="multiple" name="files" accept=".csv"/>
        </div>
        <footer>
          <div class="divider">
            <span>
              <AR>Ficheros</AR>
            </span>
          </div>
          <div class="list-files">
            <!--   template   -->
          </div>
          <input type="submit" name="subidaFile" value="SUBIR" class="importar"/>
        </footer>
      </div>
    </div>
  </form> 
  <script src="Assets/js/index.js"></script>
</body>
</html>


