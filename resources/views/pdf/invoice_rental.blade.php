<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $data['invoice']['number'] }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 14px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .header-table td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: middle;
        }

        .logo {
            width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 0;
        }

        .invoice-title {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin: 0;
        }

        .header-right {
            width: 30%;
            font-size: 13px;
            line-height: 1.4;
        }

        .client-details {
            margin-bottom: 20px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        .items-table th {
            background-color: #f2f2f2;
        }

        .terbilang {
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 20px;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .summary-table td {
            border: 1px solid #000;
            padding: 5px;
        }

        .notes {
            margin-top: 20px;
            margin-bottom: 40px;
        }

        .payment-info {
            width: 100%;
            /* display: inline-flex;
            justify-content: space-between; */
            margin-top: 60px;
        }

        .payment-box {
            border: 1px solid #000;
            padding: 10px;
        }

        .signature {
            text-align: center;
            width: 25%;
        }

        .qr-code {
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <table class="header-table">
        <tr>
            <td rowspan="3" width="14%">
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/logo.png'))) }}"
                    class="logo" alt="Logo">
            </td>
            <td rowspan="2" style="text-align: center; vertical-align: middle; border-right: none; padding: 10px 0;">
                <div class="company-name">BAGUS LAUNDRY</div>
            </td>
            <td class="header-right" style="width: 30%; text-align: left; padding: 3px 10px;">
                <div>Cab. : {{ $data['branch']['name'] }}</div>
            </td>
        </tr>
        <tr>
            {{-- <td></td> --}}
            {{-- <td></td> --}}
            <td class="header-right" style="width: 30%; text-align: left; padding: 3px 10px;">
                <div>No. : {{ $data['invoice']['number'] }}</div>
            </td>
        </tr>
        <tr>
            {{-- <td></td> --}}
            <td style="text-align: center; vertical-align: middle; border-right: none; padding: 10px 0;">
                <div class="invoice-title">INVOICE</div>
            </td>
            <td class="header-right" style="width: 30%; text-align: left; padding: 3px 10px;">
                <div>Tgl. : {{ $data['invoice']['date'] }}</div>
            </td>
        </tr>
    </table>

    <div class="client-details">
        <p>Kepada yth,</p>
        <p>{{ $data['client']['name'] ?? '-' }}</p>
        <p>{{ $data['client']['address'] ?? '-' }}</p>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>NO</th>
                <th>DESKRIPSI</th>
                <th>QTY (Kg)</th>
                <th>HARGA/Kg</th>
                <th>TOTAL HARGA</th>
            </tr>
        </thead>
        <tbody>
            @php $counter = 1; @endphp
            @foreach ($data['items_by_type'] as $item)
                <tr>
                    <td>{{ $counter++ }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $item['type'])) }}</td>
                    <td>{{ number_format($item['total_weight'], 2) }}</td>
                    <td>Rp {{ number_format($item['price_weight']) }}</td>
                    <td>Rp {{ number_format($item['total_price']) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="summary-table">
        <tr>
            <td style="width: 50%" rowspan="4">
                <p><strong>Terbilang :</strong></p>
                <p>{{ ucwords(terbilang($data['invoice']['total_price'])) }} Rupiah</p>
            </td>
            <td style="width: 20%">Sub Total</td>
            <td>Rp {{ number_format($data['invoice']['price']) }}</td>
        </tr>
        <tr>
            <td>Diskon</td>
            <td>{{ $data['invoice']['promo'] > 0 ? 'Rp ' . number_format($data['invoice']['promo']) : '-' }}</td>
        </tr>
        <tr>
            <td>Biaya Lain-lain</td>
            <td>{{ $data['invoice']['additional_cost'] > 0 ? 'Rp ' . number_format($data['invoice']['additional_cost']) : '-' }}
            </td>
        </tr>
        <tr>
            <td><strong>Total</strong></td>
            <td><strong>Rp {{ number_format($data['invoice']['total_price']) }}</strong></td>
        </tr>
    </table>

    <div class="notes">
        <p><strong>NOTE :</strong></p>
        @if ($data['invoice']['custom_note'])
            <p>{{ $data['invoice']['custom_note'] }}</p>
        @endif
    </div>

    <table class="payment-info">
        <tr>
            <td style="width: 25%" class="payment-box">
                <p><strong>Pembayaran :</strong></p>
                <p>{{ $data['invoice']['payment'] }}</p>
                <p style="font-size: 20px; font-weight: bold; color: #666; margin-top: 20px;">Bagus Laundry</p>
            </td>
            <td style="width: 25%"></td>

            <td class="signature">
                <p>Hormat kami,</p>
                <div class="qr-code">
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/qr_code.png'))) }}"
                        style="height: 60px" alt="QR Code">
                </div>
                <p>(Bagus Laundry)</p>
            </td>
        </tr>
    </table>
</body>

</html>

<?php
// Helper function to convert numbers to words in Indonesian
function terbilang($angka)
{
    $angka = abs($angka);
    $baca = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];
    $terbilang = '';

    if ($angka < 12) {
        $terbilang = ' ' . $baca[$angka];
    } elseif ($angka < 20) {
        $terbilang = terbilang($angka - 10) . ' belas';
    } elseif ($angka < 100) {
        $terbilang = terbilang(floor($angka / 10)) . ' puluh' . terbilang($angka % 10);
    } elseif ($angka < 200) {
        $terbilang = ' seratus' . terbilang($angka - 100);
    } elseif ($angka < 1000) {
        $terbilang = terbilang(floor($angka / 100)) . ' ratus' . terbilang($angka % 100);
    } elseif ($angka < 2000) {
        $terbilang = ' seribu' . terbilang($angka - 1000);
    } elseif ($angka < 1000000) {
        $terbilang = terbilang(floor($angka / 1000)) . ' ribu' . terbilang($angka % 1000);
    } elseif ($angka < 1000000000) {
        $terbilang = terbilang(floor($angka / 1000000)) . ' juta' . terbilang($angka % 1000000);
    } elseif ($angka < 1000000000000) {
        $terbilang = terbilang(floor($angka / 1000000000)) . ' milyar' . terbilang($angka % 1000000000);
    } elseif ($angka < 1000000000000000) {
        $terbilang = terbilang(floor($angka / 1000000000000)) . ' trilyun' . terbilang($angka % 1000000000000);
    }

    return $terbilang;
}
?>
