<?php

class cartModel extends Controllers
{
    public function getCart($id)
    {
        $shoppingCart = custom("
        SELECT shoppingCart.productID,product.name,product.image ,shoppingCart.quantity,  A.unitPrice, unitPrice*quantity AS subTotal,IF(quantity<A.stock,1, 0) AS status
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
        return $obj;
    }
}