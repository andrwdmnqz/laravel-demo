<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="css/app.css" rel="stylesheet">
    <title>Home page</title>
</head>
<body>
    @auth
        <p>Authorized</p>
        <form id="logout-form">
            @csrf
            <button type="submit">Log out</button>
        </form>
        <div class="border-div">
            <form id="create-post">
                @csrf
                <h2>Create post</h2>
                <label for="posttitle">Post title</label>
                <input type="text" name="title" id="posttitle">
                <label for="postbody">Post body</label>
                <textarea name="body" id="postbody"></textarea>
                <input type="submit" value="Create" class="input-submit">
            </form>
        </div>

        <div class="border-div" id="show-posts">
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js" integrity="sha512-3gJwYpMe3QewGELv8k/BX9vcqhryRdzRMxVfq6ngyWXwo03GFEzjsUm8Q7RZcHPHksttq7/GFoxjCVUjkjvPdw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        $(document).ready(function() {
            displayPosts();
        });

        function displayPosts() {
            $.ajax({
                url: '{{ route('show_posts') }}',
                method: 'get',
                dataType: 'json',
                success: function(response) {
                    var postsDiv = $('#show-posts');
                    postsDiv.empty();
                    var allPostsH2 = "<h2>All posts</h2>";
                    postsDiv.append(allPostsH2);

                    if (response.length > 0) {
                        response.forEach(function(post) {
                            var postHtml = '<div class="post">' +
                                '<h3>' + post.title + ' by ' + post.author + '</h3>' +
                                post.body +
                                (post.is_author ? '<p><a href="/edit-post/' + post.id + '">Edit</a></p>' +
                                '<form action="/delete-post/' + post.id + '" method="POST">' +
                                    '@csrf' +
                                    '@method("DELETE")' +
                                    '<button type="submit">Delete</button>' +
                                '</form>' : '') +
                            '</div>';
                            postsDiv.append(postHtml);
                        })
                    }
                }
            });
        }

        $('#logout-form').submit(function(event) {
            event.preventDefault();

            $.ajax({
                url: '{{ route('logout') }}',
                method: 'post',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log(response);
                    location.reload();
                }
            })
        });

        $('#create-post').submit(function(event) {
            event.preventDefault();

            var form = $(this);
            var formData = form.serialize();

            $.ajax({
                url: '{{ route('create_post') }}',
                method: 'post',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status == 201) {
                        console.log("Post created successfully");
                        form.trigger('reset');
                    }
                    displayPosts();
                }
            });
        });
    </script>
</body>
</html>
