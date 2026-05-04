<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tardy Letter — {{ $employee->full_name ?: $employee->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Times+New+Roman&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            color: #000;
            background: #fff;
        }

        /* ── Screen toolbar ── */
        .toolbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            background: #1e293b;
            color: #fff;
            padding: 10px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 999;
            gap: 12px;
        }
        .toolbar a {
            color: #94a3b8;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
        }
        .toolbar-right { display: flex; gap: 10px; }
        .btn-print {
            background: #ef4444; color: #fff;
            border: none; padding: 8px 18px;
            border-radius: 8px; font-size: 13px;
            font-weight: 600; cursor: pointer;
            font-family: 'Inter', sans-serif;
        }
        .btn-print:hover { opacity: .85; }

        /* ── Document ── */
        .print-area {
            margin-top: 56px;
            padding: 28px 52px 40px;
            max-width: 850px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 24px;
        }
        .header-logos {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 18px;
            margin-bottom: 10px;
        }
        .header-logos img {
            height: 72px;
            width: auto;
        }
        .header p { line-height: 1.45; font-size: 11pt; }
        .header .republic { font-size: 11pt; }
        .header .province { font-weight: bold; font-size: 13pt; }
        .header .address { font-size: 11pt; }
        .header .office { font-weight: bold; font-size: 12pt; margin-top: 6px; text-transform: uppercase; }
        .header-divider {
            border: none;
            border-top: 2px solid #000;
            margin: 10px 0 0;
        }

        /* Date line */
        .doc-date {
            text-align: right;
            margin-top: 18px;
            margin-bottom: 20px;
            font-size: 12pt;
        }

        /* Recipient block */
        .recipient { margin-bottom: 20px; font-size: 12pt; line-height: 1.7; }
        .recipient .emp-name { font-weight: bold; text-transform: uppercase; }
        .recipient .emp-position { font-style: italic; }

        /* Salutation */
        .salutation { margin-bottom: 20px; font-size: 12pt; }

        /* Body */
        .body-para {
            font-size: 12pt;
            line-height: 1.8;
            text-align: justify;
            margin-bottom: 18px;
            text-indent: 50px;
        }
        .body-para .tardy-count {
            font-weight: bold;
            text-decoration: underline;
        }

        /* Closing */
        .closing { margin-top: 28px; margin-bottom: 60px; font-size: 12pt; }

        /* Signature block */
        .sig-section {
            margin-top: 8px;
        }
        .sig-label { font-size: 12pt; margin-bottom: 32px; }
        .sig-name {
            font-weight: bold;
            font-size: 12pt;
            text-transform: uppercase;
            text-decoration: underline;
        }
        .sig-title { font-size: 11.5pt; margin-top: 2px; }

        /* Print styles */
        @media print {
            .toolbar { display: none !important; }
            .print-area {
                margin-top: 0;
                padding: 14mm 20mm 18mm;
                max-width: 100%;
            }
            @page {
                size: 8.5in 11in portrait;
                margin: 10mm;
            }
        }
    </style>
