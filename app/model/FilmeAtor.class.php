<?php

use Adianti\Database\TRecord;

class FilmeAtor extends TRecord
{
    const TABLENAME = 'filme_ator';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'max';

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        
        parent::addAttribute('filme_id');
        parent::addAttribute('ator_id');
    }

}
?>