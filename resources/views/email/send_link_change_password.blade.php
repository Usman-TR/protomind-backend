<!-- resources/views/email/reset_password.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сброс пароля</title>
    <style>
        body {
            font-family: Arial, sans-serif !important;
            background-color: #f4f4f4 !important;
            color: #333 !important;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff !important;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #e4e4e4 !important;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #7130A3 !important;
        }
        .content {
            padding: 20px 0;
        }
        .content p {
            margin: 0 0 10px;
            font-size: 16px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 10px;
            font-size: 16px;
            color: #ffffff !important;
            background-color: #7130A3 !important;
            text-decoration: none;
            border-radius: 5px;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #e4e4e4 !important;
            font-size: 12px;
            color: #777 !important;
        }
    </style>
</head>
<body>
<table class="container" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td class="header">
            <h1>Сброс пароля</h1>
        </td>
    </tr>
    <tr>
        <td class="content">
            <p>Здравствуйте,</p>
            <p>Вы запросили сброс пароля для вашей учетной записи. Нажмите на кнопку ниже, чтобы установить новый пароль:</p>
            <p style="text-align: center;">
                <a href="{{ $url }}" class="button">Сбросить пароль</a>
            </p>
            <p>Если вы не запрашивали сброс пароля, просто проигнорируйте это письмо.</p>
        </td>
    </tr>
    <tr>
        <td class="footer">
            <p>&copy; 2024 Protomind. Все права защищены.</p>
        </td>
    </tr>
</table>
</body>
</html>
