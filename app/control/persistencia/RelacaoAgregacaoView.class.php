<?php

use Adianti\Control\TPage;

class RelacaoAgregacaoView extends TPage{

    public function __construct(){

        parent:: __construct();
    
        try{

            TTransaction::open('filme');

            $filme = new Filme(1);

            print $filme->titulo . '<br><br>';
            
            foreach ($filme->getAtores() as $ator){
                print $ator->nome . '<br>';
            }

            /*
            $ator = new Ator(5);
            $filme->addAtor ( $ator );
            
            $filme->store();
            */
            
            TTransaction::close();
        }
        catch (Exception $e){

            new TMessage('error', $e->getMessage());

        }
    }
}


?>