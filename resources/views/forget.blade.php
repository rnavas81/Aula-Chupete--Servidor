<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Activar cuenta</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Delius&display=swap" rel="stylesheet">

    <!-- Styles -->

    <style>
        *,
        :after,
        :before {
            box-sizing: border-box;
            border: 0 solid #e2e8f0
        }

        html {
            line-height: 1.15;
            -webkit-text-size-adjust: 100%;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji;
            line-height: 1.5;
        }

        body {
            margin: 0;
            font-family: 'Delius', cursive;
        }

        [hidden] {
            display: none
        }

        a {
            background-color: transparent;
            color: inherit;
            text-decoration: inherit
        }

        .container {
            color: #34495e;
            margin: 4% 10% 2%;
            text-align: justify;
        }

        .cabecera {
            color: #e67e22;
            margin: 0 0 7px;
        }

        p.parrafo {
            margin: 10px 5px;
            font-size: 15px
        }

        .cierre {
            color: #b3b3b3;
            font-size: 12px;
            font-family: sans-serif;
            text-align: center;
            margin: 30px 0 0
        }
    </style>
    <style>
        @media (min-width:640px) {}

        @media (min-width:768px) {}

        @media (min-width:1024px) {}
    </style>
</head>

<body class="">
    <div class="container">
        <h2 class="caebecera">Saludos de <b>Aula Chupetes</b></h2>
        <p class='parrafo'>
            Le comunicamos que su contraseña ha sido modificada.
            Si usted no ha solicitado este cambio pongase en contacto lo antes posible con nosotros a través de nuestra dirección de correo:
            aula.chupetes@gmail.com
        </p>
        @if (isset($password))
        <p class='parrafo'>
            Su nueva contaseña es: <b>{{ $password ?? 'Ha ocurrido un error.' }}</b></p>
        </p>
        <p class='parrafo'>
            Para cualquier problema póngase en contacto con nosotros a través de nuestra dirección de correo:
            aula.chupetes@gmail.com
        </p>
        @else
        <p class="parrafo">
            Ha ocurrido un error con la solicitud, por favor pongase en contacto con nosotros a través de:
            aula.chupetes@gmail.com
        </p>
        @endif
        <p class="cierre">AulaChupetes©</p>
    </div>
</body>

</html>
