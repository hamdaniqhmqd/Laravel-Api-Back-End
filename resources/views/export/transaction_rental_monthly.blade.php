<div>
    <h2>REPORT PEMINJAMAN {{ strtoupper($title) }}</h2>

    <table>
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
        <tr>
            <td>Stok Awal</td>
            <td>: {{ $initialStock }}</td>
        </tr>
    </table>

    <table border="1" cellspacing="0" cellpadding="10">
        <thead>
            <tr>
                <th>NO</th>
                <th>TANGGAL</th>
                <th>JAM</th>
                <th>TIPE</th>
                <th>MASUK</th>
                <th>KELUAR</th>
                <th>JUMLAH STOK</th>
                <th>BERAT MASUK (KG)</th>
                <th>BERAT KELUAR (KG)</th>
                <th>KURIR</th>
                <th>PENERIMA</th>
            </tr>
        </thead>
        <tbody>
            @php $rowNumber = 1; @endphp
            @foreach ($transactions as $transaction)
                @if ($transaction->masuk > 0)
                    <tr>
                        <td>{{ $rowNumber++ }}</td>
                        <td>{{ \Carbon\Carbon::parse($transaction->tanggal)->format('d M y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($transaction->jam)->format('H:i') }}</td>
                        <td>{{ $transaction->status }}</td>
                        <td>{{ $transaction->masuk }}</td>
                        <td>0</td>
                        <td>{{ $transaction->stok_after_masuk }}</td>
                        <td>{{ number_format($transaction->berat_masuk, 2) }}</td>
                        <td>0</td>
                        <td>{{ $transaction->kurir_name ?? $transaction->kurir_id }}</td>
                        <td>{{ $transaction->penerima }}</td>
                    </tr>
                @endif

                @if ($transaction->keluar > 0)
                    <tr>
                        <td>{{ $rowNumber++ }}</td>
                        <td>{{ \Carbon\Carbon::parse($transaction->tanggal)->format('d M y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($transaction->jam)->format('H:i') }}</td>
                        <td>{{ $transaction->status }}</td>
                        <td>0</td>
                        <td>{{ $transaction->keluar }}</td>
                        <td>{{ $transaction->stok_after_keluar }}</td>
                        <td>0</td>
                        <td>{{ number_format($transaction->berat_keluar, 2) }}</td>
                        <td>{{ $transaction->kurir_name ?? $transaction->kurir_id }}</td>
                        <td>{{ $transaction->penerima }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" align="right"><strong>TOTAL</strong></td>
                <td align="right"><strong>{{ number_format($totalInWeight, 2) }}</strong></td>
                <td align="right"><strong>{{ number_format($totalOutWeight, 2) }}</strong></td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>

    <h3><strong>CATATAN :</strong></h3>

    @if (count($notes) > 0)
        <table border="1" cellspacing="0" cellpadding="5" style="width: 100%;">
            <thead>
                <tr>
                    <th width="10%">No</th>
                    <th width="90%">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($notes as $index => $note)
                    <tr>
                        <td align="center">{{ $index + 1 }}</td>
                        <td>{{ $note }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
