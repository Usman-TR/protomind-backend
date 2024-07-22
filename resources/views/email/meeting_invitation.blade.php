<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Приглашение на совещание</title>
    <style>
        body {
            font-family: Arial, sans-serif;
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
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
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
            text-align: center !important;
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
            <h1>Приглашение на совещание</h1>
        </td>
    </tr>
    <tr>
        <td class="content" style="text-align: center">
            <p>Здравствуйте!</p>
            <p>Вы приглашены на совещание</p>
            <p><strong>Тема:</strong> {{ $meeting->theme }}</p>
            <p><strong>День проведения:</strong> {{ $meeting->event_date }}</p>
            <p><strong>Начало совещания:</strong> {{ $meeting->event_start_time }}</p>
            <p><strong>Конец совещания:</strong> {{ $meeting->event_end_time }}</p>
            <p style="text-align: center;">
                <a style="color: #ffffff;" href="{{ $meeting->link }}" class="button">Ссылка</a>
            </p>
            <p>Благодарим вас за внимание!</p>
        </td>
    </tr>
    <tr>
        <td class="footer">
            <p>С уважением, Protomind</p>
            <p>&copy; 2024 Protomind. Все права защищены.</p>
        </td>
    </tr>
</table>
</body>
</html>
