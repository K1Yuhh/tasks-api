<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register</title>
    <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@latest/css/pico.min.css">
</head>
<body>

    <main class="container">

        <h1>Register</h1>

        <form method="post">

            <label for="name">
                Name
                <input id="name" type="text" name="name" />
            </label>

            <label for="username">
                Username
                <input id="username" type="text" name="username" />
            </label>

            <label for="password">
                Password
                <input id="password" type="password" name="password" />
            </label>

            <button>Register</button>
        </form>
    </main>
</body>
</html>