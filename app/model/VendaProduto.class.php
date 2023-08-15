<?php

use Adianti\Database\TRecord;

    class VendaProduto extends TRecord
    {
        const TABLENAME = 'vendaproduto';
        const PRIMARYKEY = 'venda_id';
        const IDPOLICY = 'max';

        public function __construct($id = NULL, $callObjectLoad = TRUE)
        {
            parent::__construct($id, $callObjectLoad);
        
            parent::addAttribute('venda_id');
            parent::addAttribute('produto_id');
            parent::addAttribute('quantidade');
        }

        public function set_produto(Produto $object){
            $this->produto = $object;
            $this->produto_id = $object->nome;
        }
    
        public function get_produto(){
            if(empty($this->produto)){
                $this->produto = new Produto($this->produto_id);
            }
    
            return $this->produto;
        }

        public function set_venda(Venda $object){
            $this->venda = $object;
            $this->venda_id = $object->cliente;
        }
    
        public function get_venda(){
            if(empty($this->venda)){
                $this->venda = new Venda($this->venda_id);
            }
    
            return $this->venda;
        }

    }
