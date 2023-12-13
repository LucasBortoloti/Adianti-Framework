<?php

use Adianti\Database\TRecord;

    class JogosImages extends TRecord
    {
        const TABLENAME = 'jogos_images';
        const PRIMARYKEY = 'jogos_id';
        const IDPOLICY = 'max';

        public function __construct($id = NULL, $callObjectLoad = TRUE)
        {
            parent::__construct($id, $callObjectLoad);
        
            parent::addAttribute('id');
            parent::addAttribute('jogos_id');
            parent::addAttribute('images_id');
        }
    }
