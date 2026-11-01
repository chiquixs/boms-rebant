<!DOCTYPE html>
<html lang="id">
<body style="margin:0;padding:24px;background:#f4f6f2;font-family:Arial,sans-serif;color:#252823;">
    <div style="max-width:620px;margin:0 auto;background:#fff;border:1px solid #ecefe9;border-radius:12px;padding:28px;">

        <h2 style="margin:0 0 4px;font-size:20px;">Purchase Order {{ $po->po_no }}</h2>
        <p style="margin:0 0 20px;font-size:13px;color:#6b7280;">
            Tanggal {{ $po->created_at->translatedFormat('d F Y') }}
        </p>

        <p style="font-size:14px;line-height:1.6;">
            Yth. {{ $po->supplier->name }},<br>
            Mohon proses pemesanan bahan berikut:
        </p>

        <table width="100%" cellpadding="8" cellspacing="0"
               style="border-collapse:collapse;font-size:13px;margin:16px 0;">
            <thead>
                <tr style="background:#fafbf8;text-align:left;">
                    <th style="border-bottom:1px solid #ecefe9;">Bahan</th>
                    <th style="border-bottom:1px solid #ecefe9;" align="right">Qty</th>
                    <th style="border-bottom:1px solid #ecefe9;" align="right">Harga</th>
                    <th style="border-bottom:1px solid #ecefe9;" align="right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($po->items as $item)
                    <tr>
                        <td style="border-bottom:1px solid #ecefe9;">{{ $item->rawMaterial->name ?? '-' }}</td>
                        <td style="border-bottom:1px solid #ecefe9;" align="right">
                            {{ rtrim(rtrim(number_format((float) $item->qty_ordered, 3, ',', '.'), '0'), ',') }}
                            {{ $item->rawMaterial->unit ?? '' }}
                        </td>
                        <td style="border-bottom:1px solid #ecefe9;" align="right">
                            Rp {{ number_format((float) $item->unit_price, 0, ',', '.') }}
                        </td>
                        <td style="border-bottom:1px solid #ecefe9;" align="right">
                            Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" align="right" style="padding-top:12px;"><strong>Total</strong></td>
                    <td align="right" style="padding-top:12px;">
                        <strong>Rp {{ number_format($po->items->sum(fn ($i) => (float) $i->subtotal), 0, ',', '.') }}</strong>
                    </td>
                </tr>
            </tfoot>
        </table>

        @if ($po->notes)
            <p style="font-size:13px;background:#fafbf8;border-radius:8px;padding:10px 12px;">
                <strong>Catatan:</strong> {{ $po->notes }}
            </p>
        @endif

        <p style="font-size:13px;color:#6b7280;margin-top:20px;">
            Mohon konfirmasi penerimaan pesanan ini dengan membalas email ini.
        </p>
    </div>
</body>
</html>