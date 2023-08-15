<?php

use Adianti\Control\TPage;

class ObjetoAlterarColecaoView extends TPage{

    public function __construct(){

        parent::__construct();

        try{
            TTransaction::open('filme');

            //Se a pesquisa for igual, ou seja do mesmo atributo, necessita usar o TExpression
            $criteria = new TCriteria;
            $criteria->add( new TFilter('genero_id', '=', 3));

            $repos = new TRepository('Filme');
            $objects = $repos->load( $criteria );

            foreach ($objects as $object){
                $object->titulo = $object->titulo. '(Ficção)';
                $object->store();
            }

            TTransaction::close();


        }
        catch (Exception $e){
            new TMessage('error', $e->getMessage());
        }


    }

}

?>