<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifica tu cuenta</title>
</head>
<body>
    <h1>Hola, {{ $user->name }}</h1>
    <p>Tu código de verificación es: <strong>{{ $otp_code }}</strong></p>
    <p>Ingresa este código en la web para activar tu cuenta.</p>
</body>
</html>
