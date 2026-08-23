<?php
/**
 * Created by PhpStorm.
 * User: aziz
 * Date: 11/22/2018
 * Time: 8:38 PM
 */
$config["coTransaksiCore"] = array(

    "999" => array(
        "counters" => array(
            "stepCode|placeID",
            "stepCode|olehID",
            "stepCode|placeID|olehID",
        ),
        "formatNota" => "stepCode|placeID",
        "valueGates" => array(//==sumber nilai yang dikirim kemana2
            "master" => array(//==sumber nilai utama

            ),
            "detail" => array(//===sumber nilai berupa rincian

            ),
        ),
        "valueBuilders" => array(
            //            "debet" => "debet",
            //            "kredit" => "kredit",
        ),
        "preProcessor" => array(),
        "tableIn" => array(
            "master" => array(
                "jenis_master" => "jenisTrMaster",
                "jenis_top" => "jenisTrTop",
                "jenis" => "jenisTr",
                "jenis_label" => "jenisTrName",
                "div_id" => "divID",
                "div_nama" => "divName",
                "dtime" => "dtime",
                "fulldate" => "fulldate",
                "oleh_id" => "olehID",
                "oleh_nama" => "olehName",

                "cabang2_id" => "placeID",
                "cabang2_nama" => "placeName",

                "cabang_id" => "placeID",
                "cabang_nama" => "placeName",
                "transaksi_nilai" => "srcDefValue",
                "transaksi_jenis" => "jenisTr",
                "keterangan" => "description",

                "gudang_id" => "gudangID",
                "gudang_nama" => "gudangName",
                "gudang2_id" => "gudang2ID",
                "gudang2_nama" => "gudang2Name",
            ),
            "mainValues" => array(
                "debet" => "debet",
                "kredit" => "kredit",
            ),
            "detail" => array(
                "dtime" => "dtime",
                "produk_id" => "id",
                "produk_kode" => "code",
                "produk_label" => "label",
                "produk_nama" => "name",
                "produk_ord_jml" => "jml",
                "produk_ord_hrg" => "harga",
                "hpp" => "harga",
                "satuan" => "satuan",
            ),
            "detailValues" => array(
                "debet" => "debet",
                "kredit" => "kredit",
            ),

        ),
        "tableIn_static" => array(
            "master" => array(
                "trash" => 0,
            ),
            "detail" => array(
                "trash" => 0,
                "produk_jenis" => "account",
            ),
        ),
        "components" => array(
            "999" => array(
                "master" => array(
                    array(
                        "comName" => "Jurnal",
                        "loop" => array(
                            "{srcAccount}" => "srcDefValue",
                            "{targetAccount}" => "targetDefValue",
                            "{target3Account}" => "target3DefValue",
                            "{target4Account}" => "target4DefValue",
                        ),
                        "static" => array(
                            "cabang_id" => "placeID",
                            "jenis" => "jenisTr",
                            // "transaksi_no" => "nomer",
                        ),
                        "srcGateName" => "main",
                        "srcRawGateName" => "main",
                    ),
                    array(
                        "comName" => "Rekening",
                        "loop" => array(
                            "{srcAccount}" => "srcDefValue",
                            "{targetAccount}" => "targetDefValue",
                            "{target3Account}" => "target3DefValue",
                            "{target4Account}" => "target4DefValue",
                        ),
                        "static" => array(
                            "cabang_id" => "placeID",
                            "jenis" => "jenisTr",
                            // "transaksi_no" => "nomer",
                        ),
                        "srcGateName" => "main",
                        "srcRawGateName" => "main",
                    ),

                    array(
                        "comName" => "{srcRel}",
                        "loop" => array(
                            "{srcAccount}" => "srcDefValue",
                        ),
                        "static" => array(
                            "cabang_id" => "placeID",
                            "extern_id" => "extern1",
                            "extern_nama" => "extern1__label",
                            "jenis" => "jenisTr",
                            // "transaksi_no" => "nomer",
                        ),
                        "srcGateName" => "main",
                        "srcRawGateName" => "main",
                    ),

//                    array(
//                        "comName" => "{targetRel}",
//                        "loop" => array(
//                            "{targetAccount}" => "targetDefValue",
//                        ),
//                        "static" => array(
//                            "cabang_id" => "placeID",
//                            "extern_id" => "extern2",
//                            "extern_nama" => "extern2__label",
//                            "jenis" => "jenisTr",
//                            // "transaksi_no" => "nomer",
//                        ),
//                        "srcGateName" => "main",
//                        "srcRawGateName" => "main",
//                    ),

//                    // MENGURANGI HUTANG KE KONSUMEN (UANG MUKA KONSUMEN)....
//                    array(
//                        "comName" => "RekeningPembantuCustomer",
//                        "loop" => array(
//                            "2010050" => "-srcDefValue",// hutang ke konsumen//
//                        ),
//                        "static" => array(
//                            "cabang_id" => "placeID",
//                            "extern_id" => ".2010050010",// uang muka konsumen
//                            "extern_nama" => ".Uang Muka Konsumen",
//                            "jenis" => "jenisTr",
//                            "transaksi_no" => "nomer",
//                        ),
//                        "srcGateName" => "main",
//                        "srcRawGateName" => "main",
//                    ),
//                    array(
//                        "comName" => "RekeningPembantuCustomerDetail",
//                        "loop" => array(
//                            "2010050" => "-srcDefValue",// hutang ke konsumen
//                        ),
//                        "static" => array(
//                            "cabang_id" => "placeID",
//                            "extern_id" => "extern1",
//                            "extern_nama" => "extern1__extern_nama",
//                            "extern2_id" => ".2010050010",// uang muka konsumen
//                            "extern2_nama" => ".Uang Muka Konsumen",
//                            "jenis" => "jenisTr",
//                            "transaksi_no" => "nomer",
//                        ),
//                        "srcGateName" => "main",
//                        "srcRawGateName" => "main",
//                    ),

//                    // MENAMBAH HUTANG KE KONSUMEN (UANG MUKA TANPA PPN)....
//                    array(
//                        "comName" => "RekeningPembantuCustomer",
//                        "loop" => array(
//                            "2010050" => "srcDefValue",// hutang ke konsumen Uang Muka Konsumen Tanpa Ppn
//                        ),
//                        "static" => array(
//                            "cabang_id" => "placeID",
//                            "extern_id" => ".2010050050",// Uang Muka Konsumen Tanpa Ppn
//                            "extern_nama" => ".Uang Muka Konsumen Tanpa Ppn",
//                            "jenis" => "jenisTr",
//                            "transaksi_no" => "nomer",
//                        ),
//                        "srcGateName" => "main",
//                        "srcRawGateName" => "main",
//                    ),
//                    array(
//                        "comName" => "RekeningPembantuCustomerDetail",
//                        "loop" => array(
//                            "2010050" => "srcDefValue",// hutang ke konsumen Uang Muka Konsumen Tanpa Ppn
//                        ),
//                        "static" => array(
//                            "cabang_id" => "placeID",
//                            "extern_id" => "extern1",
//                            "extern_nama" => "extern1__extern_nama",
//                            "extern2_id" => ".2010050050",// Uang Muka Konsumen Tanpa Ppn
//                            "extern2_nama" => ".Uang Muka Konsumen Tanpa Ppn",
//                            "jenis" => "jenisTr",
//                            "transaksi_no" => "nomer",
//                        ),
//                        "srcGateName" => "main",
//                        "srcRawGateName" => "main",
//                    ),

                    array(
                        "comName" => "RekeningPembantuPenjualan",
                        "loop" => array(
                            "4010" => "srcDefValue",// penjualan, rek pembantu penjualan project
                        ),
                        "static" => array(
                            "cabang_id" => "placeID",
                            "extern_id" => ".4010030",
                            "extern_nama" => ".penjualan project",
                            "extern2_id" => ".0",
                            "extern2_nama" => "",
                            "extern4_id" => "extern2",
                            "extern4_nama" => "extern2__extern_nama",
                            "jenis" => "jenisTr",
                        ),
                        "srcGateName" => "main",
                        "srcRawGateName" => "main",
                    ),
                    array(
                        "comName" => "RekeningPembantuPenjualanProject",
                        "loop" => array(
                            "4010" => "srcDefValue",// penjualan, rek pembantu penjualan project
                        ),
                        "static" => array(
                            "cabang_id" => "placeID",
                            "extern_id" => "extern_project__id",//project
                            "extern_nama" => "extern_project__nama",//project
                            "extern2_id" => ".4010030",
                            "extern2_nama" => ".penjualan project",
                            "extern3_id" => ".0",//kontrak
                            "extern3_nama" => "note",//kontrak
                            "extern4_id" => "extern2",
                            "extern4_nama" => "extern2__extern_nama",
                            "jenis" => "jenisTr",
                        ),
                        "srcGateName" => "main",
                        "srcRawGateName" => "main",
                    ),

                    // MENAMBAH HUTANG KE KONSUMEN (UANG MUKA DIPOTONG PPN)....
                    array(
                        "comName" => "RekeningPembantuCustomer",
                        "loop" => array(
                            "2010050" => "targetDefValue",// hutang ke konsumen Uang Muka Konsumen Tanpa Ppn
                        ),
                        "static" => array(
                            "cabang_id" => "placeID",
                            "extern_id" => ".2010050060",// Uang Muka Konsumen Tanpa Ppn
                            "extern_nama" => ".Uang Muka Konsumen dipotong Ppn",
                            "jenis" => "jenisTr",
                            "transaksi_no" => "nomer",
                        ),
                        "srcGateName" => "main",
                        "srcRawGateName" => "main",
                    ),
                    array(
                        "comName" => "RekeningPembantuCustomerDetail",
                        "loop" => array(
                            "2010050" => "targetDefValue",// hutang ke konsumen Uang Muka Konsumen Tanpa Ppn
                        ),
                        "static" => array(
                            "cabang_id" => "placeID",
                            "extern_id" => "extern2",
                            "extern_nama" => "extern2__extern_nama",
                            "extern2_id" => ".2010050060",// Uang Muka Konsumen Tanpa Ppn
                            "extern2_nama" => ".Uang Muka Konsumen dipotong Ppn",
                            "jenis" => "jenisTr",
                            "transaksi_no" => "nomer",
                        ),
                        "srcGateName" => "main",
                        "srcRawGateName" => "main",
                    ),

//                    // CABANG, pindah hutang pph23 di cabang ke pusat
//                    array(
//                        "comName" => "Jurnal",
//                        "loop" => array(
//                            "2040010" => "srcDefValue",//hutang ke pusat
//                            "2030010" => "-srcDefValue",//hutang pph21
//                        ),
//                        "static" => array(
//                            "cabang_id" => "placeID",
//                            "jenis" => "jenisTr",
//                        ),
//                        "srcGateName" => "main",
//                        "srcRawGateName" => "main",
//                    ),
//                    array(
//                        "comName" => "Rekening",
//                        "loop" => array(
//                            "2040010" => "srcDefValue",//hutang ke pusat
//                            "2030010" => "-srcDefValue",//hutang pph21
//                        ),
//                        "static" => array(
//                            "cabang_id" => "placeID",
//                            "jenis" => "jenisTr",
//                        ),
//                        "srcGateName" => "main",
//                        "srcRawGateName" => "main",
//                    ),
//                    array(
//                        "comName" => "RekeningPembantuAntarcabang",
//                        "loop" => array(
//                            "2040010" => "srcDefValue",//hutang ke pusat
//                        ),
//                        "static" => array(
//                            "cabang_id" => "placeID",
//                            "extern_id" => ".-1",
//                            "extern_nama" => ".pusat",
//                            "jenis" => "jenisTr",
//                            "transaksi_no" => "nomer",
//                        ),
//                        "srcGateName" => "main",
//                        "srcRawGateName" => "main",
//                    ),
//
//                    // PUSAT, terima pindahan hutang pph23 dari cabang ke pusat
//                    array(
//                        "comName" => "Jurnal",
//                        "loop" => array(
//                            "1010060010" => "srcDefValue",//piutang cabang
//                            "2030010" => "srcDefValue",//hutang pph21
//                        ),
//                        "static" => array(
//                            "cabang_id" => ".-1",
//                            "jenis" => "jenisTr",
//                        ),
//                        "srcGateName" => "main",
//                        "srcRawGateName" => "main",
//                    ),
//                    array(
//                        "comName" => "Rekening",
//                        "loop" => array(
//                            "1010060010" => "srcDefValue",//piutang cabang
//                            "2030010" => "srcDefValue",//hutang pph21
//                        ),
//                        "static" => array(
//                            "cabang_id" => ".-1",
//                            "jenis" => "jenisTr",
//                        ),
//                        "srcGateName" => "main",
//                        "srcRawGateName" => "main",
//                    ),
//                    array(
//                        "comName" => "RekeningPembantuAntarcabang",
//                        "loop" => array(
//                            "1010060010" => "srcDefValue",//piutang cabang
//                        ),
//                        "static" => array(
//                            "cabang_id" => ".-1",
//                            "extern_id" => "placeID",
//                            "extern_nama" => "placeName",
//                            "jenis" => "jenisTr",
//                            "transaksi_no" => "nomer",
//                        ),
//                        "srcGateName" => "main",
//                        "srcRawGateName" => "main",
//                    ),
//                    array(
//                        "comName" => "RekeningPembantuPphMain",
//                        "loop" => array(
//                            "2030010" => "srcDefValue",//hutang pph21
//                        ),
//                        "static" => array(
//                            "cabang_id" => ".-1",
//                            "jenis" => "jenisTr",
//                            "transaksi_no" => "nomer",
//                            "harga" => "srcDefValue",
//                            "extern2_id" => ".11",
//                            "extern2_nama" => ".freelancer",
//                            "extern_id" => ".444",// diisi customer
//                            "extern_nama" => ".PT. SEJUK JAYA ABADI",// diisi customer
//                        ),
//                        "srcGateName" => "main",
//                        "srcRawGateName" => "main",
//                    ),
//
//                    // KAS DIBAWA SETOR KE PUSAT
//                    //region bagian cabang
//                    90 => array(
//                        "comName" => "Jurnal",
//                        "loop" => array(
//                            "2040010" => "-srcDefValue",// hutang ke pusat
//                            "1010010010" => "-srcDefValue",// kas
//                        ),
//                        "static" => array(
//                            "cabang_id" => "placeID",
//                            "jenis" => "jenisTr",
//                        ),
//                        "srcGateName" => "main",
//                        "srcRawGateName" => "main",
//                    ),
//                    91 => array(
//                        "comName" => "Rekening",
//                        "loop" => array(
//                            "2040010" => "-srcDefValue",// hutang ke pusat
//                            "1010010010" => "-srcDefValue",// kas
//                        ),
//                        "static" => array(
//                            "cabang_id" => "placeID",
//                            "jenis" => "jenisTr",
//                        ),
//                        "srcGateName" => "main",
//                        "srcRawGateName" => "main",
//                    ),
//                    92 => array(
//                        "comName" => "RekeningPembantuKas",
//                        "loop" => array(
//                            "1010010010" => "-srcDefValue",// kas
//                        ),
//                        "static" => array(
//                            "cabang_id" => "placeID",
//                            "extern_id" => "extern1",// diisi id bank
//                            "extern_nama" => "extern1__label",// diisi nama bank
//                            "jenis" => "jenisTr",
//                        ),
//                        "srcGateName" => "main",
//                        "srcRawGateName" => "main",
//                    ),
//                    93 => array(
//                        "comName" => "RekeningPembantuAntarcabang",
//                        "loop" => array(
//                            "2040010" => "-srcDefValue",// hutang ke pusat
//                        ),
//                        "static" => array(
//                            "cabang_id" => "placeID",
//                            "cabang2_id" => ".-1",
//                            "cabang2_nama" => ".PUSAT (DC)",
//                            "extern_id" => ".-1",
//                            "extern_nama" => ".PUSAT (DC)",
//                            "jenis" => "jenisTr",
//                        ),
//                        "srcGateName" => "main",
//                        "srcRawGateName" => "main",
//                    ),
//
//                    //region bagian pusat
//                    95 => array(
//                        "comName" => "Jurnal",
//                        "loop" => array(
//                            "1010060010" => "-srcDefValue",// piutang cabang
//                            "1010010010" => "srcDefValue",// kas
//                        ),
//                        "static" => array(
//                            "cabang_id" => ".-1",
//                            "jenis" => "jenisTr",
//                        ),
//                        "srcGateName" => "main",
//                        "srcRawGateName" => "main",
//                    ),
//                    96 => array(
//                        "comName" => "Rekening",
//                        "loop" => array(
//                            "1010060010" => "-srcDefValue",// piutang cabang
//                            "1010010010" => "srcDefValue",// kas
//                        ),
//                        "static" => array(
//                            "cabang_id" => ".-1",
//                            "jenis" => "jenisTr",
//                        ),
//                        "srcGateName" => "main",
//                        "srcRawGateName" => "main",
//                    ),
//                    97 => array(
//                        "comName" => "RekeningPembantuKas",
//                        "loop" => array(
//                            "1010010010" => "srcDefValue",// kas
//                        ),
//                        "static" => array(
//                            "cabang_id" => ".-1",
//                            "extern_id" => "extern1",// diisi id bank
//                            "extern_nama" => "extern1__label",// diisi nama bank
//                            "jenis" => "jenisTr",
//                        ),
//                        "srcGateName" => "main",
//                        "srcRawGateName" => "main",
//                    ),
//                    98 => array(
//                        "comName" => "RekeningPembantuAntarcabang",
//                        "loop" => array(
//                            "1010060010" => "-srcDefValue",// piutang cabang
//                        ),
//                        "static" => array(
//                            "cabang_id" => ".-1",
//                            "cabang2_id" => "placeID",
//                            "cabang2_nama" => "placeName",
//                            "extern_id" => "placeID",
//                            "extern_nama" => "placeName",
//                            "jenis" => "jenisTr",
//                        ),
//                        "srcGateName" => "main",
//                        "srcRawGateName" => "main",
//                    ),



                ),
                "detail" => array(),
            ),
        ),
        "postProcessor" => array(),
    ),

    "999_1" => array(
        "counters" => array(
            "stepCode|placeID",
            "stepCode|olehID",
            "stepCode|placeID|olehID",
        ),
        "formatNota" => "stepCode|placeID",
        "valueGates" => array(//==sumber nilai yang dikirim kemana2
            "master" => array(//==sumber nilai utama

            ),
            "detail" => array(//===sumber nilai berupa rincian

            ),
        ),
        "valueBuilders" => array(
            //            "debet" => "debet",
            //            "kredit" => "kredit",
        ),
        "preProcessor" => array(),
        "tableIn" => array(
            "master" => array(
                "jenis_master" => "jenisTrMaster",
                "jenis_top" => "jenisTrTop",
                "jenis" => "jenisTr",
                "jenis_label" => "jenisTrName",
                "div_id" => "divID",
                "div_nama" => "divName",
                "dtime" => "dtime",
                "fulldate" => "fulldate",
                "oleh_id" => "olehID",
                "oleh_nama" => "olehName",

                "cabang2_id" => "placeID",
                "cabang2_nama" => "placeName",

                "cabang_id" => "placeID",
                "cabang_nama" => "placeName",
                "transaksi_nilai" => "srcDefValue",
                "transaksi_jenis" => "jenisTr",
                "keterangan" => "description",

                "gudang_id" => "gudangID",
                "gudang_nama" => "gudangName",
                "gudang2_id" => "gudang2ID",
                "gudang2_nama" => "gudang2Name",
            ),
            "mainValues" => array(
                "debet" => "debet",
                "kredit" => "kredit",
            ),
            "detail" => array(
                "dtime" => "dtime",
                "produk_id" => "id",
                "produk_kode" => "code",
                "produk_label" => "label",
                "produk_nama" => "name",
                "produk_ord_jml" => "jml",
                "produk_ord_hrg" => "harga",
                "hpp" => "harga",
                "satuan" => "satuan",
            ),
            "detailValues" => array(
                "debet" => "debet",
                "kredit" => "kredit",
            ),

        ),
        "tableIn_static" => array(
            "master" => array(
                "trash" => 0,
            ),
            "detail" => array(
                "trash" => 0,
                "produk_jenis" => "account",
            ),
        ),
        "components" => array(
            "999_1" => array(
                "master" => array(
                    array(
                        "comName" => "JurnalSisi",
                        "loop" => array(
                            "{srcAccount}" => "srcDefValue",
                            //                            "{targetAccount}" => "targetDefValue",
                        ),
                        "static" => array(
                            "cabang_id" => "placeID",
                            "jenis" => "jenisTr",
                            // "transaksi_no" => "nomer",
                        ),
                        "srcGateName" => "main",
                        "srcRawGateName" => "main",
                    ),
                    array(
                        "comName" => "RekeningSisi",
                        "loop" => array(
                            "{srcAccount}" => "srcDefValue",
                            //                            "{targetAccount}" => "targetDefValue",
                        ),
                        "static" => array(
                            "cabang_id" => "placeID",
                            "jenis" => "jenisTr",
                            // "transaksi_no" => "nomer",
                        ),
                        "srcGateName" => "main",
                        "srcRawGateName" => "main",
                    ),


                    array(
                        "comName" => "{srcRel}",
                        "loop" => array(
                            "{srcAccount}" => "srcDefValue",
                        ),
                        "static" => array(
                            "cabang_id" => "placeID",
                            "extern_id" => "extern1",
                            "extern_nama" => "extern1__label",
                            "jenis" => "jenisTr",
                            // "transaksi_no" => "nomer",
                        ),
                        "srcGateName" => "main",
                        "srcRawGateName" => "main",
                    ),

                    //                    array(
                    //                        "comName"        => "{targetRel}",
                    //                        "loop"           => array(
                    //                            "{targetAccount}" => "targetDefValue",
                    //                        ),
                    //                        "static"         => array(
                    //                            "cabang_id"   => "placeID",
                    //                            "extern_id"   => "extern2",
                    //                            "extern_nama" => "extern2__label",
                    //                            "jenis"       => "jenisTr",
                    //                            // "transaksi_no" => "nomer",
                    //                        ),
                    //                        "srcGateName"    => "main",
                    //                        "srcRawGateName" => "main",
                    //                    ),
                ),
                "detail" => array(),
            ),
        ),
        "postProcessor" => array(),
    ),
    "999_0" => array(
        "counters" => array(
            "stepCode|placeID",
            "stepCode|olehID",
            "stepCode|placeID|olehID",
        ),
        "formatNota" => "stepCode|placeID",
        "valueGates" => array(//==sumber nilai yang dikirim kemana2
            "master" => array(//==sumber nilai utama

            ),
            "detail" => array(//===sumber nilai berupa rincian

            ),
        ),
        "valueBuilders" => array(
            //            "debet" => "debet",
            //            "kredit" => "kredit",
        ),
        "preProcessor" => array(),
        "tableIn" => array(
            "master" => array(
                "jenis_master" => "jenisTrMaster",
                "jenis_top" => "jenisTrTop",
                "jenis" => "jenisTr",
                "jenis_label" => "jenisTrName",
                "div_id" => "divID",
                "div_nama" => "divName",
                "dtime" => "dtime",
                "fulldate" => "fulldate",
                "oleh_id" => "olehID",
                "oleh_nama" => "olehName",

                "cabang2_id" => "placeID",
                "cabang2_nama" => "placeName",

                "cabang_id" => "placeID",
                "cabang_nama" => "placeName",
                "transaksi_nilai" => "srcDefValue",
                "transaksi_jenis" => "jenisTr",
                "keterangan" => "description",

                "gudang_id" => "gudangID",
                "gudang_nama" => "gudangName",
                "gudang2_id" => "gudang2ID",
                "gudang2_nama" => "gudang2Name",
            ),
            "mainValues" => array(
                "debet" => "debet",
                "kredit" => "kredit",
            ),
            "detail" => array(
                "dtime" => "dtime",
                "produk_id" => "id",
                "produk_kode" => "code",
                "produk_label" => "label",
                "produk_nama" => "name",
                "produk_ord_jml" => "jml",
                "produk_ord_hrg" => "harga",
                "hpp" => "harga",
                "satuan" => "satuan",
            ),
            "detailValues" => array(
                "debet" => "debet",
                "kredit" => "kredit",
            ),

        ),
        "tableIn_static" => array(
            "master" => array(
                "trash" => 0,
            ),
            "detail" => array(
                "trash" => 0,
                "produk_jenis" => "account",
            ),
        ),
        "components" => array(
            "999_0" => array(
                "master" => array(
                    array(
                        "comName" => "Jurnal",
                        "loop" => array(
                            "{srcAccount}" => "srcDefValue",
                            //                            "{targetAccount}" => "targetDefValue",
                        ),
                        "static" => array(
                            "cabang_id" => "placeID",
                            "jenis" => "jenisTr",
                            // "transaksi_no" => "nomer",
                        ),
                        "srcGateName" => "main",
                        "srcRawGateName" => "main",
                    ),
                    array(
                        "comName" => "Rekening",
                        "loop" => array(
                            "{srcAccount}" => "srcDefValue",
                            //                            "{targetAccount}" => "targetDefValue",
                        ),
                        "static" => array(
                            "cabang_id" => "placeID",
                            "jenis" => "jenisTr",
                            // "transaksi_no" => "nomer",
                        ),
                        "srcGateName" => "main",
                        "srcRawGateName" => "main",
                    ),


//                    array(
//                        "comName" => "{srcRel}",
//                        "loop" => array(
//                            "{srcAccount}" => "srcDefValue",
//                        ),
//                        "static" => array(
//                            "cabang_id" => "placeID",
//                            "extern_id" => "extern1",
//                            "extern_nama" => "extern1__label",
//                            "jenis" => "jenisTr",
//                            // "transaksi_no" => "nomer",
//                        ),
//                        "srcGateName" => "main",
//                        "srcRawGateName" => "main",
//                    ),

                    //                    array(
                    //                        "comName"        => "{targetRel}",
                    //                        "loop"           => array(
                    //                            "{targetAccount}" => "targetDefValue",
                    //                        ),
                    //                        "static"         => array(
                    //                            "cabang_id"   => "placeID",
                    //                            "extern_id"   => "extern2",
                    //                            "extern_nama" => "extern2__label",
                    //                            "jenis"       => "jenisTr",
                    //                            // "transaksi_no" => "nomer",
                    //                        ),
                    //                        "srcGateName"    => "main",
                    //                        "srcRawGateName" => "main",
                    //                    ),
                ),
                "detail" => array(),
            ),
        ),
        "postProcessor" => array(),
    ),

    //  config penyesuaian piutang, hutang, kas. pokoknya selain produk
    "888_1" => array(
        "counters" => array(
            "stepCode|placeID",
            "stepCode|olehID",
            "stepCode|placeID|olehID",
        ),
        "formatNota" => "stepCode|placeID",
        "valueGates" => array(//==sumber nilai yang dikirim kemana2
            "master" => array(//==sumber nilai utama
                "pihakName" => "pihakID",
            ),
            "detail" => array(//===sumber nilai berupa rincian
                "harga" => "harga",
            ),
        ),
        "valueBuilders" => array(),

        "preProcessor" => array(),
        "tableIn" => array(
            "master" => array(
                "jenis_master" => "jenisTrMaster",
                "jenis_top" => "jenisTrTop",
                "jenis" => "jenisTr",
                "jenis_label" => "jenisTrName",
                "div_id" => "divID",
                "div_nama" => "divName",
                "dtime" => "dtime",
                "fulldate" => "fulldate",
                "oleh_id" => "olehID",
                "oleh_nama" => "olehName",

                "cabang2_id" => "placeID",
                "cabang2_nama" => "placeName",

                "cabang_id" => "placeID",
                "cabang_nama" => "placeName",
                "transaksi_nilai" => "srcDefValue",
                "transaksi_jenis" => "jenisTr",
                "keterangan" => "description",

                "gudang_id" => "gudangID",
                "gudang_nama" => "gudangName",
                "gudang2_id" => "gudang2ID",
                "gudang2_nama" => "gudang2Name",
            ),
            "mainValues" => array(
                "debet" => "debet",
                "kredit" => "kredit",
            ),
            "detail" => array(
                "dtime" => "dtime",
                "produk_id" => "id",
                "produk_kode" => "code",
                "produk_label" => "label",
                "produk_nama" => "name",
                "produk_ord_jml" => "jml",
                "produk_ord_hrg" => "harga",
                "hpp" => "harga",
                "satuan" => "satuan",
            ),
            "detailValues" => array(
                "debet" => "debet",
                "kredit" => "kredit",
            ),

        ),
        "tableIn_static" => array(
            "master" => array(
                "trash" => 0,
            ),
            "detail" => array(
                "trash" => 0,
                "produk_jenis" => "account",
            ),
        ),
        "components" => array(
            "888_1" => array(
                "master" => array(
                    array(
                        "comName" => "Jurnal",
                        "loop" => array(
                            "{pihakName}" => "harga",
//                            "{srcAccount}" => "srcDefValue",
                            //                            "{targetAccount}" => "targetDefValue",
                        ),
                        "static" => array(
                            "cabang_id" => "placeID",
                            "jenis" => "jenisTr",
                            // "transaksi_no" => "nomer",
                        ),
                        "srcGateName" => "main",
                        "srcRawGateName" => "main",
                    ),
                    array(
                        "comName" => "Rekening",
                        "loop" => array(
                            "{pihakName}" => "harga",
//                            "{srcAccount}" => "srcDefValue",
                            //                            "{targetAccount}" => "targetDefValue",
                        ),
                        "static" => array(
                            "cabang_id" => "placeID",
                            "jenis" => "jenisTr",
                            // "transaksi_no" => "nomer",
                        ),
                        "srcGateName" => "main",
                        "srcRawGateName" => "main",
                    ),

//                    array(
//                        "comName"        => "{srcRel}",
//                        "loop"           => array(
//                            "{srcAccount}" => "harga",
//                        ),
//                        "static"         => array(
//                            "cabang_id"   => "placeID",
//                            "extern_id"   => "extern1",
//                            "extern_nama" => "extern1__label",
//                            "jenis"       => "jenisTr",
//                            // "transaksi_no" => "nomer",
//                        ),
//                        "srcGateName"    => "main",
//                        "srcRawGateName" => "main",
//                    ),
                ),
                "detail" => array(
                    array(
                        "comName" => "{srcRel}",
                        "loop" => array(
//                            "{srcAccount}" => "harga",
                            "{srcAccount}" => "subtotal",
                        ),
                        "static" => array(
                            "cabang_id" => "placeID",
                            "extern_id" => "id",
                            "extern_nama" => "name",
                            "qty" => "jml",
//                            "produk_qty" => "jml",
//                            "produk_qty" => "-jml",
                            "produk_nilai" => "harga",
                            "gudang_id" => "gudangID",
                            "jenis" => "jenisTr",
                            "transaksi_no" => "nomer",
                        ),
                        "srcGateName" => "items",
                        "srcRawGateName" => "items",
                    ),
                ),
            ),
        ),
        "postProcessor" => array(),
    ),
    "888_2" => array(
        "counters" => array(
            "stepCode|placeID",
            "stepCode|olehID",
            "stepCode|placeID|olehID",
        ),
        "formatNota" => "stepCode|placeID",
        "valueGates" => array(//==sumber nilai yang dikirim kemana2
            "master" => array(//==sumber nilai utama
                "pihakName" => "pihakID",
            ),
            "detail" => array(//===sumber nilai berupa rincian

            ),
        ),
        "valueBuilders" => array(),

        "preProcessor" => array(
            1 => array(
                "master" => array(),
                "detail" => array(
//                    array(
//                        "comName"        => "FifoAverage",
//                        "loop"           => array(),
//                        "static"         => array(
//                            "cabang_id"   => "placeID",
//                            "extern_id"   => "id",
//                            "extern_nama" => "name",
//                            "produk_qty"  => "qty",
//                            "gudang_id"   => "gudangID",
//                        ),
//                        "resultParams"   => array(
//                            "items" => array(
//                                "hpp" => "hpp",
//                            ),
//                        ),
//                        "srcGateName"    => "items",
//                        "srcRawGateName" => "items",
//                    ),
//                    array(
//                        "comName"        => "FifoProdukJadi",
//                        "loop"           => array(),
//                        "static"         => array(
//                            "cabang_id"   => "placeID",
//                            "extern_id"   => "id",
//                            "extern_nama" => "name",
//                            "produk_qty"  => "qty",
//                            "gudang_id"   => "gudangID",
//                        ),
//                        "resultParams"   => array(
//                            "rsltItems" => array(
//                                "id"       => "produk_id",
//                                "nama"     => "nama",
//                                "name"     => "nama",
//                                "harga"    => "hpp",
//                                "hpp"      => "hpp",
//                                "jml"      => "qty",
//                                "qty"      => "qty",
//                                "subtotal" => "subtotal",
//                            ),
//                        ),
//                        "srcGateName"    => "items",
//                        "srcRawGateName" => "items",
//                    ),
                ),
            ),
        ),
        "tableIn" => array(
            "master" => array(
                "jenis_master" => "jenisTrMaster",
                "jenis_top" => "jenisTrTop",
                "jenis" => "jenisTr",
                "jenis_label" => "jenisTrName",
                "div_id" => "divID",
                "div_nama" => "divName",
                "dtime" => "dtime",
                "fulldate" => "fulldate",
                "oleh_id" => "olehID",
                "oleh_nama" => "olehName",

                "cabang2_id" => "placeID",
                "cabang2_nama" => "placeName",

                "cabang_id" => "placeID",
                "cabang_nama" => "placeName",
                "transaksi_nilai" => "srcDefValue",
                "transaksi_jenis" => "jenisTr",
                "keterangan" => "description",

                "gudang_id" => "gudangID",
                "gudang_nama" => "gudangName",
                "gudang2_id" => "gudang2ID",
                "gudang2_nama" => "gudang2Name",
            ),
            "mainValues" => array(
                "debet" => "debet",
                "kredit" => "kredit",
            ),
            "detail" => array(
                "dtime" => "dtime",
                "produk_id" => "id",
                "produk_kode" => "code",
                "produk_label" => "label",
                "produk_nama" => "name",
                "produk_ord_jml" => "jml",
                "produk_ord_hrg" => "harga",
                "hpp" => "harga",
                "satuan" => "satuan",
            ),
            "detailValues" => array(
                "debet" => "debet",
                "kredit" => "kredit",
            ),

        ),
        "tableIn_static" => array(
            "master" => array(
                "trash" => 0,
            ),
            "detail" => array(
                "trash" => 0,
                "produk_jenis" => "account",
            ),
        ),
        "components" => array(
            "888_2" => array(
                "master" => array(
                    array(
                        "comName" => "Jurnal",
                        "loop" => array(
                            "{pihakName}" => "-harga",
//                            "{srcAccount}" => "srcDefValue",
                            //                            "{targetAccount}" => "targetDefValue",
                        ),
                        "static" => array(
                            "cabang_id" => "placeID",
                            "jenis" => "jenisTr",
                            // "transaksi_no" => "nomer",
                        ),
                        "srcGateName" => "main",
                        "srcRawGateName" => "main",
                    ),
                    array(
                        "comName" => "Rekening",
                        "loop" => array(
                            "{pihakName}" => "-harga",
//                            "{srcAccount}" => "srcDefValue",
                            //                            "{targetAccount}" => "targetDefValue",
                        ),
                        "static" => array(
                            "cabang_id" => "placeID",
                            "jenis" => "jenisTr",
                            // "transaksi_no" => "nomer",
                        ),
                        "srcGateName" => "main",
                        "srcRawGateName" => "main",
                    ),
                ),
                "detail" => array(
                    array(
                        "comName" => "{srcRel}",
                        "loop" => array(
                            "{srcAccount}" => "-subtotal",
//                            "{srcAccount}" => "-harga",
                        ),
                        "static" => array(
                            "cabang_id" => "placeID",
                            "extern_id" => "id",
                            "extern_nama" => "name",
                            "qty" => "-jml",
//                            "produk_qty" => "-jml",
                            "produk_nilai" => "-harga",
                            "gudang_id" => "gudangID",
                            "jenis" => "jenisTr",
                            "transaksi_no" => "nomer",
                        ),
                        "srcGateName" => "items",
                        "srcRawGateName" => "items",
                    ),
                ),
            ),
        ),
        "postProcessor" => array(),
    ),
    //  config penyesuaian produk. pokoknya selain piutang, hutang, kas
    "777_1" => array(
        "counters" => array(
            "stepCode|placeID",
            "stepCode|olehID",
            "stepCode|placeID|olehID",
        ),
        "formatNota" => "stepCode|placeID",
        "valueGates" => array(//==sumber nilai yang dikirim kemana2
            "master" => array(//==sumber nilai utama
                "pihakName" => "pihakID",
            ),
            "detail" => array(//===sumber nilai berupa rincian

            ),
        ),
        "valueBuilders" => array(),

        "preProcessor" => array(),
        "tableIn" => array(
            "master" => array(
                "jenis_master" => "jenisTrMaster",
                "jenis_top" => "jenisTrTop",
                "jenis" => "jenisTr",
                "jenis_label" => "jenisTrName",
                "div_id" => "divID",
                "div_nama" => "divName",
                "dtime" => "dtime",
                "fulldate" => "fulldate",
                "oleh_id" => "olehID",
                "oleh_nama" => "olehName",

                "cabang2_id" => "placeID",
                "cabang2_nama" => "placeName",

                "cabang_id" => "placeID",
                "cabang_nama" => "placeName",
                "transaksi_nilai" => "srcDefValue",
                "transaksi_jenis" => "jenisTr",
                "keterangan" => "description",

                "gudang_id" => "gudangID",
                "gudang_nama" => "gudangName",
                "gudang2_id" => "gudang2ID",
                "gudang2_nama" => "gudang2Name",
            ),
            "mainValues" => array(
                "debet" => "debet",
                "kredit" => "kredit",
            ),
            "detail" => array(
                "dtime" => "dtime",
                "produk_id" => "id",
                "produk_kode" => "code",
                "produk_label" => "label",
                "produk_nama" => "name",
                "produk_ord_jml" => "jml",
                "produk_ord_hrg" => "harga",
                "hpp" => "harga",
                "satuan" => "satuan",
            ),
            "detailValues" => array(
                "debet" => "debet",
                "kredit" => "kredit",
            ),

        ),
        "tableIn_static" => array(
            "master" => array(
                "trash" => 0,
            ),
            "detail" => array(
                "trash" => 0,
                "produk_jenis" => "account",
            ),
        ),
        "components" => array(
            "777_1" => array(
                "master" => array(
                    array(
                        "comName" => "Jurnal",
                        "loop" => array(
                            "{pihakName}" => "harga",
                            "{opAccount}" => "harga",
                            //                            "{targetAccount}" => "targetDefValue",
                        ),
                        "static" => array(
                            "cabang_id" => "placeID",
                            "jenis" => "jenisTr",
                            // "transaksi_no" => "nomer",
                        ),
                        "srcGateName" => "main",
                        "srcRawGateName" => "main",
                    ),
                    array(
                        "comName" => "Rekening",
                        "loop" => array(
                            "{pihakName}" => "harga",
                            "{opAccount}" => "harga",
                            //                            "{targetAccount}" => "targetDefValue",
                        ),
                        "static" => array(
                            "cabang_id" => "placeID",
                            "jenis" => "jenisTr",
                            // "transaksi_no" => "nomer",
                        ),
                        "srcGateName" => "main",
                        "srcRawGateName" => "main",
                    ),

                ),
                "detail" => array(
                    array(
                        "comName" => "{srcRel}",
                        "loop" => array(
                            "{srcAccount}" => "harga",
                        ),
                        "static" => array(
                            "cabang_id" => "placeID",
                            "extern_id" => "id",
                            "extern_nama" => "name",
                            "produk_qty" => "jml",
                            "produk_nilai" => "harga",
                            "gudang_id" => "gudangID",
                            "jenis" => "jenisTr",
                            "transaksi_no" => "nomer",
                        ),
                        "srcGateName" => "items",
                        "srcRawGateName" => "items",
                    ),

                    array(
                        "comName" => "LockerStock",
                        "loop" => array(),
                        "static" => array(
                            "cabang_id" => "placeID",
                            "jenis" => ".produk",
                            "state" => ".active",
                            "jumlah" => "qty",
                            "produk_id" => "id",
                            "nama" => "name",
                            "satuan" => "satuan",
                            "transaksi_id" => ".0",
                            "oleh_id" => ".0",
                            "gudang_id" => "gudangID",
                        ),
                        "srcGateName" => "items",
                        "srcRawGateName" => "items",
                    ),
                    array(
                        "comName" => "FifoAverage",
                        "loop" => array(),
                        "static" => array(
                            "jenis" => ".produk",
                            "jml" => "qty",
                            "produk_id" => "id",
                            "hpp" => "harga",
                            "jml_nilai" => "sub_harga",
                            "nama" => "name",
                            "cabang_id" => "placeID",
                            "gudang_id" => "gudangID",
                        ),
                        "srcGateName" => "items",
                        "srcRawGateName" => "items",
                    ),
                    array(
                        "comName" => "FifoProdukJadi",
                        "loop" => array(),
                        "static" => array(
                            "unit" => "qty",
                            "produk_id" => "id",
                            "produk_nama" => "name",
                            "hpp" => "harga",
                            "jml_nilai" => "sub_harga",
                            "cabang_id" => "placeID",
                            "gudang_id" => "gudangID",
                        ),
                        "srcGateName" => "items",
                        "srcRawGateName" => "items",
                    ),
                ),
            ),
        ),
        "postProcessor" => array(),
    ),
    "777_2" => array(
        "counters" => array(
            "stepCode|placeID",
            "stepCode|olehID",
            "stepCode|placeID|olehID",
        ),
        "formatNota" => "stepCode|placeID",
        "valueGates" => array(//==sumber nilai yang dikirim kemana2
            "master" => array(//==sumber nilai utama

            ),
            "detail" => array(//===sumber nilai berupa rincian

            ),
        ),
        "valueBuilders" => array(),

        "preProcessor" => array(
            "777_2" => array(
                "master" => array(),
                "detail" => array(
                    array(
                        "comName" => "FifoAverage",
                        "loop" => array(),
                        "static" => array(
                            "cabang_id" => "placeID",
                            "extern_id" => "id",
                            "extern_nama" => "name",
                            "produk_qty" => "qty",
                            "gudang_id" => "gudangID",
                        ),
                        "resultParams" => array(
                            "items" => array(
                                "hpp" => "hpp",
                            ),
                        ),
                        "srcGateName" => "items",
                        "srcRawGateName" => "items",
                    ),
                    array(
                        "comName" => "FifoProdukJadi",
                        "loop" => array(),
                        "static" => array(
                            "cabang_id" => "placeID",
                            "extern_id" => "id",
                            "extern_nama" => "name",
                            "produk_qty" => "qty",
                            "gudang_id" => "gudangID",
                        ),
                        "resultParams" => array(
                            "rsltItems" => array(
                                "id" => "produk_id",
                                "nama" => "nama",
                                "name" => "nama",
                                "harga" => "hpp",
                                "hpp" => "hpp",
                                "jml" => "qty",
                                "qty" => "qty",
                                "subtotal" => "subtotal",
                            ),
                        ),
                        "srcGateName" => "items",
                        "srcRawGateName" => "items",
                    ),
                ),
            ),
        ),
        "tableIn" => array(
            "master" => array(
                "jenis_master" => "jenisTrMaster",
                "jenis_top" => "jenisTrTop",
                "jenis" => "jenisTr",
                "jenis_label" => "jenisTrName",
                "div_id" => "divID",
                "div_nama" => "divName",
                "dtime" => "dtime",
                "fulldate" => "fulldate",
                "oleh_id" => "olehID",
                "oleh_nama" => "olehName",

                "cabang2_id" => "placeID",
                "cabang2_nama" => "placeName",

                "cabang_id" => "placeID",
                "cabang_nama" => "placeName",
                "transaksi_nilai" => "srcDefValue",
                "transaksi_jenis" => "jenisTr",
                "keterangan" => "description",

                "gudang_id" => "gudangID",
                "gudang_nama" => "gudangName",
                "gudang2_id" => "gudang2ID",
                "gudang2_nama" => "gudang2Name",
            ),
            "mainValues" => array(
                "debet" => "debet",
                "kredit" => "kredit",
            ),
            "detail" => array(
                "dtime" => "dtime",
                "produk_id" => "id",
                "produk_kode" => "code",
                "produk_label" => "label",
                "produk_nama" => "name",
                "produk_ord_jml" => "jml",
                "produk_ord_hrg" => "harga",
                "hpp" => "harga",
                "satuan" => "satuan",
            ),
            "detailValues" => array(
                "debet" => "debet",
                "kredit" => "kredit",
            ),

        ),
        "tableIn_static" => array(
            "master" => array(
                "trash" => 0,
            ),
            "detail" => array(
                "trash" => 0,
                "produk_jenis" => "account",
            ),
        ),
        "components" => array(
            "777_2" => array(
                "master" => array(
                    array(
                        "comName" => "Jurnal",
                        "loop" => array(
                            "{pihakName}" => "-harga",
                            "{opAccount}" => "-harga",
                            //                            "{targetAccount}" => "targetDefValue",
                        ),
                        "static" => array(
                            "cabang_id" => "placeID",
                            "jenis" => "jenisTr",
                            // "transaksi_no" => "nomer",
                        ),
                        "srcGateName" => "main",
                        "srcRawGateName" => "main",
                    ),
                    array(
                        "comName" => "Rekening",
                        "loop" => array(
                            "{pihakName}" => "-harga",
                            "{opAccount}" => "-harga",
                            //                            "{targetAccount}" => "targetDefValue",
                        ),
                        "static" => array(
                            "cabang_id" => "placeID",
                            "jenis" => "jenisTr",
                            // "transaksi_no" => "nomer",
                        ),
                        "srcGateName" => "main",
                        "srcRawGateName" => "main",
                    ),
                ),
                "detail" => array(
                    array(
                        "comName" => "{srcRel}",
                        "loop" => array(
                            "{srcAccount}" => "-subtotal",
//                            "{srcAccount}" => "-harga",
                        ),
                        "static" => array(
                            "cabang_id" => "placeID",
                            "extern_id" => "id",
                            "extern_nama" => "name",
                            "produk_qty" => "-jml",
                            "produk_nilai" => "-harga",
                            "gudang_id" => "gudangID",
                            "jenis" => "jenisTr",
                            "transaksi_no" => "nomer",
                        ),
                        "srcGateName" => "rsltItems",
                        "srcRawGateName" => "rsltItems",
                    ),

                    array(
                        "comName" => "LockerStock",
                        "loop" => array(),
                        "static" => array(
                            "cabang_id" => "placeID",
                            "jenis" => ".produk",
                            "state" => ".active",
                            "jumlah" => "-qty",
                            "produk_id" => "id",
                            "nama" => "name",
                            "satuan" => "satuan",
                            "transaksi_id" => ".0",
                            "oleh_id" => ".0",
                            "oleh_nama" => "",
                            "gudang_id" => "gudangID",
                        ),
                        "srcGateName" => "items",
                        "srcRawGateName" => "items",
                    ),
                    array(
                        "comName" => "LockerStock",
                        "loop" => array(),
                        "static" => array(
                            "cabang_id" => "placeID",
                            "jenis" => ".produk",
                            "state" => ".adjusted",
                            "jumlah" => "qty",
                            "produk_id" => "id",
                            "nama" => "name",
                            "satuan" => "satuan",
                            "transaksi_id" => ".0",
                            "oleh_id" => ".0",
                            "oleh_nama" => "",
                            "gudang_id" => "gudangID",
                        ),
                        "srcGateName" => "items",
                        "srcRawGateName" => "items",
                    ),
                ),
            ),
        ),
        "postProcessor" => array(),
    ),
    "7778" => array(
        "counters" => array(
            "stepCode|placeID",
            "stepCode|olehID",
            "stepCode|placeID|olehID",
        ),
        "formatNota" => "stepCode|placeID",
        "valueGates" => array(//==sumber nilai yang dikirim kemana2
            "master" => array(//==sumber nilai utama

            ),
            "detail" => array(//===sumber nilai berupa rincian

            ),
        ),
        "valueBuilders" => array(),

        "preProcessor" => array(),
        "tableIn" => array(
            "master" => array(
                "jenis_master" => "jenisTrMaster",
                "jenis_top" => "jenisTrTop",
                "jenis" => "jenisTr",
                "jenis_label" => "jenisTrName",
                "div_id" => "divID",
                "div_nama" => "divName",
                "dtime" => "dtime",
                "fulldate" => "fulldate",
                "oleh_id" => "olehID",
                "oleh_nama" => "olehName",

                "cabang2_id" => "placeID",
                "cabang2_nama" => "placeName",

                "cabang_id" => "placeID",
                "cabang_nama" => "placeName",
                "transaksi_nilai" => "srcDefValue",
                "transaksi_jenis" => "jenisTr",
                "keterangan" => "description",

                "gudang_id" => "gudangID",
                "gudang_nama" => "gudangName",
                "gudang2_id" => "gudang2ID",
                "gudang2_nama" => "gudang2Name",
            ),
            "mainValues" => array(
                "debet" => "debet",
                "kredit" => "kredit",
            ),
            "detail" => array(
                "dtime" => "dtime",
                "produk_id" => "id",
                "produk_kode" => "code",
                "produk_label" => "label",
                "produk_nama" => "name",
                "produk_ord_jml" => "jml",
                "produk_ord_hrg" => "harga",
                "hpp" => "harga",
                "satuan" => "satuan",
            ),
            "detailValues" => array(
                "debet" => "debet",
                "kredit" => "kredit",
            ),

        ),
        "tableIn_static" => array(
            "master" => array(
                "trash" => 0,
            ),
            "detail" => array(
                "trash" => 0,
                "produk_jenis" => "account",
            ),
        ),
        "components" => array(),
        "postProcessor" => array(),
    ),
    //  config penyesuaian supplies. pokoknya selain piutang, hutang, kas
    "666_1" => array(
        "counters" => array(
            "stepCode|placeID",
            "stepCode|olehID",
            "stepCode|placeID|olehID",
        ),
        "formatNota" => "stepCode|placeID",
        "valueGates" => array(//==sumber nilai yang dikirim kemana2
            "master" => array(//==sumber nilai utama

            ),
            "detail" => array(//===sumber nilai berupa rincian

            ),
        ),
        "valueBuilders" => array(),

        "preProcessor" => array(),
        "tableIn" => array(
            "master" => array(
                "jenis_master" => "jenisTrMaster",
                "jenis_top" => "jenisTrTop",
                "jenis" => "jenisTr",
                "jenis_label" => "jenisTrName",
                "div_id" => "divID",
                "div_nama" => "divName",
                "dtime" => "dtime",
                "fulldate" => "fulldate",
                "oleh_id" => "olehID",
                "oleh_nama" => "olehName",

                "cabang2_id" => "placeID",
                "cabang2_nama" => "placeName",

                "cabang_id" => "placeID",
                "cabang_nama" => "placeName",
                "transaksi_nilai" => "srcDefValue",
                "transaksi_jenis" => "jenisTr",
                "keterangan" => "description",

                "gudang_id" => "gudangID",
                "gudang_nama" => "gudangName",
                "gudang2_id" => "gudang2ID",
                "gudang2_nama" => "gudang2Name",
            ),
            "mainValues" => array(
                "debet" => "debet",
                "kredit" => "kredit",
            ),
            "detail" => array(
                "dtime" => "dtime",
                "produk_id" => "id",
                "produk_kode" => "code",
                "produk_label" => "label",
                "produk_nama" => "name",
                "produk_ord_jml" => "jml",
                "produk_ord_hrg" => "harga",
                "hpp" => "harga",
                "satuan" => "satuan",
            ),
            "detailValues" => array(
                "debet" => "debet",
                "kredit" => "kredit",
            ),

        ),
        "tableIn_static" => array(
            "master" => array(
                "trash" => 0,
            ),
            "detail" => array(
                "trash" => 0,
                "produk_jenis" => "account",
            ),
        ),
        "components" => array(
            "666_1" => array(
                "master" => array(
                    array(
                        "comName" => "Jurnal",
                        "loop" => array(
                            "{pihakName}" => "harga",
                            "{opAccount}" => "harga",
                            //                            "{targetAccount}" => "targetDefValue",
                        ),
                        "static" => array(
                            "cabang_id" => "placeID",
                            "jenis" => "jenisTr",
                            // "transaksi_no" => "nomer",
                        ),
                        "srcGateName" => "main",
                        "srcRawGateName" => "main",
                    ),
                    array(
                        "comName" => "Rekening",
                        "loop" => array(
                            "{pihakName}" => "harga",
                            "{opAccount}" => "harga",
                            //                            "{targetAccount}" => "targetDefValue",
                        ),
                        "static" => array(
                            "cabang_id" => "placeID",
                            "jenis" => "jenisTr",
                            // "transaksi_no" => "nomer",
                        ),
                        "srcGateName" => "main",
                        "srcRawGateName" => "main",
                    ),
                ),
                "detail" => array(
                    array(
                        "comName" => "{srcRel}",
                        "loop" => array(
                            "{srcAccount}" => "harga",
                        ),
                        "static" => array(
                            "cabang_id" => "placeID",
                            "extern_id" => "id",
                            "extern_nama" => "name",
                            "produk_qty" => "-jml",
                            "produk_nilai" => "harga",
                            "gudang_id" => "gudangID",
                            "jenis" => "jenisTr",
                            "transaksi_no" => "nomer",
                        ),
                        "srcGateName" => "items",
                        "srcRawGateName" => "items",
                    ),

                    array(
                        "comName" => "LockerStockSupplies",
                        "loop" => array(),
                        "static" => array(
                            "cabang_id" => "placeID",
                            "jenis" => ".supplies",
                            "state" => ".active",
                            "jumlah" => "qty",
                            "produk_id" => "id",
                            "nama" => "name",
                            "satuan" => "satuan",
                            "transaksi_id" => ".0",
                            "oleh_id" => ".0",
                            "gudang_id" => "gudangID",
                        ),
                        "srcGateName" => "items",
                        "srcRawGateName" => "items",
                    ),
                    array(
                        "comName" => "FifoAverage",
                        "loop" => array(),
                        "static" => array(
                            "jenis" => ".supplies",
                            "jml" => "qty",
                            "produk_id" => "id",
                            "hpp" => "harga",
                            "jml_nilai" => "sub_harga",
                            "nama" => "name",
                            "cabang_id" => "placeID",
                            "gudang_id" => "gudangID",
                        ),
                        "srcGateName" => "items",
                        "srcRawGateName" => "items",
                    ),
                    array(
                        "comName" => "FifoSupplies",
                        "loop" => array(),
                        "static" => array(
                            "unit" => "qty",
                            "produk_id" => "id",
                            "produk_nama" => "name",
                            "hpp" => "harga",
                            "jml_nilai" => "sub_harga",
                            "cabang_id" => "placeID",
                            "gudang_id" => "gudangID",
                        ),
                        "srcGateName" => "items",
                        "srcRawGateName" => "items",
                    ),
                ),
            ),
        ),
        "postProcessor" => array(),
    ),
    "666_2" => array(
        "counters" => array(
            "stepCode|placeID",
            "stepCode|olehID",
            "stepCode|placeID|olehID",
        ),
        "formatNota" => "stepCode|placeID",
        "valueGates" => array(//==sumber nilai yang dikirim kemana2
            "master" => array(//==sumber nilai utama

            ),
            "detail" => array(//===sumber nilai berupa rincian

            ),
        ),
        "valueBuilders" => array(),

        "preProcessor" => array(
            1 => array(
                "master" => array(),
                "detail" => array(
                    array(
                        "comName" => "FifoAverageSupplies",
                        "loop" => array(),
                        "static" => array(
                            "cabang_id" => "placeID",
                            "extern_id" => "id",
                            "extern_nama" => "name",
                            "produk_qty" => "qty",
                            "gudang_id" => "gudangID",
                        ),
                        "resultParams" => array(
                            "items" => array(
                                "hpp" => "hpp",
                            ),
                        ),
                        "srcGateName" => "items",
                        "srcRawGateName" => "items",
                    ),
                    array(
                        "comName" => "FifoSupplies",
                        "loop" => array(),
                        "static" => array(
                            "cabang_id" => "placeID",
                            "extern_id" => "id",
                            "extern_nama" => "name",
                            "produk_qty" => "qty",
                            "gudang_id" => "gudangID",
                        ),
                        "resultParams" => array(
                            "rsltItems" => array(
                                "id" => "produk_id",
                                "nama" => "nama",
                                "name" => "nama",
                                "harga" => "hpp",
                                "hpp" => "hpp",
                                "jml" => "qty",
                                "qty" => "qty",
                                "subtotal" => "subtotal",
                            ),
                        ),
                        "srcGateName" => "items",
                        "srcRawGateName" => "items",
                    ),
                ),
            ),
        ),
        "tableIn" => array(
            "master" => array(
                "jenis_master" => "jenisTrMaster",
                "jenis_top" => "jenisTrTop",
                "jenis" => "jenisTr",
                "jenis_label" => "jenisTrName",
                "div_id" => "divID",
                "div_nama" => "divName",
                "dtime" => "dtime",
                "fulldate" => "fulldate",
                "oleh_id" => "olehID",
                "oleh_nama" => "olehName",

                "cabang2_id" => "placeID",
                "cabang2_nama" => "placeName",

                "cabang_id" => "placeID",
                "cabang_nama" => "placeName",
                "transaksi_nilai" => "srcDefValue",
                "transaksi_jenis" => "jenisTr",
                "keterangan" => "description",

                "gudang_id" => "gudangID",
                "gudang_nama" => "gudangName",
                "gudang2_id" => "gudang2ID",
                "gudang2_nama" => "gudang2Name",
            ),
            "mainValues" => array(
                "debet" => "debet",
                "kredit" => "kredit",
            ),
            "detail" => array(
                "dtime" => "dtime",
                "produk_id" => "id",
                "produk_kode" => "code",
                "produk_label" => "label",
                "produk_nama" => "name",
                "produk_ord_jml" => "jml",
                "produk_ord_hrg" => "harga",
                "hpp" => "harga",
                "satuan" => "satuan",
            ),
            "detailValues" => array(
                "debet" => "debet",
                "kredit" => "kredit",
            ),

        ),
        "tableIn_static" => array(
            "master" => array(
                "trash" => 0,
            ),
            "detail" => array(
                "trash" => 0,
                "produk_jenis" => "account",
            ),
        ),
        "components" => array(
            "666_2" => array(
                "master" => array(
                    array(
                        "comName" => "Jurnal",
                        "loop" => array(
                            "{pihakName}" => "-harga",
                            "{opAccount}" => "-harga",
                            //                            "{targetAccount}" => "targetDefValue",
                        ),
                        "static" => array(
                            "cabang_id" => "placeID",
                            "jenis" => "jenisTr",
                            // "transaksi_no" => "nomer",
                        ),
                        "srcGateName" => "main",
                        "srcRawGateName" => "main",
                    ),
                    array(
                        "comName" => "Rekening",
                        "loop" => array(
                            "{pihakName}" => "-harga",
                            "{opAccount}" => "-harga",
                            //                            "{targetAccount}" => "targetDefValue",
                        ),
                        "static" => array(
                            "cabang_id" => "placeID",
                            "jenis" => "jenisTr",
                            // "transaksi_no" => "nomer",
                        ),
                        "srcGateName" => "main",
                        "srcRawGateName" => "main",
                    ),
                ),
                "detail" => array(
                    array(
                        "comName" => "{srcRel}",
                        "loop" => array(
                            "{srcAccount}" => "-subtotal",
//                            "{srcAccount}" => "-harga",
                        ),
                        "static" => array(
                            "cabang_id" => "placeID",
                            "extern_id" => "id",
                            "extern_nama" => "name",
                            "produk_qty" => "-jml",
                            "produk_nilai" => "-harga",
                            "gudang_id" => "gudangID",
                            "jenis" => "jenisTr",
                            "transaksi_no" => "nomer",
                        ),
                        "srcGateName" => "rsltItems",
                        "srcRawGateName" => "rsltItems",
                    ),

                    array(
                        "comName" => "LockerStockSupplies",
                        "loop" => array(),
                        "static" => array(
                            "cabang_id" => "placeID",
                            "jenis" => ".supplies",
                            "state" => ".active",
                            "jumlah" => "-qty",
                            "produk_id" => "id",
                            "nama" => "name",
                            "satuan" => "satuan",
                            "transaksi_id" => ".0",
                            "oleh_id" => ".0",
                            "oleh_nama" => "",
                            "gudang_id" => "gudangID",
                        ),
                        "srcGateName" => "items",
                        "srcRawGateName" => "items",
                    ),
                    array(
                        "comName" => "LockerStockSupplies",
                        "loop" => array(),
                        "static" => array(
                            "cabang_id" => "placeID",
                            "jenis" => ".supplies",
                            "state" => ".adjust",
                            "jumlah" => "qty",
                            "produk_id" => "id",
                            "nama" => "name",
                            "satuan" => "satuan",
                            "transaksi_id" => ".0",
                            "oleh_id" => ".0",
                            "oleh_nama" => "",
                            "gudang_id" => "gudangID",
                        ),
                        "srcGateName" => "items",
                        "srcRawGateName" => "items",
                    ),
                ),
            ),
        ),
        "postProcessor" => array(),
    ),

);


