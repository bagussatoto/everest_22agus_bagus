<?php
/**
 * [AGENT_LOG]
 * ROLE      : Software Engineer Agent
 * PURPOSE   : Eksekusi simpan amandemen (ACID) dengan pemisahan Notes Client & Catatan Internal Amandemen
 * COMPLIANCE: CI3 Database Transaction (Strict), ISO 9001
 * LOG_EXPIRE: 2026-11-07
 * [/AGENT_LOG]
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . "modules/amandemen_invoice/controllers/Modul_Controller.php";

class FollowUp extends Modul_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Coms/ComAmandemenInvoice');
    }

    public function validate_and_save($invoice_id) {
// START OF COMPLETE REPEATED LOGIC
        $invoice_id = (int) $invoice_id;
        $description = $this->input->post('description'); // Notes Client (Dicetak di Invoice)
        $catatan_amandemen = $this->input->post('catatan_amandemen'); // Catatan Amandemen Khusus Internal
        $post_items = $this->input->post('items');
        $post_jurnal_custom = $this->input->post('jurnal_custom'); // Custom Manual Journal Override

        if (empty($post_items) || !is_array($post_items)) {
            die('Gagal: Tidak ada item produk yang ditagihkan.');
        }

        // 1. Snapshot lama
        $snapshot = $this->ComAmandemenInvoice->createAuditSnapshot($invoice_id);
        if (!$snapshot) {
            die('Gagal membuat snapshot audit.');
        }

        // Lapis 0 Blocker: Cek status pajak Approved DJP (PER-03/PJ/2022)
        $tax_info = $this->ComAmandemenInvoice->checkTaxStatus($invoice_id);
        if ($tax_info['status'] === 'APPROVED_DJP') {
            die('Gagal: Invoice ini tidak dapat diamandemen karena sudah memiliki Faktur Pajak Resmi yang disetujui DJP (Nomor e-Faktur: ' . $tax_info['efaktur'] . '). Harap selesaikan prosedur Faktur Pengganti (011) atau Nota Retur di Modul Taxes terlebih dahulu.');
        }

        // Lapis 1 Blocker: Cek apakah tagihan sudah dibayar (dicicil/lunas)
        $this->db->select('transaksi_dibayar');
        $this->db->where('id', $invoice_id);
        $cek_bayar = $this->db->get('transaksi')->row_array();
        if ($cek_bayar && (float)$cek_bayar['transaksi_dibayar'] > 0) {
            die('Gagal: Invoice tidak dapat diamandemen karena sudah diproses Penerimaan (A/R Receipt) sebesar Rp ' . number_format($cek_bayar['transaksi_dibayar'], 0, ',', '.') . '. Harap batalkan Penerimaan tersebut terlebih dahulu.');
        }

        $this->db->trans_start();

        // 2. Kalkulasi ulang JSON & eksekusi langsung ke transaksi
        $tagihan_baru = $this->ComAmandemenInvoice->processAmandemenJSON($invoice_id, $post_items, $snapshot, $description, $catatan_amandemen, $post_jurnal_custom);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE || $tagihan_baru === false) {
            $err = $this->db->error();
            die('Transaksi database gagal diselesaikan. Error: ' . json_encode($err));
        }

        echo "<h2>Amandemen Berhasil Disimpan!</h2>";
        echo "<p>Total Tagihan Invoice kini telah berubah menjadi: <b>Rp " . number_format($tagihan_baru, 0, ',', '.') . "</b></p>";
        echo "<script>
                setTimeout(function(){ 
                    if(window.parent) {
                        window.parent.location.reload(); 
                    }
                }, 3000);
              </script>";
    }

    public function restore_history($invoice_id, $history_id) {
        $invoice_id = (int)$invoice_id;
        $history_id = (int)$history_id;

        // Lapis 1 Blocker: Cek pembayaran A/R Receipt
        $this->db->select('transaksi_dibayar');
        $this->db->where('id', $invoice_id);
        $cek_bayar = $this->db->get('transaksi')->row_array();
        if ($cek_bayar && (float)$cek_bayar['transaksi_dibayar'] > 0) {
            die('Gagal: Invoice tidak dapat dipulihkan karena sudah ada pembayaran A/R Receipt sebesar Rp ' . number_format($cek_bayar['transaksi_dibayar'], 0, ',', '.'));
        }

        $this->db->trans_start();

        $tagihan_baru = $this->ComAmandemenInvoice->rollbackToHistory($invoice_id, $history_id);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE || $tagihan_baru === false) {
            die('Gagal memulihkan ke versi histori.');
        }

        echo "<h2>Pemulihan Versi Berhasil!</h2>";
        echo "<p>Invoice telah dipulihkan ke versi histori #" . $history_id . ". Nilai tagihan aktif kini: <b>Rp " . number_format($tagihan_baru, 0, ',', '.') . "</b></p>";
        echo "<script>
                setTimeout(function(){ 
                    if(window.parent) {
                        window.parent.location.reload(); 
                    }
                }, 3000);
              </script>";
    }
}
