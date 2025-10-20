<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Bienvenido a Intranet TEQMED</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f9fafb; padding: 30px;">
    <div style="max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; padding: 20px;">
        <div class="header" style="text-align: center; margin-bottom: 20px;"></div>
        <img src="{{ asset('favicon.ico') }}" alt="TEQMED Logo" class="logo">
    </div>
    <h2 style="color: #004a6b;"> ¡Bienvenido, {{ $user->name }}!</h2>

    <p>Tu cuenta ha sido creada en <strong>Intranet TEQMED</strong>.</p>

    <p>Puedes acceder con las siguientes credenciales:</p>

    <ul style="background: #f4f4f5; padding: 10px 20px; border-radius: 6px;">
        <li><strong>Correo:</strong> {{ $user->email }}</li>
        <li><strong>Contraseña:</strong> {{ $password }}</li>
    </ul>

    <p>Accede al sistema en: <a href="{{ url('/') }}">{{ url('/') }}</a></p>

    <p style="margin-top: 20px;">Por seguridad, te recomendamos cambiar tu contraseña al iniciar sesión.</p>

    <p style="color: #6b7280; font-size: 12px; margin-top: 30px;">
        — Equipo TEQMED SpA
    </p>
    <div class="footer">
        &copy; {{ date('Y') }} TEQMED SpA | Técnicos en equipos médicos SpA. Todos los derechos reservados.
    </div>
    </div>
</body>

</html>
