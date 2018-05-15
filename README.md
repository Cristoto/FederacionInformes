# Ámbito 

Aplicación destinada a la centralización de datos de jornadas deportivas (tiempos, categoría, prueba, etc) para su posterior generación de informes dependiendo de los criterios que indique el usuario.

# Especificación de requisitos 

La aplicación se encargará de realizar las siguientes acciones: 
- Será una aplicación web para poder ser accesible desde cualquier sitio y desde cualquier dispositivo. 
- Se cargará un fichero “Csv” descargado de la federación española con un formato concreto y dichos datos del fichero se centralizarán. 
- El usuario especifica cuantos participantes puntúan de un determinado equipo. 
- El usuario especificará si quiere utilizar el bloqueo de puntos o no. 
- El usuario indicará la puntuación de cada puesto. 
- El usuario especifica cuando comienza y termina una temporada. 
- Generará informes de cada categoría clasificándolos por género, prueba y temporada de la competición. 
- Generará informes clasificados por clubes dependiendo de su categoría que son: femenino, masculino y general; la clasificación se hará por puntos totales y temporada de la competición.

# Especificación técnica 

Para la implementación de dicho programa se utilizará: 

- HTML5. 
- CSS3. 
- JavaScript. 
- Base de datos MySQL. 
- Servidor web (local o remoto).
- PHP

# Requisitos

- PHP 5 o superior.
- Composer.
- Asegurar que la extensión "fileinfo" está habilitada en el php.ini.
- Tu php-ini debe tener la siguiente directiva activada.

  > file_uploads = On
  > extension=xsl

# Instalación

- Instalar el servidor web al gusto (apache, nginX, etc) que pueda ejecutar código PHP
- Instalar Composer
- Descargar el zip del respositorio, descomprimirlo en la carpeta pública del servidor
- En la carpeta donde se haya descomprimido, ejecutar el comando (por ejemplo desde consola de git):
```
composer install
```
# Dependencias

Se utiliza Composer para satisfacer las librerías requeridas, no hay que realizar ninguna configuración, a modo de información son las siguientes librerías:

- phpoffice/phpspreadsheet: "1.1" (IMPORTANTE versión 1.1, con la 1.2 el código no funciona)
- gargron/fileupload: "~1.4.0"
- setasign/fpdf: "1.8.1"

# Documentación sobre la API

En el siguiente enlace encontrará documentación relacionada con la API (métodos, propiedades, herencia, etc).

https://cristoto.github.io/FederacionInformes

# Problemas

A continuación se menciona algunos problemas conocidos a la hora de la ejecución del programa y su correspondiente solución:

**El programa no puede crear la carpeta "Files"**

Para solventar este problema, simplemente debe modificar los permisos de la carpeta del proyecto para que cualquiera pueda escribir en dicho directorio.
