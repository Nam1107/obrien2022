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
function getUser() {
    $.ajax({
        url: 'http://localhost/PHP/obrien/user/getuser',
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

function getProfile() {
    $.ajax({
        url: 'http://localhost/PHP/obrien/user/getprofile',
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
        url: 'http://localhost/PHP/obrien/user/listuser',
        type: 'get',
        data: {
            'page': 1,
            'perPage': 4,
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
        url: 'http://localhost/PHP/obrien/user/updateprofile',
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

function deleteUser() {
    $.ajax({
        url: 'http://localhost/PHP/obrien/user/deleteuser',
        type: 'delete',
        data: {
            'ID': 14,
        },
        success: function(data) {
            var obj = JSON.parse(data);
            console.log(obj);
        }
    })

}

function changePass() {
    $.ajax({
        url: 'http://localhost/PHP/obrien/user/changePassword',
        type: 'POST',
        data: {
            'password': '654321',
            're_pass': '654321',
        },
        success: function(data) {
            var obj = JSON.parse(data);
            console.log(obj);
        }
    })

}

function Login() {
    $.ajax({
        url: 'http://localhost/PHP/obrien/auth/login',
        type: 'POST',
        data: {
            'email': 'admin@admin',
            'password': '654321',
        },
        success: function(data) {
            var obj = JSON.parse(data);
            console.log(obj);
        }
    })

}

function Logout() {
    $.ajax({
        url: 'http://localhost/PHP/obrien/auth/logout',
        type: 'POST',
        success: function(data) {
            var obj = JSON.parse(data);
            console.log(obj);
        }
    })

}

function Register() {
    $.ajax({
        url: 'http://localhost/PHP/obrien/auth/register',
        type: 'POST',
        data: {
            'firstName': 'Tran',
            'lastName': 'Nam',
            'email': 'admin@admin',
            'password': '123456',
            're_pass': '123456',
        },
        success: function(data) {
            var obj = JSON.parse(data);
            console.log(obj);
        }
    })

}
</script>

<body>
    <button onclick="getUser()">getuser</button>
    <p></p>
    <button onclick="getProfile()">getProfile</button>
    <p></p>
    <button onclick="getlistUser()">getlist</button>
    <p></p>
    <button onclick="updateProfile()">Update</button>
    <p></p>
    <button onclick="deleteUser()">delete</button>
    <p></p>
    <button onclick="changePass()">changePass</button>
    <p></p>

    <button onclick="Register()">register</button>
    <p></p>
    <button onclick="Login()">login</button>
    <p></p>
    <button onclick="Logout()">logout</button>
    <p></p>

</body>

</html>