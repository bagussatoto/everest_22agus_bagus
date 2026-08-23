<!-- [AGENT_LOG]
ROLE      : Software Engineer Agent
PURPOSE   : Form UI untuk amandemen, dengan pencegahan XSS dan Smart-Detect separator
COMPLIANCE: Frontend Safety Rules (AGENTS.md)
LOG_EXPIRE: 2026-11-07
[/AGENT_LOG] -->
<!DOCTYPE html>
<html>
<head>
    <title>Amandemen Invoice</title>
    <!-- Load jQuery & Bootstrap secara mandiri karena ini halaman popup tanpa layout induk -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- DataTables Core + Buttons + ColVis -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.colVis.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f2f2f2; }
        .drag-handle { cursor: grab; font-size: 1.2em; color: #888; margin-right: 5px; user-select: none; }
        .drag-handle:active { cursor: grabbing; }
        .draggable-row { transition: background-color 0.2s; }
        .draggable-row.dragging { opacity: 0.5; background-color: #e9ecef; }
        .btn { padding: 8px 15px; border: none; cursor: pointer; color: white; margin-bottom: 10px; }
        .btn-warning { background-color: #ff7700; }
        .btn-primary { background-color: #007bff; }
        .btn-danger { background-color: #dc3545; padding: 5px 10px; }
        .table th.text-right, .table td.text-right, .text-right { text-align: right !important; }
    </style>
</head>
<body>
    <div>
        <div style="background-color: #d4edda; border-left: 5px solid #28a745; padding: 12px; margin-bottom: 20px; color: #155724;">
            <strong>ℹ️ STATUS PAJAK (e-Faktur DJP):</strong> DRAFT / PRE-UPLOAD.<br>
            <span style="font-size: 0.9em; color: #1e7e34;">Invoice ini aman diamandemen karena e-Faktur belum diterbitkan/disetujui DJP.</span>
        </div>

        <div style="background-color: #ffe6e6; border-left: 5px solid #ff0000; padding: 15px; margin-bottom: 20px;">
            <strong style="color: #cc0000; font-size: 1.1em;">⚠️ PERHATIAN:</strong><br>
            <span style="color: #990000;">
                Transaksi ini, transaksi luar biasa.<br>
                Transaksi yang penuh resiko.<br>
                Karena itu tidak bisa diwakilkan kepada siapapun kecuali orang-orang kepercayaan perusahaan.
            </span>
        </div>
        
        <h3>Form Amandemen Invoice #<?php echo $invoice_no; ?> (Project #<?php echo $project_id; ?>)</h3>
        <p>Gunakan form ini untuk mengubah rincian tagihan secara item-per-item (Clean Invoice).</p>

        <?php if (!empty($project_info)): ?>
        <div style="background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px; padding: 15px; margin-bottom: 20px;">
            <h4 style="margin-top: 0; color: #0056b3;">Detail Project: <?php echo htmlspecialchars($project_info['project_nama']); ?></h4>
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="width: 25%; color: #6c757d;">Total Nilai RAB:</td>
                    <td style="width: 25%; font-weight: bold;">Rp <?php echo number_format($project_info['nilai_project'], 0, ',', '.'); ?></td>
                    <td style="width: 25%; color: #6c757d;">Total SPK Aktif:</td>
                    <td style="width: 25%; font-weight: bold;"><?php echo number_format($project_info['total_spk']); ?> SPK</td>
                </tr>
                <tr>
                    <td style="color: #6c757d;">Total Macam Item:</td>
                    <td style="font-weight: bold;"><?php echo number_format($project_info['total_items']); ?> Item</td>
                    <td style="color: #6c757d;">Rincian Item:</td>
                    <td style="font-weight: bold;">
                        <span style="color:#28a745;"><?php echo number_format($project_info['total_produk']); ?> Unit</span> / 
                        <span style="color:#ffc107;"><?php echo number_format($project_info['total_supplies']); ?> Jasa & Material</span>
                    </td>
                </tr>
            </table>
        </div>
        <?php endif; ?>
        
        <form id="frmAmandemen" action="<?php echo base_url(); ?>amandemen_invoice/FollowUp/validate_and_save/<?php echo $invoice_id; ?>" method="POST" target="result" onsubmit="showAmandemenLoader()">
            <div style="margin-bottom: 15px; background-color: #fff3cd; border: 1px solid #ffeba2; border-radius: 4px; padding: 10px;">
                <label style="display:block; font-weight: bold; color: #856404; margin-bottom: 5px;">
                    🔒 Catatan Amandemen (Khusus Internal Perusahaan):
                </label>
                <span style="font-size: 0.85em; color: #555; display: block; margin-bottom: 5px;">
                    Catatan khusus internal mengenai alasan perubahan amandemen ini. <b>TIDAK</b> akan dicetak di Invoice Client.
                </span>
                <textarea id="catatan_amandemen_input" name="catatan_amandemen" placeholder="Alasan perubahan amandemen untuk audit internal..." style="width:100%; height:50px; padding: 5px; border: 1px solid #ced4da; border-radius: 4px;"></textarea>
            </div>

            <h4>Rincian Produk (Jembatan Khusus SPK)</h4>
            <?php if ($project_id > 0): ?>
                <button type="button" class="btn btn-primary" onclick="openSpkPicker()">+ Pilih Produk dari SPK/RAB</button>
            <?php endif; ?>
            <button type="button" class="btn btn-success" onclick="addCustomRow()">+ Tambah Baris Kustom (Non-Stok)</button>
            <button type="button" class="btn btn-danger" onclick="removeAllRows()">🗑️ Kosongkan Tabel (Hapus Semua Baris)</button>

            <?php if (!empty($spk_info_list)): ?>
                <div style="margin-top: 12px; margin-bottom: 15px; background: #eef6ff; border: 1px solid #b6d4fe; border-radius: 6px; padding: 12px 16px;">
                    <h5 style="margin: 0 0 8px 0; color: #084298; font-weight: bold; font-size: 0.95em;">
                        📋 DAFTAR SPK & GUDANG WO TERLIBAT DALAM INVOICE INI (<?php echo count($spk_info_list); ?> SPK)
                    </h5>
                    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                        <?php foreach ($spk_info_list as $spk_idx => $spk): 
                            $no_spk = !empty($spk['no_spk']) ? $spk['no_spk'] : '-';
                            $g_wo_raw = !empty($spk['gudang_wo']) ? $spk['gudang_wo'] : '';
                            $gudang_wo_val = !empty($spk['gudang_wo_nama']) ? $spk['gudang_wo_nama'] : (!empty($g_wo_raw) ? $g_wo_raw : 'Gudang Virtual WO (Auto)');
                            $paket_nama = !empty($spk['produk_paket_nama']) ? $spk['produk_paket_nama'] : '-';
                            $spk_nama_val = !empty($spk['spk_nama']) ? $spk['spk_nama'] : '-';
                            $cur_locker_stocks = (!empty($g_wo_raw) && isset($locker_stock_list[$g_wo_raw])) ? $locker_stock_list[$g_wo_raw] : array();
                        ?>
                            <div class="spk-card-container" data-nospk="<?php echo htmlspecialchars($no_spk); ?>" style="display: none; background: #ffffff; border: 1px solid #9ec5fe; border-radius: 5px; padding: 8px 14px; font-size: 0.85em; flex: 1 1 320px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                                <span class="badge badge-primary" style="font-size:0.85em; margin-bottom:4px;">SPK #<?php echo ($spk_idx + 1); ?></span>
                                <div style="font-family: monospace; font-weight: bold; color: #084298; font-size:1.05em; margin-top:2px;">
                                    <?php echo htmlspecialchars($no_spk); ?>
                                </div>
                                <div style="color: #495057; margin-top: 4px;">
                                    <b>📦 Gudang Virtual WO:</b> <span class="badge badge-warning" style="font-size:0.9em; font-weight:bold; background-color:#fff3cd; color:#856404; border:1px solid #ffeba2;"><?php echo htmlspecialchars($gudang_wo_val); ?></span>
                                </div>
                                <div style="color: #495057; margin-top: 3px;">
                                    <b>📌 Nama Pekerjaan:</b> <?php echo htmlspecialchars($spk_nama_val); ?>
                                </div>
                                <div style="color: #495057; margin-top: 3px;">
                                    <b>📌 Paket/Fase:</b> <?php echo htmlspecialchars($paket_nama); ?>
                                </div>

                                <!-- INFORMASI STOK TERKUNCI DI STOCK LOCKER -->
                                <div style="margin-top: 8px; padding-top: 6px; border-top: 1px dashed #cbd5e0;">
                                    <b style="color: #2b6cb0;">🔒 Stok Bahan Terikat di Stock Locker:</b>
                                    <?php if (!empty($cur_locker_stocks)): ?>
                                        <ul style="margin: 4px 0 0 0; padding-left: 18px; color: #2d3748; font-size: 0.9em;">
                                            <?php foreach ($cur_locker_stocks as $stk): 
                                                $stk_pid = (int)(isset($stk['produk_id']) ? $stk['produk_id'] : (isset($stk['produk_dasar_id']) ? $stk['produk_dasar_id'] : 0));
                                                $stk_nama = htmlspecialchars(isset($stk['nama']) ? $stk['nama'] : (isset($stk['produk_nama']) ? $stk['produk_nama'] : 'Item'));
                                                $stk_state = htmlspecialchars(isset($stk['state']) ? $stk['state'] : 'hold');
                                                $stk_qty = (float)(isset($stk['jumlah']) ? $stk['jumlah'] : 0);
                                                $stk_satuan = htmlspecialchars(isset($stk['satuan']) ? $stk['satuan'] : '');
                                                $stk_tbl = htmlspecialchars($stk['_source_table']);
                                            ?>
                                                <li>
                                                    <b><?php echo $stk_nama; ?></b>
                                                    <?php if ($stk_pid > 0): ?>
                                                        <span class="badge badge-info" style="font-size:0.8em; font-family:monospace; background-color:#17a2b8;">PID: <?php echo $stk_pid; ?></span>
                                                    <?php endif; ?>
                                                    - Qty: <span class="badge badge-success"><?php echo $stk_qty; ?> <?php echo $stk_satuan; ?></span>
                                                    <span style="font-size:0.8em; color:#718096;">(State: <b><?php echo $stk_state; ?></b> | Table: <?php echo $stk_tbl; ?>)</span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <div style="font-style: italic; color: #718096; margin-top: 2px;">(Belum ada item aktif terkunci di stock_locker untuk gudang WO ini)</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div id="no_spk_selected_msg" style="display: none; color: #666; font-style: italic; padding: 10px; text-align: center;">Belum ada item produk dari SPK yang masuk ke dalam invoice ini.</div>
                </div>
            <?php endif; ?>

            <table class="table" id="tableItems">
                <thead>
                    <tr style="background-color:#f8f9fa;">
                        <th width="50">NO</th>
                        <th>DESCRIPTION (NAMA PENAGIHAN)</th>
                        <th width="110" class="text-right">LIMIT SPK</th>
                        <th width="125" class="text-right" style="color:#d9534f; background-color:#fff3cd; border-bottom:2px solid #d9534f;">📦 QTY RETUR (FISIK)</th>
                        <th width="110" class="text-right" style="color:#28a745;">QTY PENAGIHAN</th>
                        <th width="130" class="text-right">HARGA</th>
                        <th width="140" class="text-right">JUMLAH</th>
                        <th width="50" style="text-align:center;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $grandTotal = 0;
                    $rowNumber = 1;
                    if (!empty($items5_sum)): 
                        foreach ($items5_sum as $idx => $item): 
                            $qty = isset($item['jml']) ? (float)$item['jml'] : 0;
                            $harga = isset($item['harga']) ? (float)$item['harga'] : 0;
                            $subtotal = $qty * $harga;
                            $grandTotal += $subtotal;
                            $stok_aktif = isset($item['stok_aktif']) ? (float)$item['stok_aktif'] : 0;
                            $item_pid = (int)(isset($item['produk_dasar_id']) ? $item['produk_dasar_id'] : (isset($item['id']) ? $item['id'] : 0));

                            $nama_lower = isset($item['nama']) ? strtolower($item['nama']) : '';
                            $is_jasa = (strpos($nama_lower, 'jasa') !== false) || (strpos($nama_lower, 'biaya') !== false) || ($item_pid === 0);
                    ?>
                    <tr id="row_<?php echo $idx; ?>" class="draggable-row" data-no-spk="<?php echo htmlspecialchars(isset($item['no_spk']) ? $item['no_spk'] : ''); ?>" draggable="true">
                        <td class="row-no" style="display:flex; align-items:center;">
                            <span class="drag-handle" title="Tahan dan geser untuk memindahkan urutan">☰</span> 
                            <span class="nomor-urut"><?php echo $rowNumber++; ?></span>
                        </td>
                        <td>
                            <input type="hidden" name="items[<?php echo $idx; ?>][id]" value="<?php echo isset($item['id']) ? htmlspecialchars($item['id']) : ''; ?>">
                            <input type="hidden" name="items[<?php echo $idx; ?>][no_spk]" value="<?php echo htmlspecialchars(isset($item['no_spk']) ? $item['no_spk'] : ''); ?>">
                            <input type="hidden" name="items[<?php echo $idx; ?>][produk_dasar_id]" value="<?php echo isset($item['produk_dasar_id']) ? htmlspecialchars($item['produk_dasar_id']) : ''; ?>">
                            <input type="hidden" name="items[<?php echo $idx; ?>][satuan]" value="<?php echo isset($item['satuan']) ? htmlspecialchars($item['satuan']) : ''; ?>">
                            <input type="text" name="items[<?php echo $idx; ?>][nama]" class="inp-nama" data-orig-nama="<?php echo isset($item['nama']) ? htmlspecialchars($item['nama']) : ''; ?>" value="<?php echo isset($item['nama']) ? htmlspecialchars($item['nama']) : ''; ?>" style="width:100%; padding:4px; border:1px solid #ced4da; border-radius:3px;" onkeyup="checkNameChange(this)">
                            <div class="name-info" style="font-size:0.8em; color:#0056b3; margin-top:2px;"></div>
                            <?php if ($item_pid > 0): ?>
                                <div style="font-size:0.8em; color:#0056b3; font-family:monospace; font-weight:bold; margin-top:2px;">
                                    🆔 PID (ID Produk): <?php echo $item_pid; ?>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($item['jenis'])): ?>
                                <?php 
                                    $jcolor = '#17a2b8';
                                    if (strtolower($item['jenis']) == 'jasa' || strtolower($item['jenis']) == 'biaya') $jcolor = '#e83e8c';
                                    else if (strtolower($item['jenis']) == 'supplies') $jcolor = '#fd7e14';
                                ?>
                                <div style="margin-top: 3px;">
                                    <span class="badge" style="font-size:0.75em; background-color:<?php echo $jcolor; ?>; color:#fff;">
                                        <i class="fa fa-cube"></i> <?php echo strtoupper(htmlspecialchars($item['jenis'])); ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($item['keterangan'])): ?>
                                <div style="margin-top:6px; font-size:0.8em; color:#6c757d; background:#f8f9fa; padding:4px; border-radius:4px; border:1px solid #dee2e6; white-space:pre-wrap; line-height:1.2;"><b>Bahan Baku / Keterangan:</b><br><?php echo htmlspecialchars($item['keterangan']); ?></div>
                            <?php endif; ?>
                            <?php if (!empty($item['spk_nama'])): ?>
                                <div style="font-size:0.8em; color:#6c757d; font-style:italic; margin-top:2px;">
                                    📌 Peruntukan SPK: <b><?php echo htmlspecialchars($item['spk_nama']); ?></b>
                                </div>
                            <?php endif; ?>
                        </td>

                        <!-- LIMIT SPK & STOK AKTIF -->
                        <td class="text-right" style="font-size:0.88em;">
                            <div style="color:#cc0000; font-weight:bold;">Limit: <?php echo (float)$qty; ?></div>
                            <?php if ($stok_aktif > 0): ?>
                                <div style="margin-top:3px;">
                                    <span class="badge badge-success" style="font-size:0.8em; font-weight:bold; padding:2px 5px;" title="Stok Aktif di stock_locker yang dapat di-retur">
                                        📦 Stok: <?php echo $stok_aktif; ?>
                                    </span>
                                </div>
                            <?php else: ?>
                                <div style="margin-top:3px;">
                                    <span class="badge badge-secondary" style="font-size:0.8em; background-color:#6c757d; color:#ffffff; padding:2px 5px;" title="Stok Aktif 0 di stock_locker">
                                        ⚠️ Stok: 0
                                    </span>
                                </div>
                            <?php endif; ?>
                        </td>

                        <!-- KOLOM EKSPLISIT: QTY RETUR (FISIK) DITETAPKAN KONSEN OLEH USER -->
                        <td class="text-right" style="background-color:#fff8f8;">
                            <?php if ($is_jasa): ?>
                                <input type="text" name="items[<?php echo $idx; ?>][retur_qty]" class="inp-retur-qty" value="0" readonly disabled style="width:100%; text-align:right; background-color:#e9ecef; color:#6c757d; font-size:0.9em;" title="Jasa / Non-Stok tidak memiliki mutasi retur fisik">
                                <div style="font-size:0.75em; color:#6c757d; margin-top:2px; font-weight:bold;">📝 Non-Stok</div>
                            <?php else: ?>
                                <input type="text" name="items[<?php echo $idx; ?>][retur_qty]" class="inp-retur-qty" value="0" data-orig-qty="<?php echo (float)$qty; ?>" data-stok-aktif="<?php echo $stok_aktif; ?>" style="width:100%; text-align:right; border:1.5px solid #d9534f; color:#d9534f; font-weight:bold; padding:4px;" onchange="onReturQtyChange(this)" onkeyup="onReturQtyChange(this)">
                                <div class="retur-status-lbl" style="font-size:0.78em; color:#28a745; margin-top:2px; font-weight:bold;">✅ 0 Retur</div>
                            <?php endif; ?>
                        </td>

                        <!-- QTY PENAGIHAN NETT (SETELAH RETUR) -->
                        <td class="text-right">
                            <input type="text" name="items[<?php echo $idx; ?>][jml]" class="inp-qty" value="<?php echo (float)$qty; ?>" style="width:100%; text-align:right; font-weight:bold;" onchange="onQtyPenagihanChange(this)" onkeyup="onQtyPenagihanChange(this)">
                        </td>

                        <td class="text-right">
                            <input type="text" name="items[<?php echo $idx; ?>][harga]" class="inp-harga" value="<?php echo number_format($harga, 0, ',', '.'); ?>" style="width:100%; text-align:right; font-weight:bold;" onkeyup="formatCurrencyInput(this); calcSub(this);" onchange="formatCurrencyInput(this); calcSub(this);">
                        </td>
                        <td class="text-right subtotal-val"><?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                        <td style="text-align:center;">
                            <button type="button" class="btn btn-danger" onclick="removeRow(this)">X</button>
                        </td>
                    </tr>
                    <?php 
                        endforeach; 
                    endif; 
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="6" class="text-right">Total DPP</th>
                        <th class="text-right" id="dppVal"><?php echo number_format($grandTotal, 0, ',', '.'); ?></th>
                        <th></th>
                    </tr>
                    <tr>
                        <th colspan="6" class="text-right">PPN 11%</th>
                        <th class="text-right" id="ppnVal"><?php echo number_format($grandTotal * 0.11, 0, ',', '.'); ?></th>
                        <th></th>
                    </tr>
                    <tr>
                        <th colspan="6" class="text-right">GRAND TOTAL</th>
                        <th class="text-right" id="grandTotalVal" style="font-size:1.1em; color:#0056b3;"><?php echo number_format($grandTotal * 1.11, 0, ',', '.'); ?></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>

            <div style="margin-top: 20px; margin-bottom: 20px; background-color: #eef6ff; border: 1px solid #b8daff; border-radius: 4px; padding: 12px;">
                <label style="display:block; font-weight: bold; color: #004085; margin-bottom: 5px;">
                    📝 Notes Client (Dicetak di Invoice Penagihan):
                </label>
                <span style="font-size: 0.85em; color: #555; display: block; margin-bottom: 5px;">
                    Catatan ini akan tercetak pada lembar Invoice penagihan untuk Client (tampil di bagian N O T E S Invoice).
                </span>
                <?php 
                    $desc_for_textarea = isset($current_description) ? $current_description : '';
                    $desc_for_textarea = str_ireplace(array('<br>', '<br/>', '<br />'), "\n", $desc_for_textarea);
                ?>
                <textarea id="main_description_input" name="description" placeholder="Catatan untuk penagihan Client..." style="width:100%; height:60px; padding: 5px; border: 1px solid #ced4da; border-radius: 4px;"><?php echo htmlspecialchars($desc_for_textarea); ?></textarea>
            </div>
            <div id="boxJurnalPreview" style="margin-top: 20px; margin-bottom: 20px; background-color: #f8f9fa; border: 1px solid #ced4da; border-radius: 5px; padding: 15px;">
                <h4 style="margin-top: 0; color: #0056b3;">📊 Live Preview: Jurnal Perbaikan Akuntansi (Clean Replaced Journal)</h4>
                <p style="font-size: 0.85em; color: #6c757d; margin-bottom: 10px;">
                    Simulasi pembukuan jurnal perbaikan akuntansi yang akan dituliskan secara bersih oleh sistem sesuai nilai UI perbaikan saat amandemen disimpan:
                    <?php if(!empty($termin_nomer)): ?>
                    <br><span style="color:#0056b3; font-weight:bold;">📌 Target Transaksi: Memperbarui Jurnal Penagihan <?php echo $termin_nomer; ?></span>
                    <?php endif; ?>
                </p>
                <div id="jurnalStatusInfo" style="margin-bottom: 10px; font-weight: bold; color: #17a2b8;">
                    ℹ️ Nominal tagihan belum berubah.
                </div>
                <table class="table" id="tableJurnalPreview" style="display: none; background-color: #ffffff;">
                    <thead>
                        <tr style="background-color: #e9ecef;">
                            <th width="120">Kode Akun</th>
                            <th>Nama Rekening Akuntansi</th>
                            <th width="150" class="text-right">DEBIT (Rp)</th>
                            <th width="150" class="text-right">KREDIT (Rp)</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyJurnalPreview">
                    </tbody>
                    <tfoot>
                        <tr style="font-weight: bold; background-color: #f1f3f5;">
                            <td colspan="2" class="text-right">TOTAL SEIMBANG (BALANCED):</td>
                            <td class="text-right" id="jurnalTotalDebit" style="color: #28a745;">Rp 0</td>
                            <td class="text-right" id="jurnalTotalKredit" style="color: #28a745;">Rp 0</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- RINGKASAN RETUR FISIK DARI KOLOM TABEL -->
            <div id="boxActiveReturSummary" style="margin-top: 15px; margin-bottom: 20px; background-color: #f8d7da; border: 1.5px solid #f5c6cb; border-radius: 5px; padding: 15px; display: none;">
                <strong style="color: #721c24; font-size: 1.05em;">📦 Ringkasan Mutasi Retur Fisik yang Ditetapkan (Akan Diproses ke Modul Distribusi saat Disimpan):</strong>
                <p style="margin-top: 4px; margin-bottom: 8px; font-size: 0.85em; color: #721c24;">
                    Item di bawah ini memiliki kuantitas retur fisik (> 0) dan akan langsung dipotong stoknya dari gudang WO serta diterbitkan dokumen retur resminya ke modul distribusi:
                </p>
                <ul id="listActiveReturSummary" style="margin-bottom: 0; padding-left: 20px; color: #721c24; font-weight: bold;">
                </ul>
            </div>

            <div id="boxDeletedItems" style="margin-top: 15px; margin-bottom: 20px; background-color: #fff3cd; border: 1px solid #ffeba2; border-radius: 5px; padding: 15px; display: none;">
                <strong style="color: #856404; font-size: 1.05em;">🔄 Rincian Baris yang Dihapus dari Tampilan:</strong>
                <p style="margin-top: 4px; margin-bottom: 8px; font-size: 0.85em; color: #856404;">
                    Daftar baris yang dihapus dari formulir penagihan:
                </p>
                <ul id="listDeletedItems" style="margin-bottom: 0; padding-left: 20px; color: #856404;">
                </ul>
            </div>

            <button type="submit" class="btn btn-warning" style="font-size:16px;">Simpan Amandemen</button>
        </form>
    </div>

    <!-- Tempat hasil submit (sesuai arsitektur iframe #result ERP) -->
    <div style="margin-top:20px;">
        <iframe id="result" name="result" style="width:100%; height:80px; border:1px solid #ccc; background-color:#f9f9f9; display:none;"></iframe>
    </div>

    <!-- HISTORY SECTION -->
    <div style="margin-top:30px; border-top:2px solid #ccc; padding-top:20px;">
        <h3>📜 Riwayat Aktivitas Invoice #<?php echo $invoice_no; ?></h3>
        <p style="color:#666;">Data di bawah ini merupakan catatan sistem mengenai Amandemen, Retur, dan Jurnal Akuntansi yang pernah terjadi pada tagihan ini.</p>

        <div style="margin-bottom: 20px;">
            <h4 style="color:#856404;">🔄 Riwayat Amandemen</h4>
            <?php if (!empty($history_amandemen)): ?>
                <table class="table" style="font-size:0.85em;">
                    <thead>
                        <tr style="background-color:#fff3cd; color:#856404;">
                            <th>NO. AMANDEMEN</th>
                            <th>WAKTU</th>
                            <th>OLEH</th>
                            <th>KETERANGAN / ALASAN</th>
                            <th class="text-right">NILAI SEBELUM</th>
                            <th class="text-right">NILAI SESUDAH</th>
                            <th class="text-right">NILAI DELTA</th>
                            <th style="text-align:center;">AKSI PEMULIHAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history_amandemen as $row): ?>
                            <tr>
                                <td><b><?php echo htmlspecialchars($row['No. Amandemen']); ?></b></td>
                                <td><?php echo htmlspecialchars($row['Waktu']); ?></td>
                                <td><?php echo htmlspecialchars($row['Oleh']); ?></td>
                                <td><?php echo htmlspecialchars($row['Keterangan']); ?></td>
                                <td class="text-right"><?php echo htmlspecialchars($row['Nilai Sebelum']); ?></td>
                                <td class="text-right"><?php echo htmlspecialchars($row['Nilai Sesudah']); ?></td>
                                <td class="text-right"><b><?php echo htmlspecialchars($row['Nilai Delta']); ?></b></td>
                                <td style="text-align:center;">
                                    <a href="<?php echo base_url(); ?>amandemen_invoice/FollowUp/restore_history/<?php echo $invoice_id; ?>/<?php echo $row['history_id']; ?>" class="btn btn-sm btn-info" style="font-size:0.8em; padding:3px 8px;" onclick="return confirm('Apakah Anda yakin ingin memulihkan (Rollback) tagihan ke versi <?php echo htmlspecialchars($row['No. Amandemen']); ?> ini?');" target="result">🔄 Pulihkan Versi Ini</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color:#888;">Belum ada riwayat amandemen.</p>
            <?php endif; ?>
        </div>

        <div style="margin-bottom: 20px;">
            <h4 style="color:#d9534f;">📦 Riwayat Pembuatan Retur (Otomatis)</h4>
            <?php if (!empty($history_return)): ?>
                <table class="table" style="font-size:0.85em;">
                    <thead><tr>
                        <th>ID</th><th>Nomer</th><th>Jenis</th><th>Waktu</th><th>Keterangan</th>
                    </tr></thead>
                    <tbody>
                        <?php foreach ($history_return as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars(isset($row['id']) ? $row['id'] : '-'); ?></td>
                                <td><?php echo htmlspecialchars(isset($row['nomer']) ? $row['nomer'] : '-'); ?></td>
                                <td><?php echo htmlspecialchars(isset($row['transaksi_jenis']) ? $row['transaksi_jenis'] : (isset($row['jenisTr']) ? $row['jenisTr'] : '-')); ?></td>
                                <td><?php echo htmlspecialchars(isset($row['dtime']) ? $row['dtime'] : (isset($row['waktu_dibuat']) ? $row['waktu_dibuat'] : '-')); ?></td>
                                <td><?php echo htmlspecialchars(isset($row['keterangan']) ? $row['keterangan'] : '-'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color:#888;">Belum ada riwayat retur untuk invoice ini.</p>
            <?php endif; ?>
        </div>

        <div style="margin-bottom: 20px;">
            <h4 style="color:#0056b3;">📓 Jurnal Aktif di Database Saat Ini (Sebelum Amandemen)</h4>
            <span style="font-size: 0.85em; color: #6c757d; display: block; margin-bottom: 10px;">
                Tabel di bawah menampilkan ayat jurnal yang sedang aktif di database sebelum amandemen disimpan. Saat tombol <b>"Simpan Amandemen"</b> ditekan, jurnal lama di bawah ini akan dimatikan (trash = 1) dan digantikan 100% oleh <b>Jurnal Perbaikan Baru</b> sesuai simulasi Live Preview.
            </span>
            <?php if (!empty($history_jurnal)): ?>
                <table class="table table-bordered table-striped compact" id="tableRiwayatJurnal" style="font-size:0.85em; width:100%;">
                    <thead>
                        <tr style="background-color:#e9ecef;">
                            <?php 
                            $headers = array_keys($history_jurnal[0]);
                            foreach ($headers as $k): ?>
                                <th><?php echo htmlspecialchars($k); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $tot_debet = 0;
                        $tot_kredit = 0;
                        foreach ($history_jurnal as $row): 
                            $tot_debet += isset($row['debet']) ? (float)$row['debet'] : 0;
                            $tot_kredit += isset($row['kredit']) ? (float)$row['kredit'] : 0;
                        ?>
                            <tr>
                                <?php foreach ($row as $k => $v): 
                                    if ($k == 'debet' || $k == 'kredit') {
                                        echo "<td class='text-right'>" . number_format((float)$v, 0, ',', '.') . "</td>";
                                    } else {
                                        echo "<td>" . htmlspecialchars($v) . "</td>";
                                    }
                                endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color:#f8f9fa; font-weight:bold; font-size:1.1em;">
                            <?php 
                            $idx_debet = array_search('debet', $headers);
                            $idx_kredit = array_search('kredit', $headers);
                            $min_idx = min($idx_debet, $idx_kredit);
                            $max_idx = max($idx_debet, $idx_kredit);
                            $sisa_colspan = count($headers) - $max_idx - 1;
                            ?>
                            <td colspan="<?php echo $min_idx; ?>" class="text-right">TOTAL SEIMBANG (BALANCED):</td>
                            <?php if ($idx_debet < $idx_kredit): ?>
                                <td class="text-right" style="color:#28a745;">Rp <?php echo number_format($tot_debet, 0, ',', '.'); ?></td>
                                <td class="text-right" style="color:#0056b3;">Rp <?php echo number_format($tot_kredit, 0, ',', '.'); ?></td>
                                <?php if ($sisa_colspan > 0): ?>
                                    <td colspan="<?php echo $sisa_colspan; ?>"></td>
                                <?php endif; ?>
                            <?php else: ?>
                                <td class="text-right" style="color:#0056b3;">Rp <?php echo number_format($tot_kredit, 0, ',', '.'); ?></td>
                                <td class="text-right" style="color:#28a745;">Rp <?php echo number_format($tot_debet, 0, ',', '.'); ?></td>
                                <?php if ($sisa_colspan > 0): ?>
                                    <td colspan="<?php echo $sisa_colspan; ?>"></td>
                                <?php endif; ?>
                            <?php endif; ?>
                        </tr>
                    </tfoot>
                </table>
            <?php else: ?>
                <p style="color:#888;">Belum ada riwayat jurnal akuntansi.</p>
            <?php endif; ?>
        </div>

        <!-- SACLAR TOGGLE JURNAL PENYESUAIAN MANUAL -->
        <div style="margin-bottom: 20px; background-color: #f8f9fa; border: 1px solid #ced4da; border-radius: 4px; padding: 15px;">
            <div style="display:flex; align-items:center;">
                <input type="checkbox" id="toggle_manual_jurnal" name="enable_manual_jurnal" value="1" onchange="toggleManualJurnal(this)" style="width:18px; height:18px; cursor:pointer;">
                <label for="toggle_manual_jurnal" style="margin-left:10px; margin-bottom:0; font-size:1.05em; font-weight:bold; color:#0056b3; cursor:pointer;">
                    ⚙️ Aktifkan Pengisian Jurnal Penyesuaian Manual (Custom COA Override)
                </label>
            </div>
            <span style="font-size:0.85em; color:#6c757d; display:block; margin-top:4px; margin-left:28px;">
                Centang opsi ini jika Anda ingin meng-override/menyesuaikan sendiri susunan Rekening COA (Debet/Kredit) untuk transaksi amandemen ini.
            </span>

            <div id="box_manual_jurnal_container" style="display:none; margin-top:15px; border-top:1px dashed #ccc; padding-top:15px;">
                <h5 style="color:#0056b3; font-weight:bold; margin-bottom:10px;">📐 Builder Jurnal Penyesuaian Manual (Harus Seimbang/Balance)</h5>
                
                <table class="table table-bordered table-sm" id="tblManualJurnal" style="font-size:0.9em; background:#ffffff;">
                    <thead>
                        <tr style="background:#e9ecef; color:#495057;">
                            <th width="150">KODE COA</th>
                            <th>NAMA REKENING COA</th>
                            <th width="180" class="text-right">DEBET (RP)</th>
                            <th width="180" class="text-right">KREDIT (RP)</th>
                            <th width="50" class="text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyManualJurnal">
                        <!-- Default Row 1: Piutang Usaha -->
                        <tr>
                            <td>
                                <input type="text" name="jurnal_custom[0][rekening]" class="form-control form-control-sm inp-coa-code" list="dl_coa_list" value="749.1.171.50" oninput="onCoaSelected(this)" required>
                            </td>
                            <td>
                                <input type="text" name="jurnal_custom[0][rekening_nama]" class="form-control form-control-sm inp-coa-nama" value="Piutang Usaha (A/R)" readonly>
                            </td>
                            <td>
                                <input type="number" name="jurnal_custom[0][debet]" class="form-control form-control-sm text-right inp-debet" value="0" onkeyup="calcManualJurnalBalance()" onchange="calcManualJurnalBalance()">
                            </td>
                            <td>
                                <input type="number" name="jurnal_custom[0][kredit]" class="form-control form-control-sm text-right inp-kredit" value="0" onkeyup="calcManualJurnalBalance()" onchange="calcManualJurnalBalance()">
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-danger" onclick="removeManualJurnalRow(this)">X</button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr style="background:#f8f9fa; font-weight:bold;">
                            <td colspan="2" class="text-right">TOTAL JURNAL MANUAL:</td>
                            <td class="text-right" id="lblTotDebetManual" style="color:#0056b3;">Rp 0</td>
                            <td class="text-right" id="lblTotKreditManual" style="color:#0056b3;">Rp 0</td>
                            <td></td>
                        </tr>
                        <tr style="font-weight:bold;">
                            <td colspan="5" class="text-center" id="lblBalanceStatus" style="font-size:1.1em; color:#dc3545;">
                                ❌ KESEIMBANGAN: DUA SISI HARUS SEIMBANG (DEBET == KREDIT)
                            </td>
                        </tr>
                    </tfoot>
                </table>
                <button type="button" class="btn btn-sm btn-success" onclick="addManualJurnalRow()"><i class="fa fa-plus"></i> + Tambah Baris Jurnal Manual</button>
            </div>
        </div>
    </div>

    <script>
    var project_id = <?php echo (int)$project_id; ?>;
    var invoice_id = <?php echo (int)$invoice_id; ?>;
    var rowIdx = <?php echo count($items5_sum); ?>;
    var customRowCounter = 1;

    var origDPP = <?php echo (float)$orig_dpp; ?>;
    var origPPN = <?php echo (float)$orig_ppn; ?>;
    var origGrandTotal = <?php echo (float)$orig_gt; ?>;
    
    // Bawa data jurnal asli ke JS untuk simulasi Live Preview yang akurat!
    var originalJurnal = <?php echo json_encode(isset($history_jurnal) ? $history_jurnal : array()); ?>;

    function openSpkPicker() {
        if (project_id <= 0) {
            alert('Project ID tidak valid, tidak dapat membuka SPK.');
            return;
        }
        var url = '<?php echo base_url(); ?>amandemen_invoice/Selector/spk_items/' + project_id + '/' + invoice_id;
        window.open(url, "PickerSPK", "width=800,height=600,scrollbars=yes");
    }

    function loadSpkItems(no_spk, project_id, current_invoice_id) {
        var base_url = "<?php echo base_url(); ?>";
        var formData = new FormData();
        formData.append('no_spk', no_spk);

        fetch(base_url + "amandemen_invoice/Selector/get_spk_items_ajax/" + project_id + "/" + current_invoice_id, {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(text => {
            var data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                alert("Gagal memproses respon dari server:\n" + text.substring(0, 300));
                return;
            }
            if (data.status && data.items) {
                // Hapus baris lama yang menginduk ke no_spk ini (jika pernah di-load sebelumnya)
                if (no_spk) {
                    var oldTrs = document.querySelectorAll('#tableItems tbody tr');
                    oldTrs.forEach(function(tr) {
                        if (tr.getAttribute('data-no-spk') === no_spk) {
                            tr.parentNode.removeChild(tr);
                        }
                    });
                }

                data.items.forEach(function(itemData) {
                    var tbody = document.querySelector('#tableItems tbody');
                    var tr = document.createElement('tr');
                    tr.className = 'draggable-row';
                    tr.setAttribute('draggable', 'true');
                    tr.setAttribute('data-jenis', itemData.jenis);
                    tr.setAttribute('data-no-spk', no_spk);
                    var maxVal = (itemData.sisa_qty > 0) ? itemData.sisa_qty : '-';
                    
                    var subtotal = parseFloat(itemData.sisa_qty) * parseFloat(itemData.harga);
                    var pid = itemData.produk_dasar_id || itemData.id || 0;
                    var stokAktif = parseFloat(itemData.stok_aktif || 0);

                    var isJasa = (itemData.jenis === 'biaya' || itemData.nama.toLowerCase().includes('jasa') || itemData.nama.toLowerCase().includes('bongkar') || itemData.nama.toLowerCase().includes('penarikan') || itemData.nama.toLowerCase().includes('instalasi') || itemData.nama.toLowerCase().includes('biaya'));

                    var stokBadge = '';
                    if (isJasa) {
                        stokBadge = `<div style="margin-top:3px;"><span class="badge badge-warning" style="font-size:0.8em; background-color:#ffc107; color:#212529; padding:2px 5px;" title="Item Jasa / Biaya">⚠️ Stok: 0</span></div>`;
                    } else if (stokAktif > 0) {
                        stokBadge = `<div style="margin-top:3px;"><span class="badge badge-success" style="font-size:0.8em; font-weight:bold; padding:2px 5px;" title="Stok Aktif di stock_locker yang dapat di-retur">📦 Stok: ${stokAktif}</span></div>`;
                    } else {
                        stokBadge = `<div style="margin-top:3px;"><span class="badge badge-secondary" style="font-size:0.8em; background-color:#6c757d; color:#ffffff; padding:2px 5px;" title="Stok Aktif 0 di stock_locker">⚠️ Stok: 0</span></div>`;
                    }

                    var returColHtml = '';
                    if (isJasa) {
                        returColHtml = `
                            <input type="number" step="0.01" name="items[${rowIdx}][retur_qty]" class="inp-retur-qty" value="0" readonly disabled style="width:100%; text-align:right; background-color:#e9ecef; color:#6c757d; font-size:0.9em;" title="Jasa / Non-Stok tidak memiliki mutasi retur fisik">
                            <div style="font-size:0.75em; color:#6c757d; margin-top:2px; font-weight:bold;">📝 Non-Stok</div>
                        `;
                    } else {
                        returColHtml = `
                            <input type="number" step="0.01" min="0" max="${parseFloat(itemData.sisa_qty)}" name="items[${rowIdx}][retur_qty]" class="inp-retur-qty" value="0" data-orig-qty="${parseFloat(itemData.sisa_qty)}" data-stok-aktif="${stokAktif}" style="width:100%; text-align:right; border:1.5px solid #d9534f; color:#d9534f; font-weight:bold; padding:4px;" onchange="onReturQtyChange(this)" onkeyup="onReturQtyChange(this)">
                            <div class="retur-status-lbl" style="font-size:0.78em; color:#28a745; margin-top:2px; font-weight:bold;">✅ 0 Retur</div>
                        `;
                    }

                    var html = `
                        <td class="row-no" style="display:flex; align-items:center;">
                            <span class="drag-handle" title="Tahan dan geser untuk memindahkan urutan">☰</span> 
                            <span class="nomor-urut"></span>
                        </td>
                        <td>
                            <input type="hidden" name="items[${rowIdx}][id]" value="${itemData.id}">
                            <input type="hidden" name="items[${rowIdx}][no_spk]" value="${no_spk}">
                            <input type="hidden" name="items[${rowIdx}][produk_dasar_id]" value="${itemData.produk_dasar_id}">
                            <input type="hidden" name="items[${rowIdx}][satuan]" value="${itemData.satuan}">
                            <input type="text" name="items[${rowIdx}][nama]" class="inp-nama" data-orig-nama="${itemData.nama}" value="${itemData.nama}" style="width:100%; padding:4px; border:1px solid #ced4da; border-radius:3px;" onkeyup="checkNameChange(this)">
                            <div class="name-info" style="font-size:0.8em; color:#0056b3; margin-top:2px;"></div>
                            <div style="font-size:0.8em; color:#0056b3; font-family:monospace; font-weight:bold; margin-top:2px;">🆔 PID (ID Produk): ${pid}</div>
                        </td>
                        <td class="text-right" style="font-size:0.88em;">
                            <div style="color:#cc0000; font-weight:bold;">Limit: ${maxVal}</div>
                            ${stokBadge}
                        </td>
                        <td class="text-right" style="background-color:#fff8f8;">
                            ${returColHtml}
                        </td>
                        <td class="text-right">
                            <input type="text" name="items[${rowIdx}][jml]" class="inp-qty" value="${parseFloat(itemData.sisa_qty)}" style="width:100%; text-align:right; font-weight:bold;" onchange="onQtyPenagihanChange(this)" onkeyup="onQtyPenagihanChange(this)">
                        </td>
                        <td class="text-right">
                            <input type="text" class="inp-harga" name="items[${rowIdx}][harga]" value="${Math.round(parseFloat(itemData.harga)).toLocaleString('id-ID')}" onkeyup="formatCurrencyInput(this); calcSub(this);" onchange="formatCurrencyInput(this); calcSub(this);" style="width:100%; text-align:right; font-weight:bold;">
                        </td>
                        <td class="text-right subtotal-val">${subtotal.toLocaleString('id-ID')}</td>
                        <td style="text-align:center;">
                            <button type="button" class="btn btn-danger" onclick="removeRow(this)">X</button>
                        </td>
                    `;
                    tr.innerHTML = html;
                    tbody.appendChild(tr);
                    rowIdx++;
                });
                
                recalcGrandTotal();
                updateRowNumbers();
            } else {
                alert("Gagal memuat item dari SPK ini: " + (data.message || "Unknown error"));
            }
        })
        .catch(error => {
            alert("Gagal menghubungi server: " + error);
            console.error(error);
        });
    }

    function addCustomRow() {
        var tbody = document.querySelector('#tableItems tbody');
        var tr = document.createElement('tr');
        tr.className = 'draggable-row';
        tr.setAttribute('data-jenis', 'biaya');
        tr.setAttribute('draggable', 'true');
        var customId = 'custom_' + customRowCounter;
        customRowCounter++;
        
        var idx = document.querySelectorAll('#tableItems tbody tr').length + 1;

        tr.innerHTML = `
            <td class="row-no" style="display:flex; align-items:center;">
                <span class="drag-handle" title="Tahan dan geser untuk memindahkan urutan">☰</span> 
                <span class="nomor-urut">${idx}</span>
            </td>
            <td>
                <input type="text" class="form-control inp-nama" name="items[${customId}][nama]" value="Jasa / Tambahan Kustom" placeholder="Nama Jasa/Produk" style="width:100%; padding:4px; border:1px solid #ced4da; border-radius:3px;">
                <input type="hidden" name="items[${customId}][produk_dasar_id]" value="0">
                <input type="hidden" name="items[${customId}][id]" value="0">
                <div style="font-size:0.8em; color:#6c757d; font-style:italic; margin-top:2px;">📝 Baris Kustom (Non-Stok)</div>
            </td>
            <td class="text-right" style="font-size:0.88em; color:#6c757d;">-</td>
            <td class="text-right" style="background-color:#fff8f8;">
                <input type="text" name="items[${customId}][retur_qty]" class="inp-retur-qty" value="0" readonly disabled style="width:100%; text-align:right; background-color:#e9ecef; color:#6c757d;">
                <div style="font-size:0.75em; color:#6c757d; margin-top:2px;">📝 Non-Stok</div>
            </td>
            <td class="text-right">
                <input type="text" class="inp-qty" name="items[${customId}][jml]" value="1" style="width:100%; text-align:right; font-weight:bold;" onkeyup="onQtyPenagihanChange(this)" onchange="onQtyPenagihanChange(this)">
            </td>
            <td class="text-right">
                <input type="text" class="inp-harga" name="items[${customId}][harga]" value="0" style="width:100%; text-align:right; font-weight:bold;" onkeyup="formatCurrencyInput(this); calcSub(this);" onchange="formatCurrencyInput(this); calcSub(this);">
            </td>
            <td class="text-right subtotal-val">0</td>
            <td style="text-align:center;">
                <button type="button" class="btn btn-danger" onclick="removeRow(this)">X</button>
            </td>
        `;
        tbody.appendChild(tr);
        recalcGrandTotal();
        updateRowNumbers();
    }

    function parseNumber(val) {
        if (val === null || val === undefined || val === '') return 0;
        if (typeof val === 'number') return val;
        var clean = val.toString().replace(/\./g, '').replace(/,/g, '.');
        var num = parseFloat(clean);
        return isNaN(num) ? 0 : num;
    }

    function formatCurrencyInput(input) {
        if (!input) return;
        var cursorPosition = input.selectionStart;
        var originalLength = input.value.length;
        var rawVal = input.value.replace(/[^0-9]/g, '');
        if (rawVal === '') {
            input.value = '0';
            return;
        }
        var num = parseInt(rawVal, 10);
        var formatted = num.toLocaleString('id-ID');
        input.value = formatted;
        var newLength = formatted.length;
        if (cursorPosition !== null) {
            var newPos = cursorPosition + (newLength - originalLength);
            if (newPos < 0) newPos = 0;
            input.setSelectionRange(newPos, newPos);
        }
    }

    function calcSub(input) {
        if (!input) return;
        var tr = input.closest('tr');
        if (!tr) return;
        var qtyInput = tr.querySelector('.inp-qty');
        var hargaInput = tr.querySelector('.inp-harga');
        var subtotalCell = tr.querySelector('.subtotal-val');
        
        var qty = parseNumber(qtyInput ? qtyInput.value : 0);
        var harga = parseNumber(hargaInput ? hargaInput.value : 0);
        var subtotal = qty * harga;
        
        if (subtotalCell) {
            subtotalCell.innerText = subtotal.toLocaleString('id-ID');
        }
        
        recalcGrandTotal();
    }

    function onReturQtyChange(returInput) {
        var tr = returInput.closest('tr');
        var qtyInput = tr.querySelector('.inp-qty');
        var maxQty = parseNumber(returInput.getAttribute('data-orig-qty'));
        var stokAktif = parseNumber(returInput.getAttribute('data-stok-aktif'));
        var returVal = parseNumber(returInput.value);

        if (returVal < 0) {
            returVal = 0;
            returInput.value = 0;
        }

        if (returVal > maxQty) {
            alert('Jumlah retur tidak boleh melebihi Limit SPK (' + maxQty + ').');
            returVal = maxQty;
            returInput.value = returVal;
        }

        if (stokAktif <= 0 && returVal > 0) {
            alert('PERINGATAN LOGISTIK:\nStok Aktif di stock_locker bernilai 0.\nTidak ada persediaan fisik tersisa yang dapat di-retur ke gudang.');
            returVal = 0;
            returInput.value = 0;
        } else if (returVal > stokAktif && stokAktif > 0) {
            alert('Jumlah retur melebihi Stok Aktif tersisa (' + stokAktif + '). Diturunkan ke ' + stokAktif + '.');
            returVal = stokAktif;
            returInput.value = returVal;
        }

        var netQty = maxQty - returVal;
        if (netQty < 0) netQty = 0;
        if (qtyInput) qtyInput.value = netQty;

        var lbl = tr.querySelector('.retur-status-lbl');
        if (lbl) {
            if (returVal > 0) {
                lbl.innerHTML = '🔄 User Menetapkan ' + returVal + ' Retur';
                lbl.style.color = '#d9534f';
            } else {
                lbl.innerHTML = '✅ 0 Retur';
                lbl.style.color = '#28a745';
            }
        }

        if (qtyInput) calcSub(qtyInput);
    }

    function onQtyPenagihanChange(qtyInput) {
        var tr = qtyInput.closest('tr');
        var returInput = tr.querySelector('.inp-retur-qty');
        if (!returInput || returInput.disabled) {
            calcSub(qtyInput);
            return;
        }
        var maxQty = parseNumber(returInput.getAttribute('data-orig-qty'));
        var currQty = parseNumber(qtyInput.value);

        if (currQty < 0) {
            currQty = 0;
            qtyInput.value = 0;
        }

        if (currQty > maxQty) {
            alert('Qty Penagihan tidak boleh melebihi Limit SPK (' + maxQty + ').');
            currQty = maxQty;
            qtyInput.value = currQty;
        }

        var returVal = maxQty - currQty;
        if (returVal < 0) returVal = 0;
        returInput.value = returVal;
        
        onReturQtyChange(returInput);
    }

    function removeRow(btn) {
        var tr = btn.closest('tr');
        if (!tr) return;
        var nameInput = tr.querySelector('.inp-nama');
        var returInput = tr.querySelector('.inp-retur-qty');
        var pdInput = tr.querySelector('input[name*="[produk_dasar_id]"]');
        
        var nama = nameInput ? nameInput.value : 'Item Produk';
        var maxQty = returInput ? (parseNumber(returInput.getAttribute('data-orig-qty')) || 0) : 0;
        var stokAktif = returInput ? (parseNumber(returInput.getAttribute('data-stok-aktif')) || 0) : 0;
        var pdId = pdInput ? pdInput.value : '';

        var namaLower = nama.toLowerCase();
        var isJasa = (namaLower.indexOf('jasa') !== -1) || 
                     (namaLower.indexOf('biaya') !== -1) || 
                     (namaLower.indexOf('penarikan') !== -1) || 
                     (namaLower.indexOf('bongkar') !== -1) ||
                     (namaLower.indexOf('instalasi') !== -1) ||
                     (namaLower.indexOf('588st') !== -1) ||
                     (namaLower.indexOf('749') !== -1) ||
                     (namaLower.indexOf('termin') !== -1) ||
                     (namaLower.indexOf('progres') !== -1) ||
                     (tr.getAttribute('data-jenis') === 'biaya') ||
                     (stokAktif <= 0);

        var isDuplicate = false;
        if (pdId !== '' && pdId !== '0') {
            var allPdInputs = document.querySelectorAll('#tableItems tbody input[name*="[produk_dasar_id]"]');
            allPdInputs.forEach(function(input) {
                if (input !== pdInput && input.value === pdId) {
                    isDuplicate = true;
                }
            });
        }

        var list = document.getElementById('listDeletedItems');
        var box = document.getElementById('boxDeletedItems');
        if (list && box) {
            var li = document.createElement('li');
            li.style.marginBottom = "6px";
            if (isDuplicate) {
                li.innerHTML = "📌 <strong>" + escapeHtml(nama) + "</strong> ➔ <span style='color:#0056b3; font-weight:bold;'>Baris Ganda Dihapus (Koreksi Tampilan)</span>";
            } else if (namaLower.indexOf('588st') !== -1 || namaLower.indexOf('749') !== -1) {
                li.innerHTML = "📌 <strong>" + escapeHtml(nama) + "</strong> ➔ <span style='color:#6c757d; font-weight:bold;'>Dihapus (Header Realisasi SPK digantikan rincian item SPK baru)</span>";
            } else if (isJasa || stokAktif <= 0) {
                li.innerHTML = "📌 <strong>" + escapeHtml(nama) + "</strong> ➔ <span style='color:#6c757d; font-weight:bold;'>Dihapus (Item Non-Stok / Jasa, Tidak Ada Mutasi Fisik)</span>";
            } else {
                li.innerHTML = "📌 <strong>" + escapeHtml(nama) + "</strong> (Qty Awal: <strong>" + maxQty + " Unit</strong> | Stok: <strong>" + stokAktif + "</strong>) ➔ <span style='color:#d9534f; font-weight:bold;'>100% Menjadi Retur Fisik ke Gudang</span>";
            }
            list.appendChild(li);
            box.style.display = 'block';
        }

        tr.parentNode.removeChild(tr);
        recalcGrandTotal();
        updateRowNumbers();
    }

    function removeAllRows() {
        if (!confirm("Apakah Anda yakin ingin MENGHAPUS SEMUA BARIS di tabel penagihan ini?\nTabel akan dikosongkan agar Anda dapat memuat data segar dari SPK.")) {
            return;
        }
        var tbody = document.querySelector('#tableItems tbody');
        if (tbody) {
            tbody.innerHTML = '';
        }
        recalcGrandTotal();
        updateRowNumbers();
    }

    function escapeHtml(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    function updateRowNumbers() {
        var rows = document.querySelectorAll('#tableItems tbody tr');
        rows.forEach(function(row, index) {
            var numSpan = row.querySelector('.nomor-urut');
            if (numSpan) {
                numSpan.innerText = index + 1;
            }
        });
    }
    function updateSpkCardVisibility() {
        var activeSpks = {};
        document.querySelectorAll('#tableItems tbody tr.draggable-row').forEach(function(tr) {
            var val = tr.getAttribute('data-no-spk');
            if (val && val.trim() !== "") {
                activeSpks[val.trim()] = true;
            }
        });
        
        var anyVisible = false;
        document.querySelectorAll('.spk-card-container').forEach(function(card) {
            var noSpk = card.getAttribute('data-nospk');
            if (activeSpks[noSpk]) {
                card.style.display = 'block';
                anyVisible = true;
            } else {
                card.style.display = 'none';
            }
        });
        
        var noSpkMsg = document.getElementById('no_spk_selected_msg');
        if (noSpkMsg) {
            noSpkMsg.style.display = anyVisible ? 'none' : 'block';
        }
    }

    function recalcGrandTotal() {
        var sum = 0;
        var trs = document.querySelectorAll('#tableItems tbody tr');
        trs.forEach(function(tr) {
            var qty = parseNumber(tr.querySelector('.inp-qty') ? tr.querySelector('.inp-qty').value : 0);
            var harga = parseNumber(tr.querySelector('.inp-harga') ? tr.querySelector('.inp-harga').value : 0);
            sum += (qty * harga);
        });
        
        var ppn = Math.round(sum * 0.11);
        var grandTotal = sum + ppn;

        var dppElem = document.getElementById('dppVal');
        var ppnElem = document.getElementById('ppnVal');
        var gtElem = document.getElementById('grandTotalVal');

        if (dppElem) dppElem.innerText = sum.toLocaleString('id-ID');
        if (ppnElem) ppnElem.innerText = ppn.toLocaleString('id-ID');
        if (gtElem) gtElem.innerText = grandTotal.toLocaleString('id-ID');

        // Auto-update nominal Inc.PPn di dalam textarea Notes Client
        var notesElem = document.querySelector('textarea[name="keterangan"]');
        if (notesElem && notesElem.value.indexOf('Inc.PPn') !== -1) {
            notesElem.value = notesElem.value.replace(/\([0-9\.\,]+\)\s*Inc\.PPn/i, '(' + grandTotal.toLocaleString('id-ID') + ') Inc.PPn');
        }

        updateJurnalPreview(sum, ppn, grandTotal);
        updateReturSummary();
        updateSpkCardVisibility();
    }

    function updateReturSummary() {
        var box = document.getElementById('boxActiveReturSummary');
        var list = document.getElementById('listActiveReturSummary');
        if (!box || !list) return;

        var rows = document.querySelectorAll('#tableItems tbody tr');
        var returItems = [];
        rows.forEach(function(tr) {
            var nameInput = tr.querySelector('.inp-nama');
            var returInput = tr.querySelector('.inp-retur-qty');
            var returVal = parseNumber(returInput ? returInput.value : 0);
            if (returVal > 0) {
                var nama = nameInput ? nameInput.value : 'Item Produk';
                returItems.push({ nama: nama, qty: returVal });
            }
        });

        if (returItems.length > 0) {
            var html = '';
            returItems.forEach(function(it) {
                html += '<li>🚚 <strong>' + escapeHtml(it.nama) + '</strong> ➔ Retur Fisik: <strong>' + it.qty + ' Unit</strong> ke Gudang WO</li>';
            });
            list.innerHTML = html;
            box.style.display = 'block';
        } else {
            list.innerHTML = '';
            box.style.display = 'none';
        }
    }

    function updateJurnalPreview(currDPP, currPPN, currGrandTotal) {
        var deltaDPP = currDPP - origDPP;
        var deltaPPN = currPPN - origPPN;
        var deltaGT = currGrandTotal - origGrandTotal;

        var boxTable = document.getElementById('tableJurnalPreview');
        var statusInfo = document.getElementById('jurnalStatusInfo');
        var tbody = document.getElementById('tbodyJurnalPreview');

        boxTable.style.display = 'table';
        var html = '';
        var totalDebit = 0;
        var totalKredit = 0;

        if (Math.abs(deltaGT) >= 0.01 || Math.abs(deltaDPP) >= 0.01 || Math.abs(deltaPPN) >= 0.01) {
            statusInfo.innerHTML = '🔄 <b>Jurnal Perbaikan (Clean Replaced Journal)</b>: Nilai jurnal akan ditulis ulang secara bersih menyesuaikan UI terbaru (Grand Total: <b>Rp ' + currGrandTotal.toLocaleString('id-ID') + '</b>).';
            statusInfo.style.color = '#0056b3';
        } else {
            statusInfo.innerHTML = '✅ <b>Jurnal Perbaikan (Clean Replaced Journal)</b>: Nilai jurnal tetap akan ditulis ulang secara bersih saat amandemen disimpan.';
            statusInfo.style.color = '#28a745';
        }

        if (originalJurnal && originalJurnal.length > 0) {
            for (var i = 0; i < originalJurnal.length; i++) {
                var row = originalJurnal[i];

                var isDebet = parseFloat(row.debet) > 0;
                var oldVal = isDebet ? parseFloat(row.debet) : parseFloat(row.kredit);
                var namaAsli = row.rekening_nama ? row.rekening_nama : (row.rekening_2 ? row.rekening_2 : 'Akun ' + row.rekening);
                var recLower = namaAsli.toLowerCase();
                var rekCode = (row.rekening || '').toString().trim();
                
                // Tentukan nilai perbaikan baru (Clean Replaced Value) secara presisi & seimbang
                var newVal = 0;
                if (rekCode === '4010' || rekCode === '4010030' || rekCode === '411.1.171.01' || (recLower.indexOf('penjualan') !== -1 && recLower.indexOf('belum realisasi') === -1)) {
                    // Akun Penjualan / Hasil Karya = DPP Murni
                    newVal = currDPP;
                } else if (rekCode === '2030060' || rekCode === '211.1.171.01' || recLower.indexOf('ppn') !== -1) {
                    // Akun Hutang PPN = PPN Murni
                    newVal = currPPN;
                } else if (rekCode === '1010020010' || rekCode === '1010070030' || rekCode === '4030' || rekCode === '749.1.171.50' || recLower.indexOf('piutang') !== -1 || recLower.indexOf('belum realisasi') !== -1) {
                    // Akun Piutang & Kontra-Akun Proyek = Grand Total (DPP + PPN)
                    newVal = currGrandTotal;
                } else {
                    newVal = (origDPP > 0) ? Math.round(oldVal * (currDPP / origDPP)) : oldVal;
                }

                if (newVal <= 0) continue;

                html += '<tr>';
                html += '<td><code>' + row.rekening + '</code></td>';
                html += '<td>' + escapeHtml(namaAsli) + ' (Clean Replacement)</td>';
                
                if (isDebet) {
                    html += '<td class="text-right" style="color:#28a745; font-weight:bold;">' + newVal.toLocaleString('id-ID') + '</td>';
                    html += '<td class="text-right">-</td>';
                    totalDebit += newVal;
                } else {
                    html += '<td class="text-right">-</td>';
                    html += '<td class="text-right" style="color:#0056b3; font-weight:bold;">' + newVal.toLocaleString('id-ID') + '</td>';
                    totalKredit += newVal;
                }
                html += '</tr>';
            }
        } else {
            // Fallback standar jika data jurnal awal belum ada
            html += '<tr>';
            html += '<td><code>749.1.171.50</code></td><td>Rekening A/R Penagihan Termin (Clean Replacement)</td>';
            html += '<td class="text-right" style="color:#28a745; font-weight:bold;">' + currGrandTotal.toLocaleString('id-ID') + '</td><td class="text-right">-</td>';
            html += '</tr>';
            
            html += '<tr>';
            html += '<td><code>411.1.171.01</code></td><td>Hasil Karya / Penjualan Project (Clean Replacement)</td>';
            html += '<td class="text-right">-</td><td class="text-right" style="color:#0056b3; font-weight:bold;">' + currDPP.toLocaleString('id-ID') + '</td>';
            html += '</tr>';

            if (currPPN > 0) {
                html += '<tr>';
                html += '<td><code>211.1.171.01</code></td><td>Hutang PPN Keluaran 11% (Clean Replacement)</td>';
                html += '<td class="text-right">-</td><td class="text-right" style="color:#0056b3; font-weight:bold;">' + currPPN.toLocaleString('id-ID') + '</td>';
                html += '</tr>';
            }
            totalDebit = currGrandTotal;
            totalKredit = currDPP + currPPN;
        }

        tbody.innerHTML = html;
        document.getElementById('jurnalTotalDebit').innerText = 'Rp ' + totalDebit.toLocaleString('id-ID');
        document.getElementById('jurnalTotalKredit').innerText = 'Rp ' + totalKredit.toLocaleString('id-ID');
    }

    // HTML5 Drag and Drop Reordering Logic
    var dragSrcEl = null;

    function handleDragStart(e) {
        if (e.target.tagName !== 'TR') return;
        dragSrcEl = e.target;
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/html', e.target.outerHTML);
        setTimeout(function() {
            dragSrcEl.classList.add('dragging');
        }, 0);
    }

    function handleDragOver(e) {
        if (e.preventDefault) { e.preventDefault(); }
        var targetTr = e.target.closest('tr');
        if (targetTr && targetTr !== dragSrcEl && targetTr.parentNode.tagName === 'TBODY') {
            targetTr.style.borderTop = "2px dashed #0056b3";
        }
        return false;
    }

    function handleDragEnter(e) {
        // ...
    }

    function handleDragLeave(e) {
        var targetTr = e.target.closest('tr');
        if (targetTr && targetTr.parentNode.tagName === 'TBODY') {
            targetTr.style.borderTop = "";
            targetTr.style.borderBottom = "";
        }
    }

    function handleDrop(e) {
        if (e.stopPropagation) { e.stopPropagation(); }
        var targetTr = e.target.closest('tr');
        if (targetTr && dragSrcEl !== targetTr && targetTr.parentNode.tagName === 'TBODY') {
            targetTr.style.borderTop = "";
            var tbody = targetTr.parentNode;
            
            // Reorder the DOM nodes
            var draggedIndex = Array.from(tbody.children).indexOf(dragSrcEl);
            var targetIndex = Array.from(tbody.children).indexOf(targetTr);
            
            if (draggedIndex < targetIndex) {
                tbody.insertBefore(dragSrcEl, targetTr.nextSibling);
            } else {
                tbody.insertBefore(dragSrcEl, targetTr);
            }
            
            updateRowNumbers();
        }
        return false;
    }

    function handleDragEnd(e) {
        if (dragSrcEl) {
            dragSrcEl.classList.remove('dragging');
        }
        var trs = document.querySelectorAll('#tableItems tbody tr');
        trs.forEach(function (tr) {
            tr.style.borderTop = "";
            tr.style.borderBottom = "";
        });
    }

    var tbody = document.querySelector('#tableItems tbody');
    tbody.addEventListener('dragstart', handleDragStart, false);
    tbody.addEventListener('dragenter', handleDragEnter, false);
    tbody.addEventListener('dragover', handleDragOver, false);
    tbody.addEventListener('dragleave', handleDragLeave, false);
    tbody.addEventListener('drop', handleDrop, false);
    tbody.addEventListener('dragend', handleDragEnd, false);
</script>

<script>
    // Inisialisasi DataTables untuk tabel Riwayat Jurnal
    $(document).ready(function() {
        // Ambil semua nama kolom dari header tabel untuk menentukan index yang akan di-hide default
        var headers = [];
        $('#tableRiwayatJurnal thead th').each(function() {
            headers.push($(this).text().trim());
        });
        
        // Sesuaikan dengan nama kolom DB yang dicetak
        var visibleCols = ['dtime', 'transaksi_no', 'rekening', 'rekening_nama', 'rekening_2', 'keterangan', 'debet', 'kredit'];
        var hiddenIndices = [];
        for (var i = 0; i < headers.length; i++) {
            if (visibleCols.indexOf(headers[i]) === -1) {
                hiddenIndices.push(i);
            }
        }

        $('#tableRiwayatJurnal').DataTable({
            "paging": false,
            "searching": false,
            "info": false,
            "dom": "<'row'<'col-sm-12 text-center'B>>" +
                   "<'row'<'col-sm-12'tr>>",
            "buttons": [
                {
                    extend: 'colvis',
                    text: '👁️ Tampilkan/Sembunyikan Kolom',
                    className: 'btn btn-sm btn-outline-secondary'
                }
            ],
            "columnDefs": [
                { "targets": hiddenIndices, "visible": false }
            ],
            "order": [[headers.indexOf('dtime') > -1 ? headers.indexOf('dtime') : 0, "desc"]], // Default urut dtime
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Indonesian.json"
            }
        });
    });

    // --- JS LOGIC UNTUK CUSTOM MANUAL JOURNAL OVERRIDE ---
    function toggleManualJurnal(chk) {
        var box = document.getElementById('box_manual_jurnal_container');
        if (box) {
            if (chk.checked) {
                box.style.display = 'block';
                if (document.querySelectorAll('#tbodyManualJurnal tr').length === 0) {
                    initManualJurnalRows();
                }
            } else {
                box.style.display = 'none';
                document.querySelector('#frmAmandemen button[type="submit"]').disabled = false;
            }
        }
    }

    var manualJurnalIdx = 0;
    var coaMap = <?php echo json_encode(!empty($coa_list) ? $coa_list : array()); ?>;

    function initManualJurnalRows() {
        var tbody = document.getElementById('tbodyManualJurnal');
        if (!tbody) return;
        tbody.innerHTML = '';
        
        var curDPP = parseFloat(document.getElementById('dppVal').innerText.replace(/\./g, '').replace(/,/g, '.')) || 0;
        var curPPN = parseFloat(document.getElementById('ppnVal').innerText.replace(/\./g, '').replace(/,/g, '.')) || 0;
        var curGT = curDPP + curPPN;

        // Default Row 1: Debet Piutang Usaha Proyek (1010070030)
        addManualJurnalRow('1010070030', 'Piutang Usaha Kontijensi / Proyek', curGT, 0);
        // Default Row 2: Kredit Penjualan Project (4010030)
        addManualJurnalRow('4010030', 'Penjualan Project', 0, curDPP);
        // Default Row 3: Kredit PPN Keluaran (2030060)
        addManualJurnalRow('2030060', 'PPN Keluaran (Belum Faktur)', 0, curPPN);
    }

    function addManualJurnalRow(code, name, debet, kredit) {
        code = code || '';
        name = name || '';
        debet = debet || 0;
        kredit = kredit || 0;
        
        var idx = manualJurnalIdx++;
        var tbody = document.getElementById('tbodyManualJurnal');
        if (!tbody) return;
        var tr = document.createElement('tr');
        tr.id = 'jrow_' + idx;
        tr.innerHTML = '<td><input type="text" list="dl_coa_list" name="jurnal_custom[' + idx + '][rekening]" value="' + code + '" style="width:100%; padding:3px; font-family:monospace;" placeholder="Pilih/Ketik COA..." onchange="onCoaSelected(this)" required></td>' +
                       '<td><input type="text" name="jurnal_custom[' + idx + '][rekening_nama]" value="' + name + '" style="width:100%; padding:3px;" placeholder="Nama Rekening Akuntansi"></td>' +
                       '<td><input type="number" step="0.01" name="jurnal_custom[' + idx + '][debet]" class="inp-debet-manual" value="' + debet + '" style="width:100%; text-align:right;" onkeyup="calcManualJurnalBalance()" onchange="calcManualJurnalBalance()"></td>' +
                       '<td><input type="number" step="0.01" name="jurnal_custom[' + idx + '][kredit]" class="inp-kredit-manual" value="' + kredit + '" style="width:100%; text-align:right;" onkeyup="calcManualJurnalBalance()" onchange="calcManualJurnalBalance()"></td>' +
                       '<td style="text-align:center;"><button type="button" class="btn btn-sm btn-danger" onclick="removeManualJurnalRow(this)">X</button></td>';
        tbody.appendChild(tr);
        calcManualJurnalBalance();
    }

    function onCoaSelected(inp) {
        var val = inp.value.trim();
        if (!val) return;
        var tr = inp.closest('tr');
        var nameInp = tr.querySelector('input[name*="[rekening_nama]"]');
        if (nameInp && coaMap && coaMap.length > 0) {
            for (var i = 0; i < coaMap.length; i++) {
                if (coaMap[i].kode == val) {
                    nameInp.value = coaMap[i].nama;
                    break;
                }
            }
        }
    }

    function removeManualJurnalRow(btn) {
        var tr = btn.closest('tr');
        if (tr) tr.parentNode.removeChild(tr);
        calcManualJurnalBalance();
    }

    function calcManualJurnalBalance() {
        var totDebet = 0;
        var totKredit = 0;
        document.querySelectorAll('.inp-debet-manual').forEach(function(inp) {
            totDebet += parseFloat(inp.value) || 0;
        });
        document.querySelectorAll('.inp-kredit-manual').forEach(function(inp) {
            totKredit += parseFloat(inp.value) || 0;
        });

        var lblDebet = document.getElementById('lblTotDebetManual');
        var lblKredit = document.getElementById('lblTotKreditManual');
        if (lblDebet) lblDebet.innerText = 'Rp ' + totDebet.toLocaleString('id-ID');
        if (lblKredit) lblKredit.innerText = 'Rp ' + totKredit.toLocaleString('id-ID');

        var statusLbl = document.getElementById('lblBalanceStatus');
        var diff = Math.abs(totDebet - totKredit);

        if (statusLbl) {
            if (diff <= 1 && (totDebet > 0 || totKredit > 0)) {
                statusLbl.style.color = '#28a745';
                statusLbl.innerHTML = '✓ KESEIMBANGAN JURNAL MANUAL SEIMBANG (DEBET == KREDIT)';
                var btnSubmit = document.querySelector('#frmAmandemen button[type="submit"]');
                if (btnSubmit) btnSubmit.disabled = false;
            } else {
                statusLbl.style.color = '#dc3545';
                statusLbl.innerHTML = '❌ DUA SISI HARUS SEIMBANG! (Selisih: Rp ' + diff.toLocaleString('id-ID') + ')';
                var chk = document.getElementById('toggle_manual_jurnal');
                if (chk && chk.checked) {
                    var btnSubmit = document.querySelector('#frmAmandemen button[type="submit"]');
                    if (btnSubmit) btnSubmit.disabled = true;
                }
            }
        }
    }

    function checkNameChange(inp) {
        var origName = inp.getAttribute('data-orig-nama') || '';
        var curName = inp.value || '';
        var tr = inp.closest('tr');
        if (!tr) return;
        var infoDiv = tr.querySelector('.name-info');
        if (infoDiv) {
            if (curName.trim() !== origName.trim()) {
                infoDiv.innerHTML = '✏️ Nama dikustomisasi (Original: ' + origName + ')';
            } else {
                infoDiv.innerHTML = '';
            }
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        recalcGrandTotal();
    });

    function showAmandemenLoader() {
        var btn = document.querySelector('#frmAmandemen button[type="submit"]');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Sedang Menyimpan...';
            btn.style.opacity = '0.7';
        }
        var iframe = document.getElementById('result');
        if (iframe) {
            iframe.style.display = 'block';
        }
    }
</script>
</body>
</html>
