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

function updateProfile() {
    $.ajax({
        url: 'http://localhost/PHP/obrien/product/updateprofile',
        type: 'put',
        data: {
            'ID': 5,
            'name': '12312',
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
    <button onclick="updateProfile()">Update</button>
    <p></p>
    <button onclick="deleteProduct()">delete</button>
    <p></p>
    <button onclick="createProduct()">create</button>
    <p></p>


</body>

</html>