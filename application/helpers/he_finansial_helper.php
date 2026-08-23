<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Helper Finansial Global & Pembulatan Presisi (ISO 20022 / ISO 80000-1)
 * Kompatibel 100% dengan PHP 5.6 dan CodeIgniter 3.1.8
 */

// START OF COMPLETE REPEATED LOGIC
if (!function_exists('fin_sub')) {
    /**
     * Pengurangan string desimal presisi tinggi (BC Math)
     * Mengeliminasi floating-point precision bug pada PHP
     */
    function fin_sub($val1, $val2, $precision = 4)
    {
        $v1 = isset($val1) && strlen(trim($val1)) > 0 ? strval($val1) : "0";
        $v2 = isset($val2) && strlen(trim($val2)) > 0 ? strval($val2) : "0";
        return bcsub($v1, $v2, intval($precision));
    }
}

if (!function_exists('fin_add')) {
    /**
     * Penambahan string desimal presisi tinggi (BC Math)
     */
    function fin_add($val1, $val2, $precision = 4)
    {
        $v1 = isset($val1) && strlen(trim($val1)) > 0 ? strval($val1) : "0";
        $v2 = isset($val2) && strlen(trim($val2)) > 0 ? strval($val2) : "0";
        return bcadd($v1, $v2, intval($precision));
    }
}

if (!function_exists('fin_mul')) {
    /**
     * Perkalian string desimal presisi tinggi (BC Math)
     */
    function fin_mul($val1, $val2, $precision = 4)
    {
        $v1 = isset($val1) && strlen(trim($val1)) > 0 ? strval($val1) : "0";
        $v2 = isset($val2) && strlen(trim($val2)) > 0 ? strval($val2) : "0";
        return bcmul($v1, $v2, intval($precision));
    }
}

if (!function_exists('fin_div')) {
    /**
     * Pembagian string desimal presisi tinggi (BC Math)
     */
    function fin_div($val1, $val2, $precision = 4)
    {
        $v1 = isset($val1) && strlen(trim($val1)) > 0 ? strval($val1) : "0";
        $v2 = isset($val2) && strlen(trim($val2)) > 0 ? strval($val2) : "0";
        if (bccomp($v2, "0", intval($precision)) == 0) {
            return "0";
        }
        return bcdiv($v1, $v2, intval($precision));
    }
}

if (!function_exists('fin_is_zero')) {
    /**
     * Pengecekan sisa nol mutlak (Epsilon Comparison)
     * Mengembalikan true jika nilai bernilai 0 (bebas dari sisa desimal mikro 0.000000000001)
     */
    function fin_is_zero($val, $precision = 4)
    {
        $v = isset($val) && strlen(trim($val)) > 0 ? strval($val) : "0";
        $clean = bcsub($v, "0", intval($precision));
        return bccomp($clean, "0", intval($precision)) == 0;
    }
}

if (!function_exists('fin_round')) {
    /**
     * Pembulatan Akuntansi Perbankan (Bankers Rounding ISO 80000-1)
     */
    function fin_round($val, $precision = 0)
    {
        $v = isset($val) ? floatval($val) : 0.0;
        return round($v, intval($precision), PHP_ROUND_HALF_EVEN);
    }
}
// END OF COMPLETE REPEATED LOGIC
