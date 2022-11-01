<?php

class cartModel
{
    protected $table = 'user';
    public $middle;
    public function __construct()
    {
        $this->middle = new middleware();
    }
    public function getCart($id)
    {
        $shoppingCart = custom("
        SELECT shoppingCart.productID,product.name,product.image ,shoppingCart.quanity,  A.unitPrice, unitPrice*quanity AS subTotal,IF(quanity<A.stock,1, 0) AS status
        FROM (SELECT *, IF(startSale<NOW() && endSale>NOW(),product.priceSale, product.price) AS unitPrice
        FROM product) AS A,shoppingCart,product
        WHERE A.ID = shoppingCart.productID
        AND userID = $id
        AND shoppingCart.productID = product.ID
        AND product.IsPublic = 1
        ");
        $total = 0;
        foreach ($shoppingCart as $key => $val) {
            $total = $total + $val['subTotal'];
        }
        $res['status'] = 1;
        $res['numOfProduct'] = count($shoppingCart);
        $res['total'] = $total;
        $res['obj'] = $shoppingCart;

        return $res;
    }
    public function getProductInCart($userID, $productID)
    {
        $condition = [
            'userID' => $userID,
            'productID' => $productID,
        ];
        $obj = selectOne('shoppingCart', $condition);
        if (!$obj) {
            $res['status'] = 0;
            $res['errors'] = 'Cannot found product in your cart';
            dd($res);
            exit();
        }
        if ($obj['quanity'] > 5) {
            $res['status'] = 0;
            $res['errors'] = 'You cannot add more than 6 quantities of this product';
            dd($res);
            exit();
        }
        return $obj;
    }
}