<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Timeline Proyek & Tagihan</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f4f6f9;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .no-print {
            margin-bottom: 20px;
            text-align: right;
        }
        .btn-print {
            background-color: #d9534f;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
        }
        .btn-print:hover {
            background-color: #c9302c;
        }
        h2 {
            border-bottom: 2px solid #d9534f;
            padding-bottom: 10px;
            color: #2c3e50;
            margin-top: 0;
        }
        .step-block {
            margin-bottom: 25px;
            padding-left: 15px;
            border-left: 3px solid #3498db;
        }
        .step-title {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 8px;
        }
        ul.step-details {
            list-style-type: disc;
            padding-left: 20px;
            margin: 0;
        }
        ul.step-details li {
            margin-bottom: 5px;
        }
        .inner-list {
            list-style-type: circle;
            padding-left: 20px;
            margin-top: 5px;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background-color: #fff;
                padding: 0;
            }
            .container {
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="no-print">
        <button class="btn-print" onclick="window.print()"><i class="fa fa-print"></i> Cetak / Simpan PDF</button>
    </div>

    <?php
    // Helper format tanggal Indonesia
    if (!function_exists('formatTglIndo')) {
        function formatTglIndo($datetimeStr) {
            if (empty($datetimeStr) || $datetimeStr == '-' || $datetimeStr == '0000-00-00 00:00:00') {
                return '-';
            }
            $timestamp = strtotime($datetimeStr);
            if (!$timestamp) return $datetimeStr;
            $bulan = array(1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
            $tgl = date('j', $timestamp);
            $bln = $bulan[(int)date('n', $timestamp)];
            $thn = date('Y', $timestamp);
            $jam = date('H:i', $timestamp);
            return "$tgl $bln $thn, jam $jam WIB";
        }
    }

    $customerNama = isset($trx->customers_nama) ? $trx->customers_nama : '-';
    $nomerUtama = isset($trx->nomer2) && !empty($trx->nomer2) ? $trx->nomer2 : (isset($trx->nomer) ? $trx->nomer : '-');

    // Deteksi Nama Proyek & Tipe Transaksi (Prioritas Utama: Transaksi Aktif $trx)
    $projectName = '';

    // 1. Cek langsung pada transaksi aktif ($trx)
    if (!empty($trx->project_nama)) {
        $projectName = $trx->project_nama;
    } elseif (!empty($trx->nama_proyek)) {
        $projectName = $trx->nama_proyek;
    } elseif (!empty($trx->keterangan) && preg_match('/Project:\s*([^<,\n]+)/i', $trx->keterangan, $m)) {
        $projectName = trim($m[1]);
    }

    // 2. Fallback: Ambil langsung dari tabel master project_produk (Fast SQL Query)
    if (empty($projectName) && isset($trx->id)) {
        $ci =& get_instance();
        $ci->db->select("nama");
        $ci->db->from("project_produk");
        $ci->db->where("trash", 0);
        $ci->db->where("(transaksi_id = '" . (int)$trx->id . "' OR quot_id = '" . (int)$trx->id . "' OR project_start_id = '" . (int)$trx->id . "')", null, false);
        $pQ = $ci->db->get();
        $pRow = is_object($pQ) ? $pQ->row() : null;
        if (!empty($pRow->nama)) {
            $projectName = $pRow->nama;
        }
    }

    // 3. Fallback: Ambil dari transaksi_data_registry milik $trx->id
    if (empty($projectName) && isset($trx->id)) {
        $ci =& get_instance();
        $ci->db->select("main");
        $ci->db->from("transaksi_data_registry");
        $ci->db->where("transaksi_id", $trx->id);
        $regQ = $ci->db->get();
        $regRow = is_object($regQ) ? $regQ->row() : null;
        if (!empty($regRow->main)) {
            $mDec = @unserialize(base64_decode($regRow->main));
            if (is_array($mDec) && !empty($mDec['referensi_so_project__nama'])) {
                $projectName = $mDec['referensi_so_project__nama'];
            }
        }
    }

    // 3. Fallback: Cek seluruh tahapan di $timelineSteps (dari tahap terbaru ke terawal)
    if (empty($projectName) && isset($timelineSteps) && is_array($timelineSteps)) {
        $reversedSteps = array_reverse($timelineSteps);
        foreach ($reversedSteps as $s) {
            if (!empty($s->project_nama)) {
                $projectName = $s->project_nama;
                break;
            } elseif (!empty($s->nama_proyek)) {
                $projectName = $s->nama_proyek;
                break;
            } elseif (!empty($s->keterangan) && preg_match('/Project:\s*([^<,\n]+)/i', $s->keterangan, $m)) {
                $projectName = trim($m[1]);
                break;
            }
        }
    }

    $isProjectTrx = false;
    if (!empty($projectName)) {
        $isProjectTrx = true;
    } else {
        $jm = isset($trx->jenis_master) ? (string)$trx->jenis_master : '';
        if ($jm == '588' || strpos($jm, 'project') !== false || strpos($jm, 'proyek') !== false) {
            $isProjectTrx = true;
        }
    }

    $reportTitle = $isProjectTrx ? "Laporan Timeline Rantai Transaksi Proyek" : "Laporan Timeline Rantai Transaksi";

    // Hitung Total Uang Muka (DP) langsung dari tabel relational project_produk_items4 (Fast Indexed SQL Query)
    $totUangMuka = 0;
    if (isset($timelineSteps) && is_array($timelineSteps)) {
        $stepIds = array();
        $stepNos = array();
        foreach ($timelineSteps as $us) {
            if (!empty($us->id)) $stepIds[] = (int)$us->id;
            if (!empty($us->nomer)) $stepNos[] = $us->nomer;
            if (!empty($us->nomer2)) $stepNos[] = $us->nomer2;
        }

        if (sizeof($stepIds) > 0 || sizeof($stepNos) > 0) {
            $ci =& get_instance();
            $ci->db->select("project_produk_items4.jumlah, project_produk_items4.harga");
            $ci->db->from("project_produk_items4");
            $ci->db->where("project_produk_items4.trash", 0);

            $dpConds = array();
            if (sizeof($stepIds) > 0) {
                $dpConds[] = "project_produk_items4.transaksi_id IN (" . implode(",", $stepIds) . ")";
            }
            if (sizeof($stepNos) > 0) {
                $escapedNos = array();
                foreach ($stepNos as $sn) {
                    $escapedNos[] = "'" . $ci->db->escape_str($sn) . "'";
                }
                $dpConds[] = "project_produk_items4.transaksi_no IN (" . implode(",", $escapedNos) . ")";
                $dpConds[] = "project_produk_items4.nomer IN (" . implode(",", $escapedNos) . ")";
            }
            $ci->db->where("(" . implode(" OR ", $dpConds) . ")", null, false);

            $dpQ = $ci->db->get();
            $dpRows = is_object($dpQ) ? $dpQ->result() : array();
            foreach ($dpRows as $dpR) {
                $j = isset($dpR->jumlah) ? (float)$dpR->jumlah : 0;
                $h = isset($dpR->harga) ? (float)$dpR->harga : 0;
                $valDp = $j > 0 ? $j : $h;
                if ($valDp > 0) {
                    $totUangMuka += $valDp;
                }
            }
        }
    }

    // Fallback: Hitung dari steps / receipts jika $totUangMuka masih 0
    if ($totUangMuka == 0 && isset($timelineSteps) && is_array($timelineSteps)) {
        foreach ($timelineSteps as $st) {
            $j = isset($st->jenis) ? (string)$st->jenis : '';
            $ket = isset($st->keterangan) ? strtolower($st->keterangan) : '';
            $lbl = isset($st->jenis_label) ? strtolower($st->jenis_label) : '';
            if ($j == '4467' || $j == '4467_1' || $j == '583' || $j == '583r' || strpos($j, '4467') !== false || strpos($ket, 'dp project') !== false || strpos($lbl, 'uang muka') !== false) {
                $net = isset($st->transaksi_net) ? (float)$st->transaksi_net : 0;
                $nilai = isset($st->transaksi_nilai) ? (float)$st->transaksi_nilai : 0;
                $nomSt = $net > 0 ? $net : $nilai;
                $totUangMuka += $nomSt;
            }
        }
    }

    // Ambil detail data skema dari project_produk dan project_produk_items* (Fast Indexed Queries)
    $projectSchemaItems3 = array();
    $projectSchemaItems4 = array();
    $projectSchemaItems5 = array();
    $projectProgress = '';
    $projectNoKontrak = '';
    $projectAlamat = '';
    $projectId = '';

    if ($isProjectTrx && isset($timelineSteps) && is_array($timelineSteps)) {
        $pSearchIds = array();
        foreach ($timelineSteps as $st) {
            if (!empty($st->id)) $pSearchIds[] = (int)$st->id;
        }

        if (sizeof($pSearchIds) > 0) {
            $ci =& get_instance();
            $ci->db->select("id, nama, persen_progress, nomor_kontrak, alamat, quot_nomer, transaksi_id");
            $ci->db->from("project_produk");
            $ci->db->where("trash", 0);
            $ci->db->where("(transaksi_id IN (" . implode(",", $pSearchIds) . ") OR quot_id IN (" . implode(",", $pSearchIds) . ") OR project_start_id IN (" . implode(",", $pSearchIds) . "))", null, false);
            $pHeaderQ = $ci->db->get();
            $pHeaderRow = is_object($pHeaderQ) ? $pHeaderQ->row() : null;

            if (!empty($pHeaderRow->id)) {
                $projectId = $pHeaderRow->id;
                $pId = $pHeaderRow->id;
                $pTrxId = $pHeaderRow->transaksi_id;
                $pQuotNo = $pHeaderRow->quot_nomer;

                if (empty($projectName) && !empty($pHeaderRow->nama)) {
                    $projectName = $pHeaderRow->nama;
                }

                if ((float)$pHeaderRow->persen_progress > 0) {
                    $projectProgress = number_format((float)$pHeaderRow->persen_progress, 0) . '%';
                }
                if (!empty($pHeaderRow->nomor_kontrak)) {
                    $projectNoKontrak = $pHeaderRow->nomor_kontrak;
                }
                if (!empty($pHeaderRow->alamat)) {
                    $projectAlamat = $pHeaderRow->alamat;
                }

                // Ambil Items 3 (Termin Schedule)
                $ci->db->select("nama, persen, harga, jumlah");
                $ci->db->from("project_produk_items3");
                $ci->db->where("trash", 0);
                $ci->db->where("(transaksi_id = '$pTrxId' OR nomer = '$pQuotNo')", null, false);
                $qIt3 = $ci->db->get();
                if (is_object($qIt3)) $projectSchemaItems3 = $qIt3->result();

                // Ambil Items 4 (DP Schedule)
                $ci->db->select("persen, harga, jumlah, keterangan_dp");
                $ci->db->from("project_produk_items4");
                $ci->db->where("trash", 0);
                $ci->db->where("(transaksi_id = '$pTrxId' OR nomer = '$pQuotNo')", null, false);
                $qIt4 = $ci->db->get();
                if (is_object($qIt4)) $projectSchemaItems4 = $qIt4->result();

                // Ambil Items 5 (Retensi / Garansi Schedule)
                $ci->db->select("persen, harga, tgl_akhir_garansi, keterangan_garansi");
                $ci->db->from("project_produk_items5");
                $ci->db->where("trash", 0);
                $ci->db->where("(transaksi_id = '$pTrxId' OR nomer = '$pQuotNo')", null, false);
                $qIt5 = $ci->db->get();
                if (is_object($qIt5)) $projectSchemaItems5 = $qIt5->result();
            }
        }
    }

    $projectNameDisplay = $projectName;
    if (!empty($projectName) && !empty($projectId)) {
        $projectNameDisplay = $projectName . " (" . $projectId . ")";
    }
    ?>

    <h2 style="margin-top:0; border-bottom:2px solid #d9534f; padding-bottom:10px; color:#2c3e50;">
        <?php echo htmlspecialchars($reportTitle); ?>
    </h2>
    <div style="margin-bottom:20px; background:#eef2f7; padding:12px 16px; border-radius:5px; font-size:14px; line-height:1.7;">
        <strong>Pelanggan / Konsumen:</strong> <?php echo htmlspecialchars($customerNama); ?><br>
        <?php if (!empty($projectNameDisplay)): ?>
            <strong>Nama Proyek:</strong> <span style="color:#2b6cb0; font-weight:bold;"><?php echo htmlspecialchars($projectNameDisplay); ?></span><br>
        <?php endif; ?>
        <strong>Dokumen Acuan:</strong> <?php echo htmlspecialchars($nomerUtama); ?>
        <?php if ($isProjectTrx): ?>
            <?php 
            $totTag = isset($totalTagihan) ? (float)$totalTagihan : 0;
            $totPpnVal = isset($totalPpn) ? (float)$totalPpn : ($totTag - ($totTag / 1.11));
            $totDppVal = $totTag - $totPpnVal;
            ?>
            <br><strong>Total Nilai Proyek (Incl. PPN):</strong> <span style="color:#2d3748; font-weight:bold;">Rp<?php echo number_format($totTag, 0, ',', '.'); ?></span>
            <br><strong>Total PPN (11%):</strong> <span style="color:#d9534f; font-weight:bold;">Rp<?php echo number_format($totPpnVal, 0, ',', '.'); ?></span>
            <br><strong>Nilai Proyek DPP (Excl. PPN):</strong> <span style="color:#2d3748; font-weight:bold;">Rp<?php echo number_format($totDppVal, 0, ',', '.'); ?></span>
            <br><strong>Uang Muka (DP):</strong> <span style="color:#2b6cb0; font-weight:bold;">Rp<?php echo number_format($totUangMuka, 0, ',', '.'); ?></span>
            <?php if (!empty($projectProgress)): ?>
                <br><strong>Progress Pekerjaan:</strong> <span style="color:#2f855a; font-weight:bold;"><?php echo htmlspecialchars($projectProgress); ?> Selesai</span>
            <?php endif; ?>
            <?php if (!empty($projectNoKontrak)): ?>
                <br><strong>Nomor Kontrak:</strong> <?php echo htmlspecialchars($projectNoKontrak); ?>
            <?php endif; ?>
            <?php if (!empty($projectAlamat)): ?>
                <br><strong>Lokasi Site:</strong> <?php echo htmlspecialchars($projectAlamat); ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <?php if ($isProjectTrx && (sizeof($projectSchemaItems3) > 0 || sizeof($projectSchemaItems4) > 0)): ?>
        <div style="margin-bottom:25px; border:1px solid #cbd5e0; border-radius:6px; background:#fff; overflow:hidden;">
            <div style="background:#2c3e50; color:#fff; padding:8px 14px; font-weight:bold; font-size:13px;">
                📋 Skema Kontrak & Jadwal Termin Proyek
            </div>
            <table style="width:100%; border-collapse:collapse; font-size:12px;">
                <thead>
                    <tr style="background:#f7fafc; border-bottom:1px solid #e2e8f0; text-align:left; color:#4a5568;">
                        <th style="padding:8px 12px;">Skema Pembayaran</th>
                        <th style="padding:8px 12px; text-align:center;">Bobot Persen</th>
                        <th style="padding:8px 12px; text-align:right;">Nominal Acuan Kontrak</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projectSchemaItems4 as $i4R): ?>
                        <tr style="border-bottom:1px solid #edf2f7; background-color:#ebf8ff;">
                            <td style="padding:8px 12px; font-weight:bold; color:#2b6cb0;">
                                Uang Muka (DP Project)
                            </td>
                            <td style="padding:8px 12px; text-align:center; font-weight:bold; color:#2b6cb0;">
                                <?php echo (int)$i4R->persen; ?>%
                            </td>
                            <td style="padding:8px 12px; text-align:right; font-weight:bold; color:#2b6cb0;">
                                Rp<?php echo number_format((float)$totUangMuka, 0, ',', '.'); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php foreach ($projectSchemaItems3 as $i3R): ?>
                        <?php 
                        $tNom = (float)$i3R->harga > 0 ? (float)$i3R->harga : ((float)$totTag * ((float)$i3R->persen / 100));
                        ?>
                        <tr style="border-bottom:1px solid #edf2f7;">
                            <td style="padding:8px 12px; font-weight:bold; color:#2d3748;">
                                <?php echo !empty($i3R->nama) ? htmlspecialchars($i3R->nama) : 'Termin Invoice'; ?>
                            </td>
                            <td style="padding:8px 12px; text-align:center; font-weight:bold;">
                                <?php echo (int)$i3R->persen; ?>%
                            </td>
                            <td style="padding:8px 12px; text-align:right; font-weight:bold; color:#2d3748;">
                                Rp<?php echo number_format($tNom, 0, ',', '.'); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php foreach ($projectSchemaItems5 as $i5R): ?>
                        <?php if ((int)$i5R->persen > 0): ?>
                            <tr style="border-bottom:1px solid #edf2f7; background-color:#fffaf0;">
                                <td style="padding:8px 12px; font-weight:bold; color:#c05621;">
                                    Retensi / Garansi Pemeliharaan
                                    <?php if (!empty($i5R->tgl_akhir_garansi) && $i5R->tgl_akhir_garansi != '0000-00-00 00:00:00'): ?>
                                        <span style="font-weight:normal; font-size:11px;"> (s/d <?php echo formatTglIndo($i5R->tgl_akhir_garansi); ?>)</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding:8px 12px; text-align:center; font-weight:bold; color:#c05621;">
                                    <?php echo (int)$i5R->persen; ?>%
                                </td>
                                <td style="padding:8px 12px; text-align:right; font-weight:bold; color:#c05621;">
                                    Rp<?php echo number_format((float)$totTag * ((float)$i5R->persen / 100), 0, ',', '.'); ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <?php if (isset($timelineSteps) && is_array($timelineSteps) && sizeof($timelineSteps) > 0): ?>
        <?php foreach ($timelineSteps as $index => $step): ?>
            <?php
            $stepNum = $index + 1;

            // Resolusi Nomor Dokumen Resmi
            $docNo = '-';
            if (!empty($step->nomer2) && $step->nomer2 != '-') {
                $docNo = $step->nomer2;
            } elseif (!empty($step->nomer_top) && $step->nomer_top != '-') {
                $docNo = $step->nomer_top;
            } elseif (!empty($step->nomer)) {
                $docNo = $step->nomer;
            }

            // Resolusi Judul Step
            $stepTitleText = "Tahap " . $stepNum;
            if (!empty($step->jenis_label)) {
                $stepTitleText .= ": " . $step->jenis_label;
            } elseif (!empty($step->jenis)) {
                $stepTitleText .= ": Modul " . strtoupper($step->jenis);
            }

            $oleh = !empty($step->oleh_nama) ? $step->oleh_nama : '-';
            $waktu = !empty($step->dtime) ? formatTglIndo($step->dtime) : '-';
            $ket = !empty($step->keterangan) ? $step->keterangan : '-';
            $jSt = isset($step->jenis) ? (string)$step->jenis : '';
            $isReceiptStep = false;
            if ($jSt == '749' || $jSt == '749_1' || $jSt == '4467' || $jSt == '4467_1' || $jSt == '583' || $jSt == '583r' || (strpos($jSt, '749') !== false && strpos($jSt, '7499') === false) || strpos($jSt, '4467') !== false) {
                $isReceiptStep = true;
            }

            $nilai = isset($step->transaksi_nilai) ? (float)$step->transaksi_nilai : 0;
            $net = isset($step->transaksi_net) ? (float)$step->transaksi_net : 0;
            $ppnVal = isset($step->transaksi_ppn) ? (float)$step->transaksi_ppn : 0;

            if ($isReceiptStep) {
                $nominal = $net > 0 ? $net : $nilai;
                $stepPpn = $ppnVal > 0 ? $ppnVal : ($nominal - ($nominal / 1.11));
            } else {
                if ($net > 0) {
                    $nominal = $net;
                    $stepPpn = $ppnVal > 0 ? $ppnVal : ($net - ($net / 1.11));
                } else if ($nilai > 0) {
                    $stepPpn = $ppnVal > 0 ? $ppnVal : ($nilai * 0.11);
                    $nominal = $nilai + $stepPpn;
                } else {
                    $nominal = 0;
                    $stepPpn = 0;
                }
            }

            // Deteksi tipe transaksi (#penerimaan vs #pembayaran hutang)
            $isHutang = false;
            if ((isset($step->jenis_master) && (strpos($step->jenis_master, 'pembelian') !== false || strpos($step->jenis_master, 'hutang') !== false || strpos($step->jenis_master, 'supplies') !== false)) || !empty($step->suppliers_nama)) {
                $isHutang = true;
            }

            $namaKonsumen = !empty($trx->customers_nama) ? $trx->customers_nama : (!empty($step->customers_nama) ? $step->customers_nama : (!empty($trx->pihak_nama) ? $trx->pihak_nama : '-'));
            $namaSupplier = !empty($trx->suppliers_nama) ? $trx->suppliers_nama : (!empty($step->suppliers_nama) ? $step->suppliers_nama : (!empty($trx->pihak_nama) ? $trx->pihak_nama : '-'));
            ?>

            <div class="step-block" style="margin-bottom:20px; padding-left:15px; border-left:4px solid #3498db;">
                <div class="step-title" style="font-size:16px; font-weight:bold; color:#2c3e50; margin-bottom:8px;">
                    <?php echo htmlspecialchars($stepTitleText); ?>
                </div>
                <ul class="step-details" style="list-style-type:disc; padding-left:20px; margin:0;">
                    <li><strong>Oleh:</strong> <?php echo htmlspecialchars($oleh); ?></li>
                    <li><strong>Waktu:</strong> <?php echo htmlspecialchars($waktu); ?></li>
                    <li><strong>Nomor Dokumen:</strong> <?php echo htmlspecialchars($docNo); ?></li>
                    <?php if ($nominal > 0): ?>
                        <?php 
                        $jSt = isset($step->jenis) ? (string)$step->jenis : '';
                        $lblVal = "Nilai Transaksi (Incl. PPN)";
                        if (strpos($jSt, '588st') !== false || strpos($jSt, '588so') !== false || strpos($jSt, '588spo') !== false) {
                            $lblVal = "Nilai Total Kontrak Proyek (Incl. PPN)";
                        } elseif ($jSt == '7499' || $jSt == '4822') {
                            $lblVal = "Nilai Tagihan Invoice (Incl. PPN)";
                        } elseif ($jSt == '749' || $jSt == '4467' || $jSt == '583') {
                            $lblVal = "Nilai Pembayaran Diterima (Incl. PPN)";
                        }
                        ?>
                        <li><strong><?php echo $lblVal; ?>:</strong> Rp<?php echo number_format($nominal, 0, ',', '.'); ?></li>
                        <?php if ($stepPpn > 0): ?>
                            <li><strong>PPN (11%):</strong> Rp<?php echo number_format($stepPpn, 0, ',', '.'); ?></li>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if ($ket != '-'): ?>
                        <li><strong>Keterangan / Catatan:</strong> <?php echo htmlspecialchars($ket); ?></li>
                    <?php endif; ?>
                </ul>
                <?php if (isset($amandemenEvents[$step->id]) && is_array($amandemenEvents[$step->id]) && sizeof($amandemenEvents[$step->id]) > 0): ?>
                    <div style="margin-top:10px; margin-left:20px; background-color:#fff3cd; border:1px solid #ffeeba; border-radius:4px; padding:8px 12px;">
                        <strong style="color:#856404; font-size:13px;">Riwayat Amandemen (Perubahan Data):</strong>
                        <ul style="list-style-type:circle; margin:5px 0 0 20px; color:#856404; font-size:12px;">
                            <?php foreach ($amandemenEvents[$step->id] as $amE): ?>
                                <li>
                                    <strong>[<?php echo formatTglIndo($amE->dtime_amandemen); ?>]</strong>
                                    Oleh <?php echo !empty($amE->user_nama) ? htmlspecialchars($amE->user_nama) : (isset($amE->user_id) ? "User ID " . $amE->user_id : "Sistem"); ?>
                                    - Alasan: <em><?php echo !empty($amE->alasan) ? htmlspecialchars($amE->alasan) : '-'; ?></em>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div style="padding:20px; text-align:center; color:#888;">
            Tidak ada riwayat tahapan transaksi yang ditemukan untuk dokumen ini.
        </div>
    <?php endif; ?>

    <?php
    $totTagihan = isset($totalTagihan) ? (float)$totalTagihan : 0;
    $totDiterima = isset($totalDiterima) ? (float)$totalDiterima : 0;
    $sisaTag = isset($sisaTagihan) ? (float)$sisaTagihan : 0;
    $stPembayaran = isset($statusPembayaran) ? $statusPembayaran : 'BELUM_LUNAS';

    $statusLabel = 'BELUM LUNAS';
    $statusColor = '#d9534f';
    if ($stPembayaran == 'LUNAS') {
        $statusLabel = 'LUNAS';
        $statusColor = '#5cb85c';
    } elseif ($stPembayaran == 'CICILAN') {
        $statusLabel = 'DIBAYAR SEBAGIAN (CICILAN)';
        $statusColor = '#f0ad4e';
    }

    $isGlobalHutang = (isset($trx->jenis_master) && (strpos($trx->jenis_master, 'pembelian') !== false || strpos($trx->jenis_master, 'hutang') !== false)) || !empty($trx->suppliers_nama);
    $namaKonsumenHeader = !empty($trx->customers_nama) ? $trx->customers_nama : (!empty($trx->pihak_nama) ? $trx->pihak_nama : '-');
    $namaSupplierHeader = !empty($trx->suppliers_nama) ? $trx->suppliers_nama : (!empty($trx->pihak_nama) ? $trx->pihak_nama : '-');
    $namaPenagihanHeader = !empty($trx->oleh_nama) ? $trx->oleh_nama : '-';
    ?>

    <div style="margin-top:35px; border-top:2px dashed #ccc; padding-top:20px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
            <h3 style="margin:0; color:#2c3e50; font-size:16px;">
                <?php echo $isGlobalHutang ? 'Informasi & Status Pembayaran Hutang Vendor' : 'Informasi & Status Penerimaan Tagihan Konsumen'; ?>
            </h3>
            <span style="background-color:<?php echo $statusColor; ?>; color:#fff; padding:6px 14px; border-radius:4px; font-weight:bold; font-size:12px; letter-spacing:0.5px;">
                <?php echo $statusLabel; ?>
            </span>
        </div>

        <table style="width:100%; border-collapse:collapse; background:#fdfdfd; border:1px solid #e2e8f0; border-radius:6px; font-size:13px;">
            <?php 
            $totPpnSummary = isset($totalPpn) ? (float)$totalPpn : ($totTagihan - ($totTagihan / 1.11));
            $totDppSummary = $totTagihan - $totPpnSummary;
            ?>
            <tr style="border-bottom:1px solid #edf2f7;">
                <td style="padding:10px 14px; color:#4a5568; font-weight:bold;">Total Nilai DPP (Excl. PPN):</td>
                <td style="padding:10px 14px; text-align:right; font-weight:bold; color:#2d3748;">
                    Rp<?php echo number_format($totDppSummary, 0, ',', '.'); ?>
                </td>
            </tr>
            <tr style="border-bottom:1px solid #edf2f7; background-color:#fff5f5;">
                <td style="padding:10px 14px; color:#c53030; font-weight:bold;">Total PPN (11%):</td>
                <td style="padding:10px 14px; text-align:right; font-weight:bold; color:#c53030;">
                    Rp<?php echo number_format($totPpnSummary, 0, ',', '.'); ?>
                </td>
            </tr>
            <tr style="border-bottom:1px solid #edf2f7; background-color:#edf2f7;">
                <td style="padding:10px 14px; color:#2d3748; font-weight:bold;">Total Nilai Tagihan Invoice (Incl. PPN):</td>
                <td style="padding:10px 14px; text-align:right; font-weight:bold; color:#2d3748;">
                    Rp<?php echo number_format($totTagihan, 0, ',', '.'); ?>
                </td>
            </tr>
            <tr style="border-bottom:1px solid #edf2f7; background-color:#f7fafc;">
                <td style="padding:10px 14px; color:#4a5568; font-weight:bold;">
                    <?php echo $isGlobalHutang ? 'Total Pembayaran Diserahkan:' : 'Total Pembayaran Diterima:'; ?>
                </td>
                <td style="padding:10px 14px; text-align:right; font-weight:bold; color:#2b6cb0;">
                    Rp<?php echo number_format($totDiterima, 0, ',', '.'); ?>
                </td>
            </tr>
            <tr>
                <td style="padding:10px 14px; color:#4a5568; font-weight:bold;">
                    <?php echo $isGlobalHutang ? 'Sisa Hutang / Kurang Bayar:' : 'Sisa Piutang / Kurang Bayar:'; ?>
                </td>
                <td style="padding:10px 14px; text-align:right; font-weight:bold; color:<?php echo $sisaTag > 0 ? '#c53030' : '#2f855a'; ?>;">
                    Rp<?php echo number_format($sisaTag, 0, ',', '.'); ?>
                </td>
            </tr>
        </table>

        <?php if (isset($paymentReceipts) && is_array($paymentReceipts) && sizeof($paymentReceipts) > 0): ?>
            <div style="margin-top:15px;">
                <div style="font-weight:bold; color:#2d3748; margin-bottom:8px; font-size:13px;">
                    <?php echo $isGlobalHutang ? 'Rincian Bukti Pembayaran Hutang:' : 'Rincian Bukti Penerimaan Pembayaran:'; ?>
                </div>
                <table style="width:100%; border-collapse:collapse; font-size:12px; border:1px solid #cbd5e0;">
                    <thead>
                        <tr style="background:#edf2f7; color:#2d3748; text-align:left;">
                            <th style="padding:8px 10px; border:1px solid #cbd5e0;">No. Dokumen</th>
                            <th style="padding:8px 10px; border:1px solid #cbd5e0;">Tanggal & Jam</th>
                            <th style="padding:8px 10px; border:1px solid #cbd5e0;">Dibuat oleh</th>
                            <?php if ($isGlobalHutang): ?>
                                <th style="padding:8px 10px; border:1px solid #cbd5e0;">Diserahkan oleh</th>
                                <th style="padding:8px 10px; border:1px solid #cbd5e0;">Diterima oleh</th>
                            <?php else: ?>
                                <th style="padding:8px 10px; border:1px solid #cbd5e0;">Dibayar oleh</th>
                                <th style="padding:8px 10px; border:1px solid #cbd5e0;">Diterima oleh</th>
                            <?php endif; ?>
                            <th style="padding:8px 10px; border:1px solid #cbd5e0; text-align:right;">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($paymentReceipts as $pRec): ?>
                            <?php
                            $rDoc = !empty($pRec->nomer2) ? $pRec->nomer2 : (!empty($pRec->nomer) ? $pRec->nomer : '-');
                            $rWaktu = !empty($pRec->dtime) ? formatTglIndo($pRec->dtime) : '-';
                            $rKasir = !empty($pRec->oleh_nama) ? $pRec->oleh_nama : '-';
                            $rNet = isset($pRec->transaksi_net) ? (float)$pRec->transaksi_net : 0;
                            $rNilai = isset($pRec->transaksi_nilai) ? (float)$pRec->transaksi_nilai : 0;
                            $rNom = $rNet > 0 ? $rNet : $rNilai;
                            ?>
                            <tr>
                                <td style="padding:8px 10px; border:1px solid #cbd5e0; font-weight:bold;"><?php echo htmlspecialchars($rDoc); ?></td>
                                <td style="padding:8px 10px; border:1px solid #cbd5e0;"><?php echo htmlspecialchars($rWaktu); ?></td>
                                <td style="padding:8px 10px; border:1px solid #cbd5e0;"><?php echo htmlspecialchars($namaPenagihanHeader); ?></td>
                                <?php if ($isGlobalHutang): ?>
                                    <td style="padding:8px 10px; border:1px solid #cbd5e0;"><?php echo htmlspecialchars($rKasir); ?></td>
                                    <td style="padding:8px 10px; border:1px solid #cbd5e0;"><?php echo htmlspecialchars($namaSupplierHeader); ?></td>
                                <?php else: ?>
                                    <td style="padding:8px 10px; border:1px solid #cbd5e0;"><?php echo htmlspecialchars($namaKonsumenHeader); ?></td>
                                    <td style="padding:8px 10px; border:1px solid #cbd5e0;"><?php echo htmlspecialchars($rKasir); ?></td>
                                <?php endif; ?>
                                <td style="padding:8px 10px; border:1px solid #cbd5e0; text-align:right; font-weight:bold; color:#2b6cb0;">
                                    Rp<?php echo number_format($rNom, 0, ',', '.'); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div style="margin-top:10px; font-size:12px; color:#718096; font-style:italic;">
                * Belum ada riwayat transaksi pembayaran yang dicatat untuk invoice ini.
            </div>
        <?php endif; ?>
    </div>

</div>

</body>
</html>
