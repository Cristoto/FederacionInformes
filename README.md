# Ámbito 

Aplicación destinada a la centralización de datos de jornadas deportivas (tiempos, categoría, prueba, etc) para su posterior generación de informes dependiendo de los criterios que indique el usuario.

# Especificación de requisitos 

- La aplicación se encargará de realizar las siguientes acciones: 
- Será una aplicación web para poder ser accesible desde cualquier sitio y desde cualquier dispositivo. 
- Se cargará un fichero “Excel” descargado de la federación española con un formato concreto y dichos datos del fichero se centralizarán. 
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
- Symfony (PHP). 
- Base de datos MySQL. 
- Servidor web (local o remoto).



# Requisitos

- PHP 5 o superior.

- Composer.

- Asegurar que la extensión "fileinfo" está habilitada en el php.ini.

- Tu php.ini debe tener la siguiente directiva activada:

  > file_uploads = On



# Instalación

Descargar el zip del respositorio, descomprimirlo y en la carpeta ejecutar el comando:

```
composer install
```

# Problemas

A continuación se menciona algunos problemas conocidos a la hora de la ejecución del programa y su correspondiente solución:

**El programa no puede crear la carpeta "Files"**

Para solventar este problema, simplemente debe modificar los permisos de la carpeta del proyecto para que cualquiera pueda escribir en dicho directorio.