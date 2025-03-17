<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reset de tu contraseña</title>
</head>
<body>
    <h1>Hola, {{ $user->name }}</h1>
    <p>Tu código de verificación es: <strong>{{ $user->re_get_password_token }}</strong></p>
    <p>Ingresa este código en la web para cambiar la contraseña.</p>
</body>
</html>
