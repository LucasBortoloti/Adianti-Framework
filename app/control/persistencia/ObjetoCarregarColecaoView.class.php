<?php

use Adianti\Control\TPage;

class ObjetoCarregarColecaoView extends TPage{

    public function __construct(){

        parent::__construct();

        try{
            TTransaction::open('filme');

            //Se a pesquisa for igual, ou seja do mesmo atributo, necessita usar o TExpression
            $criteria = new TCriteria;
            $criteria->add( new TFilter('orcamento', '=', 19000000), TExpression::OR_OPERATOR );
            $criteria->add( new TFilter('orcamento', '>=', 20000000), TExpression::OR_OPERATOR );
            $criteria->add( new TFilter('genero_id', 'IN', array(2,3)));

            $repos = new TRepository('Filme');
            $objects = $repos->load( $criteria );

            foreach ($objects as $object){
                print $object->titulo. ' - '. $object->duracao . ' - '. $object-> orcamento . '<br>';
            }




            
            TTransaction::close();


        }
        catch (Exception $e){
            new TMessage('error', $e->getMessage());
        }


    }

}

?>