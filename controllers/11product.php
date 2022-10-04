<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script type="text/javascript" src="/cocomic/client/js/jquery-3.6.0.min.js"></script>
</head>
<script>
function getProduct() {
    $.ajax({
        url: 'http://localhost/PHP/obrien/product/getproduct',
        type: 'get',
        data: {
            'ID': 2,
        },
        success: function(data) {
            var obj = JSON.parse(data);
            // $('#textShow').append(obj);
            console.log(obj);
        }
    })

}

function getlistProduct() {
    $.ajax({
        url: 'http://localhost/PHP/obrien/product/listproduct',
        type: 'get',
        data: {
            'page': 1,
            'perPage': 4,
            'searchType': 'name',
        },
        success: function(data) {
            var obj = JSON.parse(data);
            // $('#textShow').append(obj);
            console.log(obj);
        }
    })

}

function updateProduct() {
    $.ajax({
        url: 'http://localhost/PHP/obrien/product/updateProduct',
        type: 'put',
        data: {
            'ID': 1,
            'price': '12',
        },
        success: function(data) {
            var obj = JSON.parse(data);
            console.log(obj);
        }
    })

}

function updateGallery() {
    $.ajax({
        url: 'http://localhost/PHP/obrien/gallery/updateGallery',
        type: 'put',
        data: {
            'ID': 1,
            'URLImage': '12',
        },
        success: function(data) {
            var obj = JSON.parse(data);
            console.log(obj);
        }
    })

}

function addImage() {
    $.ajax({
        url: 'http://localhost/PHP/obrien/gallery/addImage',
        type: 'post',
        data: {
            'productID': 1,
            'URLImage': '123',
        },
        success: function(data) {
            var obj = JSON.parse(data);
            console.log(obj);
        }
    })

}

function deleteGallery() {
    $.ajax({
        url: 'http://localhost/PHP/obrien/gallery/deleteimage',
        type: 'Delete',
        data: {
            'ID': 4,
        },
        success: function(data) {
            var obj = JSON.parse(data);
            console.log(obj);
        }
    })

}

function deleteProduct() {
    $.ajax({
        url: 'http://localhost/PHP/obrien/product/deleteproduct',
        type: 'delete',
        data: {
            'ID': 5,
        },
        success: function(data) {
            var obj = JSON.parse(data);
            console.log(obj);
        }
    })

}

function createProduct() {
    $.ajax({
        url: 'http://localhost/PHP/obrien/product/createproduct',
        type: 'post',
        data: {
            'name': 'kiwi',
        },
        success: function(data) {
            var obj = JSON.parse(data);
            console.log(obj);
        }
    })

}
</script>

<body>
    <button onclick="getProduct()">getByID</button>
    <p></p>
    <button onclick="getlistProduct()">getlist</button>

    <p></p>
    <button onclick="createProduct()">create</button>
    <p></p>
    <button onclick="updateProduct()">updateProduct</button>
    <p></p>
    <button onclick="deleteProduct()">deleteProduct</button>
    <p></p>
    <button onclick="addImage()">addImage</button>
    <p></p>
    <button onclick="updateGallery()">updateGallery</button>
    <p></p>
    <button onclick="deleteGallery()">deleteGallery</button>
    <p></p>



</body>

</html>