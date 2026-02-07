<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
            margin: 30px;
        }

        .header {
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h1 {
            color: #4f46e5;
            margin: 0;
            font-size: 22px;
            text-transform: uppercase;
        }

        .meta-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .meta-table td {
            padding: 5px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .label {
            font-weight: bold;
            color: #666;
            width: 120px;
            text-transform: uppercase;
            font-size: 10px;
        }

        .question-box {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .q-num {
            font-weight: 900;
            color: #4f46e5;
            font-size: 14px;
            margin-right: 10px;
        }

        .transcript-section {
            background: #f9fafb;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 3px solid #d1d5db;
            font-style: italic;
            color: #4b5563;
        }

        .section-title {
            font-size: 10px;
            font-weight: bold;
            color: #4f46e5;
            text-transform: uppercase;
            margin-bottom: 5px;
            display: block;
        }

        .badge {
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            float: right;
        }

        .concerning {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        .clear {
            background-color: #dcfce7;
            color: #15803d;
        }

        .ai-grid {
            width: 100%;
            margin-top: 10px;
            table-layout: fixed;
        }

        .ai-column {
            vertical-align: top;
            width: 50%;
            padding-right: 10px;
        }

        ul {
            margin: 0;
            padding-left: 15px;
        }

        li {
            margin-bottom: 4px;
            color: #4b5563;
            font-size: 11px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Interview Assessment Report</h1>
    </div>

    <table class="meta-table">
        <tr>
            <td class="label">Student:</td>
            <td>{{ $report['student']['full_name'] ?? 'N/A' }}</td>
            <td class="label">Intake:</td>
            <td>{{ $report['intake'] ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Institution:</td>
            <td>{{ $report['institution'] ?? 'N/A' }}</td>
            <td class="label">Program:</td>
            <td>{{ $report['program'] ?? 'N/A' }}</td>
        </tr>
    </table>

    @foreach(($report['interviews'][0]['questions'] ?? []) as $index => $q)
    <div class="question-box">
        <span class="badge {{ $q['consistency'] ?? 'unclear' }}">Consistency: {{ $q['consistency'] ?? 'N/A' }}</span>
        <span class="q-num">Q{{ $index + 1 }}</span>
        <div style="margin-top: 5px; font-weight: bold;">{{ $q['text'] }}</div>

        <div class="transcript-section">
            <span class="section-title" style="color:#666">Student Transcript</span>
            "{{ $q['transcript'] ?? 'No transcript available' }}"
        </div>

        <table class="ai-grid">
            <tr>
                <td class="ai-column">
                    <span class="section-title">AI Summary & Observations</span>
                    <ul>
                        @foreach(($q['summary'] ?? []) as $point)
                        <li>{{ $point }}</li>
                        @endforeach
                    </ul>
                </td>
                <td class="ai-column">
                    <span class="section-title">Key Facts Extracted</span>
                    <ul>
                        @foreach(($q['key_points'] ?? []) as $kp)
                        <li>{{ $kp }}</li>
                        @endforeach
                    </ul>
                </td>
            </tr>
        </table>
    </div>
    @endforeach
</body>

</html>