<?php

use Adianti\Database\TRecord;

    class Filme extends TRecord
    {
        const TABLENAME = 'filme';
        const PRIMARYKEY = 'id';
        const IDPOLICY = 'max';

        private $criticas;
        private $atores;
        private $genero;
        private $distribuidor;

        public function __construct($id = NULL, $callObjectLoad = TRUE)
        {
            parent::__construct($id, $callObjectLoad);
            
            parent::addAttribute('titulo');
            parent::addAttribute('duracao');
            parent::addAttribute('dt_lcto');
            parent::addAttribute('orcamento');
            parent::addAttribute('distribuidor_id');
            parent::addAttribute('genero_id');
        }


        public function set_genero( Genero $object ){

            $this->genero = $object;
            $this->genero_id = $object->id;
        }

        public function set_distribuidor( Distribuidor $object ){

            $this->distribuidor = $object;
            $this->distribuidor_id = $object->id;
        }


        public function get_genero(){

            if(empty($this->genero)){
   
            $this->genero = new Genero($this->genero_id);
        }
            return $this->genero;
           
        }

        public function get_distribuidor(){

            if(empty($this->distribuidor)){
   
            $this->distribuidor = new Distribuidor($this->distribuidor_id);
        }
            return $this->distribuidor;
           
        }

        public function get_nome_genero(){

            if(empty($this->genero)){
   
            $this->genero = new Genero($this->genero_id);
        }
            return $this->genero->nome;
           
        }

        public function set_dt_lcto( $value ){
            
            $parts = explode('-', $value);
            
            if (checkdate($parts[1], $parts[2], $parts[0])){
                $this->data['dt_lcto'] = $value;
            }
            else{
                throw new Exception("Não pode atribuir {$value} em dt_lcto ");
            }
            
        }

        public function addCritica(Critica $object){
            
            $this->criticas[] = $object;

        }

        public function addAtor( Ator $object ){

            $this-> atores[] = $object;

        }

        public function getAtores(){

            return $this->atores;

        }

        public function getCriticas(){

            return $this->criticas;

        }

        public function load($id){

            /*
            $repository = new TRepository('Critica');
            $criteria = new TCriteria;
            $criteria->add(new TFilter('filme_id', '=', $id));
            */

            $this->criticas = parent::loadComposite('Critica', 'filme_id', $id);

            $this->atores = parent::loadAggregate('Ator' ,'FilmeAtor', 'filme_id', 'ator_id', $id);

            return parent::load($id);

        }

        public function store(){

            parent::store();

            parent::saveComposite('Critica', 'filme_id', $this->id, $this->criticas);
            parent::saveAggregate('FilmeAtor', 'filme_id', 'ator_id',$this->id, $this->atores);

            /*
            $repository = new TRepository('Critica');
            $criteria = new TCriteria;
            $criteria->add(new TFilter('filme_id', '=', $this->id));
            $repository->delete($criteria);

            if ($this->criticas){

                foreach($this->criticas as $critica){
                    
                    unset($critica->id);
                    $critica->filme_id = $this->id;
                    $critica->store();

                }

            }
            */

        }

        public function delete($id = NULL){

            $id = isset($id) ? $id: $this->id;
            
            /*
            $repository = new TRepository('Critica');
            $criteria = new TCriteria;
            $criteria->add(new TFilter('filme_id', '=', $this->id));
            $repository->delete($criteria);
            */
            parent::deleteComposite('Critica', 'filme_id', $id);
            parent::deleteComposite('FilmeAtor', 'filme_id', $id);
            parent::delete($id);
            
        }
    }
?>