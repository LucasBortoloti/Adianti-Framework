<?php
class ConexaoManual extends TPage{

    public function __construct(){
        
        parent::__construct();

        try{
            TTransaction::open('filme');

            $conn = TTransaction::get();
            
            $result = $conn->query('SELECT id, nome from ator');

            foreach ($result as $row){
                print $row['id'] . ' - '. $row['nome'] . '<br>';
            }

            TTransaction::close();
        }
        catch (Exception $e){
            new TMessage('error', $e->getMessage());
        }
    }


}




?>