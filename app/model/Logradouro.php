
<?php

use Adianti\Database\TRecord;

/**
 * @author Lucas Bortoloti <bortoloti91@gmail.com
 */
class Logradouro extends TRecord
{
    const TABLENAME = 'logradouro';
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
