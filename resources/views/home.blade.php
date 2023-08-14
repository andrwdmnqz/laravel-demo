<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="css/app.css" rel="stylesheet">
    <title>Home page</title>
</head>
<body>
    @auth
        <p>Authorized</p>
        <form action="/logout" method="POST">
            @csrf
            <button type="submit">Log out</button>
        </form>
        <div class="border-div">
            <form method="POST" action="/create-post">
                @csrf
                <h2>Create post</h2>
                <label for="posttitle">Post title</label>
                <input type="text" name="title" id="posttitle">
                <label for="postbody">Post body</label>
                <textarea name="body" id="postbody"></textarea>
                <input type="submit" value="Create" class="input-submit-zxc">
            </form>
        </div>
    @else
        <h2>Please, sign in</h2>
        <div class="border-div">
            <form method="POST" action="/register">
                @csrf
                <h2>Registration</h2>
                <label for="regname">Username </label>
                <input type="text" name="name" id="regname">
                <label for="regemail">Email </label>
                <input type="email" name="email" id="regemail">
                <label for="regpassword">Password </label>
                <input type="password" name="password" id="regpassword">
                <input type="submit" value="Sign up">
            </form>
        </div>
        <div class="border-div">
            <form action="/login" method="POST">
                @csrf
                <h2>Login</h2>
                <label for="logemail">Email </label>
                <input type="email" name="logemail" id="logemail">
                <label for="logpassword">Password </label>
                <input type="password" name="logpassword" id="logpassword">
                <input type="submit" value="Sign in">
            </form>
        </div>
    @endauth

</body>
</html>
