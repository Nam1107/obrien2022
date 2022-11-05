<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script type="text/javascript" src="./js/path.js"></script>
    <script type="text/javascript" src="./js/jquery-3.6.0.min.js"></script>
</head>
<script>
function getProduct() {
    var id = 6;
    $.ajax({
        url: ROOT + 'product/getproduct/' + id,
        type: 'get',

        success: function(data) {
            var obj = JSON.parse(data);
            // $('#textShow').append(obj);
            console.log(obj);
        }
    })

}




function getlistProduct() {
    $.ajax({
        url: ROOT + 'product/listproduct',
        type: 'get',
        data: JSON.stringify({
            'page': 1,
            'perPage': 4,
            'category': '',
            'name': '',
            'sale': '',
            'sortType': 'ASC',
            'sortBy': 'name'
        }),
        success: function(data) {
            var obj = JSON.parse(data);
            // $('#textShow').append(obj);
            console.log(obj);
        }
    })

}

function updateProduct() {
    var id = 1;
    $.ajax({
        url: ROOT + 'product/updateProduct/' + id,
        type: 'put',
        data: {

            'price': '12',
        },
        success: function(data) {
            var obj = JSON.parse(data);
            console.log(obj);
        }
    })

}

function updateGallery() {
    var id = 24;
    $.ajax({
        url: ROOT + 'gallery/updateGallery/' + id,
        type: 'put',
        data: {
            'URLImage': '1284',
            'Sort': 2,
        },
        success: function(data) {
            var obj = JSON.parse(data);
            console.log(obj);
        }
    })

}

function addImage() {
    var id = 1;
    var image = ['123', '456'];
    $.ajax({
        url: ROOT + 'gallery/addImage/' + id,
        type: 'post',
        data: {
            'gallery': ['URL', 'URL'],
        },
        success: function(data) {
            var obj = JSON.parse(data);
            console.log(obj);
        }
    })

}

function deleteGallery() {
    var id = 24;
    $.ajax({
        url: ROOT + 'gallery/deleteimage/' + id,
        type: 'Delete',
        success: function(data) {
            var obj = JSON.parse(data);
            console.log(obj);
        }
    })

}

function deleteProduct() {
    var id = 19;
    $.ajax({
        url: ROOT + 'product/deleteproduct/' + id,
        type: 'delete',
        success: function(data) {
            var obj = JSON.parse(data);
            console.log(obj);
        }
    })

}

function createProduct() {
    $.ajax({
        url: ROOT + 'product/createproduct',
        type: 'post',
        data: {
            'category': 'fruits',
            'name': 'kiwi',
            'gallery': ['123', '456'],
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