{{-- resources/views/reports/interview_per_question.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Interview Assessment Report</title>

    <style>
        * {
            box-sizing: border-box;
        }

        @page {
            margin: 20px;
        }

        html,
        body {
            font-family: 'DejaVu Sans', sans-serif;
            /* Required for symbol support */
            margin: 0;
            padding: 0;
            color: #0f172a;
            background: #ffffff;
            font-size: 12px;
            line-height: 1.4;
        }

        .page-wrapper {
            width: 100%;
            padding: 0;
            margin: 0;
        }

        .no-break {
            page-break-inside: avoid;
        }

        /* Header with solid fallback for visibility */
        .header {
            border-radius: 15px;
            padding: 20px;
            background-color: #4f46e5;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: #3F2CC8 !important;
            margin-bottom: 15px;
        }

        .h-title {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
            color: #3F2CC8 !important;
        }

        .h-sub {
            color: #3F2CC8 !important;
            opacity: 0.9;
            font-size: 11px;
            margin-top: 4px;
        }

        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }

        .badge-danger {
            background: #fee2e2;
            color: #dc2626;
        }

        .badge-success {
            background: #dcfce7;
            color: #16a34a;
        }

        .badge-warning {
            background: #fef3c7;
            color: #d97706;
        }

        .info-grid {
            margin-bottom: 15px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 6px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .label {
            color: #64748b;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            width: 110px;
        }

        .value {
            color: #0f172a;
            font-weight: bold;
            font-size: 12px;
        }

        .stat-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px;
            text-align: center;
        }

        .q-card {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
            page-break-inside: avoid;
        }

        .transcript-box {
            background: #fdf2f8;
            border-left: 3px solid #db2777;
            padding: 8px 12px;
            border-radius: 6px;
            margin: 10px 0;
            font-style: italic;
        }

        .ai-box {
            background: #f0fdf4;
            border-radius: 8px;
            padding: 10px;
            border: 1px solid #dcfce7;
        }

        .fact-box {
            background: #eff6ff;
            border-radius: 8px;
            padding: 10px;
            border: 1px solid #dbeafe;
        }
    </style>
</head>

<body>
    <div class="page-wrapper">
        @php
        $studentName = trim(($report['student']['first_name'] ?? '') . ' ' . ($report['student']['last_name'] ?? ''));
        $cp = $report['compliance_profile'] ?? [];
        $questions = $report['questions'] ?? [];
        $concerning = collect($questions)->filter(fn($q) => strtolower($q['ai_summary_json']['consistency'] ?? '') === 'concerning')->count();
        $total = count($questions);

        // Use HTML Entity Codes for Result Icons
        $overallStatus = $concerning > 0
        ? ($concerning > ($total/2) ? 'High Concern &#128680;' : 'Mixed &#9888;')
        : 'Excellent &#9989;';
        $overallClass = $concerning > 0 ? ($concerning > ($total/2) ? 'badge-danger' : 'badge-warning') : 'badge-success';
        @endphp

        <div class="header no-break">
            <table style="width: 100%;">
                <tr>
                    <td>
                        <div class="h-title">&#127891; Interview Assessment</div>
                        <div class="h-sub">Detailed Compliance Review &#38; Analysis</div>
                    </td>
                    <td style="text-align:right;">
                        <span class="badge {{ $overallClass }}" style="background: #ffffff;">
                            Result: {!! $overallStatus !!}
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        <div class="info-grid no-break">
            <table class="info-table">
                <tr>
                    <td class="label">&#128100; Student</td>
                    <td class="value">{{ $studentName }}</td>
                    <td class="label">&#128197; Intake</td>
                    <td class="value">{{ $cp['intake'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">&#127979; Institution</td>
                    <td class="value">{{ $cp['institution'] ?? 'N/A' }}</td>
                    <td class="label">&#128214; Program</td>
                    <td class="value">{{ $cp['program'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">&#128181; Tuition / Paid</td>
                    <td class="value">£{{ $cp['tuition_fee'] ?? '0' }} / £{{ $cp['paid_amount'] ?? '0' }}</td>
                    <td class="label">&#9888; Remaining</td>
                    <td class="value" style="color:#dc2626;">£{{ $cp['remaining_amount'] ?? '0' }}</td>
                </tr>
            </table>
        </div>

        <table style="width: 100%; border-spacing: 10px; margin-bottom: 10px;">
            <tr>
                <td class="stat-card">
                    <span style="font-size: 18px; font-weight: bold;">&#128269; {{ $total }}</span><br>
                    <span style="font-size: 10px; color: #64748b;">Total Questions</span>
                </td>
                <td class="stat-card">
                    <span style="font-size: 18px; font-weight: bold; color:#dc2626;">&#128681; {{ $concerning }}</span><br>
                    <span style="font-size: 10px; color: #64748b;">Flags Found</span>
                </td>
                <td class="stat-card">
                    <span style="font-size: 18px; font-weight: bold; color:#16a34a;">&#9989; {{ $total - $concerning }}</span><br>
                    <span style="font-size: 10px; color: #64748b;">Clear Responses</span>
                </td>
            </tr>
        </table>

        @foreach($questions as $index => $q)
        <div class="q-card no-break">
            <div style="font-weight: bold; margin-bottom: 10px; border-bottom: 1px solid #f1f5f9; padding-bottom: 5px;">
                Q{{ $index + 1 }}: {{ $q['question_text'] }}
            </div>
            <div class="transcript-box">
                <strong>&#127881; Student Said:</strong> "{{ $q['transcript_text'] }}"
            </div>
            <table style="width: 100%; border-spacing: 5px;">
                <tr>
                    <td style="width: 50%; vertical-align: top;">
                        <div class="ai-box">
                            <div style="font-weight: bold; font-size: 10px; color: #166534;">&#129302; AI OBSERVATIONS</div>
                            <ul style="margin: 5px 0; padding-left: 15px; font-size: 10px;">
                                @foreach($q['ai_summary_json']['concerns'] ?? [] as $c)
                                <li>{{ $c }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </td>
                    <td style="width: 50%; vertical-align: top;">
                        <div class="fact-box">
                            <div style="font-weight: bold; font-size: 10px; color: #1e40af;">&#128204; EXTRACTED FACTS</div>
                            <ul style="margin: 5px 0; padding-left: 15px; font-size: 10px;">
                                @foreach($q['ai_summary_json']['key_points'] ?? [] as $f)
                                <li>{{ $f }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        @endforeach

        <div style="text-align: center; font-size: 9px; color: #94a3b8; margin-top: 20px;">
            &#128274; Confidential Report Generated on {{ now()->format('M d, Y H:i') }}
        </div>
    </div>
</body>

</html>