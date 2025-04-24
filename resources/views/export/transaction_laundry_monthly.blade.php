<!-- resources/views/export/transaction_laundry_monthly.blade.php -->
<div>
    <h2>REPORT TRANSAKSI LAUNDRY</h2>

    <table>
        <tr>
            <td>Lokasi</td>
            <td>: {{ $location }}</td>
        </tr>
        <tr>
            <td>Periode</td>
            <td>: {{ $period }}</td>
        </tr>
    </table>

    <table border="1" cellspacing="0" cellpadding="5">
        <thead>
            <tr>
                <th>NO</th>
                <th>TANGGAL</th>
                <th>JAM</th>
                <th>NAMA PELANGGAN</th>
                <th>STATUS</th>
                <th>JUMLAH (PCS)</th>
                <th>BERAT (KG)</th>
                <th>HARGA</th>
                <th>PROMO</th>
                <th>BIAYA TAMBAHAN</th>
                <th>TOTAL</th>
                <th>STAFF</th>
                <th>TGL TERIMA</th>
                <th>TGL SELESAI</th>
            </tr>
        </thead>
        <tbody>
            @php $rowNumber = 1; @endphp
            @foreach ($transactions as $transaction)
                <tr>
                    <td align="center">{{ $rowNumber++ }}</td>
                    <td>{{ \Carbon\Carbon::parse($transaction->tanggal)->format('d M y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($transaction->jam)->format('H:i') }}</td>
                    <td>{{ $transaction->client_name }}</td>
                    <td>{{ ucfirst($transaction->status) }}</td>
                    <td align="center">{{ $transaction->qty }}</td>
                    <td align="right">{{ number_format($transaction->weight, 2) }}</td>
                    <td align="right">{{ number_format($transaction->price, 0) }}</td>
                    <td align="right">{{ number_format($transaction->promo, 0) }}</td>
                    <td align="right">{{ number_format($transaction->additional_cost, 0) }}</td>
                    <td align="right">{{ number_format($transaction->total, 0) }}</td>
                    <td>{{ $transaction->staff_name ?? $transaction->user_id }}</td>
                    <td>{{ $transaction->first_date ? \Carbon\Carbon::parse($transaction->first_date)->format('d M y') : '-' }}
                    </td>
                    <td>{{ $transaction->last_date ? \Carbon\Carbon::parse($transaction->last_date)->format('d M y') : '-' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" align="right"><strong>TOTAL</strong></td>
                <td align="center"><strong>{{ $totalQty }}</strong></td>
                <td align="right"><strong>{{ number_format($totalWeight, 2) }}</strong></td>
                <td align="right"><strong>{{ number_format($totalPrice, 0) }}</strong></td>
                <td align="right"><strong>{{ number_format($totalPromo, 0) }}</strong></td>
                <td align="right"><strong>{{ number_format($totalAdditionalCost, 0) }}</strong></td>
                <td align="right"><strong>{{ number_format($totalAmount, 0) }}</strong></td>
                <td colspan="3"></td>
            </tr>
        </tfoot>
    </table>

    @if (count($notes) > 0)
        <div style="margin-top: 20px;">
            <p><strong>CATATAN :</strong></p>
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
        </div>
    @endif
</div>
