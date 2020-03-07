<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader {

    private $targetDirectory ;

    public function __construct( ?string $targetDirectory = NULL ) {

        if( $targetDirectory ) {

            $this->targetDirectory = $targetDirectory ;
        }
    }

    public function remove( $target ) {

        if( \file_exists( $target ) ) {

            return \unlink( $target ) ;
        }

        return false ;
    }

    public function upload( UploadedFile $file ) {

        $realFilename = \pathinfo( $file->getClientOriginalName(), PATHINFO_FILENAME ) ;

        $safeFilename =  \transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $realFilename);

        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension() ;

        try {
            // try persist file inner target directory
            $file->move( $this->getTargetDirectory(), $fileName) ;

            return $fileName ;

        } catch ( FileException $e ) {

            return false ;
        }
    }

    public function getTargetDirectory() {

        return $this->targetDirectory ;
    }

    public function setTargetDirectory( string $targetDirectory ) {

        $this->targetDirectory = $targetDirectory ;
    }

}