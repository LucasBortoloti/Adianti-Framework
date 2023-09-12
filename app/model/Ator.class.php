<?php

use Adianti\Database\TRecord;

class Ator extends TRecord
{
    const TABLENAME = 'ator';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'max';

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        
        parent::addAttribute('nome');
        parent::addAttribute('nome_real');
        parent::addAttribute('dt_nascimento');
        
    }
}
?>