</head>
<body>

    {{-- Toolbar (screen only) --}}
    <div class="toolbar">
        <a href="javascript:window.close()">← Close</a>
        <span style="color:#94a3b8; font-size:13px; font-family:Inter,sans-serif;">
            Tardy Letter — {{ $employee->full_name ?: $employee->name }}
        </span>
        <div class="toolbar-right">
            <button class="btn-print" onclick="window.print()">🖨 Print / Save PDF</button>
        </div>
    </div>

    @php
        // Resolve full name for display: Given Middle Last
        $isUser = $employee instanceof \App\Models\User;
        $fullName = $employee->full_name ?: $employee->name;

        // PDF-style (Given Middle Last) for the letter header
        $given  = trim($employee->given_name ?? '');
        $middle = trim($employee->middle_name ?? '');
        $last   = trim($employee->last_name ?? '');
        $suffix = trim($employee->suffix ?? '');

        $pdfName = collect([$given, $middle, $last . ($suffix ? ' ' . $suffix : '')])->filter()->implode(' ');
        if (!$pdfName) { $pdfName = $fullName; }

        $position = trim($employee->position ?? '');
        $office   = trim($employee->office ?? '');

        // Pronoun based on prefix
        $pronoun = (stripos($prefix, 'Ms') !== false || stripos($prefix, 'Mrs') !== false) ? 'her' : 'his';
    @endphp

    <div class="print-area">

        {{-- ══ OFFICIAL HEADER ══ --}}
        <div class="header">
            <div class="header-logos">
                <img src="{{ asset('images/hrlogo.png') }}" alt="HR Logo">
                <img src="{{ asset('images/phrmo-logo.png') }}" alt="PHRMO Logo">
                <img src="{{ asset('images/bp-logo.png') }}" alt="Bukidnon Logo">
            </div>
            <p class="republic">Republic of the Philippines</p>
            <p class="province">PROVINCE OF BUKIDNON</p>
            <p class="address">Provincial Capitol</p>
            <p class="office" style="margin-top: 15px;">PROVINCIAL HUMAN RESOURCE MANAGEMENT OFFICE</p>
        </div>

        {{-- ══ DATE ══ --}}
        <div class="doc-date" style="text-align: left; margin-bottom: 30px;">
            {{ now()->format('d F Y') }}
        </div>

        {{-- ══ RECIPIENT ══ --}}
        <div class="recipient" style="line-height: 1.2; margin-bottom: 20px;">
            <span class="emp-name" style="font-weight: bold; text-transform: uppercase;">{{ strtoupper($prefix) }} {{ strtoupper($pdfName) }}</span><br>
            @if($position)
            <span style="display: block; margin-top: 2px;">{{ $position }}</span>
            @endif
            @if($office)
            <span style="display: block; margin-top: 2px;">{{ $office }}</span>
            @endif
        </div>

        {{-- ══ SALUTATION ══ --}}
        <div class="salutation" style="margin-bottom: 20px;">
            Dear {{ $prefix }} {{ $last ?: $pdfName }}:
        </div>

        {{-- ══ BODY ══ --}}
        <div class="body-para" style="text-indent: 0; text-align: justify; margin-bottom: 20px;">
            As evidenced by your Daily Time Record for the month of {{ $month }} {{ $year }}, you have incurred <span style="text-decoration: underline;">{{ $tardinessCount }}</span> times of tardiness.
        </div>

        <div class="body-para" style="text-indent: 0; text-align: justify; margin-bottom: 20px;">
            Please be reminded that Rule XVII, Section 8 on Government Office Hours of the Omnibus Rules Implementing Book V of Executive Order 292 states that:
        </div>

        <div style="margin: 0 40px 20px 40px; text-align: justify; font-style: italic; font-size: 11pt; line-height: 1.6;">
            "Officers and employees who have incurred tardiness and undertime regardless of the number of minutes per day, ten (10) times a month for atleast two (2) consecutive months during the year or for at least two (2) months in a semester shall be subject to disciplinary action."
        </div>

        <div class="body-para" style="text-indent: 0; text-align: justify; margin-bottom: 20px;">
            Be advised further that under Rule X, Section 46 (F)(4) of the Revised Rules on Administrative Cases in the Civil Service, the corresponding penalty for the above stated offense is as follows:
        </div>

        <table style="margin: 0 auto 20px auto; font-style: italic; font-size: 11pt; width: 60%; border-collapse: collapse;">
            <tr>
                <td style="padding: 2px 10px; width: 40px;">a.</td>
                <td style="padding: 2px 10px; width: 140px;">1st Offense -</td>
                <td style="padding: 2px 10px;">Reprimand</td>
            </tr>
            <tr>
                <td style="padding: 2px 10px;">b.</td>
                <td style="padding: 2px 10px;">2nd Offense -</td>
                <td style="padding: 2px 10px;">Suspension for one to thirty days</td>
            </tr>
            <tr>
                <td style="padding: 2px 10px;">c.</td>
                <td style="padding: 2px 10px;">3rd Offense -</td>
                <td style="padding: 2px 10px;">Dismissal</td>
            </tr>
        </table>

        <div class="body-para" style="text-indent: 0; text-align: justify; margin-bottom: 20px;">
            Please consider this as a warning. We expect improvement in your attendance to avoid further disciplinary action.
        </div>

        <div class="body-para" style="text-indent: 0; margin-bottom: 40px;">
            Thank you.
        </div>

        {{-- ══ CLOSING ══ --}}
        <div class="closing" style="margin-bottom: 50px;">
            Very truly yours,
        </div>

        {{-- ══ SIGNATURE ══ --}}
        <div class="sig-section" style="margin-bottom: 40px;">
            <p class="sig-name" style="font-weight: bold; text-transform: uppercase;">{{ strtoupper($certifierName) }}</p>
            <p class="sig-title">{!! nl2br(e($certifierPosition)) !!}</p>
        </div>

        {{-- ══ REFERENCE NO ══ --}}
        <div style="font-size: 9pt; margin-top: 8px;">
            Reference No. <strong>{{ $referenceNo ?? '—' }}</strong>
        </div>

    </div>

</body>
</html>
