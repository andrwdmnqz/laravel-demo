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
        <a href="{{ route('admin_view') }}">Check admin view</a>
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
            <form class="reg-form">
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
            <form class="login-form">
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

        // Function to show all posts
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
                                '<div class="post-title" data-user_id="' + post.author_id +'" data-is_online="' + post.is_online +'">' +
                                '<h3><span class="author-name">' + post.title + ' by ' + post.author + '</span>';

                            if (post.is_online === true) {
                                postHtml += '<span class="status-circle status-online"></span>' +
                                    '<span class="text-success">Online</span>';
                            } else {
                                postHtml += '<span class="status-circle status-offline"></span>' +
                                    '<span class="text-secondary">Offline</span>';
                            }

                            postHtml += ' last seen: ' + post.last_seen +
                                        '</h3>' +
                                        '</div>' +
                                        post.body +
                                        (post.is_author ? '<p><a href="/edit-post/' + post.id + '">Edit</a></p>' +
                                        '<form action="/delete-post/' + post.id + '" method="POST" class="delete-post-form">' +
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

        // Logout ajax query
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
                },
                error: function(xhr, textStatus, errorThrown) {
                    console.log("Error:", errorThrown);
                }
            })
        });

        // Create post ajax query
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

        // Delete post ajax query
        $(document).on('submit', '.delete-post-form', function(event) {
            event.preventDefault();

            var formAction = $(this).attr('action');

            $.ajax({
                url: formAction,
                method: 'delete',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log("Deleted");
                    displayPosts();
                }
            });
        });

        // Registration form
        $(document).on('submit', '.reg-form', function(event) {
            event.preventDefault();
            var form = $(this);
            var formData = form.serialize();

            var name = form.find('#regname').val();
            var email = form.find('#regemail').val();
            var password = form.find('#regpassword').val();

            $.ajax({
                url: '{{ route('register') }}',
                method: 'post',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log("Registered");
                    location.reload();
                },
                error: function(xhr, textStatus, errorThrown) {
                    console.log("Error:", errorThrown);
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        console.log("Validation errors:", xhr.responseJSON.errors);
                    }
                }
            })
        });

        // Login form
        $(document).on('submit', '.login-form', function(event) {
            event.preventDefault();

            var form = $(this);
            var formData = form.serialize();

            $.ajax({
                url: '{{ route('login') }}',
                method: 'post',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log("Logged in successfully");
                    location.reload();
                }
            })
        });
    </script>
</body>
</html>
