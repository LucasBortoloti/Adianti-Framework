<?php

use Adianti\Database\TRecord;

/**
 * Sale Active Record
 * @author Lucas Bortoloti <bortoloti91@gmail.com
 */
class Sale extends TRecord
{
    const TABLENAME = 'sale';
    const PRIMARYKEY = 'id';
    const IDPOLICY =  'max'; // {max, serial}
    private $cliente;
    private $products;
    private $sale_items;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {

        parent::__construct($id, $callObjectLoad);

        parent::addAttribute('id');
        parent::addAttribute('date');
        parent::addAttribute('cliente_id');
        parent::addAttribute('total');
        parent::addAttribute('status_id');
    }

    //feito para eu pegar a quantidade da tabela sale_items e do model saleitems
    public function get_saleitems()
    {
        if (empty($this->sale_items)) {
            $this->sale_items = SaleItem::where('sale_id', '=', $this->id)->load();
        }
        return $this->sale_items;
    }

    // feito para eu pegar os produtos da tabela e do model product
    public function get_products()
    {
        $product_ids = SaleItem::where('sale_id', '=', $this->id)->getIndexedArray('product_id');
        $this->products = Product::where('id', 'IN', $product_ids)->load();

        return $this->products;
    }

    public function set_product(Product $object)
    {
        $this->product = $object;
    }

    public function get_product()
    {
        if (empty($this->product)) {
            $this->product = new Product($this->product_id);
        }

        return $this->product;
    }

    public function set_cliente(Cliente $object)
    {
        $this->cliente = $object;
    }

    public function get_cliente()
    {
        if (empty($this->cliente)) {
            $this->cliente = new Cliente($this->cliente_id);
        }

        return $this->cliente;
    }

    public function set_status(SaleStatus $object)
    {
        $this->status = $object;
    }

    public function get_status()
    {
        if (empty($this->status)) {
            $this->status = new SaleStatus($this->status_id);
        }

        return $this->status;
    }

    public function onBeforeDelete($param)
    {

        try {   //echo "<pre>";
            //print_r($param);

            // TTransaction::open('sale');

            SaleItem::where('sale_id', '=', $this->id)->delete();

            // foreach( $sale_items as $item){

            //     $item->delete();
            // }

            // parent::delete();

            // TTransaction::close();

        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
        //echo "</pre>";
    }
}
