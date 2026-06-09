<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Restablecer contraseña - Stellar Traffic</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #02060f;
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #e2e8f0;
        }

        .wrapper {
            width: 100%;
            background-color: #02060f;
            padding: 40px 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #030a1c;
            border: 1px solid rgba(37, 99, 235, 0.25);
            border-radius: 16px;
            overflow: hidden;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #030a1c 0%, #061529 100%);
            border-bottom: 1px solid rgba(37, 99, 235, 0.2);
            padding: 32px 40px;
            text-align: center;
        }

        .header-logo {
            display: inline-block;
        }

        .header-logo img {
            width: 72px;
            height: 72px;
            border-radius: 16px;
            object-fit: cover;
        }

        .header-brand {
            margin-top: 14px;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: -0.5px;
            color: #ffffff;
        }

        .header-brand span {
            color: #2563eb;
        }

        /* Top accent line */
        .accent-line {
            height: 3px;
            background: linear-gradient(90deg, transparent, #2563eb, transparent);
            opacity: 0.7;
        }

        /* Body */
        .body {
            padding: 40px 40px 32px;
        }

        .greeting {
            font-size: 20px;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 16px;
        }

        .text {
            font-size: 15px;
            color: #94a3b8;
            line-height: 1.7;
            margin-bottom: 14px;
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

        /* Note box */
        .note-box {
            background-color: rgba(37, 99, 235, 0.08);
            border: 1px solid rgba(37, 99, 235, 0.2);
            border-radius: 10px;
            padding: 14px 18px;
            margin: 24px 0;
        }

        .note-box p {
            font-size: 13px;
            color: #7dd3fc;
            line-height: 1.6;
        }

        .note-box strong {
            color: #93c5fd;
        }

        .salutation {
            margin-top: 28px;
            font-size: 14px;
            color: #94a3b8;
            line-height: 1.8;
        }

        .salutation strong {
            color: #cbd5e1;
        }

        /* Divider */
        .divider {
            border: none;
            border-top: 1px solid rgba(37, 99, 235, 0.15);
            margin: 0 40px;
        }

        /* Subcopy / Fallback */
        .subcopy {
            padding: 24px 40px;
        }

        .subcopy p {
            font-size: 12px;
            color: #64748b;
            line-height: 1.7;
        }

        .subcopy a {
            color: #2563eb;
            text-decoration: none;
            word-break: break-all;
        }

        /* Footer */
        .footer {
            background-color: #020812;
            border-top: 1px solid rgba(37, 99, 235, 0.1);
            padding: 20px 40px;
            text-align: center;
        }

        .footer p {
            font-size: 12px;
            color: #475569;
        }

        @media only screen and (max-width: 600px) {

            .body,
            .subcopy,
            .divider {
                padding-left: 24px;
                padding-right: 24px;
            }

            .header {
                padding: 24px;
            }

            .footer {
                padding: 16px 24px;
            }
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
                                <p class="greeting">¡Hola, {{ $nombreCompleto }}!</p>

                                <p class="text">
                                    Has recibido este correo porque solicitaste restablecer la contraseña de tu cuenta
                                    en <strong style="color:#cbd5e1;">Stellar Traffic</strong>.
                                </p>
                                <p class="text">
                                    Para continuar con el proceso, por favor haz clic en el siguiente botón:
                                </p>

                                <!-- BUTTON -->
                                <div class="btn-container">
                                    <a href="{{ $url }}" class="btn" target="_blank">Restablecer contraseña</a>
                                </div>

                                <!-- NOTE -->
                                <div class="note-box">
                                    <p>
                                        <strong>Nota importante:</strong> Este enlace de recuperación expirará en
                                        <strong>{{ $expira }} minutos</strong>.
                                    </p>
                                </div>

                                <p class="text">
                                    Si tú no solicitaste este cambio, puedes ignorar este correo de forma segura; tu
                                    contraseña seguirá siendo la misma.
                                </p>

                                <div class="salutation">
                                    Saludos,<br>
                                    <strong>El equipo de Stellar Traffic</strong>
                                </div>
                            </div>
                        </td>
                    </tr>

                    <!-- DIVIDER -->
                    <tr>
                        <td>
                            <hr class="divider" />
                        </td>
                    </tr>

                    <!-- SUBCOPY / FALLBACK -->
                    <tr>
                        <td>
                            <div class="subcopy">
                                <p>
                                    Si tienes problemas para hacer clic en el botón "Restablecer contraseña", copia y
                                    pega la siguiente URL en tu navegador web:
                                </p>
                                <p style="margin-top:8px;">
                                    <a href="{{ $url }}">{{ $url }}</a>
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td>
                            <div class="footer">
                                <p>© {{ date('Y') }} Stellar Traffic. Todos los derechos reservados.</p>
                            </div>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>