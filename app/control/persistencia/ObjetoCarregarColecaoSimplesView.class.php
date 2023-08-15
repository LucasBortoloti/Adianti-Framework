<?php

use Adianti\Control\TPage;

class ObjetoCarregarColecaoSimplesView extends TPage{

    public function __construct(){

        parent::__construct();

        try{
            TTransaction::open('filme');

            $repos = new TRepository('Filme');

            $objects = $repos->where('genero_id', '=',1)
                             ->where('orcamento', '>=', 50000000)
                             ->load();
    


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