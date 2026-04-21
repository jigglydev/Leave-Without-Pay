<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Leave Records Report</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Times+New+Roman:wght@400;700&display=swap" rel="stylesheet" />
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            color: #000;
            background: #fff;
            padding: 24px 32px;
        }

        /* ── Header ── */
        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            height: 60px;
            margin: 0 6px;
        }

        .header-logos {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px;
        }

        .header p {
            line-height: 1.5;
        }

        .header .agency {
            font-size: 11pt;
            font-weight: bold;
        }

        .header .office-title {
            font-size: 11pt;
            font-weight: bold;
            margin-top: 8px;
        }

        h2.report-title {
            text-align: center;
            font-size: 12pt;
            margin-bottom: 20px;
            font-weight: bold;
        }

        /* ── Table ── */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 18px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
        }

        th {
            font-weight: bold;
            font-size: 8pt;
        }

        td.left-align {
            text-align: left;
        }

        /* ── Footer ── */
        .doc-date {
            font-size: 10pt;
            margin-bottom: 30px;
        }

        .sig-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            overflow: hidden;
            page-break-inside: avoid;
        }

        .sig-block {
            font-size: 10pt;
        }

        .sig-block .sig-name {
            font-weight: bold;
            font-size: 10pt;
            display: block;
            margin-top: 20px;
        }

        .sig-block .sig-title {
            font-size: 9.5pt;
        }

        .sig-block .sig-label {
            font-size: 10pt;
            margin-bottom: 2px;
        }

        /* ── Print ── */
        @media print {
            body {
                padding: 12mm 14mm;
            }

            .no-print {
                display: none !important;
            }

            @page {
                size: 13in 8.5in;
                margin: 12mm;
            }

            /* Long paper size (Folio) landscape */
        }

        /* ── Screen toolbar ── */
        .toolbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #1e293b;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 20px;
            z-index: 999;
            gap: 12px;
        }

        .toolbar button {
            padding: 7px 18px;
            border-radius: 8px;
            border: none;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: opacity .15s;
        }

        .toolbar button:hover {
            opacity: .85;
        }

        .btn-print {
            background: #ef4444;
            color: #fff;
        }

        .btn-download {
            background: #3b82f6;
            color: #fff;
        }

        .btn-back {
            background: transparent;
            color: #94a3b8;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
        }

        .print-area {
            margin-top: 56px;
        }

        @media print {
            .toolbar {
                display: none;
            }

            .print-area {
                margin-top: 0;
            }
        }
    </style>
</head>

<body>

    {{-- ── Screen Toolbar ── --}}
    <div class="toolbar no-print">
        <a href="{{ route('admin.recorded-entries') }}" class="btn-back">← Back</a>
        <span style="color:#94a3b8; font-size:13px;">Leave Records Report</span>
        <div style="display:flex;gap:10px;">
            <button class="btn-print" onclick="window.print()">🖨 Print</button>
            <button class="btn-download" onclick="window.print()">⬇ Save as PDF</button>
        </div>
    </div>

    <div class="print-area">

        {{-- ── Official Header ── --}}
        <div class="header">
            <div class="header-logos" style="margin-bottom: 12px; gap: 16px;">
                <img src="{{ asset('images/hrlogo.png') }}" alt="HR Logo" style="height: 75px; width: auto;" />
                <img src="{{ asset('images/phrmo-logo.png') }}" alt="PHRMO Logo" style="height: 75px; width: auto;" />
                <img src="{{ asset('images/bp-logo.png') }}" alt="BP Logo" style="height: 75px; width: auto;" />
            </div>

            <p>Republic of the Philippines</p>
            <p class="agency">PROVINCE OF BUKIDNON</p>
            <p>Provincial Capitol</p>
            <br />
            <p class="office-title">PROVINCIAL HUMAN RESOURCE MANAGEMENT OFFICE</p>
        </div>

        <h2 class="report-title">LEAVE RECORDS REPORT</h2>

        <div class="doc-date">
            <strong>Generated on:</strong> {{ now()->format('F j, Y, g:i a') }}<br>
            <strong>Total Records:</strong> {{ $records->count() }}
        </div>

        {{-- ── Data Table ── --}}
        <table>
            <thead>
                <tr>
                    <th rowspan="2" style="vertical-align:middle; min-width:120px;">NAME</th>
                    <th rowspan="2" style="vertical-align:middle; min-width:100px;">POSITION</th>
                    <th rowspan="2" style="vertical-align:middle; min-width:100px;">OFFICE</th>
                    <th colspan="3">NO. OF DAYS OF<br />W/OUT PAY</th>
                    <th colspan="2">NO. OF DAYS OF UNDERTIME/<br />TARDY W/OUT PAY</th>
                </tr>
                <tr>
                    <th style="vertical-align:middle">VL</th>
                    <th style="vertical-align:middle">SL</th>
                    <th style="vertical-align:middle">TOTAL</th>
                    <th style="vertical-align:middle">HRS</th>
                    <th style="vertical-align:middle">MINS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $rec)
                    @php
                        $u = $rec->user;
                        $displayName = $rec->snapshot_name ?? ($u ? $u->full_name : '');
                        $displayPosition = $rec->snapshot_position ?? ($u ? $u->position : '');
                        $displayOffice = $rec->snapshot_office ?? ($u ? $u->office : '');
                    @endphp
                    <tr>
                        <td class="left-align">{{ $displayName }}</td>
                        <td>{{ $displayPosition }}</td>
                        <td>{{ $displayOffice }}</td>
                        <td>{{ $rec->no_pay_vl ?? '' }}</td>
                        <td>{{ $rec->no_pay_sl ?? '' }}</td>
                        <td>{{ $rec->no_pay_total ?? '' }}</td>
                        <td>{{ $rec->undertime_hours ?? '' }}</td>
                        <td>{{ $rec->undertime_minutes ?? '' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="padding: 20px;">No leave records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- ── Signatures ── --}}
        <div style="margin-top: 40px; width: 100%; page-break-inside: avoid;">
            <div style="float: left; width: 40%; text-align: left;">
                <div class="sig-label" style="text-align: left; margin-bottom: 20px;">Prepared By:</div>
                <strong class="sig-name">{{ strtoupper($preparedBy?->pdf_name ?? auth()->user()->pdf_name) }}</strong>
                <div class="sig-title">
                    {{ $preparedBy?->position ?? auth()->user()->position ?? 'Leave Processing In-Charge' }}</div>
            </div>
            <div style="clear: both;"></div>
        </div>

    </div>
</body>

</html>