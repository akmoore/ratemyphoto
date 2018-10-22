<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Access Code</title>
</head>
<body>
    <p>{{$user->first_name}},</p>
    <p>Below is your <b>Rate My Photo</b> access code. It expires in two minutes.</p>
    <p>Access Code: <b style="color:red;">{{$token}}</b></p>
</body>
</html>