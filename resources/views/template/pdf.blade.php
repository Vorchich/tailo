<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF</title>
    <style>
        body {
            font-family: "DejaVu Sans", sans-serif;
        }
        .header {
            font-size: 14px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
        }
        .header .left {
            font-size: 20px;
            font-weight: bold;
        }
        .header .right {
            text-align: right;
        }
        .content {
            font-size: 16px;
            margin-top: 50px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="right">Згенеровано за допомогою застосунку Tailor</div>
        <div class="left">{{$category_name}}</div>
    </div>
    <div class="content">
        <table>
            <thead>
                <tr>
                    <th>Розмір</th>
                    <th>Значення</th>
                </tr>
            </thead>
            <tbody>
                @foreach($elements as $index => $element)
                    <tr>
                        <td>{{ $index }}</td>
                        <td>{{ $element }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
