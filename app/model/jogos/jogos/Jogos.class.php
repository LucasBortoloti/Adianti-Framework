<?php
/**
 * Jogos Active Record
 * @author Marcelo Barreto Nees <marcelo.linux@gmail.com>
 */
class Jogos extends TRecord
{
    const TABLENAME = 'jogos';
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
        parent::addAttribute('ano_lancamento');       
        parent::addAttribute('quantidade_avaliacoes');       
        parent::addAttribute('desenvolvedoras_id');       
        parent::addAttribute('thumbnail');       
        parent::addAttribute('sinopse');       
        parent::addAttribute('avaliacoes');       
        parent::addAttribute('vendas'); 
    } 

}
