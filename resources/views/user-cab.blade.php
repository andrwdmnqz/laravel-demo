<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <title>Document</title>
</head>
<body>
    <h2>User cabinet</h2>
    <h4>Hi, {{ $user->name }}</h4>
    <a href="/">Back to main page</a>
    <br><br>
    <img src="/storage/{{ $user->image }}" alt="user_avatar" style="width: 10%;">

    <br><br>
    <form action="/edit-user/{{ $user->id }}" enctype="multipart/form-data" method="POST">
        @csrf
        @method('PUT')
        <label for="#editname">Change your name: </label>
        <input type="text" name="name" id="editname" value="{{ $user->name }}">
        <label for="#editemail">Change your email: </label>
        <input type="email" name="email" id="editemail" value="{{ $user->email }}">
        <label for="#editpassword">Change your password: </label>
        <input type="password" name="password" id="editpassword" value="">
        <br>
        <label for="#image">Change your avatar:</label>
        <input type="file" name="img" id="image">
        <br>
        <input type="submit" value="Update">
    </form>
</body>
</html>
