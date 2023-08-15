<?php

use Adianti\Database\TRecord;

/**
 * Desenvolvedoras Active Record
 * @author Lucas Bortoloti <bortoloti91@gmail.com
 */
class Desenvolvedoras extends TRecord
{
    const TABLENAME = 'desenvolvedoras';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
      
        parent::__construct($id, $callObjectLoad);
       
        parent::addAttribute('id');       
        parent::addAttribute('nome');       
        parent::addAttribute('ano_fundacao');       
        parent::addAttribute('assinatura'); 
    } 

}
