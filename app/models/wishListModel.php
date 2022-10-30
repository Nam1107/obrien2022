<?php

class wishListModel
{
    protected $ID;
    protected int $userID;
    protected int $productID;
    protected String $createdAt;



    public function __construct($ID, $userID, $productID, $createdAt)
    {
        $this->ID = $ID;
        $this->userID = $userID;
        $this->productID = $productID;
        $this->createdAt = $createdAt;
    }
    public function getID()
    {
        return $this->createdAt;
    }
}