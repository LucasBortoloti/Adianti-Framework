<?php

use Adianti\Control\TPage;

class ObjetoContarColecaoView extends TPage{

    public function __construct(){

        parent::__construct();

        try{
            TTransaction::open('filme');

            //Se a pesquisa for igual, ou seja do mesmo atributo, necessita usar o TExpression
            $criteria = new TCriteria;
            $criteria->add( new TFilter('orcamento', '=', 19000000), TExpression::OR_OPERATOR );
            $criteria->add( new TFilter('orcamento', '>=', 20000000), TExpression::OR_OPERATOR );
            $criteria->add( new TFilter('genero_id', 'IN', array(1,3)));

            $repos = new TRepository('Filme');
            $qtde = $repos->count( $criteria );

            print "Quantidade: " .  $qtde;

            
            TTransaction::close();


        }
        catch (Exception $e){
            new TMessage('error', $e->getMessage());
        }


    }

}

?>