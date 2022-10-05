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
    $.ajax({
        url: ROOT + 'user/getuser',
        type: 'get',
        data: {
            'ID': 4,
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
        url: ROOT + 'user/updateprofile',
        type: 'put',
        data: {
            'ID': 4,
            'name': 'test',
            'firstName': '123',
        },
        success: function(data) {
            var obj = JSON.parse(data);
            console.log(obj);
        }
    })

}

function updateUser() {
    $.ajax({
        url: ROOT + 'user/updateUser',
        type: 'put',
        data: {
            'ID': 4,
            'name': 'test',
        },
        success: function(data) {
            var obj = JSON.parse(data);
            console.log(obj);
        }
    })

}

function deleteUser() {
    $.ajax({
        url: ROOT + 'user/deleteuser',
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
        url: ROOT + 'user/changePassword',
        type: 'POST',
        data: {
            'password': '123456',
            'newPass': '654321',
            'confirmPass': '654321',
        },
        success: function(data) {
            var obj = JSON.parse(data);
            console.log(obj);
        }
    })

}

function setPass() {
    $.ajax({
        url: ROOT + 'user/setPassword',
        type: 'POST',
        data: {
            'ID': '14',
            'password': '123456',
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
            'email': 'admin@admin',
            'password': '123456',
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
            'firstName': 'Tran',
            'lastName': 'Nam',
            'email': 'admin1@admin',
            'password': '123456',
            'confirmPass': '123456',
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
    <button onclick="updateProfile()">Updateprofile</button>
    <p></p>
    <button onclick="updateUser()">UpdateUser</button>
    <p></p>
    <button onclick="deleteUser()">delete</button>
    <p></p>
    <button onclick="changePass()">changePass</button>
    <p></p>
    <button onclick="setPass()">setPass</button>
    <p></p>
    <button onclick="Register()">register</button>
    <p></p>
    <button onclick="Login()">login</button>
    <p></p>
    <button onclick="Logout()">logout</button>
    <p></p>

</body>

</html>