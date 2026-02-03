<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px
        }

        h1 {
            font-size: 18px
        }

        .box {
            border: 1px solid #ddd;
            padding: 10px;
            margin: 10px 0
        }
    </style>
</head>

<body>
    <h1>Interview Report</h1>

    <p>
        Student: {{ $report['student']['name'] ?? '' }}<br>
        Institution: {{ $report['application']['institution'] ?? '' }}<br>
        Program: {{ $report['application']['program'] ?? '' }}<br>
        Intake: {{ $report['application']['intake'] ?? '' }}<br>
    </p>

    @foreach(($report['per_question'] ?? []) as $q)
    <div class="box">
        <strong>Q{{ $q['order_index'] + 1 }}:</strong> {{ $q['question'] }}<br><br>
        <strong>Transcript:</strong>
        <div style="white-space: pre-wrap;">{{ $q['transcript'] }}</div><br>
        <strong>AI Summary:</strong>
        <div style="white-space: pre-wrap;">{{ json_encode($q['summary'], JSON_UNESCAPED_UNICODE) }}</div>
    </div>
    @endforeach
</body>

</html>