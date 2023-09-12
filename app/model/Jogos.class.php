<?php

use Adianti\Database\TRecord;

/**
 * Jogos Active Record
 * @author Lucas Bortoloti <bortoloti91@gmail.com
 */
class Jogos extends TRecord
{
    
    const TABLENAME = 'jogos';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    private $desenvolvedoras;
    private $jogosimages;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
      
        parent::__construct($id, $callObjectLoad);
       
        parent::addAttribute('id');       
        parent::addAttribute('nome');       
        parent::addAttribute('ano_lancamento');       
        parent::addAttribute('quantidade_avaliacoes');       
        parent::addAttribute('desenvolvedoras_id');
    }

    public function set_desenvolvedoras(Desenvolvedoras $object){
        $this->desenvolvedoras = $object;
        $this->desenvolvedoras_id = $object->nome;
    }

    public function get_desenvolvedoras(){
        if(empty($this->desenvolvedoras)){
            $this->desenvolvedoras = new Desenvolvedoras($this->desenvolvedoras_id);
        }

        return $this->desenvolvedoras;
    }

}

