<?php
class testModel
{
    private $name;
    private $address;

    function test($name, $address)
    {
        $this->name = $name;
        $this->address = $address;
    }
    function ToView()
    {
        $testModel = [
            'name' => $this->name,
            'address' => $this->address
        ];
        echo json_encode($testModel);
    }
}