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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js" integrity="sha512-3gJwYpMe3QewGELv8k/BX9vcqhryRdzRMxVfq6ngyWXwo03GFEzjsUm8Q7RZcHPHksttq7/GFoxjCVUjkjvPdw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $(document).ready(function() {
            displayData();
        });

        function formatDateTime(dateTimeStr) {
            const options = { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit' };
            const dateTime = new Date(dateTimeStr);
            return dateTime.toLocaleString('en-US', options);
        }

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
                                '<th scope="col">Action</th>' +
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
                                    '<td>Actions</td>' +
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
    </script>
</body>
</html>
