
<?php

use Adianti\Database\TRecord;

/**
 * @author Lucas Bortoloti <bortoloti91@gmail.com
 */
class Bairro extends TRecord
{
    const TABLENAME = 'bairro';
    const PRIMARYKEY = 'id';
    const IDPOLICY =  'max'; // {max, serial}

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {

        parent::__construct($id, $callObjectLoad);

        parent::addAttribute('id');
        parent::addAttribute('nome');
    }
}
