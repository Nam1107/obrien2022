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
function Show() {
    $.ajax({
        url: 'http://localhost/PHP/obrien/Api/user/updateprofile',
        type: 'put',
        data: {
            'ID': 5,
            'email': 'user3@user',
            'name': 'tết123',

        },
        success: function(data) {
            var obj = JSON.parse(data);
            console.log(obj);
        }
    })

}
</script>

<body>
    <button onclick="Show()">Hello</button>

</body>

</html>