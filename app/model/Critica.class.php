<?php

use Adianti\Database\TRecord;

class Critica extends TRecord
{
    const TABLENAME = 'critica';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'max';

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        
        parent::addAttribute('dt_publicacao');
        parent::addAttribute('veiculo');
        parent::addAttribute('conteudo');
        parent::addAttribute('filme_id');
    }


}
?>