<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-size: 17px;
            font-family: DejaVu Sans;
        }
        .text-center {
            text-align: center;
        }
        .text-bold {
            font-weight: bold;
        }
        .mt-1 {
            margin-top: 10px;
        }
    </style>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title></title>
</head>
<body>
<div class="text-center text-bold">ПРОТОКОЛ №{{ $user_protocol_number }}</div>
<div class="text-center text-bold">заседания</div>

<div class="mt-1">{{ $event_date }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;г. {{ $city }}</div>

<div class="mt-1">Время проведение заседания:&nbsp;&nbsp;&nbsp;&nbsp;{{ $event_start_time }}</div>
<div>Место проведения: {{ $location }}</div>

<div class="mt-1">В заседании приняли участие {{ $members_count }} участников</div>

<div class="mt-1 text-bold">Повестка дня заседания: {{ $agenda }}</div>

<div class="mt-1 text-bold">Ход заседания и принятые решения:</div>

@foreach ($final_transcript as $item)
    @if (!empty($item['value']))
        <div class="text-bold">{{ mb_strtoupper($item['key']) }}:</div>
        <div>{{ $item['value'] }}</div>
        <div class="mt-1"></div>
    @endif
@endforeach
</body>
</html>
