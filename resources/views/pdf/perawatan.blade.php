<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Form Perawatan {{ $form->nomor_form }}</title>
    <style>
        @page { margin: 25mm; }
        body { margin: 0; padding: 0; font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #1a1a1a; line-height: 1.4; }
        .container { width: 100%; }
        .header { text-align: center; margin-bottom: 12px; border-bottom: 2px solid #000; padding-bottom: 8px; }
        .header h1 { font-size: 14px; font-weight: bold; margin-bottom: 2px; }
        .header p { font-size: 9px; color: #555; }
        .form-number { font-size: 11px; font-weight: bold; text-align: center; margin-bottom: 10px; }
        table.info-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; table-layout: fixed; }
        table.info-table td { padding: 3px 5px; border: 1px solid #ccc; vertical-align: middle; word-wrap: break-word; overflow-wrap: break-word; }
        table.info-table .label { background: #f3f4f6; font-weight: 600; width: 20%; font-size: 8px; }
        table.info-table .value { font-size: 9px; }
        h3 { font-size: 10px; font-weight: bold; margin: 10px 0 5px; padding: 2px 5px; background: #f3f4f6; border-left: 3px solid #333; }
        table.items-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; table-layout: fixed; }
        table.items-table th { background: #f9fafb; border: 1px solid #ccc; padding: 2px 4px; font-size: 8px; text-align: left; font-weight: 600; }
        table.items-table td { border: 1px solid #ddd; padding: 2px 4px; font-size: 8px; word-wrap: break-word; overflow-wrap: break-word; }
        table.items-table tr:nth-child(even) { background: #fafafa; }
        .notes { margin: 8px 0; padding: 5px; border: 1px solid #ddd; border-radius: 4px; font-size: 8px; word-wrap: break-word; }
        .notes strong { display: block; margin-bottom: 2px; }
        .signatures { width: 100%; border-collapse: collapse; margin-top: 25px; page-break-inside: avoid; }
        .signatures td { width: 33.33%; text-align: center; vertical-align: top; padding: 0 5px; }
        .sig-label { font-size: 9px; font-weight: bold; margin-bottom: 4px; text-decoration: underline; }
        .sig-name { font-size: 8px; margin-top: 4px; }
        .sig-date { font-size: 7px; color: #777; margin-top: 2px; }
        .sig-img { width: 100px; height: 40px; margin: 4px auto; border: none; background: transparent; object-fit: contain; }
        .sig-empty { width: 100px; height: 40px; margin: 4px auto; border-bottom: 1px solid #999; }
        .footer { margin-top: 15px; text-align: center; font-size: 7px; color: #999; border-top: 1px solid #eee; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <table style="width: 100%; border: none; border-collapse: collapse;">
                <tr>
                    <td style="width: 80px; border: none; padding: 0; vertical-align: middle;">
                        <img src="{{ public_path('images/asri.png') }}" style="width: 70px;">
                    </td>
                    <td style="border: none; padding: 0; vertical-align: middle; text-align: center;">
                        <h1>FORM PERAWATAN PERANGKAT</h1>
                        <p>IT Department &mdash; ASRI</p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="form-number">No. Form: {{ $form->nomor_form }}</div>

        <table class="info-table">
            <tr>
                <td class="label">Teknisi</td>
                <td class="value">{{ $form->teknisi->name ?? '-' }}</td>
                <td class="label">Tanggal Perawatan</td>
                <td class="value">{{ $form->submitted_at ? $form->submitted_at->format('d/m/Y H:i') : '-' }}</td>
            </tr>
            <tr>
                <td class="label">NIK Teknisi</td>
                <td class="value">{{ $form->teknisi->nik ?? '-' }}</td>
                <td class="label">Department</td>
                <td class="value">{{ $form->teknisi->department ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Pengguna</td>
                <td class="value">{{ $form->pengguna->name ?? '-' }}</td>
                <td class="label">Site</td>
                <td class="value">{{ $form->teknisi->site ?? '-' }}</td>
            </tr>
        </table>

        <h3>Informasi Perangkat</h3>
        <table class="info-table">
            <tr>
                <td class="label">Kategori</td>
                <td class="value">{{ $form->asset->kategori ?? '-' }}</td>
                <td class="label">Brand / Tipe</td>
                <td class="value">{{ $form->asset->brand ?? '-' }} / {{ $form->asset->tipe ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Nama Perangkat</td>
                <td class="value">{{ $form->asset->nama_perangkat ?? '-' }}</td>
                <td class="label">No. Serial</td>
                <td class="value">{{ $form->asset->no_serial ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">No. Asset</td>
                <td class="value">{{ $form->asset->no_asset ?? '-' }}</td>
                <td class="label">Kondisi Akhir</td>
                <td class="value">{{ $form->kondisi_akhir === 'good_normal' ? 'Good / Normal' : 'Caution / Poor' }} {{ $form->kondisi_akhir_notes ? "({$form->kondisi_akhir_notes})" : '' }}</td>
            </tr>
        </table>

        @php $categories = ['hardware' => 'Perawatan Hardware', 'aplikasi' => 'Perawatan Aplikasi', 'operating_system' => 'Operating System']; @endphp
        @foreach($categories as $catKey => $catLabel)
            @php $items = $form->items->where('category', $catKey)->sortBy('sort_order'); @endphp
            @if($items->count() > 0)
                <h3>{{ $catLabel }}</h3>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th style="width:5%">No</th>
                            <th style="width:40%">Item</th>
                            <th style="width:20%">Status</th>
                            <th style="width:35%">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $idx => $item)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->status === 'baik' ? 'Baik' : ($item->status === 'tidak_baik' ? 'Tidak Baik' : '-') }}</td>
                                <td>{{ $item->keterangan ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endforeach

        @if($form->notes)
            <div class="notes">
                <strong>Catatan Tambahan:</strong>
                {{ $form->notes }}
            </div>
        @endif

        {{-- Signatures --}}
        @php
            $diperiksa = $form->approvals->firstWhere('approval_level', 'diperiksa_oleh');
            $diketahui = $form->approvals->firstWhere('approval_level', 'diketahui_oleh');
            $disetujui = $form->approvals->firstWhere('approval_level', 'disetujui_oleh');
        @endphp

        <table class="signatures">
            <tr>
                <td>
                    <div class="sig-label">Diperiksa Oleh</div>
                    @if($diperiksa && $diperiksa->signature_path)
                        <img src="{{ $diperiksa->signature_path }}" class="sig-img" alt="TTD">
                    @else
                        <div class="sig-empty"></div>
                    @endif
                    <div class="sig-name">{{ $diperiksa->user->name ?? '_______________' }}</div>
                    <div class="sig-date">{{ $diperiksa && $diperiksa->approved_at ? $diperiksa->approved_at->format('d/m/Y') : '_______________' }}</div>
                </td>
                <td>
                    <div class="sig-label">Diketahui Oleh</div>
                    @if($diketahui && $diketahui->signature_path)
                        <img src="{{ $diketahui->signature_path }}" class="sig-img" alt="TTD">
                    @else
                        <div class="sig-empty"></div>
                    @endif
                    <div class="sig-name">{{ $diketahui->user->name ?? '_______________' }}</div>
                    <div class="sig-date">{{ $diketahui && $diketahui->approved_at ? $diketahui->approved_at->format('d/m/Y') : '_______________' }}</div>
                </td>
                <td>
                    <div class="sig-label">Disetujui Oleh</div>
                    @if($disetujui && $disetujui->signature_path)
                        <img src="{{ $disetujui->signature_path }}" class="sig-img" alt="TTD">
                    @else
                        <div class="sig-empty"></div>
                    @endif
                    <div class="sig-name">{{ $disetujui->user->name ?? '_______________' }}</div>
                    <div class="sig-date">{{ $disetujui && $disetujui->approved_at ? $disetujui->approved_at->format('d/m/Y') : '_______________' }}</div>
                </td>
            </tr>
        </table>

        <div class="footer">
            Form Perawatan Perangkat &mdash; {{ $form->nomor_form }} &mdash; Generated on {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>
</body>
</html>
