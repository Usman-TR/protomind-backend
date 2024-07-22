<!-- resources/views/email/confirm_email.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Подтверждение Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #e4e4e4;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #7130A3;
        }
        .content {
            padding: 20px 0;
        }
        .content p {
            margin: 0 0 10px;
            font-size: 16px;
        }
        .credentials {
            background-color: #f9f9f9;
            padding: 10px;
            border: 1px solid #e4e4e4;
        }
        .credentials p {
            margin: 0;
            font-size: 14px;
            color: #555;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #e4e4e4;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
<table width="100%" cellpadding="0" cellspacing="0" bgcolor="#f4f4f4">
    <tr>
        <td>
            <table class="container" width="100%" cellpadding="0" cellspacing="0" bgcolor="#ffffff" align="center">
                <tr>
                    <td class="header" style="padding: 20px; text-align: center; border-bottom: 1px solid #e4e4e4;">
                        <h1 style="margin: 0; font-size: 24px; color: #7130A3;">Добро пожаловать в Protomind</h1>
                    </td>
                </tr>
                <tr>
                    <td class="content" style="padding: 20px;">
                        <p style="margin: 0 0 10px; font-size: 16px;">Здравствуйте,</p>
                        <p style="margin: 0 0 10px; font-size: 16px;">Вы успешно зарегистрированы в системе Protomind. Пожалуйста, используйте следующие данные для входа:</p>
                        <br>
                        <table class="credentials" width="100%" cellpadding="10" cellspacing="0" bgcolor="#f9f9f9" style="border: 1px solid #e4e4e4;">
                            <tr>
                                <td style="font-size: 14px; color: #555;">
                                    <p style="margin: 0;"><strong>Email:</strong> {{$email}}</p>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-size: 14px; color: #555;">
                                    <p style="margin: 0;"><strong>Password:</strong> {{$password}}</p>
                                </td>
                            </tr>
                        </table>
                        <br>
                        <p style="margin: 0 0 10px; font-size: 16px;">Если у вас возникли вопросы, свяжитесь с нашей поддержкой.</p>
                    </td>
                </tr>
                <tr>
                    <td class="footer" style="padding: 20px; text-align: center; border-top: 1px solid #e4e4e4; font-size: 12px; color: #777;">
                        <p style="margin: 0;">&copy; 2024 Protomind. Все права защищены.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
