<?php

class galleryModel
{
    function getDetail($imageID)
    {
        $image = custom("SELECT * from gallery Where id = $imageID");
        if (!$image) {
            return null;
        } else {
            $image = $image[0];
        }
        return $image;
    }
    function listByProduct($productID)
    {
        $image = custom("SELECT * from gallery Where productID = $productID");
        return $image;
    }
}