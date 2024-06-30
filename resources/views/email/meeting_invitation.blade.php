<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Приглашение на совещание</title>
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
            color: #007bff;
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
            color: #ffffff;
            background-color: #007bff;
            text-decoration: none;
            border-radius: 5px;
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
<table class="container" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td class="header">
            <h1>Приглашение на совещание</h1>
        </td>
    </tr>
    <tr>
        <td class="content">
            <p>Здравствуйте,</p>
            <p>Вы приглашены на совещание</p>
            <p><strong>Тема:</strong> {{ $meeting->theme }}</p>
            <p><strong>День проведения:</strong> {{ $meeting->event_date }}</p>
            <p><strong>Начало совещания:</strong> {{ $meeting->event_start_time }}</p>
            <p><strong>Конец совещания:</strong> {{ $meeting->event_end_time }}</p>
            <p style="text-align: center;">
                <a href="{{ $meeting->link }}" class="button">Ссылка</a>
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
