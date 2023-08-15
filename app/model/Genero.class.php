<?php

use Adianti\Database\TRecord;

    class Genero extends TRecord
    {
        const TABLENAME = 'genero';
        const PRIMARYKEY = 'id';
        const IDPOLICY = 'max';

        public function __construct($id = NULL, $callObjectLoad = TRUE)
        {
            parent::__construct($id, $callObjectLoad);
            
            parent::addAttribute('nome');
        }

        



    }
?>