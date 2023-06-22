<?php

require_once 'models/Product.php';
require_once 'models/Sale.php';
require_once 'models/SaleItem.php';
require_once 'helpers/ResponseHandler.php';

class SaleController
{
    public function index()
    {
        $sales = new Sale();
        
        $sales = $sales->getAll();

        ResponseHandler::reponse('', 200, $sales);
    }

    public function store()
    {

        $data = $this->getDataRequest();

        if(count($data) > 0){

            $sale = new Sale();
            $sale_record = $sale->save();

            foreach($data as $item)
            {
                $product = new Product();
                $product = $product->getById($item['product_id']);
                
                $value = $product->price * (int)$item['quantity'];

                $sale_item = new SaleItem();
                $sale_item->sale_id = $sale_record['id'];
                $sale_item->product_id = $product->id;
                $sale_item->quantity = $item['quantity'];
                $sale_item->value = $value;
                $sale_item->tax_value = round($value * ($product->tax_percentage / 100));

                $sale_item->save();
                
            }
        }
    }

    public function destroy($request)
    {

        $id = (int)$request['id'];

        if (is_int($id) && $id != 0) {

           
            $saleModel = new Sale();

            if($saleModel->delete($id)){
                ResponseHandler::reponse('Product type deleted.', 200, []);
            }else{
                ResponseHandler::reponse('Error when trying to delete product type', 400, []);
            }


        }else{
            ResponseHandler::reponse('invalid ID', 400, []);
        }

    }

    public function getDataRequest()
    {
        $requestData = file_get_contents('php://input');

        $data = json_decode($requestData, true);

        return $data;
    }
}