<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Absence Without Pay Report</title>
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
            font-size: 11pt;
            color: #000;
            background: #fff;
            padding: 24px 32px;
        }

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
            font-size: 12pt;
            font-weight: bold;
        }

        .header .office-title {
            font-size: 12pt;
            font-weight: bold;
            margin-top: 8px;
        }

        .doc-date {
            text-align: right;
            margin-right: 300px;
            margin-bottom: 14px;
            font-size: 11pt;
        }

        .to-line {
            margin-bottom: 8px;
            font-size: 11pt;
        }

        .to-line strong {
            font-size: 11pt;
        }

        .body-para {
            margin-bottom: 14px;
            font-size: 11pt;
            line-height: 1.6;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
            margin-bottom: 18px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: center;
        }

        th {
            font-weight: bold;
            font-size: 9pt;
        }

        td.left-align {
            text-align: left;
        }

        .footer-note {
            margin-bottom: 8px;
            font-size: 11pt;
        }

        .thank-you {
            margin-bottom: 28px;
            font-size: 11pt;
        }

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
        }

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

    @php
        $primaryRecord = $records->first();
        $primaryUser = $primaryRecord?->user;
        $asOfDate = $primaryRecord?->as_of_date;
        $hasCertifier = filled($certifierName ?? null);
        $formattedLeaveTypes = $leaveTypes ?? '';

        $allDates = $records->flatMap(function ($record) {
            return array_merge((array) $record->no_pay_dates, (array) $record->undertime_dates);
        })->filter()->sort()->values();

        if ($allDates->isNotEmpty()) {
            $yearsAndMonths = [];
            foreach ($allDates as $d) {
                $dt = \Carbon\Carbon::parse($d);
                $year = $dt->format('Y');
                $month = strtoupper($dt->format('F'));
                if (!isset($yearsAndMonths[$year])) {
                    $yearsAndMonths[$year] = [];
                }
                if (!in_array($month, $yearsAndMonths[$year])) {
                    $yearsAndMonths[$year][] = $month;
                }
            }

            // Ensure years are also in order
            ksort($yearsAndMonths);

            $formattedParts = [];
            foreach ($yearsAndMonths as $year => $months) {
                if (count($months) === 1) {
                    $formattedParts[] = $months[0] . ' ' . $year;
                } elseif (count($months) === 2) {
                    $formattedParts[] = $months[0] . ' & ' . $months[1] . ' ' . $year;
                } else {
                    $lastMonth = array_pop($months);
                    $formattedParts[] = implode(', ', $months) . ' & ' . $lastMonth . ' ' . $year;
                }
            }
            $monthStr = implode('; ', $formattedParts);
        } else {
            $monthStr = $asOfDate ? strtoupper($asOfDate->format('F Y')) : '';
        }

        $asOfFormatted = $asOfDate ? $asOfDate->format('F j, Y') : '';
        $subjectLabel = $records->count() === 1 ? 'employee has' : 'employees have';
    @endphp

    <div class="toolbar no-print">
        <a href="{{ route('admin.recorded-entries') }}" class="btn-back">← Back</a>
        <span style="color:#94a3b8; font-size:13px;">Absence Without Pay Report</span>
        <div style="display:flex;gap:10px;">
            <button class="btn-print" onclick="window.print()">🖨 Print</button>
            <button class="btn-download" onclick="window.print()">⬇ Save as PDF</button>
        </div>
    </div>

    <div class="print-area">

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
            <p style="font-size: 12pt; font-weight: bold; margin-top: 12px; text-transform: uppercase;">ABSENCE WITHOUT PAY REPORT</p>
        </div>

        <div class="doc-date">
            {{ now()->format('d F Y') }}
        </div>

        <div class="to-line">
            TO: <strong>PROVINCIAL ACCOUNTANT'S OFFICE</strong>
        </div>

        <div class="body-para">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Per records of this office, the following {{ $subjectLabel }} incurred
            <strong>{{ $formattedLeaveTypes }}</strong>
            @if(str_contains($formattedLeaveTypes, 'leave of absence') || str_contains($formattedLeaveTypes, 'undertime') || str_contains($formattedLeaveTypes, 'tardy'))
                <strong>without pay</strong>
            @endif
            for the month of <strong>{{ $monthStr }}</strong>:
        </div>

        <table>
            <thead>
                <tr>
                    <th rowspan="3" style="vertical-align:middle">NAME</th>
                    <th rowspan="3" style="vertical-align:middle">POSITION</th>
                    <th rowspan="3" style="vertical-align:middle">OFFICE</th>
                    <th colspan="2">
                        EARNED LEAVE<br />CREDITS BALANCE As<br />of {{ $asOfFormatted }}
                    </th>
                    <th colspan="4">NO. OF DAYS OF<br />W/OUT PAY</th>
                    <th colspan="3">NO. OF DAYS OF UNDERTIME/<br />TARDY W/OUT PAY</th>
                </tr>
                <tr>
                    <th rowspan="2" style="vertical-align:middle">VL</th>
                    <th rowspan="2" style="vertical-align:middle">SL</th>
                    <th colspan="3">DAYS</th>
                    <th rowspan="2" style="vertical-align:middle">INCLUSIVE DATES</th>
                    <th rowspan="2" style="vertical-align:middle">HRS</th>
                    <th rowspan="2" style="vertical-align:middle">MINS</th>
                    <th rowspan="2" style="vertical-align:middle">INCLUSIVE DATES</th>
                </tr>
                <tr>
                    <th>VL</th>
                    <th>SL</th>
                    <th>TOTAL</th>
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
                        <td>{{ $rec->el_vl !== null ? $rec->el_vl : '' }}</td>
                        <td>{{ $rec->el_sl !== null ? $rec->el_sl : '' }}</td>
                        <td>{{ $rec->no_pay_vl ?? '' }}</td>
                        <td>{{ $rec->no_pay_sl ?? '' }}</td>
                        <td>{{ $rec->no_pay_total ?? '' }}</td>
                        <td class="left-align">
                            {{ $rec->no_pay_dates ? \App\Models\LeaveRecord::formatDates($rec->no_pay_dates) : '' }}
                            @if($rec->remarks)
                                ({{ $rec->remarks }})
                            @endif
                        </td>
                        <td>{{ $rec->undertime_hours ?? '' }}</td>
                        <td>{{ $rec->undertime_minutes ?? '' }}</td>
                        <td class="left-align">
                            {{ $rec->undertime_dates ? \App\Models\LeaveRecord::formatDates($rec->undertime_dates) : '' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" style="padding: 20px;">No leave records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer-note">
            Please administer the necessary deductions on his/her salary and PERA/ACA.
        </div>
        <div class="thank-you">Thank you.</div>

        <div style="margin-top: 40px; width: 100%;">
            <div style="float: right; width: 28%; text-align: center;">
                <div class="sig-label" style="text-align: left; margin-bottom: 20px;">Prepared By:</div>
                <strong class="sig-name">{{ strtoupper($preparedBy?->pdf_name ?? auth()->user()->pdf_name) }}</strong>
                <div class="sig-title">
                    {{ $preparedBy?->position ?? auth()->user()->position ?? 'Leave Processing In-Charge' }}
                </div>
            </div>
            <div style="clear: both;"></div>

            @if($hasCertifier)
                <div style="float: left; width: 28%; text-align: center; margin-top: 20px;">
                    <div class="sig-label" style="text-align: left; margin-bottom: 20px;">Noted:</div>
                    <strong class="sig-name">{{ strtoupper($certifierName) }}</strong>
                    <div class="sig-title">{!! nl2br(e($certifierPosition ?? '')) !!}</div>
                </div>
                <div style="clear: both;"></div>
            @endif
        </div>

    </div>

    {{-- Reference Number --}}
    <div style="font-size: 10pt; color: #555; margin-top: 30px;">
        @foreach($records as $rec)
            @if($rec->reference_no_lw || $rec->ref_number) Reference No.: <strong>{{ $rec->reference_no_lw ?? $rec->ref_number }}</strong><br>
            @endif
        @endforeach
    </div>
</body>

</html>
