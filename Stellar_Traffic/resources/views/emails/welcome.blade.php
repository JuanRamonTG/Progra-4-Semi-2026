<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>¡Te damos la bienvenida a Stellar Traffic!</title>
    <style>
        /* Base styles */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Outfit', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #02060f;
            color: #ffffff;
            -webkit-font-smoothing: antialiased;
        }

        /* Container */
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #02060f;
            padding-bottom: 60px;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #030a1c;
            border-radius: 24px;
            overflow: hidden;
            border: 1.5px solid rgba(37, 99, 235, 0.2);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            margin-top: 40px;
        }

        /* Header */
        .header {
            padding: 40px 40px 30px;
            text-align: center;
        }

        .header-brand {
            font-size: 28px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.5px;
        }

        .header-brand span {
            color: #2563eb;
        }

        .header-logo {
            display: inline-block;
            margin-bottom: 12px;
        }

        .header-logo img {
            width: 72px;
            height: 72px;
            border-radius: 16px;
            object-fit: cover;
        }

        /* Accent Line */
        .accent-line {
            height: 4px;
            background: linear-gradient(90deg, transparent, #2563eb, transparent);
            width: 100%;
            opacity: 0.8;
        }

        /* Body */
        .body {
            padding: 0 40px 40px;
        }

        h1 {
            font-size: 24px;
            font-weight: 600;
            margin-top: 0;
            margin-bottom: 24px;
            color: #ffffff;
        }

        p {
            font-size: 16px;
            line-height: 1.6;
            color: #94a3b8;
            margin-top: 0;
            margin-bottom: 24px;
        }

        .bullet-list {
            margin: 0 0 24px 0;
            padding: 0 0 0 20px;
            color: #94a3b8;
            font-size: 16px;
            line-height: 1.6;
        }

        .bullet-list li {
            margin-bottom: 12px;
        }

        .bullet-list li strong {
            color: #ffffff;
        }

        /* Button */
        .btn-container {
            text-align: center;
            margin: 32px 0;
        }

        .btn {
            display: inline-block;
            background-color: #2563eb;
            color: #ffffff !important;
            text-decoration: none;
            font-size: 15px;
            font-weight: 600;
            padding: 14px 36px;
            border-radius: 10px;
            letter-spacing: 0.3px;
        }

        /* Footer */
        .footer {
            padding: 30px 40px;
            background-color: rgba(5, 15, 35, 0.4);
            border-top: 1px solid rgba(37, 99, 235, 0.1);
            text-align: center;
        }

        .footer-text {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 16px;
            line-height: 1.5;
        }

        .footer-link {
            color: #2563eb;
            word-break: break-all;
        }

        .copyright {
            font-size: 12px;
            color: #475569;
            margin: 0;
        }
    </style>
</head>

<body>
    <table class="wrapper" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table class="container" cellpadding="0" cellspacing="0">

                    <!-- HEADER -->
                    <tr>
                        <td>
                            <div class="accent-line"></div>
                            <div class="header">
                                <div class="header-logo">
                                    <img src="{{ asset('/images/logo.png') }}" alt="Stellar Traffic Logo" />
                                </div>
                                <div class="header-brand">Stellar <span>Traffic</span></div>
                            </div>
                        </td>
                    </tr>

                    <!-- BODY -->
                    <tr>
                        <td>
                            <div class="body">
                                <h1>¡Hola, {{ $nombreCompleto }}!</h1>

                                <p>Nos alegra muchísimo tenerte con nosotros. Tu cuenta en <strong>Stellar
                                        Traffic</strong> ha sido creada con éxito y ya está lista para que empieces.</p>

                                <p>Queremos que aproveches la plataforma al máximo desde el primer minuto. Para comenzar
                                    a explorar tu panel de control, haz clic en el siguiente botón:</p>

                                <div class="btn-container">
                                    <a href="{{ url('/dashboard') }}" class="btn">Acceder a mi Cuenta</a>
                                </div>

                                <p style="color: #ffffff; font-weight: 600; margin-bottom: 12px;">¿Por dónde empezar?
                                </p>

                                <ul class="bullet-list">
                                    <li><strong>Configura tu Panel:</strong> Personaliza tu entorno de trabajo en un par
                                        de clics.</li>
                                    <li><strong>Explora las Herramientas:</strong> Descubre las funciones clave que
                                        tenemos listas para ti.</li>
                                    <li><strong>Soporte a un clic:</strong> Si tienes alguna duda, nuestro equipo está
                                        aquí para ayudarte en lo que necesites.</li>
                                </ul>

                                <p>¡Gracias por unirte a nuestra comunidad!</p>

                                <p>
                                    Saludos,<br>
                                    El equipo de Stellar Traffic
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td>
                            <div class="footer">
                                <p class="footer-text">
                                    Si el botón no funciona, copia y pega este enlace en tu navegador:<br>
                                    <a href="{{ url('/dashboard') }}" class="footer-link">{{ url('/dashboard') }}</a>
                                </p>
                                <p class="copyright">© {{ date('Y') }} Stellar Traffic. Todos los derechos reservados.
                                </p>
                            </div>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>