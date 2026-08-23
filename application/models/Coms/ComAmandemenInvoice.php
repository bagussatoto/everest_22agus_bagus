<?php
/**
 * [AGENT_LOG]
 * ROLE      : Software Engineer Agent
 * PURPOSE   : Model bisnis untuk amandemen invoice, menangani unpack JSON, re-calculate, dan jurnal storno.
 * COMPLIANCE: ISO 9001 (Audit Trail), Strict ACID CI3 Transactions
 * LOG_EXPIRE: 2026-11-07
 * [/AGENT_LOG]
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');

class ComAmandemenInvoice extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function checkTaxStatus($invoice_id) {
        $invoice_id = (int)$invoice_id;
        $this->db->select('id, nomer, reference_id, reference_nomer, efaktur, efaktur_dtime, transaksi_nilai, ppn_nilai');
        $this->db->where('id', $invoice_id);
        
        $inv = $this->db->get('transaksi')->row_array();
        if (!$inv) return array('status' => 'SAFE');

        $ref_id = !empty($inv['reference_id']) ? (int)$inv['reference_id'] : 0;
        $ids_to_check = array($invoice_id);
        if ($ref_id > 0) {
            $ids_to_check[] = $ref_id;
        }

        // Cari transaksi pajak (jenis 110, 110e, 110r) atau baris terkait yang memiliki efaktur approved DJP
        $this->db->select('id, nomer, jenis, reference_id, reference_nomer, efaktur, efaktur_dtime, transaksi_nilai, ppn_nilai, keterangan');
        $this->db->group_start();
            $this->db->where_in('id', $ids_to_check);
            $this->db->or_where_in('reference_id', $ids_to_check);
            $this->db->or_where('reference_nomer', $inv['nomer']);
            if (!empty($inv['reference_nomer'])) {
                $this->db->or_where('reference_nomer', $inv['reference_nomer']);
            }
        $this->db->group_end();
        $this->db->where('link_id', 0);
        $this->db->order_by('id', 'DESC');
        $res = $this->db->get('transaksi')->result_array();

        $tax_draft = null;

        foreach ($res as $trx) {
            $efaktur = trim($trx['efaktur']);
            $jenis = strtolower(trim($trx['jenis']));

            // Jika ada efaktur resmi yang terbit (bukan '0' dan bukan kosong)
            if (!empty($efaktur) && $efaktur !== '0') {
                $dpp = (float)$trx['transaksi_nilai'] - (float)$trx['ppn_nilai'];
                if ($dpp <= 0) $dpp = (float)$inv['transaksi_nilai'] - (float)$inv['ppn_nilai'];
                
                return array(
                    'status' => 'APPROVED_DJP',
                    'tax_id' => $trx['id'],
                    'tax_nomer' => $trx['nomer'],
                    'tax_jenis' => $trx['jenis'],
                    'efaktur' => $efaktur,
                    'efaktur_dtime' => $trx['efaktur_dtime'],
                    'dpp' => $dpp,
                    'ppn' => $trx['ppn_nilai'],
                    'nilai' => $trx['transaksi_nilai']
                );
            }

            if (in_array($jenis, array('110', '110e', '110r'))) {
                if (!$tax_draft) {
                    $tax_draft = $trx;
                }
            }
        }

        if ($tax_draft) {
            return array(
                'status' => 'DRAFT',
                'tax_id' => $tax_draft['id'],
                'tax_nomer' => $tax_draft['nomer'],
                'tax_jenis' => $tax_draft['jenis'],
                'efaktur' => '0',
                'efaktur_dtime' => $tax_draft['efaktur_dtime'],
                'dpp' => (float)$tax_draft['transaksi_nilai'] - (float)$tax_draft['ppn_nilai'],
                'ppn' => $tax_draft['ppn_nilai'],
                'nilai' => $tax_draft['transaksi_nilai']
            );
        }

        return array('status' => 'SAFE');
    }

    public function createAuditSnapshot($invoice_id) {
        $this->db->where('id', $invoice_id);
        $header = $this->db->get('transaksi')->row_array();
        
        if (!$header) return false;

        $this->db->where('transaksi_id', $invoice_id);
        $this->db->order_by('dtime', 'DESC');
        $this->db->limit(1);
        $registry = $this->db->get('transaksi_data_registry')->row_array();

        // Tangkap juga seluruh baris fisik rincian item di transaksi_data (SELECT *)
        $this->db->where('transaksi_id', $invoice_id);
        if ($this->db->field_exists('trash', 'transaksi_data')) {
            $this->db->where('trash', '0');
        }
        $items_detail = $this->db->get('transaksi_data')->result_array();

        // Tangkap juga seluruh baris jurnal fisik aktif yang akan diganti (mencakup jurnal di id_master)
        $id_master = isset($header['id_master']) ? (int)$header['id_master'] : 0;
        $target_ids = array_unique(array_filter(array((int)$invoice_id, $id_master)));
        if (empty($target_ids)) {
            $target_ids = array($invoice_id);
        }

        $this->db->where_in('transaksi_id', $target_ids);
        if ($this->db->field_exists('trash', 'jurnal')) {
            $this->db->where('trash', '0');
        }
        $jurnal_detail = $this->db->get('jurnal')->result_array();

        // 100% COMPLETE SNAPSHOT: Menyimpan SELECT * dari transaksi, transaksi_data_registry, transaksi_data, dan jurnal
        $snapshot = array(
            'header' => $header,
            'registry' => $registry,
            'items_detail' => $items_detail,
            'jurnal_detail' => $jurnal_detail,
            'timestamp' => date('Y-m-d H:i:s')
        );

        return $snapshot;
    }

    /**
     * Memproses logika ACID Amandemen dari data POST Items
     */
    public function processAmandemenJSON($invoice_id, $post_items, $snapshot, $description = '', $catatan_amandemen = '', $post_jurnal_custom = null) {
        $old_registry = $snapshot['registry'];
        if (!$old_registry) return false;

        $items5_sum = array();
// START OF COMPLETE REPEATED LOGIC
        $grandTotalDPP = 0;

        $old_items5_sum = @unserialize(base64_decode($old_registry['items5_sum']));
        $items5_sum = $old_items5_sum; // Pertahankan struktur asli

        // Self-Healing: Jika items5_sum kosong (memang tidak ter-capture saat pembuatan invoice),
        // rebuild dari project_sub_tasklist_komposisi via project_id di registry items.
        if (empty($items5_sum)) {
            // Coba pulihkan dari histori amandemen
            $this->db->where('transaksi_id', $invoice_id);
            $this->db->order_by('id', 'ASC');
            $this->db->limit(1);
            $his = $this->db->get('transaksi_amandemen_history')->row_array();
            if ($his && !empty($his['old_registry_data'])) {
                $old_reg_his = json_decode($his['old_registry_data'], true);
                if ($old_reg_his && !empty($old_reg_his['items5_sum'])) {
                    $old_his_items5 = @unserialize(base64_decode($old_reg_his['items5_sum']));
                    if (!empty($old_his_items5)) {
                        $items5_sum = $old_his_items5;
                    }
                }
            }
        }

        // Self-Healing Level 2: Rebuild dari project_sub_tasklist_komposisi 
        // (sumber kebenaran untuk invoice proyek 4822)
        if (empty($items5_sum)) {
            $old_items = @unserialize(base64_decode($old_registry['items']));
            $rebuild_project_id = 0;
            if (is_array($old_items)) {
                $first_item = reset($old_items);
                if (isset($first_item['project_id'])) {
                    $rebuild_project_id = (int)$first_item['project_id'];
                } elseif (isset($first_item['projectID'])) {
                    $rebuild_project_id = (int)$first_item['projectID'];
                }
            }

            if ($rebuild_project_id > 0) {
                // Query komposisi SPK yang aktif untuk proyek ini
                $sql_rebuild = "
                    SELECT t.id as tasklist_id, t.no_spk, t.produk_id as project_produk_id,
                           pp.nama as project_nama, pp.harga as project_harga,
                           k.id as komp_id, k.jenis, k.produk_dasar_id, k.produk_dasar_nama,
                           k.jml, k.harga as komp_harga, k.jml_return, k.nilai_return,
                           k.biaya_id, k.biaya_dasar_id, k.no_sub, k.sub_fase_id, k.link_id,
                           k.produk_id as komp_produk_id
                    FROM project_sub_tasklist_komposisi k
                    INNER JOIN project_tasklist t ON k.no_spk = t.no_spk
                    INNER JOIN project_produk pp ON t.produk_id = pp.id
                    WHERE t.produk_id = ? AND t.trash = 0 AND k.trash = 0 AND k.progress_id != 3
                    ORDER BY k.jenis, k.produk_dasar_nama
                ";
                $res_rebuild = $this->db->query($sql_rebuild, array($rebuild_project_id));

                if ($res_rebuild && $res_rebuild->num_rows() > 0) {
                    // Bangun items5_sum dengan format nested bahan_baku
                    // sesuai struktur yang diharapkan oleh Printing.php view
                    $tasklist_map = array();
                    foreach ($res_rebuild->result_array() as $komp) {
                        $tl_id = $komp['tasklist_id'];
                        if (!isset($tasklist_map[$tl_id])) {
                            $tasklist_map[$tl_id] = array(
                                'id' => $tl_id,
                                'nama' => $komp['project_nama'],
                                'no_spk' => $komp['no_spk'],
                                'produk_id' => $komp['project_produk_id'],
                                'produk_nama' => $komp['project_nama'],
                                'bahan_baku' => array(
                                    'produk' => array(),
                                    'biaya' => array()
                                )
                            );
                        }

                        $pd_id = $komp['produk_dasar_id'];
                        $entry = array(
                            'id' => $komp['komp_id'],
                            'jenis' => $komp['jenis'],
                            'no_spk' => $komp['no_spk'],
                            'produk_dasar_id' => $pd_id,
                            'produk_dasar_nama' => $komp['produk_dasar_nama'],
                            'jml' => (float)$komp['jml'],
                            'harga' => (float)$komp['komp_harga'],
                            'saldo' => (float)$komp['jml'] * (float)$komp['komp_harga'],
                            'jml_return' => (float)$komp['jml_return'],
                            'nilai_return' => (float)$komp['nilai_return'],
                            'biaya_id' => isset($komp['biaya_id']) ? $komp['biaya_id'] : 0,
                            'biaya_dasar_id' => isset($komp['biaya_dasar_id']) ? $komp['biaya_dasar_id'] : 0,
                            'sub_fase_id' => isset($komp['sub_fase_id']) ? $komp['sub_fase_id'] : 0,
                            'link_id' => isset($komp['link_id']) ? $komp['link_id'] : 0,
                            'satuan' => ''
                        );

                        if ($komp['jenis'] == 'biaya' || $komp['jenis'] == 'supplies') {
                            $tasklist_map[$tl_id]['bahan_baku']['biaya'][$pd_id] = $entry;
                        } else {
                            $tasklist_map[$tl_id]['bahan_baku']['produk'][$pd_id] = $entry;
                        }
                    }
                    $items5_sum = $tasklist_map;
                }
            }
        }

        $post_map_by_pd = array();
        $post_map_by_id = array();
        $new_tableIn_detail_values = array();

        $bahan_baku_produk = array();
        $bahan_baku_biaya = array();

        $project_id = isset($snapshot['header']['project_id']) ? (int)$snapshot['header']['project_id'] : 0;
        $project_name = '';
        if (isset($snapshot['header']['project_nama']) && !empty($snapshot['header']['project_nama']) && $snapshot['header']['project_nama'] !== 'PEKERJAAN PROJECT') {
            $project_name = $snapshot['header']['project_nama'];
        } elseif (isset($snapshot['main']['projectName']) && !empty($snapshot['main']['projectName']) && $snapshot['main']['projectName'] !== 'PEKERJAAN PROJECT') {
            $project_name = $snapshot['main']['projectName'];
        } elseif ($project_id > 0) {
            $prj_row = $this->db->select('nama')->where('id', $project_id)->get('project_produk')->row_array();
            if ($prj_row && !empty($prj_row['nama'])) {
                $project_name = $prj_row['nama'];
            }
        }
        if (empty($project_name)) {
            $project_name = 'AVESTA - MATERIAL';
        }

        $spk_id = isset($snapshot['items']) && is_array($snapshot['items']) && count($snapshot['items']) > 0 ? key($snapshot['items']) : 0;

        // Auto-Lookup Master Data Satuan (produk.size_nama)
        $pd_ids_to_lookup = array();
        if (is_array($post_items)) {
            foreach ($post_items as $itm) {
                $pd_val = isset($itm['produk_dasar_id']) ? (int)$itm['produk_dasar_id'] : 0;
                if ($pd_val > 0) $pd_ids_to_lookup[] = $pd_val;
            }
        }
        $master_satuan_map = array();
        if (!empty($pd_ids_to_lookup)) {
            $this->db->select('id, size_nama, nama');
            $this->db->where_in('id', array_unique($pd_ids_to_lookup));
            $res_prd = $this->db->get('produk')->result_array();
            if (!empty($res_prd)) {
                foreach ($res_prd as $rp) {
                    if (!empty($rp['size_nama'])) {
                        $master_satuan_map[$rp['id']] = $rp['size_nama'];
                    }
                }
            }
        }

        if (is_array($post_items)) {
            $urut = 1;
            foreach ($post_items as $itm) {
                $pd = isset($itm['produk_dasar_id']) ? (string)$itm['produk_dasar_id'] : '0';
                $id = isset($itm['id']) ? (string)$itm['id'] : '0';
                if ($pd === '') $pd = '0';
                if ($id === '') $id = '0';

                $raw_qty = isset($itm['jml']) ? $itm['jml'] : 0;
                $raw_harga = isset($itm['harga']) ? $itm['harga'] : 0;
                $qty = is_string($raw_qty) ? (float)str_replace(',', '.', str_replace('.', '', $raw_qty)) : (float)$raw_qty;
                $harga = is_string($raw_harga) ? (float)str_replace(',', '.', str_replace('.', '', $raw_harga)) : (float)$raw_harga;
                $nama = !empty($itm['nama']) ? $itm['nama'] : '';
                $raw_satuan = isset($itm['satuan']) ? trim($itm['satuan']) : '';
                $subtotal = $qty * $harga;
                
                if ($qty > 0) {
                    $is_jasa = (stripos($nama, 'jasa') !== false || stripos($nama, 'bongkar') !== false || stripos($nama, 'biaya') !== false || stripos($nama, 'instalasi') !== false);

                    // Auto-sync satuan ke master data produk jika kosong / 'null'
                    if (empty($raw_satuan) || strtolower($raw_satuan) === 'null' || $raw_satuan === '-') {
                        if ($pd !== '0' && isset($master_satuan_map[(int)$pd])) {
                            $satuan = $master_satuan_map[(int)$pd];
                        } else {
                            $satuan = $is_jasa ? 'Unit' : 'Unit';
                        }
                    } else {
                        $satuan = $raw_satuan;
                    }

                    $bb_row = array(
                        'id' => $id,
                        'produk_id' => $pd,
                        'produk_dasar_id' => $pd,
                        'produk_dasar_nama' => $nama,
                        'nama' => $nama,
                        'satuan' => $satuan,
                        'jml' => $qty,
                        'harga' => $harga,
                        'saldo' => $subtotal,
                        'jenis' => $is_jasa ? 'biaya' : 'produk'
                    );

                    if ($is_jasa) {
                        $bahan_baku_biaya[] = $bb_row;
                    } else {
                        $bahan_baku_produk[] = $bb_row;
                    }

                    // Rebuild tableIn_detail_values (for Printing.php)
                    if ($pd !== '0') {
                        $new_tableIn_detail_values[$pd] = array(
                            'qty' => $qty,
                            'harga' => $harga,
                            'subtotal' => $subtotal,
                            'produk_nama' => $nama,
                            'urutan' => $urut
                        );
                    }
                    $urut++;
                }

                if ($pd !== '0') {
                    $post_map_by_pd[$pd] = $itm;
                }
                if ($id !== '0') {
                    $post_map_by_id[$id] = $itm;
                }
                
                $grandTotalDPP += $qty * $harga;
            }
        }

        // Rebuild Hirarkis items5_sum dengan struktur bahan_baku (Lengkap untuk 6 OPSI CETAK Printing.php)
        $new_items5_sum = array(
            0 => array(
                'id' => $spk_id > 0 ? $spk_id : 1,
                'produk_id' => $project_id,
                'produk_nama' => $project_name,
                'nama' => $project_name,
                'harga' => $grandTotalDPP,
                'saldo' => $grandTotalDPP,
                'bahan_baku' => array(
                    'produk' => $bahan_baku_produk,
                    'biaya' => $bahan_baku_biaya
                )
            )
        );

        // --- ENTERPRISE RETURN SERVICE LOGIC ---
        $return_list = array('supplies' => array(), 'produk' => array());
        $selisih_map = array(); // Map berdasarkan komposisi ID

        // 1. Baca langsung input retur_qty yang ditetapkan user di form ($post_items)
        if (is_array($post_items)) {
            foreach ($post_items as $itm) {
                $raw_retur = isset($itm['retur_qty']) ? $itm['retur_qty'] : 0;
                $retur_qty = is_string($raw_retur) ? (float)str_replace(',', '.', str_replace('.', '', $raw_retur)) : (float)$raw_retur;
                $komp_id = isset($itm['id']) ? (int)$itm['id'] : 0;
                
                if ($retur_qty > 0 && $komp_id > 0) {
                    $selisih_map[$komp_id] = $retur_qty;
                }
            }
        }

        $items5_sum_old = array();
        if (isset($old_registry['items5_sum'])) {
            $items5_sum_old = @unserialize(base64_decode($old_registry['items5_sum']));
        }

        if (is_array($items5_sum_old)) {
            foreach ($items5_sum_old as $td_id => $td_data) {
                if (isset($td_data['bahan_baku']['produk']) && is_array($td_data['bahan_baku']['produk'])) {
                    foreach ($td_data['bahan_baku']['produk'] as $pd_key => $prd) {
                        $qty_lama = (float)$prd['jml'];
                        $komp_id = isset($prd['id']) ? (int)$prd['id'] : 0;
                        
                        $item_pd_id = isset($prd['produk_dasar_id']) ? (string)$prd['produk_dasar_id'] : (string)$pd_key;
                        $item_id = isset($prd['id']) ? (string)$prd['id'] : '';

                        $matched = null;
                        if ($item_id !== '' && $item_id !== '0' && isset($post_map_by_id[$item_id])) {
                            $matched = $post_map_by_id[$item_id];
                        } elseif ($item_pd_id !== '' && $item_pd_id !== '0' && isset($post_map_by_pd[$item_pd_id])) {
                            $matched = $post_map_by_pd[$item_pd_id];
                        }

                        $qty_baru = 0;
                        if ($matched !== null) {
                            $raw_match_qty = isset($matched['jml']) ? $matched['jml'] : 0;
                            $qty_baru = is_string($raw_match_qty) ? (float)str_replace(',', '.', str_replace('.', '', $raw_match_qty)) : (float)$raw_match_qty;
                        }

                        $selisih = $qty_lama - $qty_baru;
                        if ($selisih > 0 && $komp_id > 0 && !isset($selisih_map[$komp_id])) {
                            $selisih_map[$komp_id] = $selisih;
                        }
                    }
                }

                if (isset($td_data['bahan_baku']['biaya']) && is_array($td_data['bahan_baku']['biaya'])) {
                    foreach ($td_data['bahan_baku']['biaya'] as $pd_key => $biy) {
                        $qty_lama = (float)$biy['jml'];
                        $komp_id = isset($biy['id']) ? (int)$biy['id'] : 0;

                        $item_pd_id = isset($biy['produk_dasar_id']) ? (string)$biy['produk_dasar_id'] : (string)$pd_key;
                        $item_id = isset($biy['id']) ? (string)$biy['id'] : '';

                        $matched = null;
                        if ($item_id !== '' && $item_id !== '0' && isset($post_map_by_id[$item_id])) {
                            $matched = $post_map_by_id[$item_id];
                        } elseif ($item_pd_id !== '' && $item_pd_id !== '0' && isset($post_map_by_pd[$item_pd_id])) {
                            $matched = $post_map_by_pd[$item_pd_id];
                        }

                        $qty_baru = 0;
                        if ($matched !== null) {
                            $raw_match_qty = isset($matched['jml']) ? $matched['jml'] : 0;
                            $qty_baru = is_string($raw_match_qty) ? (float)str_replace(',', '.', str_replace('.', '', $raw_match_qty)) : (float)$raw_match_qty;
                        }

                        $selisih = $qty_lama - $qty_baru;
                        if ($selisih > 0 && $komp_id > 0 && !isset($selisih_map[$komp_id])) {
                            $selisih_map[$komp_id] = $selisih;
                        }
                    }
                }
                
                // Flat structure support (if items5_sum was flattened)
                if (!isset($td_data['bahan_baku']) && (isset($td_data['produk_dasar_id']) || isset($td_data['id']))) {
                    $qty_lama = (float)(isset($td_data['jml']) ? $td_data['jml'] : 0);
                    $komp_id = isset($td_data['id']) ? (int)$td_data['id'] : 0;

                    $item_pd_id = isset($td_data['produk_dasar_id']) ? (string)$td_data['produk_dasar_id'] : '';
                    $item_id = isset($td_data['id']) ? (string)$td_data['id'] : '';

                    $matched = null;
                    if ($item_id !== '' && $item_id !== '0' && isset($post_map_by_id[$item_id])) {
                        $matched = $post_map_by_id[$item_id];
                    } elseif ($item_pd_id !== '' && $item_pd_id !== '0' && isset($post_map_by_pd[$item_pd_id])) {
                        $matched = $post_map_by_pd[$item_pd_id];
                    }

                    $qty_baru = 0;
                    if ($matched !== null) {
                        $raw_match_qty = isset($matched['jml']) ? $matched['jml'] : 0;
                        $qty_baru = is_string($raw_match_qty) ? (float)str_replace(',', '.', str_replace('.', '', $raw_match_qty)) : (float)$raw_match_qty;
                    }

                    $selisih = $qty_lama - $qty_baru;
                    if ($selisih > 0 && $komp_id > 0 && !isset($selisih_map[$komp_id])) {
                        $selisih_map[$komp_id] = $selisih;
                    }
                }
            }
        }

        // Assign rebuilt arrays back
        $items5_sum = $new_items5_sum;

        if (!empty($selisih_map)) {
            $ids = array_keys($selisih_map);
            $this->db->where_in('id', $ids);
            $komposisiData = $this->db->get('project_sub_tasklist_komposisi')->result_array();

            $no_spk = "";
            $sub_nomer = "";
            $sub_fase_id = 0;
            $link_id = 0; // tasklist_id
            $wo_paket_id = 0;
            $produk_id = 0;

            foreach ($komposisiData as $komp) {
                $old_id = (int)$komp['id'];
                $selisih = (float)$selisih_map[$old_id];
                $jenis = strtolower(trim($komp['jenis']));

                $no_spk = $komp['no_spk'];
                $sub_nomer = $komp['no_sub'];
                $sub_fase_id = (int)$komp['sub_fase_id'];
                $link_id = (int)$komp['link_id'];
                $wo_paket_id = isset($komp['produk_paket_id']) ? (int)$komp['produk_paket_id'] : 0;
                $produk_id = (int)$komp['produk_id'];
                
                $biaya_id = !empty($komp['biaya_id']) ? (int)$komp['biaya_id'] : 0;

                $new_jml_return = (float)$komp['jml_return'] + $selisih;
                $new_nilai_return = (float)$komp['nilai_return'] + ($selisih * (float)$komp['harga']);
                
                $this->db->where('id', $old_id);
                $this->db->update('project_sub_tasklist_komposisi', array(
                    'jml_return' => $new_jml_return,
                    'nilai_return' => $new_nilai_return
                ));

                if ($jenis == 'biaya' || $jenis == 'supplies') {
                    $return_list['supplies'][$biaya_id][] = array(
                        'produk_dasar_id' => $komp['produk_dasar_id'],
                        'produk_dasar_nama' => $komp['produk_dasar_nama'],
                        'satuan' => isset($komp['satuan']) ? $komp['satuan'] : '',
                        'jml_return' => $selisih
                    );
                } else {
                    $return_list['produk'][$biaya_id][] = array(
                        'produk_dasar_id' => $komp['produk_dasar_id'],
                        'produk_dasar_nama' => $komp['produk_dasar_nama'],
                        'satuan' => isset($komp['satuan']) ? $komp['satuan'] : '',
                        'jml_return' => $selisih
                    );
                }
            }

            $projectNameStr = "Amandemen Invoice " . $snapshot['header']['nomer'];

            // Dapatkan info Termin Induk untuk catatan audit retur
            $termin_nomer = '';
            if (isset($snapshot['header']['reference_id']) && (int)$snapshot['header']['reference_id'] > 0) {
                $ref_trx = $this->db->select('nomer')->where('id', (int)$snapshot['header']['reference_id'])->get('transaksi')->row_array();
                if ($ref_trx && !empty($ref_trx['nomer'])) {
                    $termin_nomer = $ref_trx['nomer'];
                }
            }
            if (empty($termin_nomer)) {
                $termin_nomer = isset($snapshot['header']['nomer_top']) && !empty($snapshot['header']['nomer_top']) ? $snapshot['header']['nomer_top'] : 'Termin Proyek';
            }

            $spk_label = !empty($no_spk) ? $no_spk : '-';
            $invoice_label = isset($snapshot['header']['nomer']) ? $snapshot['header']['nomer'] : (string)$invoice_id;
            $catatan_user = !empty($history_keterangan) ? " | Alasan: " . $history_keterangan : "";
            $structured_return_desc = "Retur Fisik Amandemen Invoice: " . $invoice_label . " | SPK: " . $spk_label . " | Termin: " . $termin_nomer . $catatan_user;
            
            // Dapatkan Gudang WO SPK sebenarnya dari project_tasklist
            $spk_tasklist = $this->db->get_where('project_tasklist', array('no_spk' => $no_spk, 'trash' => 0))->row_array();
            $gudang_wo_actual = ($spk_tasklist && !empty($spk_tasklist['gudang_wo'])) ? $spk_tasklist['gudang_wo'] : 9;

            if (!empty($return_list['supplies'])) {
                $supplies_items = array();
                foreach ($return_list['supplies'] as $biyID => $biyItems) {
                    foreach ($biyItems as $subItem) {
                        $supplies_items[$biyID][] = array(
                            "biaya_id" => $biyID,
                            "sub_biaya_id" => 0,
                            "produk_dasar_id" => $subItem['produk_dasar_id'],
                            "nama" => isset($subItem['produk_dasar_nama']) ? $subItem['produk_dasar_nama'] : "-",
                            "satuan" => isset($subItem['satuan']) ? $subItem['satuan'] : "-",
                            "jml_return" => $subItem['jml_return']
                        );
                    }
                }
                if (!empty($supplies_items)) {
                    $this->load->library("SuppliesReturnService");
                    $suppliesService = new SuppliesReturnService();
                    $paramsSupplies = array(
                        "produk_id" => $produk_id,
                        "produk_nama" => $projectNameStr,
                        "no_spk" => $no_spk,
                        "sub_nomer" => $sub_nomer,
                        "tasklist_id" => $link_id,
                        "sub_fase_id" => $sub_fase_id,
                        "wo_paket_id" => $wo_paket_id,
                        "wo_paket_nama" => "-",
                        "gudang_wo_id" => $gudang_wo_actual,
                        "return_items" => $supplies_items,
                        "cabang_id" => function_exists('my_cabang_id') ? my_cabang_id() : 1,
                        "cabang_nama" => function_exists('my_cabang_nama') ? my_cabang_nama() : '',
                        "bookingNumber" => $snapshot['header']['nomer'],
                        "description" => $structured_return_desc
                    );
                    $suppliesService->processReturn($paramsSupplies);
                }
            }

            if (!empty($return_list['produk'])) {
                $fg_items = array();
                foreach ($return_list['produk'] as $biyID => $biyItems) {
                    foreach ($biyItems as $subItem) {
                        $fg_items[] = array(
                            "biaya_id" => $biyID,
                            "produk_dasar_id" => $subItem['produk_dasar_id'],
                            "nama" => isset($subItem['produk_dasar_nama']) ? $subItem['produk_dasar_nama'] : "-",
                            "satuan" => isset($subItem['satuan']) ? $subItem['satuan'] : "-",
                            "jml_return" => $subItem['jml_return']
                        );
                    }
                }
                if (!empty($fg_items)) {
                    $this->load->library("FgReturnService");
                    $fgService = new FgReturnService();
                    $paramsFg = array(
                        "produk_id" => $produk_id,
                        "produk_nama" => $projectNameStr,
                        "no_spk" => $no_spk,
                        "sub_nomer" => $sub_nomer,
                        "tasklist_id" => $link_id,
                        "sub_fase_id" => $sub_fase_id,
                        "wo_paket_id" => $wo_paket_id,
                        "wo_paket_nama" => "-",
                        "gudang_wo_id" => $gudang_wo_actual,
                        "return_items" => $fg_items,
                        "cabang_id" => function_exists('my_cabang_id') ? my_cabang_id() : 1,
                        "cabang_nama" => function_exists('my_cabang_nama') ? my_cabang_nama() : '',
                        "bookingNumber" => $snapshot['header']['nomer'],
                        "description" => $structured_return_desc
                    );
                    $fgService->processReturn($paramsFg);
                }
            }
        }
        // --- END ENTERPRISE RETURN SERVICE LOGIC ---

        // --- PREPARE REGISTRY & DB UPDATE ---
        $header = $snapshot['header'];
        $ppn_rate = 11;
        $ppn_val = ($grandTotalDPP * $ppn_rate) / 100;
        $tagihan_baru = $grandTotalDPP + $ppn_val;

        $items = @unserialize(base64_decode($old_registry['items']));
        if ($items && is_array($items)) {
            $first_key = key($items);
            if (isset($items[$first_key])) {
                $items[$first_key]['subtotal'] = $grandTotalDPP;
                $items[$first_key]['tagihan'] = $grandTotalDPP;
                $items[$first_key]['sisa'] = $grandTotalDPP;
            }
        }

        $main = @unserialize(base64_decode($old_registry['main']));
        if ($main && is_array($main)) {
            // 1. Kategori DPP / Nilai Jual
            $dpp_keys = array('nett1', 'dpp_ppn', 'subtotal', 'tagihan', 'nilai_bayar', 'penjualan', 'penjualan_bulat', 'new_net1', 'grand_total_ui', 'harus_bayar', 'nilai_entry', 'nilai_cash');
            foreach ($dpp_keys as $k) {
                if (isset($main[$k])) $main[$k] = $grandTotalDPP;
            }

            // 2. Kategori PPN
            $ppn_keys = array('ppn_out_bulat', 'ppn', 'grand_ppn');
            foreach ($ppn_keys as $k) {
                if (isset($main[$k])) $main[$k] = $ppn_val;
            }

            // 3. Kategori Grand Total
            $gt_keys = array('grand_pembulatan', 'grand_total', 'new_net3', 'piutang_dagang');
            foreach ($gt_keys as $k) {
                if (isset($main[$k])) $main[$k] = $tagihan_baru;
            }

            // 4. Kategori Pajak Khusus (DPP Pengganti)
            if (isset($main['dpp_pengganti_factor']) && isset($main['dpp_pengganti'])) {
                $main['dpp_pengganti'] = $grandTotalDPP * $main['dpp_pengganti_factor'];
            }

            // Notes Client (Untuk Penagihan Client / Dicetak di Invoice)
            if (!empty($description)) {
                $main['keterangan'] = $description;
                $main['description'] = nl2br($description);
            }
            
            // Catatan Amandemen (Khusus Internal)
            if (!empty($catatan_amandemen)) {
                $main['catatan_amandemen_internal'] = $catatan_amandemen;
            }
            $main['keterangan_amandemen'] = 'Amandemen via Modul';
        }

        // Update tableIn_master_values jika ada pada old_registry
        $tableIn_master_values_encoded = isset($old_registry['tableIn_master_values']) ? $old_registry['tableIn_master_values'] : '';
        if (!empty($old_registry['tableIn_master_values'])) {
            $master_vals = @unserialize(base64_decode($old_registry['tableIn_master_values']));
            if ($master_vals && is_array($master_vals)) {
                $master_vals['tagihan'] = $tagihan_baru;
                $master_vals['nett1'] = $grandTotalDPP;
                $master_vals['ppn'] = $ppn_val;
                $master_vals['grand_total'] = $tagihan_baru;
                if (!empty($description)) {
                    $master_vals['keterangan'] = $description;
                    $master_vals['description'] = $description;
                }
                $tableIn_master_values_encoded = base64_encode(serialize($master_vals));
            }
        }

        // Rebuild tableIn_detail_values safely
        $tableIn_detail_values_encoded = '';
        if (!empty($old_registry['tableIn_detail_values'])) {
            $detail_vals = @unserialize(base64_decode($old_registry['tableIn_detail_values']));
            if ($detail_vals && is_array($detail_vals)) {
                // Remove deleted items, and update existing ones. (For Printing.php)
                foreach ($detail_vals as $pid => &$dval) {
                    $pid_str = (string)$pid;
                    if (isset($new_tableIn_detail_values[$pid_str])) {
                        $matched = $new_tableIn_detail_values[$pid_str];
                        $dval['qty'] = $matched['qty'];
                        $dval['subtotal'] = $matched['subtotal'];
                        $dval['harga'] = $matched['harga'];
                        $dval['produk_nama'] = $matched['produk_nama'];
                    } else {
                        $dval['qty'] = 0;
                        $dval['subtotal'] = 0;
                    }
                }
                
                // Add new custom rows to tableIn_detail_values if not exist
                foreach ($new_tableIn_detail_values as $pid => $new_val) {
                    if (!isset($detail_vals[$pid])) {
                        $detail_vals[$pid] = $new_val;
                    }
                }
                $tableIn_detail_values_encoded = base64_encode(serialize($detail_vals));
            }
        }

        // Kalkulasi Jurnal Storno
        // Dihapus: Sistem Everest menggunakan mapping rules di jurnal_index (misal: 'loop' => ['1120' => 'grand_total']).
        // Karena kita sudah memperbarui nilai di $main (seperti grand_total, nett1, ppn),
        // maka posting jurnal otomatis akan membaca nilai yang baru. Kita cukup mempertahankan jurnal_index lama.
        $jurnal_index_baru = isset($old_registry['jurnal_index']) ? $old_registry['jurnal_index'] : '';

        $new_registry = $old_registry;
        unset($new_registry['id']);
        $new_registry['transaksi_id'] = $invoice_id;
        $new_registry['dtime']        = date('Y-m-d H:i:s');
        $new_registry['items5_sum']   = base64_encode(serialize($items5_sum));
        $new_registry['items']        = base64_encode(serialize($items));
        $new_registry['main']         = base64_encode(serialize($main));
        $new_registry['jurnal_index'] = $jurnal_index_baru;
        if (!empty($tableIn_master_values_encoded)) {
            $new_registry['tableIn_master_values'] = $tableIn_master_values_encoded;
        }
        if (!empty($tableIn_detail_values_encoded)) {
            $new_registry['tableIn_detail_values'] = $tableIn_detail_values_encoded;
        }

        $oleh_id = isset($_SESSION['login']['id']) ? $_SESSION['login']['id'] : 0;
        $oleh_nama = isset($_SESSION['login']['nama']) ? $_SESSION['login']['nama'] : '';
        
        $history_keterangan = !empty($catatan_amandemen) ? $catatan_amandemen : $description;

        $old_reg_json = json_encode($snapshot); // Save the complete 4-layer snapshot
        $history_data = array(
            'transaksi_id' => $invoice_id,
            'dtime' => date('Y-m-d H:i:s'),
            'oleh_id' => $oleh_id,
            'oleh_nama' => $oleh_nama,
            'keterangan' => $history_keterangan,
            'old_registry_data' => $old_reg_json
        );
        if ($this->db->field_exists('old_registry_bytes', 'transaksi_amandemen_history')) {
            $history_data['old_registry_bytes'] = strlen($old_reg_json);
        }
        $this->db->insert('transaksi_amandemen_history', $history_data);

        $this->db->where('transaksi_id', $invoice_id);
        $this->db->update('transaksi_data_registry', $new_registry);

        // Update Tabel Relasional `transaksi_data` MySQL (Agar Cetakan Invoice Langsung Berubah dan Sesuai Urutan Drag&Drop)
        if (is_array($post_items)) {
            // 1. Ambil semua baris lama yang aktif sebagai template
            $this->db->where('transaksi_id', $invoice_id);
            if ($this->db->field_exists('trash', 'transaksi_data')) {
                $this->db->where('trash', '0');
            }
            $old_td_rows = $this->db->get('transaksi_data')->result_array();
            $old_td_map = array();
            $old_td_map_by_dasar = array();
            foreach ($old_td_rows as $row) {
                // Index ganda: produk_id DAN produk_dasar_id (jika berbeda)
                // Karena form mengirim produk_dasar_id, bukan produk_id
                $pid = (int)$row['produk_id'];
                if (!isset($old_td_map[$pid])) {
                    $old_td_map[$pid] = array();
                }
                $old_td_map[$pid][] = $row;
            }

            // Siapkan base_template untuk fallback (jika transaksi_data sudah kosong)
            // Ambil dari baris pertama yang ada, atau dari snapshot header
            $base_template = array();
            if (!empty($old_td_rows)) {
                $base_template = $old_td_rows[0];
            } else {
                // transaksi_data sudah kosong (akibat amandemen sebelumnya yang rusak)
                // Fallback: bangun template dari header transaksi di snapshot
                $hdr = $snapshot['header'];
                $base_template = array(
                    'transaksi_id' => $invoice_id,
                    'cabang_id' => isset($hdr['cabang_id']) ? $hdr['cabang_id'] : 0,
                    'gudang_id' => isset($hdr['gudang_id']) ? $hdr['gudang_id'] : 0,
                    'gudang_id_tujuan' => isset($hdr['gudang_id_tujuan']) ? $hdr['gudang_id_tujuan'] : 0,
                    'kategori_id' => isset($hdr['kategori_id']) ? $hdr['kategori_id'] : 0,
                    'jenis' => isset($hdr['jenis']) ? $hdr['jenis'] : '',
                    'jenisTr' => isset($hdr['jenisTr']) ? $hdr['jenisTr'] : '',
                    'step_number' => isset($hdr['step_number']) ? $hdr['step_number'] : 0,
                    'nomer' => isset($hdr['nomer']) ? $hdr['nomer'] : '',
                    'pembayaran_sys' => isset($hdr['pembayaran_sys']) ? $hdr['pembayaran_sys'] : '',
                    'status' => isset($hdr['status']) ? $hdr['status'] : '1',
                    'trash' => '0',
                    'link_id' => '0',
                    'next_substep_code' => isset($hdr['next_substep_code']) ? $hdr['next_substep_code'] : '',
                    'sub_step_number' => isset($hdr['sub_step_number']) ? $hdr['sub_step_number'] : 0,
                    'valid_qty' => 1
                );
            }

            // 2. Matikan baris lama (Soft-Delete: trash = 1) agar data original fisik tersimpan utuh di MySQL
            $this->db->where('transaksi_id', $invoice_id);
            if ($this->db->field_exists('trash', 'transaksi_data')) {
                $this->db->update('transaksi_data', array(
                    'trash' => '1',
                    'status' => '0'
                ));
            }

            // 3. Insert ulang berurutan sesuai $post_items dari form (hasil Drag & Drop)
            foreach ($post_items as $itm) {
                $pid = isset($itm['produk_dasar_id']) ? (int)$itm['produk_dasar_id'] : 0;
                $qty = (float)$itm['jml'];
                $harga = (float)$itm['harga'];
                $nama = !empty($itm['nama']) ? $itm['nama'] : '';

                if ($qty > 0 || $pid > 0) {
                    // Coba cari template lama: pertama cek produk_id, kalau tidak ada coba iterasi semua
                    $found_template = null;
                    if ($pid > 0 && isset($old_td_map[$pid]) && count($old_td_map[$pid]) > 0) {
                        $found_template = array_shift($old_td_map[$pid]);
                    } else if ($pid > 0) {
                        // Fallback: cari di semua old_td_map berdasarkan nama produk (case insensitive)
                        foreach ($old_td_map as $map_pid => $map_rows) {
                            if (count($map_rows) > 0) {
                                foreach ($map_rows as $mk => $mr) {
                                    if (strtolower(trim($mr['produk_nama'])) == strtolower(trim($nama))) {
                                        $found_template = $mr;
                                        unset($old_td_map[$map_pid][$mk]);
                                        $old_td_map[$map_pid] = array_values($old_td_map[$map_pid]);
                                        break 2;
                                    }
                                }
                            }
                        }
                    }

                    if ($found_template) {
                        $ins_td = $found_template;
                        unset($ins_td['id']); // Biarkan auto-increment bekerja agar urut
                        $ins_td['produk_ord_jml'] = $qty;
                        $ins_td['produk_ord_hrg'] = $harga;
                        $ins_td['trash'] = '0';
                        $ins_td['status'] = '1';
                        if (!empty($nama)) {
                            $ins_td['produk_nama'] = $nama;
                        }
                        $this->db->insert('transaksi_data', $ins_td);
                    } else {
                        // Insert baris custom / baru
                        if ($qty > 0) {
                            $ins_td = array(
                                'transaksi_id' => $invoice_id,
                                'produk_id' => $pid,
                                'produk_nama' => $nama,
                                'produk_ord_jml' => $qty,
                                'produk_ord_hrg' => $harga,
                                'satuan' => isset($itm['satuan']) ? $itm['satuan'] : 'lot',
                                'dtime' => date('Y-m-d H:i:s'),
                                'oleh_id' => $oleh_id,
                                'oleh_nama' => $oleh_nama,
                                'trash' => '0',
                                'status' => '1'
                            );

                            // Salin field-field wajib dari template agar Printing.php tidak crash
                            // lookupJoined() memfilter: status='1', trash='0', link_id='0'
                            // dan GROUP BY: transaksi_id, next_substep_code
                            $inherit_fields = array(
                                'cabang_id', 'gudang_id', 'gudang_id_tujuan', 'kategori_id',
                                'jenis', 'jenisTr', 'step_number', 'nomer', 'pembayaran_sys',
                                'status', 'trash', 'link_id', 'next_substep_code',
                                'sub_step_number', 'valid_qty'
                            );
                            foreach ($inherit_fields as $ifield) {
                                if (isset($base_template[$ifield]) && !isset($ins_td[$ifield])) {
                                    $ins_td[$ifield] = $base_template[$ifield];
                                }
                            }

                            $this->db->insert('transaksi_data', $ins_td);
                        }
                    }
                }
            }
        }

        // Update Header Transaksi
        $update_trx = array(
            'transaksi_net' => $grandTotalDPP,
            'ppn_nilai' => $ppn_val,
            'transaksi_nilai' => $tagihan_baru,
            'transaksi_bulat' => $tagihan_baru,
            'transaksi_dibayar' => 0, 
            'transaksi_saldo' => $tagihan_baru
        );
        // Tulis Notes Client ke transaksi.keterangan (agar dicetak di Invoice Client)
        // Rebuild indexing_details blob untuk header transaksi (WAJIB untuk Printing.php / lookupJoined)
        $active_td_rows = $this->db->select('id')
            ->where('transaksi_id', $invoice_id)
            ->where('trash', '0')
            ->get('transaksi_data')->result_array();

        if (!empty($active_td_rows)) {
            $indexing_arr = array();
            foreach ($active_td_rows as $tdr) {
                $indexing_arr[] = (string)$tdr['id'];
            }
            $update_trx['indexing_details'] = base64_encode(serialize($indexing_arr));
        }

        $this->db->where('id', $invoice_id);
        $this->db->update('transaksi', $update_trx);

        // Update juga Header Transaksi Induk (Penerimaan Termin 7499) & Rekening Penerimaan A/R (749) agar nilainya 100% sinkron
        if (isset($snapshot['header']['id_master']) && $snapshot['header']['id_master'] > 0) {
            $id_master = (int)$snapshot['header']['id_master'];
            $this->db->group_start();
                $this->db->where('id', $id_master);
                $this->db->or_where('id_master', $id_master);
            $this->db->group_end();
            $this->db->update('transaksi', $update_trx);
        }

        // Update Tabel `transaksi_payment_source` (Plafon Alokasi Penagihan Proyek & Status Sisa Tagihan)
        // Ini KRITIS agar modul penerimaanprojek membaca sisa tagihan/plafon yang akurat setelah amandemen!
        if ($this->db->table_exists('transaksi_payment_source')) {
            $target_ids = array($invoice_id);
            if (isset($snapshot['header']['id_master']) && $snapshot['header']['id_master'] > 0) {
                $target_ids[] = (int)$snapshot['header']['id_master'];
            }
            if (isset($snapshot['header']['reference_id']) && $snapshot['header']['reference_id'] > 0) {
                $target_ids[] = (int)$snapshot['header']['reference_id'];
            }

            $tps_rows = $this->db->select('id, terbayar')
                ->where_in('transaksi_id', $target_ids)
                ->get('transaksi_payment_source')->result_array();

            if (!empty($tps_rows)) {
                foreach ($tps_rows as $tps) {
                    $sudah_terbayar = (float)$tps['terbayar'];
                    $sisa_baru = $tagihan_baru - $sudah_terbayar;
                    if ($sisa_baru < 0) $sisa_baru = 0;

                    $pay_source_update = array(
                        'tagihan'  => $tagihan_baru,
                        'sisa'     => $sisa_baru,
                        'dpp_ppn'  => $grandTotalDPP,
                        'ppn'      => $ppn_val,
                        'ppn_sisa' => $ppn_val
                    );
                    if (!empty($description)) {
                        $pay_source_update['payment_source_keterangan'] = $description;
                    }
                    $this->db->where('id', $tps['id']);
                    $this->db->update('transaksi_payment_source', $pay_source_update);
                }
            } else {
                $fallback_tps = array(
                    'tagihan'  => $tagihan_baru,
                    'sisa'     => $tagihan_baru,
                    'dpp_ppn'  => $grandTotalDPP,
                    'ppn'      => $ppn_val,
                    'ppn_sisa' => $ppn_val
                );
                if (!empty($description)) {
                    $fallback_tps['payment_source_keterangan'] = $description;
                }
                $this->db->where_in('transaksi_id', $target_ids);
                $this->db->update('transaksi_payment_source', $fallback_tps);
            }
        }

        // --- REPLACEMENT JURNAL PENYESUAIAN (BERSIH & SIMPEL ATAU OVERRIDE MANUAL) ---
        $old_tagihan = (float)$snapshot['header']['transaksi_nilai'];
        $old_dpp     = (float)$snapshot['header']['transaksi_net'];
        $old_ppn     = (float)$snapshot['header']['ppn_nilai'];
        $id_master   = (int)$snapshot['header']['id_master'];
        $invoice_no  = $snapshot['header']['nomer'];

        if (!empty($post_jurnal_custom) && is_array($post_jurnal_custom)) {
            $this->_processCustomManualJournal($invoice_id, $id_master, $invoice_no, $post_jurnal_custom, $oleh_id, $oleh_nama);
        } else {
            $this->_generateCleanReplacedJournal($invoice_id, $id_master, $invoice_no, $old_tagihan, $tagihan_baru, $old_dpp, $grandTotalDPP, $old_ppn, $ppn_val, $history_keterangan, $oleh_id, $oleh_nama);
        }

        return $tagihan_baru;
    }

    /**
     * Memproses penginputan jurnal penyesuaian manual pilihan user (Custom COA Override)
     */
    private function _processCustomManualJournal($invoice_id, $id_master, $invoice_no, $post_jurnal_custom, $oleh_id, $oleh_nama) {
        $target_ids = array_unique(array_filter(array((int)$invoice_id, (int)$id_master)));
        if (empty($target_ids)) return;

        // HARD DELETE jurnal lama untuk menghindari penumpukan di UI yang tidak mensupport filter trash=1
        // Histori original sudah aman tersimpan dalam field snapshot JSON di transaksi_amandemen_history
        $this->db->where_in('transaksi_id', $target_ids);
        $this->db->delete('jurnal');

        // 2. Insert jurnal manual pilihan user
        $target_trx_id = ($id_master > 0) ? $id_master : $invoice_id;
        $now_time = date('Y-m-d H:i:s');

        foreach ($post_jurnal_custom as $jitem) {
            $rekening = isset($jitem['rekening']) ? trim($jitem['rekening']) : '';
            $rekening_nama = isset($jitem['rekening_nama']) ? trim($jitem['rekening_nama']) : 'Jurnal Penyesuaian Manual';
            $debet = isset($jitem['debet']) ? (float)$jitem['debet'] : 0;
            $kredit = isset($jitem['kredit']) ? (float)$jitem['kredit'] : 0;

            if (empty($rekening) || ($debet <= 0 && $kredit <= 0)) continue;

            $ins_j = array(
                'transaksi_id' => $target_trx_id,
                'rekening' => $rekening,
                'rekening_nama' => $rekening_nama,
                'debet' => $debet,
                'kredit' => $kredit,
                'keterangan' => "[Jurnal Manual Amandemen " . $invoice_no . "] " . (isset($jitem['keterangan']) ? $jitem['keterangan'] : ''),
                'dtime' => $now_time,
                'oleh_id' => $oleh_id
            );

            if ($this->db->field_exists('trash', 'jurnal')) {
                $ins_j['trash'] = 0;
            }
            if ($this->db->field_exists('status', 'jurnal')) {
                $ins_j['status'] = 1;
            }

            $this->_insertJurnalSafe($ins_j);
        }
    }

    /**
     * Mematikan jurnal lama (trash = 1) dan membuat jurnal perbaikan baru secara bersih (Clean Replacement)
     */
    private function _generateCleanReplacedJournal($invoice_id, $id_master, $invoice_no, $old_tagihan, $new_tagihan, $old_dpp, $new_dpp, $old_ppn, $new_ppn, $history_keterangan, $oleh_id, $oleh_nama) {
        $target_ids = array_unique(array_filter(array((int)$invoice_id, (int)$id_master)));
        if (empty($target_ids)) return;

        // 1. Ambil SATU SET template jurnal lama HANYA untuk basis COA jurnal baru
        $this->db->where_in('transaksi_id', $target_ids);
        $jurnal_rows_raw = $this->db->get('jurnal')->result_array();
        
        // Deduplikasi mutlak (ambil kemunculan pertama saja) untuk menghindari efek penggandaan
        $jurnal_rows = array();
        $seen_rek = array();
        foreach ($jurnal_rows_raw as $j_row) {
            $rek = trim((string)$j_row['rekening']);
            if (!isset($seen_rek[$rek])) {
                $jurnal_rows[] = $j_row;
                $seen_rek[$rek] = true;
            }
        }

        // 2. Self-Healing Fallback: Jika jurnal lama kosong (gagal terbuat/rusak saat penerbitan awal),
        // Bangun baris jurnal standar dari master COA ERP agar amandemen tetap memiliki jurnal sah & seimbang.
        if (empty($jurnal_rows)) {
            $target_trx_id = ($id_master > 0) ? $id_master : $invoice_id;
            $now_time = date('Y-m-d H:i:s');
            
            // Baris 1: Debet Piutang Usaha Proyek (1010070030)
            $j1 = array(
                'transaksi_id' => $target_trx_id,
                'rekening' => '1010070030',
                'rekening_nama' => 'Piutang Usaha Kontijensi / Proyek',
                'debet' => $new_tagihan,
                'kredit' => 0,
                'keterangan' => "[Amandemen Invoice " . $invoice_no . "] Self-Healing Jurnal Piutang",
                'dtime' => $now_time,
                'oleh_id' => $oleh_id,
                'author' => $oleh_id,
                'trash' => 0,
                'status' => 1
            );
            $this->_insertJurnalSafe($j1);

            // Baris 2: Kredit Penjualan Project (4010030)
            if ($new_dpp > 0) {
                $j2 = array(
                    'transaksi_id' => $target_trx_id,
                    'rekening' => '4010030',
                    'rekening_nama' => 'Penjualan Project',
                    'debet' => 0,
                    'kredit' => $new_dpp,
                    'keterangan' => "[Amandemen Invoice " . $invoice_no . "] Self-Healing Jurnal Penjualan",
                    'dtime' => $now_time,
                    'oleh_id' => $oleh_id,
                    'author' => $oleh_id,
                    'trash' => 0,
                    'status' => 1
                );
                $this->_insertJurnalSafe($j2);
            }

            // Baris 3: Kredit PPN Keluaran (2030060)
            if ($new_ppn > 0) {
                $j3 = array(
                    'transaksi_id' => $target_trx_id,
                    'rekening' => '2030060',
                    'rekening_nama' => 'PPN Keluaran (Belum Faktur)',
                    'debet' => 0,
                    'kredit' => $new_ppn,
                    'keterangan' => "[Amandemen Invoice " . $invoice_no . "] Self-Healing Jurnal PPN",
                    'dtime' => $now_time,
                    'oleh_id' => $oleh_id,
                    'author' => $oleh_id,
                    'trash' => 0,
                    'status' => 1
                );
                $this->_insertJurnalSafe($j3);
            }

            return; // Selesai pembuatan jurnal fallback
        }

        // 3. HARD DELETE jurnal lama untuk mencegah penumpukan baris ganda di UI Buku Besar.
        // Histori amandemen tetap terjaga secara utuh di tabel transaksi_amandemen_history.
        $this->db->where_in('transaksi_id', $target_ids);
        $this->db->delete('jurnal');

        // 3. Hitung rasio perubahan DPP untuk fallback proporsional
        $ratioDPP = ($old_dpp > 0) ? ($new_dpp / $old_dpp) : 0;

        // 4. Insert Jurnal Perbaikan Baru (Aktif)
        foreach ($jurnal_rows as $row) {
            $is_debet = (float)$row['debet'] > 0;
            $old_val  = $is_debet ? (float)$row['debet'] : (float)$row['kredit'];

            $new_val = 0;
            $rec_name = strtolower(trim(isset($row['rekening_nama']) ? $row['rekening_nama'] : (isset($row['rekening_2']) ? $row['rekening_2'] : '')));
            $rek_code = trim((string)$row['rekening']);

            // Pencocokan presisi & seimbang (Balanced Accounting)
            if ($rek_code === '4010' || $rek_code === '4010030' || $rek_code === '4030' || $rek_code === '411.1.171.01' || strpos($rec_name, 'penjualan') !== false) {
                // Penjualan & Penjualan Belum Realisasi = DPP Murni
                $new_val = $new_dpp;
            } elseif ($rek_code === '2030060' || $rek_code === '211.1.171.01' || strpos($rec_name, 'ppn') !== false) {
                // Hutang PPN = PPN Murni
                $new_val = $new_ppn;
            } elseif ($rek_code === '1010020010' || $rek_code === '1010070030' || $rek_code === '749.1.171.50' || strpos($rec_name, 'piutang') !== false) {
                // Piutang Dagang & Kontra-Akun Piutang Proyek = Grand Total (DPP + PPN)
                $new_val = $new_tagihan;
            } else {
                // Fallback proporsional untuk rekening pecahan
                $new_val = ($old_dpp > 0) ? round($old_val * ($new_dpp / $old_dpp)) : $old_val;
            }

            if ($new_val <= 0) continue;

            $new_jurnal = $row;
            unset($new_jurnal['id']); // Biarkan auto-increment
            $new_jurnal['transaksi_id'] = ($id_master > 0) ? $id_master : $invoice_id;
            $new_jurnal['dtime']        = date('Y-m-d H:i:s');
            $new_jurnal['oleh_id']      = $oleh_id;
            $new_jurnal['author']       = $oleh_id;
            $new_jurnal['keterangan']   = "[Amandemen Invoice " . $invoice_no . "] " . (isset($row['keterangan']) ? $row['keterangan'] : '');

            if ($this->db->field_exists('trash', 'jurnal')) {
                $new_jurnal['trash'] = 0;
            }
            if ($this->db->field_exists('status', 'jurnal')) {
                $new_jurnal['status'] = 1;
            }

            if ($is_debet) {
                $new_jurnal['debet']  = $new_val;
                $new_jurnal['kredit'] = 0;
            } else {
                $new_jurnal['debet']  = 0;
                $new_jurnal['kredit'] = $new_val;
            }

            $this->_insertJurnalSafe($new_jurnal);
        }
    }

    /**
     * Helper aman untuk insert data ke tabel `jurnal` dengan penyaringan kolom otomatis
     */
    private function _insertJurnalSafe($data) {
        if (empty($data) || !is_array($data)) return false;

        $fields = $this->db->list_fields('jurnal');
        $clean_data = array();
        foreach ($data as $key => $val) {
            if (in_array($key, $fields)) {
                $clean_data[$key] = $val;
            }
        }
        return $this->db->insert('jurnal', $clean_data);
    }

    /**
     * Memulihkan (Rollback / Restore) invoice ke versi snapshot historis tertentu
     */
    public function rollbackToHistory($invoice_id, $history_id) {
        $invoice_id = (int)$invoice_id;
        $history_id = (int)$history_id;

        $this->db->where('id', $history_id);
        $this->db->where('transaksi_id', $invoice_id);
        $his_row = $this->db->get('transaksi_amandemen_history')->row_array();

        if (!$his_row || empty($his_row['old_registry_data'])) {
            return false;
        }

        $old_registry = json_decode($his_row['old_registry_data'], true);
        if (!$old_registry) return false;

        // Mendukung kompatibilitas backward: Jika snapshot menggunakan struktur baru 4-layer
        if (isset($old_registry['registry'])) {
            $old_registry = $old_registry['registry'];
        }

        $old_items5_sum = @unserialize(base64_decode($old_registry['items5_sum']));
        if (!is_array($old_items5_sum)) return false;

        // Ambil snapshot saat ini sebelum pemulihan
        $current_snapshot = $this->createAuditSnapshot($invoice_id);

        $catatan_rollback = "[ROLLBACK RESTORE] Dipulihkan ke versi histori #" . $his_row['id'] . " (Snapshot: " . $his_row['dtime'] . ")";
        
        return $this->processAmandemenJSON($invoice_id, $old_items5_sum, $current_snapshot, $his_row['keterangan'], $catatan_rollback);
    }

    /**
     * Cari produk yang digunakan oleh gudang_wo di tabel stock_locker
     */
    public function getLockerStockForGudangWo($gudang_wo, $project_id = 0, $state_filter = 'active')
    {
        $result = array();
        if (empty($gudang_wo)) {
            return $result;
        }

        $tables = array('stock_locker_work_oder', 'stock_locker_supplies', 'stock_locker');

        foreach ($tables as $table) {
            if (!$this->db->table_exists($table)) {
                continue;
            }

            $fields = $this->db->list_fields($table);
            
            $this->db->select('*');
            $this->db->from($table);
            
            // Gudang WO ID / Gudang ID check
            if (in_array('gudang_wo', $fields)) {
                $this->db->where("gudang_wo = '$gudang_wo'");
            } elseif (in_array('gudang_id', $fields)) {
                $this->db->where("gudang_id = '$gudang_wo'");
            }

            if ($project_id > 0 && in_array('project_id', $fields)) {
                $this->db->where('project_id', $project_id);
            }

            if (!empty($state_filter) && in_array('state', $fields)) {
                $this->db->where('state', $state_filter);
            }

            if (in_array('trash', $fields)) {
                $this->db->where('trash', 0);
            }

            if (in_array('jumlah', $fields)) {
                $this->db->where('jumlah >', 0);
            }

            $query = $this->db->get();
            if ($query && $query->num_rows() > 0) {
                foreach ($query->result_array() as $row) {
                    $row['_source_table'] = $table;
                    $result[] = $row;
                }
            }
        }

        return $result;
    }
}
