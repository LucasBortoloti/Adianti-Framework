<?php

use Adianti\Database\TRecord;

    class SaleProduct extends TRecord
    {
        const TABLENAME = 'sale_product';
        const PRIMARYKEY = 'venda_id';
        const IDPOLICY = 'max';

        public function __construct($id = NULL, $callObjectLoad = TRUE)
        {
            parent::__construct($id, $callObjectLoad);
        
            parent::addAttribute('venda_id');
            parent::addAttribute('produto_id');
        }

        public function set_product(Product $object){
            $this->produto = $object;
            $this->produto_id = $object->nome;
        }
    
        public function get_product(){
            if(empty($this->produto)){
                $this->produto = new Product($this->produto_id);
            }
    
            return $this->produto;
        }

        public function set_sale(Sale $object){
            $this->venda = $object;
            $this->venda_id = $object->cliente;
        }
    
        public function get_sale(){
            if(empty($this->venda)){
                $this->venda = new Sale($this->venda_id);
            }
    
            return $this->venda;
        }

    }