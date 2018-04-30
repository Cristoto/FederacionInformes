<?php 

namespace FederacionInformes\php;

use FileUpload\FileUpload;
use FileUpload\Validator\Simple as SimpleValidator;
use FileUpload\FileSystem\Simple as SimpleFile;
use FileUpload\PathResolver\Simple as SimplePath;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Clase encargada del manejo de los ficheros excel.
 * Subida de los ficheros en el servidor, cargar sus datos en la BD, etc.
 * 
 * @author Cristo M. Estévez Hernández <cristom.estevez@gmail.com>
 */
class Excel
{
    private $files;
    private $directory;

    function __construct(array $files, string $pathDirectoryFiles){
        $this->files = $files;
        $this->directory = $pathDirectoryFiles; 
    }

    /**
     *Upload file into server specifying the support types
    *
    * @param array $mimes Extensions of file permit.
    * @return void
    */
    public function uploadFile(array $mimes, array $serverData) : void{
        
        $this->createDirectory($this->directory);

        // Simple validation (max file size 2MB and only two allowed mime types)
        $validator = new SimpleValidator('2M', $mimes);

        // Simple path resolver, where uploads will be put
        $pathresolver = new SimplePath($this->directory);
        // The machine's filesystem
        $filesystem = new SimpleFile();

        // FileUploader itself
        $fileupload = new FileUpload($this->files , $serverData);

        // Adding it all together. Note that you can use multiple validators or none at all
        $fileupload->setPathResolver($pathresolver);
        $fileupload->setFileSystem($filesystem);
        //TODO: Search MIME Type to files csv 
        //$fileupload->addValidator($validator);

        // Doing the deed
        list($files, $headers) = $fileupload->processAll();
        
    }

    /**
     * Create a directory if not exist
     *
     * @param string $pathDirectory
     * @return void
     */
    private function createDirectory(string $pathDirectory) : void{
        if(!file_exists($pathDirectory) && !is_dir($pathDirectory)){
            mkdir($pathDirectory, 0777);
        }
    }

    /**
     * Delete all files into directory specify on the constructor
     *
     * @return void
     */
    public function deleteFiles(){
        $files = glob($this->directory . '/*');
            foreach($files as $file){
                if(is_file($file))
                    unlink($file);
            }
    }
}

