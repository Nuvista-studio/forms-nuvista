<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Form Pemeriksaan {{ $form->nomor_form }}</title>
    <style>
        @page { margin: 15mm 15mm 15mm 15mm; size: A4 portrait; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 9px; color: #1a1a1a; line-height: 1.3; }
        table { border-collapse: collapse; }
        td, th { padding: 2px 4px; }

        .header-table { width: 100%; margin-bottom: 4px; }
        .header-table td { border: none; vertical-align: middle; }
        .header-logo { width: 55px; }
        .header-title { text-align: center; }
        .header-title h1 { font-size: 13px; font-weight: bold; margin: 0; }
        .header-title p { font-size: 8px; color: #555; margin: 1px 0 0; }

        .form-row { width: 100%; margin-bottom: 4px; }
        .form-row td { border: none; padding: 0; }
        .form-no { font-size: 9px; font-weight: bold; }
        .form-date { font-size: 9px; text-align: right; }

        .info-table { width: 100%; border: 1px solid #999; margin-bottom: 6px; }
        .info-table td { border: 1px solid #ccc; padding: 2px 5px; font-size: 8.5px; }
        .info-table .lbl { background: #f0f0f0; font-weight: 600; width: 16%; font-size: 8px; }
        .info-table .val { width: 34%; }

        .section-title { font-size: 9px; font-weight: bold; margin: 6px 0 3px; padding: 2px 5px; background: #e8e8e8; border-left: 3px solid #333; }

        .device-table { width: 100%; border: 1px solid #999; margin-bottom: 6px; }
        .device-table td { border: 1px solid #ccc; padding: 2px 5px; font-size: 8px; }
        .device-table .lbl { background: #f0f0f0; font-weight: 600; font-size: 7.5px; }

        .two-col { width: 100%; margin-bottom: 2px; }
        .two-col > td { vertical-align: top; padding: 0 4px 0 0; border: none; width: 50%; }
        .two-col > td:last-child { padding: 0 0 0 4px; }

        .checklist-table { width: 100%; border: 1px solid #999; margin-bottom: 4px; table-layout: fixed; }
        .checklist-table th { background: #f5f5f5; border: 1px solid #ccc; padding: 2px 3px; font-size: 7.5px; text-align: left; font-weight: 600; }
        .checklist-table td { border: 1px solid #ddd; padding: 1.5px 3px; font-size: 7.5px; }
        .checklist-table .col-no { width: 6%; text-align: center; }
        .checklist-table .col-name { width: 30%; }
        .checklist-table .col-kondisi { width: 20%; text-align: center; }
        .checklist-table .col-ket { width: 44%; }
        .checklist-table tr:nth-child(even) td { background: #fafafa; }

        .tindakan-table { width: 100%; border: 1px solid #999; margin-bottom: 4px; }
        .tindakan-table td { border: 1px solid #ccc; padding: 2px 5px; font-size: 8px; }
        .tindakan-table .lbl { background: #f0f0f0; font-weight: 600; font-size: 7.5px; }

        .kondisi-legend { font-size: 8px; margin: 4px 0; padding: 3px 5px; border: 1px solid #ddd; background: #fafafa; }
        .kondisi-legend span { margin-right: 12px; }

        .catatan { font-size: 8px; margin: 5px 0; padding: 4px 5px; border: 1px solid #ddd; }
        .catatan strong { display: block; margin-bottom: 2px; font-size: 8.5px; }

        .signatures { width: 100%; border-collapse: collapse; margin-top: 20px; page-break-inside: avoid; }
        .signatures td { text-align: center; vertical-align: top; padding: 0 3px; }
        .sig-label { font-size: 8px; font-weight: bold; margin-bottom: 3px; text-decoration: underline; }
        .sig-role { font-size: 7px; color: #555; margin-bottom: 15px; }
        .sig-name { font-size: 7.5px; margin-top: 3px; }
        .sig-date { font-size: 7px; color: #777; margin-top: 1px; }
        .sig-img { width: 90px; height: 35px; margin: 3px auto; border: none; background: transparent; object-fit: contain; }
        .sig-line { width: 90px; border-bottom: 1px solid #999; margin: 20px auto 3px; }

        .footer { margin-top: 10px; text-align: center; font-size: 7px; color: #999; border-top: 1px solid #eee; padding-top: 4px; }
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
                <h1>FORMULIR PEMERIKSAAN PERANGKAT</h1>
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

    {{-- INFORMASI PENGGUNA --}}
    <div class="section-title">Infomasi Pengguna</div>
    <table class="info-table">
        <tr>
            <td class="lbl">Nama User</td>
            <td class="val">{{ $form->pengguna->name ?? '-' }}</td>
            <td class="lbl">NIK User</td>
            <td class="val">{{ $form->pengguna->nik ?? '-' }}</td>
        </tr>
        <tr>
            <td class="lbl">Department</td>
            <td class="val">{{ $form->pengguna->department ?? '-' }}</td>
            <td class="lbl">Site / B. Unit</td>
            <td class="val">{{ $form->pengguna->site ?? $form->pengguna->business_unit ?? '-' }}</td>
        </tr>
        <tr>
            <td class="lbl">No. Telepon</td>
            <td class="val">{{ $form->pengguna->no_telepon ?? '-' }}</td>
            <td class="lbl">Alamat Email</td>
            <td class="val">{{ $form->pengguna->email ?? '-' }}</td>
        </tr>
    </table>

    {{-- INFORMASI PERANGKAT --}}
    <div class="section-title">Informasi Perangkat</div>
    <table class="device-table">
        <tr>
            <td class="lbl" style="width:12%;">Kategori</td>
            <td style="width:14%;">{{ $form->asset->kategori ?? '-' }}</td>
            <td class="lbl" style="width:10%;">Brand</td>
            <td style="width:14%;">{{ $form->asset->brand ?? '-' }}</td>
            <td class="lbl" style="width:10%;">Tipe</td>
            <td style="width:14%;">{{ $form->asset->tipe ?? '-' }}</td>
            <td class="lbl" style="width:13%;">Nama Perangkat</td>
            <td style="width:13%;">{{ $form->asset->nama_perangkat ?? '-' }}</td>
        </tr>
        <tr>
            <td class="lbl">No. Serial</td>
            <td>{{ $form->asset->no_serial ?? '-' }}</td>
            <td class="lbl">No. Asset</td>
            <td>{{ $form->asset->no_asset ?? '-' }}</td>
            <td class="lbl">Kondisi</td>
            <td colspan="3">
                @if($form->kondisi === 'baru')
                    <strong>BARU</strong>
                @elseif($form->kondisi === 'lama')
                    LAMA {{ $form->kondisi_keterangan ? "({$form->kondisi_keterangan})" : '' }}
                @elseif($form->kondisi === 'good_normal')
                    Good / Normal {{ $form->kondisi_keterangan ? "({$form->kondisi_keterangan})" : '' }}
                @elseif($form->kondisi === 'caution_poor')
                    Caution / Poor {{ $form->kondisi_keterangan ? "({$form->kondisi_keterangan})" : '' }}
                @else
                    -
                @endif
            </td>
        </tr>
    </table>

    {{-- PEMERIKSAAN HARDWARE + APLIKASI (side by side) --}}
    @php
        $hardwareItems = $form->items->where('category', 'hardware')->sortBy('sort_order');
        $aplikasiItems = $form->items->where('category', 'aplikasi')->sortBy('sort_order');
        $osItems = $form->items->where('category', 'operating_system')->sortBy('sort_order');
    @endphp

    <table class="two-col">
        <tr>
            {{-- HARDWARE --}}
            <td>
                <div class="section-title" style="margin-top:0;">Pemeriksaan Hardware</div>
                <table class="checklist-table">
                    <thead>
                        <tr>
                            <th class="col-no">No</th>
                            <th class="col-name">Name</th>
                            <th class="col-kondisi">Kondisi</th>
                            <th class="col-ket">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hardwareItems as $idx => $item)
                            <tr>
                                <td class="col-no" style="text-align:center;">{{ $idx + 1 }}</td>
                                <td>{{ $item->name }}</td>
                                <td class="col-kondisi">
                                    @if($item->status === 'baik') &#10003; Baik
                                    @elseif($item->status === 'tidak_baik') &#10007; Tidak Baik
                                    @elseif($item->status === 'baru') &#10003; Baru
                                    @elseif($item->status === 'lama') &#10003; Lama
                                    @elseif($item->status === 'good_normal') &#10003; Good/Normal
                                    @elseif($item->status === 'caution_poor') &#10007; Caution/Poor
                                    @else - @endif
                                </td>
                                <td>{{ $item->keterangan ?? '' }}</td>
                            </tr>
                        @empty
                            <tr><td class="col-no">1</td><td>-</td><td class="col-kondisi">-</td><td>-</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </td>

            {{-- APLIKASI --}}
            <td>
                <div class="section-title" style="margin-top:0;">Pemeriksaan Aplikasi</div>
                <table class="checklist-table">
                    <thead>
                        <tr>
                            <th class="col-no">No</th>
                            <th class="col-name">Name</th>
                            <th class="col-kondisi">Kondisi</th>
                            <th class="col-ket">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($aplikasiItems as $idx => $item)
                            <tr>
                                <td class="col-no" style="text-align:center;">{{ $idx + 1 }}</td>
                                <td>{{ $item->name }}</td>
                                <td class="col-kondisi">
                                    @if($item->status === 'baik') &#10003; Baik
                                    @elseif($item->status === 'tidak_baik') &#10007; Tidak Baik
                                    @elseif($item->status === 'baru') &#10003; Baru
                                    @elseif($item->status === 'lama') &#10003; Lama
                                    @elseif($item->status === 'good_normal') &#10003; Good/Normal
                                    @elseif($item->status === 'caution_poor') &#10007; Caution/Poor
                                    @else - @endif
                                </td>
                                <td>{{ $item->keterangan ?? '' }}</td>
                            </tr>
                        @empty
                            <tr><td class="col-no">1</td><td>-</td><td class="col-kondisi">-</td><td>-</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    {{-- OPERATING SYSTEM + TINDAKAN (side by side) --}}
    @php
        $tindakanItems = ['Install / Repair / Reset OS', 'Create / Delete Account', 'Delete / Backup Data', 'Install / Uninstall Application', 'Service / Pergantian sparepart', 'Upgrade', 'Pergantian Unit (replacement unit)'];
    @endphp

    <table class="two-col">
        <tr>
            {{-- OPERATING SYSTEM --}}
            <td>
                <div class="section-title" style="margin-top:0;">Operating System</div>
                <table class="checklist-table">
                    <thead>
                        <tr>
                            <th class="col-no">No</th>
                            <th class="col-name">Name</th>
                            <th class="col-kondisi">Kondisi</th>
                            <th class="col-ket">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($osItems as $idx => $item)
                            <tr>
                                <td class="col-no" style="text-align:center;">{{ $idx + 1 }}</td>
                                <td>{{ $item->name }}</td>
                                <td class="col-kondisi">
                                    @if($item->status === 'baik') &#10003; Baik
                                    @elseif($item->status === 'tidak_baik') &#10007; Tidak Baik
                                    @elseif($item->status === 'baru') &#10003; Baru
                                    @elseif($item->status === 'lama') &#10003; Lama
                                    @elseif($item->status === 'good_normal') &#10003; Good/Normal
                                    @elseif($item->status === 'caution_poor') &#10007; Caution/Poor
                                    @else - @endif
                                </td>
                                <td>{{ $item->keterangan ?? '' }}</td>
                            </tr>
                        @empty
                            <tr><td class="col-no">1</td><td>-</td><td class="col-kondisi">-</td><td>-</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </td>

            {{-- TINDAKAN --}}
            <td>
                <div class="section-title" style="margin-top:0;">Tindakan</div>
                <table class="tindakan-table">
                    @foreach($tindakanItems as $ti)
                        <tr>
                            <td style="width:12%; text-align:center; font-size:9px;">[&nbsp;&nbsp;&nbsp;]</td>
                            <td>{{ $ti }}</td>
                        </tr>
                    @endforeach
                </table>
            </td>
        </tr>
    </table>

    {{-- KONDISI LEGEND --}}
    <div class="kondisi-legend">
        <strong style="font-size:8px;">Kondisi :</strong>
        <span>&#10003; : BAIK</span>
        <span>&#10007; : TIDAK BAIK</span>
        <span style="margin-left:8px; font-size:7px;">(Mohon jelaskan kerusakan atau masalah yang ada)</span>
    </div>

    {{-- CATATAN --}}
    @if($form->notes)
        <div class="catatan">
            <strong>CATATAN :</strong>
            {{ $form->notes }}
        </div>
    @else
        <div class="catatan">
            <strong>CATATAN :</strong>
            *) : diisi untuk perangkat lama
        </div>
    @endif

    {{-- SIGNATURES --}}
    @php
        $diperiksa = $form->approvals->firstWhere('approval_level', 'diperiksa_oleh');
        $diketahui = $form->approvals->firstWhere('approval_level', 'diketahui_oleh');
        $disetujui = $form->approvals->firstWhere('approval_level', 'disetujui_oleh');
    @endphp

    <table class="signatures">
        <tr>
            {{-- DIPERIKSA --}}
            <td style="width:28%;">
                <div class="sig-label">Diperiksa Oleh</div>
                @if($diperiksa && $diperiksa->signature_path)
                    <img src="{{ $diperiksa->signature_path }}" class="sig-img" alt="TTD">
                @else
                    <div class="sig-line"></div>
                @endif
                <div class="sig-name">{{ $diperiksa->user->name ?? '_______________' }}</div>
                <div class="sig-date">Tanggal : {{ $diperiksa && $diperiksa->approved_at ? $diperiksa->approved_at->format('d/m/Y') : '___/___/______' }}</div>
            </td>

            {{-- DIKETAHUI --}}
            <td style="width:28%;">
                <div class="sig-label">Diketahui Oleh</div>
                @if($diketahui && $diketahui->signature_path)
                    <img src="{{ $diketahui->signature_path }}" class="sig-img" alt="TTD">
                @else
                    <div class="sig-line"></div>
                @endif
                <div class="sig-name">{{ $diketahui->user->name ?? '_______________' }}</div>
                <div class="sig-date">Tanggal : {{ $diketahui && $diketahui->approved_at ? $diketahui->approved_at->format('d/m/Y') : '___/___/______' }}</div>
            </td>

            {{-- DISETUJUI --}}
            <td style="width:28%;">
                <div class="sig-label">Disetujui Oleh</div>
                @if($disetujui && $disetujui->signature_path)
                    <img src="{{ $disetujui->signature_path }}" class="sig-img" alt="TTD">
                @else
                    <div class="sig-line"></div>
                @endif
                <div class="sig-name">{{ $disetujui->user->name ?? '_______________' }}</div>
                <div class="sig-date">Tanggal : {{ $disetujui && $disetujui->approved_at ? $disetujui->approved_at->format('d/m/Y') : '___/___/______' }}</div>
            </td>

            {{-- PENGGUNA --}}
            <td style="width:16%;">
                <div class="sig-label">Pengguna</div>
                <div class="sig-line"></div>
                <div class="sig-role">Supervisor IT<br>Operation Staff IT</div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Form Pemeriksaan Perangkat &mdash; {{ $form->nomor_form }} &mdash; {{ $form->asset->nama_perangkat ?? '' }}
    </div>

</body>
</html>
