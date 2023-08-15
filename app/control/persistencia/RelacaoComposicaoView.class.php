<?php

use Adianti\Control\TPage;

class RelacaoComposicaoView extends TPage{

    public function __construct(){

        parent:: __construct();
    
        try{

            TTransaction::open('filme');

            $filme = new Filme(5);
            
            foreach ($filme->getCriticas() as $critica){

                print $critica->conteudo . ' - ' . $critica->veiculo . ' - ' . $critica->dt_publicacao . '<br>';

            }
            /*
            $critica = new Critica;
            $critica->dt_publicacao = date('Y-m-d');
            $critica->veiculo = 'Jornal News';
            $critica->conteudo = 'Somente razoável';
            
            $filme->addCritica($critica); 

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