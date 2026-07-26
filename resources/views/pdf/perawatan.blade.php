<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Form Perawatan {{ $form->nomor_form }}</title>
    <style>
        @page { margin: 20mm 20mm 20mm 20mm; size: A4 portrait; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #1a1a1a; line-height: 1.4; padding: 0 5mm; }
        table { border-collapse: collapse; }
        td, th { padding: 3px 6px; }

        .header-table { width: 100%; margin-bottom: 8px; }
        .header-table td { border: none; vertical-align: middle; }
        .header-logo { width: 55px; }
        .header-title { text-align: center; }
        .header-title h1 { font-size: 18px; font-weight: bold; margin: 0; }
        .header-title p { font-size: 10px; color: #555; margin: 1px 0 0; }

        .form-row { width: 100%; margin-bottom: 8px; }
        .form-row td { border: none; padding: 0; }
        .form-no { font-size: 12px; font-weight: bold; }
        .form-date { font-size: 12px; text-align: right; }

        .info-table { width: 100%; border: 1px solid #999; margin-bottom: 8px; }
        .info-table td { border: 1px solid #ccc; padding: 3px 6px; font-size: 11px; }
        .info-table .lbl { background: #f0f0f0; font-weight: 600; width: 16%; font-size: 10px; }
        .info-table .val { width: 34%; }

        .section-title { font-size: 12px; font-weight: bold; margin: 8px 0 4px; padding: 3px 6px; background: #e8e8e8; border-left: 3px solid #333; }

        .two-col { width: 100%; margin-bottom: 6px; }
        .two-col > td { vertical-align: top; padding: 0 4px 0 0; border: none; width: 50%; }
        .two-col > td:last-child { padding: 0 0 0 4px; }

        .checklist-table { width: 100%; border: 1px solid #999; margin-bottom: 4px; table-layout: fixed; }
        .checklist-table th { background: #f5f5f5; border: 1px solid #ccc; padding: 3px 4px; font-size: 10px; text-align: left; font-weight: 600; }
        .checklist-table td { border: 1px solid #ddd; padding: 2px 4px; font-size: 10px; }
        .checklist-table .col-name { width: 36%; }
        .checklist-table .col-kondisi { width: 20%; text-align: center; }
        .checklist-table .col-ket { width: 44%; }
        .checklist-table tr:nth-child(even) td { background: #fafafa; }
        .battery-detail { background: #f9f9f9; }
        .battery-detail td { border: 1px solid #ddd; padding: 2px 4px; font-size: 9px; }
        .battery-detail .lbl { font-weight: 600; width: 25%; text-align: right; padding-right: 6px; }
        .battery-detail .val { width: 25%; }

        .kondisi-checklist { width: 100%; border: 1px solid #999; margin-bottom: 4px; }
        .kondisi-checklist td { border: 1px solid #ccc; padding: 3px 6px; font-size: 11px; }
        .kondisi-checklist .lbl { background: #f0f0f0; font-weight: 600; width: 20%; font-size: 10px; }
        .kondisi-checklist .val { font-size: 11px; }

        .kondisi-legend { font-size: 11px; margin: 6px 0; padding: 4px 6px; border: 1px solid #ddd; background: #fafafa; }
        .kondisi-legend span { margin-right: 12px; }

        .catatan { font-size: 11px; margin: 8px 0; padding: 5px 6px; border: 1px solid #ddd; }
        .catatan strong { display: block; margin-bottom: 2px; font-size: 11px; }

        .signatures { width: 100%; border-collapse: collapse; margin-top: 25px; page-break-inside: avoid; }
        .signatures td { text-align: center; vertical-align: top; padding: 0 3px; }
        .sig-label { font-size: 11px; font-weight: bold; margin-bottom: 3px; text-decoration: underline; }
        .sig-role { font-size: 9px; color: #555; margin-bottom: 15px; }
        .sig-name { font-size: 10px; margin-top: 3px; }
        .sig-date { font-size: 9px; color: #777; margin-top: 1px; }
        .sig-img { width: 90px; height: 35px; margin: 3px auto; border: none; background: transparent; object-fit: contain; }
        .sig-line { width: 90px; border-bottom: 1px solid #999; margin: 20px auto 3px; }

        .footer { margin-top: 10px; text-align: center; font-size: 9px; color: #999; border-top: 1px solid #eee; padding-top: 4px; }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <table class="header-table">
        <tr>
            <td class="header-logo">
                <img src="{{ public_path('images/asri.png') }}" style="width: 50px;">
            </td>
            <td class="header-title">
                <h1>FORMULIR PERAWATAN PERANGKAT</h1>
                <p>IT Department &mdash; ASRI</p>
            </td>
            <td style="width: 55px; border: none;"></td>
        </tr>
    </table>

    {{-- NO. FORM --}}
    <table class="form-row">
        <tr>
            <td class="form-no">No : {{ $form->nomor_form }}</td>
            <td class="form-date">Tanggal : {{ $form->submitted_at ? $form->submitted_at->format('d/m/Y') : '-' }}</td>
        </tr>
    </table>

    {{-- INFORMASI PENGGUNA + INFORMASI PERANGKAT (side by side) --}}
    <table class="two-col">
        <tr>
            {{-- INFORMASI PENGGUNA --}}
            <td>
                <div class="section-title" style="margin-top:0;">Informasi Pengguna</div>
                <table class="info-table">
                    <tr>
                        <td class="lbl">Nama User</td>
                        <td class="val">{{ $form->pengguna->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">NIK User</td>
                        <td class="val">{{ $form->pengguna->nik ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Department</td>
                        <td class="val">{{ $form->pengguna->department ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Business Unit</td>
                        <td class="val">{{ $form->pengguna->business_unit ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Site</td>
                        <td class="val">{{ $form->pengguna->site ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">No. Telepon</td>
                        <td class="val">{{ $form->pengguna->no_telepon ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Alamat Email</td>
                        <td class="val">{{ $form->pengguna->email ?? '-' }}</td>
                    </tr>
                </table>
            </td>

            {{-- INFORMASI PERANGKAT --}}
            <td>
                <div class="section-title" style="margin-top:0;">Informasi Perangkat</div>
                <table class="info-table">
                    <tr>
                        <td class="lbl">Kategori</td>
                        <td class="val">{{ $form->asset->kategori ?? '-' }}</td>
                        <td class="lbl">Tipe</td>
                        <td class="val">{{ $form->asset->tipe ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Brand</td>
                        <td class="val">{{ $form->asset->brand ?? '-' }}</td>
                        <td class="lbl">No. Serial</td>
                        <td class="val">{{ $form->asset->no_serial ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Nama Perangkat</td>
                        <td class="val">{{ $form->asset->nama_perangkat ?? '-' }}</td>
                        <td class="lbl">No. Asset</td>
                        <td class="val">{{ $form->asset->no_asset ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Site Location</td>
                        <td class="val" colspan="3">{{ $form->site->site ?? $form->site_location ?? '-' }}</td>
                        <td class="lbl">Location Detail</td>
                        <td class="val" colspan="3">{{ $form->location_detail ?? '-' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- PEMERIKSAAN PERANGKAT --}}
    @php
        $hardwareItems = $form->items->where('category', 'hardware')->sortBy('sort_order');
        $aplikasiItems = $form->items->where('category', 'aplikasi')->sortBy('sort_order');
        $osItems = $form->items->where('category', 'operating_system')->sortBy('sort_order');
    @endphp

    <div class="section-title">Pemeriksaan Perangkat</div>

    {{-- HARDWARE + APLIKASI (side by side) --}}
    <table class="two-col">
        <tr>
            {{-- HARDWARE --}}
            <td>
                <div class="section-title" style="margin-top:0;">Perawatan Hardware</div>
                <table class="checklist-table">
                    <thead>
                        <tr>
                            <th class="col-name">Name</th>
                            <th class="col-kondisi">Status</th>
                            <th class="col-ket">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hardwareItems as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td class="col-kondisi">
                                    @if($item->status === 'baik') Baik
                                    @elseif($item->status === 'tidak_baik') Tidak Baik
                                    @else - @endif
                                </td>
                                <td>{{ $item->keterangan ?? '' }}</td>
                            </tr>
                            @if(($item->name === 'Battery' || $item->name === 'Battery Report') && ($item->full_charge_capacity || $item->design_capacity))
                                <tr class="battery-detail">
                                    <td colspan="3" style="border-top: none; padding: 1px 4px 3px;">
                                        <table style="width: 100%; border-collapse: collapse;">
                                            <tr>
                                                <td class="lbl">Full Charge Capacity</td>
                                                <td class="val">{{ $item->full_charge_capacity ?? '-' }} mWh</td>
                                                <td class="lbl">Design Capacity</td>
                                                <td class="val">{{ $item->design_capacity ?? '-' }} mWh</td>
                                                <td class="lbl">Battery Health</td>
                                                <td class="val" style="font-weight: bold;">
                                                    @if($item->full_charge_capacity && $item->design_capacity && $item->design_capacity > 0)
                                                        {{ round(($item->full_charge_capacity / $item->design_capacity) * 100) }}%
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr><td>-</td><td class="col-kondisi">-</td><td>-</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </td>

            {{-- APLIKASI --}}
            <td>
                <div class="section-title" style="margin-top:0;">Perawatan Aplikasi</div>
                <table class="checklist-table">
                    <thead>
                        <tr>
                            <th class="col-name">Name</th>
                            <th class="col-kondisi">Status</th>
                            <th class="col-ket">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($aplikasiItems as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td class="col-kondisi">
                                    @if($item->status === 'baik') Baik
                                    @elseif($item->status === 'tidak_baik') Tidak Baik
                                    @else - @endif
                                </td>
                                <td>{{ $item->keterangan ?? '' }}</td>
                            </tr>
                        @empty
                            <tr><td>-</td><td class="col-kondisi">-</td><td>-</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    {{-- OS + KONDISI AKHIR (side by side) --}}
    <table class="two-col">
        <tr>
            {{-- OPERATING SYSTEM --}}
            <td>
                <div class="section-title" style="margin-top:0;">Perawatan Operating Sistem</div>
                <table class="checklist-table">
                    <thead>
                        <tr>
                            <th class="col-name">Name</th>
                            <th class="col-kondisi">Status</th>
                            <th class="col-ket">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($osItems as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td class="col-kondisi">
                                    @if($item->status === 'baik') Baik
                                    @elseif($item->status === 'tidak_baik') Tidak Baik
                                    @else - @endif
                                </td>
                                <td>{{ $item->keterangan ?? '' }}</td>
                            </tr>
                        @empty
                            <tr><td>-</td><td class="col-kondisi">-</td><td>-</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </td>

            {{-- KONDISI SETELAH PERAWATAN --}}
            <td>
                <div class="section-title" style="margin-top:0;">Kondisi Setelah Perawatan</div>
                <table class="kondisi-checklist">
                    <tr>
                        <td class="lbl">Good / Normal</td>
                        <td class="val" style="text-align:center;">
                            @if($form->kondisi_akhir === 'good_normal') [V] @else [ ] @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="lbl">Caution / Poor</td>
                        <td class="val" style="text-align:center;">
                            @if($form->kondisi_akhir === 'caution_poor') [V] @else [ ] @endif
                        </td>
                    </tr>
                </table>

                @if($form->kondisi_akhir_notes)
                    <div style="font-size:10px; margin-top:4px;">
                        <strong>Keterangan:</strong> {{ $form->kondisi_akhir_notes }}
                    </div>
                @endif

                <div class="catatan" style="margin-top:8px;">
                    <strong>Catatan Tambahan :</strong>
                    {{ $form->notes ?? '-' }}
                </div>
            </td>
        </tr>
    </table>

    {{-- KONDISI LEGEND --}}
    <div class="kondisi-legend">
        <strong style="font-size:11px;">Kondisi :</strong>
        <span>V : DONE</span>
        <span>X : NOT YET</span>
    </div>

    {{-- KOTA & TANGGAL --}}
    <div style="font-size:11px; margin-top:8px;">Jakarta, {{ $form->submitted_at ? $form->submitted_at->format('d F Y') : '_______________' }}</div>

    {{-- SIGNATURES --}}
    @php
        $diperiksa = $form->approvals->firstWhere('approval_level', 'diperiksa_oleh');
        $diketahui = $form->approvals->firstWhere('approval_level', 'diketahui_oleh');
        $disetujui = $form->approvals->firstWhere('approval_level', 'disetujui_oleh');
    @endphp

    <table class="signatures">
        <tr>
            {{-- PERAWATAN OLEH --}}
            <td style="width:33%;">
                <div class="sig-label">Perawatan Oleh</div>
                <div class="sig-role">Staff/Teknisi IT Operation</div>
                @if($diperiksa && $diperiksa->signature_path)
                    <img src="{{ $diperiksa->signature_path }}" class="sig-img" alt="TTD">
                @else
                    <div class="sig-line"></div>
                @endif
                <div class="sig-name">{{ $diperiksa->user->name ?? '_______________' }}</div>
                <div class="sig-date">Tanggal : {{ $diperiksa && $diperiksa->approved_at ? $diperiksa->approved_at->format('d/m/Y') : '___/___/______' }}</div>
            </td>

            {{-- DIKETAHUI --}}
            <td style="width:33%;">
                <div class="sig-label">Diketahui Oleh</div>
                <div class="sig-role">Pengguna / Supervisor IT</div>
                @if($diketahui && $diketahui->signature_path)
                    <img src="{{ $diketahui->signature_path }}" class="sig-img" alt="TTD">
                @else
                    <div class="sig-line"></div>
                @endif
                <div class="sig-name">{{ $diketahui->user->name ?? '_______________' }}</div>
                <div class="sig-date">Tanggal : {{ $diketahui && $diketahui->approved_at ? $diketahui->approved_at->format('d/m/Y') : '___/___/______' }}</div>
            </td>

            {{-- DISETUJUI --}}
            <td style="width:33%;">
                <div class="sig-label">Disetujui Oleh</div>
                <div class="sig-role">Manager IT Operation</div>
                @if($disetujui && $disetujui->signature_path)
                    <img src="{{ $disetujui->signature_path }}" class="sig-img" alt="TTD">
                @else
                    <div class="sig-line"></div>
                @endif
                <div class="sig-name">{{ $disetujui->user->name ?? '_______________' }}</div>
                <div class="sig-date">Tanggal : {{ $disetujui && $disetujui->approved_at ? $disetujui->approved_at->format('d/m/Y') : '___/___/______' }}</div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Form Perawatan Perangkat &mdash; {{ $form->nomor_form }} &mdash; {{ $form->asset->nama_perangkat ?? '' }}
    </div>

</body>
</html>
