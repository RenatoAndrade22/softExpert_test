<?php

require_once 'models/Product.php';
require_once 'helpers/ResponseHandler.php';
require_once 'helpers/MonetaryConverter.php';

class ProductController
{
    public function index()
    {
        $products = new Product();
        
        $products = $products->getAll();

        ResponseHandler::reponse('', 200, $products);
    }

    public function getAllActive()
    {
        $products = new Product();
        
        $products = $products->getAllActive();

        ResponseHandler::reponse('', 200, $products);
    }

    public function show($request)
    {
        $id = (int)$request['id'];

        if (is_int($id) && $id != 0) {

            $product = new Product();
        
            $product = $product->getById($id);

            if(!$product){
                $product = [];
            }

            ResponseHandler::reponse('', 200, $product);

        } else {
            ResponseHandler::reponse('', 400, 'invalid ID');
        }
    }

    public function store()
    {

        $data = $this->getDataRequest();

        
        if (!isset($data['name']) || !isset($data['price']) || !isset($data['products_type_id'])) {
            ResponseHandler::reponse('invalid data', 400);
        }

        $productModel = new Product();

        $productModel->name = $data['name'];
        $productModel->price = MonetaryConverter::dolarToCents($data['price']);
        $productModel->products_type_id = $data['products_type_id'];

        $product = $productModel->save();
       
        ResponseHandler::reponse('', 200, $product);

    }

    public function update($request)
    {

        $id = (int)$request['id'];

        if (is_int($id) && $id != 0) {

            $data = $this->getDataRequest();

            $productModel = new Product();

            $product = $productModel->getById($id);

            $productModel->id = $product->id;
            $productModel->name  = isset($data['name']) && $data['name'] ? $data['name'] : $product->name;
            $productModel->price = isset($data['price']) && $data['price'] ? MonetaryConverter::dolarToCents($data['price']) : $product->price;
            $productModel->products_type_id  = isset($data['products_type_id']) && $data['products_type_id'] ? $data['products_type_id'] : $product->products_type_id;

            $productUpdate = $productModel->update();
        
            ResponseHandler::reponse('', 200, $productUpdate);

        }else{
            ResponseHandler::reponse('', 400, 'invalid ID');
        }

    }

    public function destroy($request)
    {

        $id = (int)$request['id'];

        if (is_int($id) && $id != 0) {

           
            $productModel = new Product();

            if($productModel->delete($id)){
                ResponseHandler::reponse('Product deleted', 200, []);
            }else{
                ResponseHandler::reponse('Error when trying to delete product', 400, []);
            }


        }else{
            ResponseHandler::reponse('', 400, 'invalid ID');
        }

    }

    public function getDataRequest()
    {
        $requestData = file_get_contents('php://input');

        $data = json_decode($requestData, true);

        return $data;
    }
}


