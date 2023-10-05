<?php

use Adianti\Database\TRecord;

/**
 * Jogos Active Record
 * @author Lucas Bortoloti <bortoloti91@gmail.com
 */
class Jogos extends TRecord
{

    const TABLENAME = 'jogos';
    const PRIMARYKEY = 'id';
    const IDPOLICY =  'max'; // {max, serial}

    private $desenvolvedoras;
    private $jogos_images;
    private $images;

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
        parent::addAttribute('thumbnail');
        parent::addAttribute('sinopse');
        parent::addAttribute('avaliacoes');
        parent::addAttribute('vendas');
    }

    public function set_desenvolvedoras(Desenvolvedoras $object)
    {
        $this->desenvolvedoras = $object;
        $this->desenvolvedoras_id = $object->nome;
    }

    public function get_desenvolvedoras()
    {
        if (empty($this->desenvolvedoras)) {
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

    public function addImages(Images $images)
    {
        $this->images[] = $images;
    }

    public function getImages()
    {

        // loads the associated object

        if (empty($this->images)) {

            $this->images = Images::where('id', '=', $this->id)->load();
        }

        // returns the associated object

        return $this->images;
    }

    public function clearParts()
    {
        $this->images = array();
    }

    public function load($id)
    {
        $jogos_images = JogosImages::where('jogos_id', '=', $id)->load();

        if ($jogos_images) {
            foreach ($jogos_images as $jogos_image) {
                $this->addImages(new Images($jogos_image->images_id));
            }
        }

        return parent::load($id);
    }

    public function store()
    {
        parent::store();

        JogosImages::where('jogos_id', '=', $this->id)->delete();

        if ($this->images) {
            foreach ($this->images as $image) {
                $jogos_image = new JogosImages;
                $jogos_image->jogos_id = $this->id;
                $jogos_image->images_id = $image->id;
                $jogos_image->store();
            }
        }
    }

    public function delete($id = NULL)
    {
        $id = isset($id) ? $id : $this->id;

        JogosImages::where('jogos_id', '=', $id)->delete();

        parent::delete($id);
    }

    public function toArray($attributes = [], $relationships = [])
    {

        try {

            TTransaction::open('jogos');

            $resource = parent::toArray($attributes);

            // if FULL

            $images = $this->getImages();

            if (count($images) > 0) {
                $resource['images'] = array_map(
                    function ($item) {
                        return $item->toArray();
                    },

                    $images

                );
            }

            TTransaction::close();

            return $resource;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
