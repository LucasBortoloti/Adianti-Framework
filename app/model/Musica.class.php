<?php

use Adianti\Database\TRecord;

/**
 * Musica Active Record
 * @author Lucas Bortoloti <bortoloti91@gmail.com
 */
class Musica extends TRecord
{
    const TABLENAME = 'musica';
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
        parent::addAttribute('genero');       
        parent::addAttribute('duracao');       
        parent::addAttribute('cantor_id'); 
    } 

    public function set_cantor(Cantor $object){
        $this->cantor = $object;
        $this->cantor_id = $object->nome;
    }

    public function get_cantor(){
        if(empty($this->cantor)){
            $this->cantor = new Cantor($this->cantor_id);
        }

        return $this->cantor;
    }

}
