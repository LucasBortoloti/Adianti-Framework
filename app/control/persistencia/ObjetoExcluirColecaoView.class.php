<?php

use Adianti\Control\TPage;

class ObjetoExcluirColecaoView extends TPage{

    public function __construct(){

        parent::__construct();

        try{
            TTransaction::open('filme');

            //Se a pesquisa for igual, ou seja do mesmo atributo, necessita usar o TExpression
            $criteria = new TCriteria;
            $criteria->add( new TFilter('genero_id', 'IN', array(2,3)));

            $repos = new TRepository('Filme');
            $repos->delete( $criteria );

            TTransaction::close();


        }
        catch (Exception $e){
            new TMessage('error', $e->getMessage());
        }


    }

}

?>