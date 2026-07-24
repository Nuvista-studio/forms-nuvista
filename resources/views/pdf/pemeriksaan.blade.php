<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Form Pemeriksaan {{ $form->nomor_form }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #1a1a1a; line-height: 1.4; }
        .container { width: 100%; padding: 30px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 3px solid #000; padding-bottom: 12px; }
        .header h1 { font-size: 18px; font-weight: bold; margin-bottom: 4px; }
        .header p { font-size: 11px; color: #555; }
        .form-number { font-size: 13px; font-weight: bold; text-align: right; margin-bottom: 15px; }
        table.info-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table.info-table td { padding: 5px 8px; border: 1px solid #ccc; vertical-align: top; }
        table.info-table .label { background: #f3f4f6; font-weight: 600; width: 30%; font-size: 10px; }
        table.info-table .value { font-size: 11px; }
        h3 { font-size: 12px; font-weight: bold; margin: 15px 0 8px; padding: 4px 8px; background: #f3f4f6; border-left: 3px solid #333; }
        table.items-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        table.items-table th { background: #f9fafb; border: 1px solid #ccc; padding: 4px 6px; font-size: 9px; text-align: left; font-weight: 600; }
        table.items-table td { border: 1px solid #ddd; padding: 4px 6px; font-size: 10px; }
        table.items-table tr:nth-child(even) { background: #fafafa; }
        .notes { margin: 12px 0; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 10px; }
        .notes strong { display: block; margin-bottom: 4px; }
        .signatures { display: flex; justify-content: space-between; margin-top: 40px; page-break-inside: avoid; }
        .sig-box { width: 30%; text-align: center; }
        .sig-box .sig-label { font-size: 10px; font-weight: bold; margin-bottom: 5px; text-decoration: underline; }
        .sig-box .sig-name { font-size: 10px; margin-top: 5px; }
        .sig-box .sig-date { font-size: 9px; color: #777; margin-top: 2px; }
        .sig-img { width: 120px; height: 50px; margin: 5px auto; border: 1px solid #ddd; background: #fafafa; object-fit: contain; }
        .sig-empty { width: 120px; height: 50px; margin: 5px auto; border-bottom: 1px solid #999; }
        .footer { margin-top: 30px; text-align: center; font-size: 8px; color: #999; border-top: 1px solid #eee; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>FORM PEMERIKSAAN PERANGKAT</h1>
            <p>IT Department &mdash; ASRI</p>
        </div>

        <div class="form-number">No. Form: {{ $form->nomor_form }}</div>

        <table class="info-table">
            <tr>
                <td class="label">Teknisi</td>
                <td class="value">{{ $form->teknisi->name ?? '-' }}</td>
                <td class="label">Tanggal Pemeriksaan</td>
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
                <td class="label">Kondisi</td>
                <td class="value">{{ $form->kondisi === 'baru' ? 'Baru' : 'Lama' }} {{ $form->kondisi_keterangan ? "({$form->kondisi_keterangan})" : '' }}</td>
            </tr>
        </table>

        @php $categories = ['hardware' => 'Pemeriksaan Hardware', 'aplikasi' => 'Pemeriksaan Aplikasi', 'operating_system' => 'Operating System']; @endphp
        @foreach($categories as $catKey => $catLabel)
            @php $items = $form->items->where('category', $catKey)->sortBy('sort_order'); @endphp
            @if($items->count() > 0)
                <h3>{{ $catLabel }}</h3>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th style="width:5%">No</th>
                            <th style="width:35%">Item</th>
                            <th style="width:15%">Status</th>
                            @if($catKey === 'operating_system')
                                <th style="width:20%">Value</th>
                            @endif
                            <th style="{{ $catKey === 'operating_system' ? 'width:25%' : 'width:45%' }}">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $idx => $item)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->status === 'baik' ? 'Baik' : ($item->status === 'tidak_baik' ? 'Tidak Baik' : '-') }}</td>
                                @if($catKey === 'operating_system')
                                    <td>{{ $item->value ?? '-' }}</td>
                                @endif
                                <td>{{ $item->keterangan ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endforeach

        @if($form->notes)
            <div class="notes">
                <strong>Catatan:</strong>
                {{ $form->notes }}
            </div>
        @endif

        {{-- Signatures --}}
        <div class="signatures">
            @php
                $diperiksa = $form->approvals->firstWhere('approval_level', 'diperiksa_oleh');
                $diketahui = $form->approvals->firstWhere('approval_level', 'diketahui_oleh');
                $disetujui = $form->approvals->firstWhere('approval_level', 'disetujui_oleh');
            @endphp

            <div class="sig-box">
                <div class="sig-label">Diperiksa Oleh</div>
                @if($diperiksa && $diperiksa->signature_path)
                    <img src="{{ $diperiksa->signature_path }}" class="sig-img" alt="TTD">
                @else
                    <div class="sig-empty"></div>
                @endif
                <div class="sig-name">{{ $diperiksa->user->name ?? '_______________' }}</div>
                <div class="sig-date">{{ $diperiksa && $diperiksa->approved_at ? $diperiksa->approved_at->format('d/m/Y') : '_______________' }}</div>
            </div>

            <div class="sig-box">
                <div class="sig-label">Diketahui Oleh</div>
                @if($diketahui && $diketahui->signature_path)
                    <img src="{{ $diketahui->signature_path }}" class="sig-img" alt="TTD">
                @else
                    <div class="sig-empty"></div>
                @endif
                <div class="sig-name">{{ $diketahui->user->name ?? '_______________' }}</div>
                <div class="sig-date">{{ $diketahui && $diketahui->approved_at ? $diketahui->approved_at->format('d/m/Y') : '_______________' }}</div>
            </div>

            <div class="sig-box">
                <div class="sig-label">Disetujui Oleh</div>
                @if($disetujui && $disetujui->signature_path)
                    <img src="{{ $disetujui->signature_path }}" class="sig-img" alt="TTD">
                @else
                    <div class="sig-empty"></div>
                @endif
                <div class="sig-name">{{ $disetujui->user->name ?? '_______________' }}</div>
                <div class="sig-date">{{ $disetujui && $disetujui->approved_at ? $disetujui->approved_at->format('d/m/Y') : '_______________' }}</div>
            </div>
        </div>

        <div class="footer">
            Form Pemeriksaan Perangkat &mdash; {{ $form->nomor_form }} &mdash; Generated on {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>
</body>
</html>
