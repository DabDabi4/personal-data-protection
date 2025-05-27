<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 0; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            background: #f9f9f9;
        }

        .certificate-container {
            border: 10px solid #0057b7;
            padding: 60px 40px;
            margin: 30px;
            background: white;
            text-align: center;
        }

        .certificate-title {
            font-size: 40px;
            color: #0057b7;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .certificate-text {
            font-size: 22px;
            margin-bottom: 15px;
            color: #333;
        }

        .certificate-name {
            font-size: 28px;
            font-weight: bold;
            color: #111;
            margin-bottom: 10px;
        }

        .certificate-topic {
            font-size: 24px;
            font-style: italic;
            margin: 20px 0;
            color: #222;
        }

        .certificate-date {
            font-size: 18px;
            color: #666;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="certificate-title">Сертифікат</div>

        <div class="certificate-text">Цей сертифікат підтверджує, що</div>

        <div class="certificate-name">{{ $user->name }}</div>

        <div class="certificate-text">успішно пройшов(ла) тестування на тему</div>

        <div class="certificate-topic">«Захист персональних даних»</div>

        <div class="certificate-date">Дата: {{ now()->format('d.m.Y') }}</div>
    </div>
</body>
</html>
