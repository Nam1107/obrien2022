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
function getUser() {
    var id = $('#idUser').val();
    $.ajax({
        url: ROOT + 'user/getuser/' + id,
        type: 'get',
        success: function(data) {
            var obj = JSON.parse(data);
            // $('#textShow').append(obj);
            console.log(obj);
        }
    })

}

function getProfile() {
    $.ajax({
        url: ROOT + 'user/getprofile',
        type: 'get',
        success: function(data) {
            var obj = JSON.parse(data);
            // $('#textShow').append(obj);
            console.log(obj);
        }
    })

}

function getlistUser() {
    $.ajax({
        url: ROOT + 'user/listuser',
        type: 'get',
        data: {

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
        url: ROOT + 'user/updateprofile',
        type: 'put',
        data: {
            'name': $('#nameProfile').val(),
        },
        success: function(data) {
            var obj = JSON.parse(data);
            console.log(obj);
        }
    })

}

function updateUser() {
    var id = $('#IDUpdate').val();
    $.ajax({
        url: ROOT + 'user/updateUser/' + id,
        type: 'put',
        data: {
            'name': $('#nameUpdate').val(),
        },
        success: function(data) {
            var obj = JSON.parse(data);
            console.log(obj);
        }
    })

}

function deleteUser() {
    var id = $('#idDelete').val();
    $.ajax({
        url: ROOT + 'user/deleteuser/' + id,
        type: 'delete',
        success: function(data) {
            var obj = JSON.parse(data);
            console.log(obj);
        }
    })

}

function changePass() {
    $.ajax({
        url: ROOT + 'user/changePassword',
        type: 'POST',
        data: {
            'password': $('#oldPass').val(),
            'newPass': $('#newPass').val(),
        },
        success: function(data) {
            var obj = JSON.parse(data);
            console.log(obj);
        }
    })

}

function setPass() {
    var id = $('#idSetPass').val();
    $.ajax({
        url: ROOT + 'user/setPassword/' + id,
        type: 'POST',
        data: {
            'password': $('#setPass').val(),
        },
        success: function(data) {
            var obj = JSON.parse(data);
            console.log(obj);
        }
    })

}

function Login() {

    $.ajax({
        url: ROOT + 'auth/login',
        type: 'POST',
        data: {
            'email': $("#email").val(),
            'password': $("#password").val(),
        },
        success: function(data) {
            var obj = JSON.parse(data);
            console.log(obj);
        }
    })

}

function Logout() {
    $.ajax({
        url: ROOT + 'auth/logout',
        type: 'POST',
        success: function(data) {
            var obj = JSON.parse(data);
            console.log(obj);
        }
    })

}

function Register() {
    $.ajax({
        url: ROOT + 'auth/register',
        type: 'POST',
        data: {
            'email': $('#emailRegister').val(),
            'password': $('#passwordRegister').val(),
        },
        success: function(data) {
            var obj = JSON.parse(data);
            console.log(obj);
        }
    })

}

function getCart() {
    $.ajax({
        url: ROOT + 'cart/getCart',
        type: 'GET',
        success: function(data) {
            var obj = JSON.parse(data);
            console.log(obj);
        }
    })

}

function addToCart() {
    var id = 6;
    $.ajax({
        url: ROOT + 'cart/addProduct/' + id,
        type: 'POST',
        data: {
            'quanity': 2,
        },
        success: function(data) {
            var obj = JSON.parse(data);
            console.log(obj);
        }
    })

}

function removeFromCart() {
    var id = 6;
    $.ajax({
        url: ROOT + 'cart/removeProduct/' + id,
        type: 'DELETE',
        success: function(data) {
            var obj = JSON.parse(data);
            console.log(obj);
        }
    })

}

function incrementByOne() {
    var id = 1;
    $.ajax({
        url: ROOT + 'cart/incrementByOne/' + id,
        type: 'PUT',
        success: function(data) {
            var obj = JSON.parse(data);
            console.log(obj);
        }
    })

}

function decrementByOne() {
    var id = 1;
    $.ajax({
        url: ROOT + 'cart/decrementByOne/' + id,
        type: 'PUT',
        success: function(data) {
            var obj = JSON.parse(data);
            console.log(obj);
        }
    })

}
</script>
<style>
* {
    display: inline-flexbox;
}

div {
    display: flex;
    padding: 5px;
    margin: 5px
}

.box {
    width: 600px;
    display: flex;
    border-style: solid;
    border-width: 2.5px;
}

button {
    margin: 5px;
}
</style>

<body>

    <div class='box'>
        <div>ID<input id='idUser' type="text"></div>
        <button onclick="getUser()">getuser</button>
        <p></p>
        <button onclick="getlistUser()">getlist</button>
        <p></p>
        <button onclick="getProfile()">getProfile</button>
        <p></p>
        <button onclick="getCart()">getCart</button>
        <p></p>
        <button onclick="addToCart()">addToCart</button>
        <p></p>
        <button onclick="removeFromCart()">removeFromCart</button>
        <p></p>
        <button onclick="incrementByOne()">incrementByOne</button>
        <p></p>
        <button onclick="decrementByOne()">decrementByOne</button>
        <p></p>

    </div>

    <div class='box'>
        <div>Name<input id='nameProfile' type="text"></div>

        <button onclick="updateProfile()">Updateprofile</button>
        <p></p>

    </div>
    <div class='box'>
        <div>ID<input id='IDUpdate' type="text"></div>
        <div>Name<input id='nameUpdate' type="text"></div>
        <button onclick="updateUser()">UpdateUser</button>
        <p></p>

    </div>
    <div class='box'>
        <div>ID<input id='idDelete' type="text"></div>
        <button onclick="deleteUser()">deleteUser</button>
        <p></p>
    </div>

    <div>old<input id='oldPass' type="text"></div>
    <div>pass<input id='newPass' type="text"></div>
    <div>passconfirm<input id='confirmPass' type="text"></div>

    <button onclick="changePass()">changePass</button>
    <p></p>


    <div class='box'>
        <div>ID<input id='idSetPass' type="text"></div>
        <div>pass<input id='setPass' type="text"></div>
        <button onclick="setPass()">setPass</button>
        <p></p>
    </div>

    <div class='box'>
        <div>email<input id='email' type="text"></div>
        <div>pass<input id='password' type="text"></div>

        <button onclick="Login()">login</button>
        <p></p>
        <button onclick="Logout()">logout</button>
        <p></p>
    </div>

    <div>email<input id='emailRegister' type="text"></div>
    <div>pass<input id='passwordRegister' type="text"></div>
    <div>confirm<input id='confirmRegister' type="text"></div>


    <button onclick="Register()">register</button>
    <p></p>








</body>

</html>