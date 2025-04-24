<!-- resources/views/export/transaction_rental_monthly.blade.php -->
<!DOCTYPE html>
<html>

<head>
</head>

<body>
    <h2>REPORT PEMINJAMAN {{ strtoupper($title) }}</h2>

    <table class="header-table" border="1">
        <tr>
            <td>Lokasi</td>
            <td>: {{ $location }}</td>
        </tr>
        <tr>
            <td>Deskripsi</td>
            <td>: {{ $description }}</td>
        </tr>
        <tr>
            <td>Periode</td>
            <td>: {{ $period }}</td>
        </tr>
    </table>

    <table class="main-table" width="100%">
        <thead>
            <tr>
                <th>TANGGAL</th>
                <th>JAM</th>
                <th>MASUK</th>
                <th>KELUAR</th>
                <th>JUMLAH STOK</th>
                <th>BERAT (KG)</th>
                <th>KURIR</th>
                <th>PENERIMA</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $transaction)
                @if ($transaction->masuk > 0)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($transaction->tanggal)->format('d M y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($transaction->jam)->format('H:i') }}</td>
                        <td>{{ $transaction->masuk }}</td>
                        <td>0</td>
                        <td>{{ $transaction->stok_after_masuk }}</td>
                        <td>{{ number_format($transaction->berat, 2) }}</td>
                        <td>{{ $transaction->kurir }}</td>
                        <td>{{ $transaction->penerima }}</td>
                    </tr>
                @endif

                @if ($transaction->keluar > 0)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($transaction->tanggal)->format('d M y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($transaction->jam)->format('H:i') }}</td>
                        <td>0</td>
                        <td>{{ $transaction->keluar }}</td>
                        <td>{{ $transaction->stok_after_keluar }}</td>
                        <td>{{ number_format($transaction->berat, 2) }}</td>
                        <td>{{ $transaction->kurir }}</td>
                        <td>{{ $transaction->penerima }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" style="text-align: right">TOTAL</td>
                <td>{{ number_format($totalWeight, 2) }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>

    <table class="notes-table" width="100%">
        <tr>
            <th colspan="3">CATATAN :</th>
        </tr>
        @foreach ($notes as $note)
            <tr>
                <td colspan="7">- {{ $note }}</td>
            </tr>
        @endforeach
    </table>
</body>

</html>
