<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css">
    <title>Admin view</title>
</head>
<body>
    <div class="mx-3" id="post-table">
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit post</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body pb-0">
                    <form>
                        <input type="hidden" id="post-id">
                        <label for="posttitle">Post title</label>
                        <input type="text" name="title" id="posttitle">
                        <br>
                        <label for="postbody">Post body</label>
                        <textarea name="body" id="postbody"></textarea>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-success update-post">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js" integrity="sha512-3gJwYpMe3QewGELv8k/BX9vcqhryRdzRMxVfq6ngyWXwo03GFEzjsUm8Q7RZcHPHksttq7/GFoxjCVUjkjvPdw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            displayData();
        });

        // Format update & create timestamps
        function formatDateTime(dateTimeStr) {
            const options = { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit' };
            const dateTime = new Date(dateTimeStr);
            return dateTime.toLocaleString('en-US', options);
        }

        // Display table with data
        function displayData() {
            $.ajax({
                url: '{{ route('admin_posts') }}',
                method: 'post',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(response) {
                    var tableDiv = $('#post-table');
                    tableDiv.empty();
                    var allPostsH2 = "<h2>Admin view</h2>";
                    tableDiv.append(allPostsH2);

                    if (response.length > 0) {
                        var table = '<table class="table">' +
                            '<thead class="thead-dark">' +
                            '<tr>' +
                                '<th scope="col">Post id</th>' +
                                '<th scope="col">Post title</th>' +
                                '<th scope="col">Created</th>' +
                                '<th scope="col">Updated</th>' +
                                '<th scope="col">Author name</th>' +
                                '<th scope="col">Actions</th>' +
                            '</tr>' +
                            '</thead>' +
                            '<tbody>';

                        response.forEach(function(post) {
                            var tableContent = '<tr>' +
                                    '<td>' + post.id + '</td>' +
                                    '<td>' + post.title + '</td>' +
                                    '<td>' + formatDateTime(post.created_at) + '</td>' +
                                    '<td>' + formatDateTime(post.updated_at) + '</td>' +
                                    '<td>' + post.author + '</td>' +
                                    '<td>' +
                                        '<button type="button" class="btn btn-primary edit-post" data-post="' + post.id + '">' +
                                        'Edit' +
                                        '</button>' +
                                        '<button type="button" class="btn btn-danger delete-post" data-post="' + post.id + '">Delete</button>' +
                                    '</td>' +
                                '</tr>';
                            table += tableContent;
                        })

                        table += '</tbody>' +
                            '</table>';
                        tableDiv.append(table);
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                    console.log(status);
                    console.log(error);
                }
            });
        }

        // Show modal
        function showEditModal(postId) {
            $.ajax({
                url: '/admin-view/edit-post/' + postId,
                method: 'get',
                success: function(response) {
                    $('#post-id').val(response.id);
                    $('#posttitle').val(response.title);
                    $('#postbody').val(response.body);

                    $('#exampleModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                    console.log(status);
                    console.log(error);
                }
            });
        }

        // Trigger on 'Edit' button
        $(document).on('click', '.edit-post', function() {
            var postId = $(this).data('post');
            showEditModal(postId);
        });

        // Trigger on 'Update' button
        $(document).on('click', '.update-post', function() {
            var postId = $('#post-id').val();
            var postTitle = $('#posttitle').val();
            var postBody = $('#postbody').val();

            updatePost(postId, postTitle, postBody);
        });

        // Trigger on 'Delete' button
        $(document).on('click', '.delete-post', function() {
            var postId = $(this).data('post');
            deletePost(postId);
        });

        // Update post
        function updatePost(postId, postTitle, postBody) {
            $.ajax({
                url: '/admin-view/edit-post/' + postId,
                method: 'put',
                data: {
                    title: postTitle,
                    body: postBody
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#exampleModal').modal('hide');
                    displayData();
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                    console.log(status);
                    console.log(error);
                }
            });
        }

        // Delete post
        function deletePost(postId) {
            $.ajax({
                url: '/admin-view/delete-post/' + postId,
                method: 'delete',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    displayData();
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                    console.log(status);
                    console.log(error);
                }
            });
        }
    </script>
</body>
</html>
