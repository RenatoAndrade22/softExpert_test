<?php

require_once 'models/ProductType.php';
require_once 'helpers/ResponseHandler.php';

class ProductTypeController
{
    public function index()
    {
        $productType = new ProductType();
        
        $productTypes = $productType->getAll();

        ResponseHandler::reponse('', 200, $productTypes);
    }

    public function getAllActive()
    {
        $productType = new ProductType();
        
        $productTypes = $productType->getAllActive();

        ResponseHandler::reponse('', 200, $productTypes);
    }

    public function show($request)
    {
        $id = (int)$request['id'];

        if (is_int($id) && $id != 0) {

            $productType = new ProductType();
        
            $productType = $productType->getById($id);

            if(!$productType){
                $productType = [];
            }

            ResponseHandler::reponse('', 200, $productType);

        } else {
            ResponseHandler::reponse('', 400, 'invalid ID');
        }
    }

    public function store()
    {

        $data = $this->getDataRequest();
        
        if (!isset($data['name']) || !isset($data['tax_percentage']) || !isset($data['status'])) {
            ResponseHandler::reponse('invalid data', 400);
        }

        $productType = new ProductType();

        $productType->name = $data['name'];
        $productType->tax_percentage = $data['tax_percentage'];
        $productType->status = $data['status'];

        $productType = $productType->save();
       
        ResponseHandler::reponse('', 200, $productType);

    }

    public function update($request)
    {

        $id = (int)$request['id'];

        if (is_int($id) && $id != 0) {

            $data = $this->getDataRequest();

            $productTypeModel = new ProductType();

            $productType = $productTypeModel->getById($id);

            $productTypeModel->id = $productType->id;
            $productTypeModel->name  = isset($data['name']) && $data['name'] ? $data['name'] : $productType->name;
            $productTypeModel->tax_percentage = isset($data['tax_percentage']) && $data['tax_percentage'] ? $data['tax_percentage'] : $productType->tax_percentage;
            $productTypeModel->status = isset($data['status']) ? $data['status'] : $productType->status;

            $producTypeUpdate = $productTypeModel->update();
        
            ResponseHandler::reponse('', 200, $producTypeUpdate);

        }else{
            ResponseHandler::reponse('', 400, 'invalid ID');
        }

    }

    public function destroy($request)
    {

        $id = (int)$request['id'];

        if (is_int($id) && $id != 0) {

           
            $productTypeModel = new ProductType();

            if($productTypeModel->delete($id)){
                ResponseHandler::reponse('Product type deleted.', 200, []);
            }else{
                ResponseHandler::reponse('Error when trying to delete product type', 400, []);
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


