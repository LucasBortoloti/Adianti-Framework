<?php

use Adianti\Database\TRecord;

/**
 * Cliente Active Record
 * @author Lucas Bortoloti <bortoloti91@gmail.com
 */
class Cliente extends TRecord
{
    const TABLENAME = 'cliente';
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
        parent::addAttribute('cnpj');   
        parent::addAttribute('cidade');   
        parent::addAttribute('fone');   
        parent::addAttribute('estado');
    } 

}