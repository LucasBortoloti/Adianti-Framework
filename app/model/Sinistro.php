
<?php

use Adianti\Database\TRecord;

/**
 * @author Lucas Bortoloti <bortoloti91@gmail.com
 */
class Sinistro extends TRecord
{
    const TABLENAME = 'sinistro';
    const PRIMARYKEY = 'id';
    const IDPOLICY =  'max'; // {max, serial}

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {

        parent::__construct($id, $callObjectLoad);

        parent::addAttribute('id');
        parent::addAttribute('descricao');
        parent::addAttribute('created_at');
    }
}
