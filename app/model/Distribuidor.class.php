<?php

use Adianti\Database\TRecord;

class Distribuidor extends TRecord
{
    const TABLENAME = 'distribuidor';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'max';

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        
        parent::addAttribute('nome');
        parent::addAttribute('local');
    }


}
?>