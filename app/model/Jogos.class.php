<?php

use Adianti\Database\TRecord;

/**
 * Jogos Active Record
 * @author Lucas Bortoloti <bortoloti91@gmail.com
 */
class Jogos extends TRecord
{
    
    const TABLENAME = 'jogos';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    private $desenvolvedoras;
    private $jogosimages;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
      
        parent::__construct($id, $callObjectLoad);
       
        parent::addAttribute('id');       
        parent::addAttribute('nome');       
        parent::addAttribute('ano_lancamento');       
        parent::addAttribute('quantidade_avaliacoes');       
        parent::addAttribute('desenvolvedoras_id');
    }

    public function set_desenvolvedoras(Desenvolvedoras $object){
        $this->desenvolvedoras = $object;
        $this->desenvolvedoras_id = $object->nome;
    }

    public function get_desenvolvedoras(){
        if(empty($this->desenvolvedoras)){
            $this->desenvolvedoras = new Desenvolvedoras($this->desenvolvedoras_id);
        }

        return $this->desenvolvedoras;
    }

    /*public function set_images(JogosImages $object){
        $this->images = $object;
        $this->images_id = $object->nome;
    }

    public function get_images(){
        if(empty($this->images)){
            $this->images_id = new JogosImages($this->images_id);
        }

        return $this->images;
    }
    */

    //Retornar dados de outra tabela, no caso JogosImages como lista no rest

    public function addImages(Images $images){
        $this->images[] = $images;
    }

    public function getImages(){
        return $this->images;
    }

    public function clearParts(){
        $this->images = array();
    }

    public function load($id)
    {
        $jogosimages = JogosImages::where('jogos_id', '=', $id)->load();

        if ($jogosimages)
        {   
            foreach ($jogosimages as $jogosimage) 
            { 
                $this->addImages( new Images($jogosimage->images_id) );
            } 
        }
        
        return parent::load($id);
    }

    public function store()
    {
        parent::store();
    
        JogosImages::where('jogos_id', '=', $this->id)->delete();
        
        if ($this->images)
        {
            foreach ($this->images as $image)
            {
                $jogosimage = new JogosImages;
                $jogosimage->jogos_id = $this->id;
                $jogosimage->images_id = $image->id;
                $jogosimage->store();
            }
        }
    }

     public function delete($id = NULL)
    {
        $id = isset($id) ? $id : $this->id;
        
        JogosImages::where('jogos_id', '=', $id)->delete();
        
        parent::delete($id);
    }
}

