<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laravel Hootsuite</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <style>
        body {
            height: 100vh;
            background: rgb(221, 218, 218);
        }
    </style>

</head>
<body class="d-flex justify-content-center align-items-center">
    <div class="container">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title text-center font-weight-bold">Congratulations. The tokens has been saved successfuly :)</h5>
                <ul class="list-group">
                    <li class="list-group-item">Access Token         => {{ $settings->get('hootsuite_access_token') }}</li>
                    <li class="list-group-item">Refresh Access Token => {{ $settings->get('hootsuite_refresh_token') }}</li>
                    <li class="list-group-item">Expiration Date      => {{ $settings->get('hootsuite_token_expires') }}</li>
                </ul>
            </div>
            <div class="card-footer d-flex justify-content-center">
                <a href="/" class="btn btn-primary btn-lg">Go to homepage</a>
            </div>
        </div>

    </div>
</body>
</html>
