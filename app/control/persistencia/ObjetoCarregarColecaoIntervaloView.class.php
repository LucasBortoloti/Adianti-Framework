<?php

use Adianti\Control\TPage;

class ObjetoCarregarColecaoIntervaloView extends TPage{

    public function __construct(){

        parent::__construct();

        try{
            TTransaction::open('filme');

            //Se a pesquisa for igual, ou seja do mesmo atributo, necessita usar o TExpression
            $criteria = new TCriteria;
            $criteria->setProperty('limit', 10); //maximo 10
            $criteria->setProperty('offset', 3); // desconsidera os 3 primeiros
            $criteria->setProperty('order', 'id'); // ordena por id
            

            $repos = new TRepository('Filme');
            $objects = $repos->load( $criteria );

            foreach ($objects as $object){
                print $object->id . ' - ' . $object->titulo. ' - '. $object->duracao . ' - '. $object-> orcamento . '<br>';
            }




            
            TTransaction::close();


        }
        catch (Exception $e){
            new TMessage('error', $e->getMessage());
        }


    }

}

?>