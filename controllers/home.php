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
        url: 'http://localhost/PHP/obrien/user/listuser',
        type: 'get',
        data: {
            'ID': 5,

        },
        success: function(data) {
            var obj = JSON.parse(data);

            console.log(obj);
        }
    })

}
</script>

<body>
    <div class="textShow">

    </div>
    <button onclick="Show()">Hello</button>

</body>

</html>