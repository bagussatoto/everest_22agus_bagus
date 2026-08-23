<?php

$config["coTransaksiLayout"] = array(
    "999" => array(
        "receiptTemplate" => array(
            1 => "template/000.html",
        ),
        "fixedElements" => array(
            1 => array(),
            2 => array(),
        ),
        "fixedSignatures" => array(
            1 => array(),
            2 => array(),
        ),
        "headerNota" => array(
            "dtime" => "date",
            "cabang_nama" => "branch",
            "tlp_1" => "phone",
            "alamat_1" => "address",
        ),
        "headerTables" => array(
            "produk_nama" => "product name",
            "produk_ord_hrg" => "price",
            "produk_ord_jml" => "jumlah",
            "sub_total" => "sub total",
        ),
        "receiptMainFields" => array(
            "jenis_label" => "activity",
            "nomer" => "reference no.",
            "result_nomer" => "receipt no.",
            "cabang2_nama" => "branch",
            "dtime" => "date",
        ),

        "receiptDetailFields" => array(
            1 => array(
                "produk_nama" => "product name",
                "harga" => "price",
            ),
        ),
        "receiptSumFields" => array(
            1 => array(//                "harga" => "grand total",
            ),
        ),
        "receiptNumFields" => array(
            1 => array(
                "jml" => "(don't change)",
                "debet" => "debet",
                "kredit" => "kredit",
            ),
            2 => array(
                "jml" => "(don't change)",
                "debet" => "debet",
                "kredit" => "kredit",
            ),
        ),
        "reportSumFields" => array(
            "cabang2_id" => "cabang2_nama",
        ),
        "printLocation" => "Printing/viewReceipt/",
        //        "receiptInword"       =>array("inWordInd"=>"harga"),
    ),
    "999_1" => array(
        "receiptTemplate" => array(
            1 => "template/000.html",
        ),
        "fixedElements" => array(
            1 => array(),
            2 => array(),
        ),
        "fixedSignatures" => array(
            1 => array(),
            2 => array(),
        ),
        "headerNota" => array(
            "dtime" => "date",
            "cabang_nama" => "branch",
            "tlp_1" => "phone",
            "alamat_1" => "address",
        ),
        "headerTables" => array(
            "produk_nama" => "item name",
            "produk_ord_hrg" => "price",
            "produk_ord_jml" => "jumlah",
            "sub_total" => "sub total",
        ),
        "receiptMainFields" => array(
            "jenis_label" => "activity",
            "nomer" => "reference no.",
            "result_nomer" => "receipt no.",
            "cabang2_nama" => "branch",
            "dtime" => "date",
        ),

        "receiptDetailFields" => array(
            1 => array(
                "produk_nama" => "item name",
                "harga" => "price",
            ),
        ),
        "receiptSumFields" => array(
            1 => array(//                "harga" => "grand total",
            ),
        ),
        "receiptNumFields" => array(
            1 => array(
                "jml" => "(don't change)",
                "debet" => "debet",
                "kredit" => "kredit",
            ),
            2 => array(
                "jml" => "(don't change)",
                "debet" => "debet",
                "kredit" => "kredit",
            ),
        ),
        "reportSumFields" => array(
            "cabang2_id" => "cabang2_nama",
        ),
        "printLocation" => "Printing/viewReceipt/",
    ),

    //  config penyesuaian piutang, hutang, kas. pokoknya selain produk
    "888_1" => array(
        "receiptTemplate" => array(
            1 => "template/000.html",
        ),
        "fixedElements" => array(
            1 => array(),
            2 => array(),
        ),
        "fixedSignatures" => array(
            1 => array(),
            2 => array(),
        ),
        "headerNota" => array(
            "dtime" => "date",
            "cabang_nama" => "branch",
            "tlp_1" => "phone",
            "alamat_1" => "address",
        ),
        "headerTables" => array(
            "produk_nama" => "item name",
            "produk_ord_hrg" => "price",
            "produk_ord_jml" => "jumlah",
            "sub_total" => "sub total",
        ),
        "receiptMainFields" => array(
            "jenis_label" => "activity",
            "nomer" => "reference no.",
            "result_nomer" => "receipt no.",
            "cabang2_nama" => "branch",
            "dtime" => "date",
        ),

        "receiptDetailFields" => array(
            1 => array(
                "produk_nama" => "item name",
                "harga" => "price",
            ),
        ),
        "receiptSumFields" => array(
            1 => array(
                "harga" => "grand total",
            ),
        ),
        "receiptNumFields" => array(
            1 => array(
                "jml" => "(don't change)",
                "harga" => "harga",
            ),
        ),

        "reportSumFields" => array(
            "cabang2_id" => "cabang2_nama",
        ),
        "printLocation" => "Printing/viewReceipt/",
    ),
    "888_2" => array(
        "receiptTemplate" => array(
            1 => "template/000.html",
        ),
        "fixedElements" => array(
            1 => array(),
            2 => array(),
        ),
        "fixedSignatures" => array(
            1 => array(),
            2 => array(),
        ),
        "headerNota" => array(
            "dtime" => "date",
            "cabang_nama" => "branch",
            "tlp_1" => "phone",
            "alamat_1" => "address",
        ),
        "headerTables" => array(
            "produk_nama" => "item name",
            "produk_ord_hrg" => "price",
            "produk_ord_jml" => "jumlah",
            "sub_total" => "sub total",
        ),
        "receiptMainFields" => array(
            "jenis_label" => "activity",
            "nomer" => "reference no.",
            "result_nomer" => "receipt no.",
            "cabang2_nama" => "branch",
            "dtime" => "date",
        ),

        "receiptDetailFields" => array(
            1 => array(
                "produk_nama" => "item name",
                "harga" => "price",
            ),
        ),
        "receiptSumFields" => array(
            1 => array(//                "harga" => "grand total",
            ),
        ),
        "receiptNumFields" => array(
            1 => array(
                "jml" => "(don't change)",
                "harga" => "harga",
            ),
        ),
        "reportSumFields" => array(
            "cabang2_id" => "cabang2_nama",
        ),
        "printLocation" => "Printing/viewReceipt/",
    ),
    //  config penyesuaian produk/supplies. pokoknya selain piutang, hutang, kas
    "777_1" => array(
        "receiptTemplate" => array(
            1 => "template/000.html",
        ),
        "fixedElements" => array(
            1 => array(),
            2 => array(),
        ),
        "fixedSignatures" => array(
            1 => array(),
            2 => array(),
        ),
        "headerNota" => array(
            "dtime" => "date",
            "cabang_nama" => "branch",
            "tlp_1" => "phone",
            "alamat_1" => "address",
        ),
        "headerTables" => array(
            "produk_nama" => "item name",
            "produk_ord_hrg" => "price",
            "produk_ord_jml" => "jumlah",
            "sub_total" => "sub total",
        ),
        "receiptMainFields" => array(
            "jenis_label" => "activity",
            "nomer" => "reference no.",
            "result_nomer" => "receipt no.",
            "cabang2_nama" => "branch",
            "dtime" => "date",
        ),

        "receiptDetailFields" => array(
            1 => array(
                "produk_nama" => "item name",
                "harga" => "price",
            ),
        ),
        "receiptSumFields" => array(
            1 => array(//                "harga" => "grand total",
            ),
        ),
        "receiptNumFields" => array(
            1 => array(
                "jml" => "(don't change)",
                //                "debet" => "debet",
                //                "kredit" => "kredit",
                "harga" => "harga",
            ),
            //            2 => array(
            //                "jml" => "(don't change)",
            ////                "debet" => "debet",
            ////                "kredit" => "kredit",
            //                "harga" => "harga",
            //            ),
        ),
        "reportSumFields" => array(
            "cabang2_id" => "cabang2_nama",
        ),
        "printLocation" => "Printing/viewReceipt/",
    ),
    "777_2" => array(
        "receiptTemplate" => array(
            1 => "template/000.html",
        ),
        "fixedElements" => array(
            1 => array(),
            2 => array(),
        ),
        "fixedSignatures" => array(
            1 => array(),
            2 => array(),
        ),
        "headerNota" => array(
            "dtime" => "date",
            "cabang_nama" => "branch",
            "tlp_1" => "phone",
            "alamat_1" => "address",
        ),
        "headerTables" => array(
            "produk_nama" => "item name",
            "produk_ord_hrg" => "price",
            "produk_ord_jml" => "jumlah",
            "sub_total" => "sub total",
        ),
        "receiptMainFields" => array(
            "jenis_label" => "activity",
            "nomer" => "reference no.",
            "result_nomer" => "receipt no.",
            "cabang2_nama" => "branch",
            "dtime" => "date",
        ),

        "receiptDetailFields" => array(
            1 => array(
                "produk_nama" => "item name",
                "harga" => "price",
            ),
        ),
        "receiptSumFields" => array(
            1 => array(//                "harga" => "grand total",
            ),
        ),
        "receiptNumFields" => array(
            1 => array(
                "jml" => "(don't change)",
                "harga" => "harga",
            ),
        ),
        "reportSumFields" => array(
            "cabang2_id" => "cabang2_nama",
        ),
        "printLocation" => "Printing/viewReceipt/",
    ),
    "7778" => array(
        "receiptTemplate" => array(
            1 => "template/000.html",
        ),
        "fixedElements" => array(
            1 => array(),
            2 => array(),
        ),
        "fixedSignatures" => array(
            1 => array(),
            2 => array(),
        ),
        "headerNota" => array(
            "dtime" => "date",
            "cabang_nama" => "branch",
            "tlp_1" => "phone",
            "alamat_1" => "address",
        ),
        "headerTables" => array(
            "produk_nama" => "item name",
            "produk_ord_hrg" => "price",
            "produk_ord_jml" => "jumlah",
            "sub_total" => "sub total",
        ),
        "receiptMainFields" => array(
            "jenis_label" => "activity",
            "nomer" => "reference no.",
            "result_nomer" => "receipt no.",
            "cabang2_nama" => "branch",
            "dtime" => "date",
        ),

        "receiptDetailFields" => array(
            1 => array(
                "produk_nama" => "item name",
                "harga" => "price",
            ),
        ),
        "receiptSumFields" => array(
            1 => array(//                "harga" => "grand total",
            ),
        ),
        "receiptNumFields" => array(
            1 => array(
                "jml" => "(don't change)",
                "harga" => "harga",
            ),
        ),
        "reportSumFields" => array(
            "cabang2_id" => "cabang2_nama",
        ),
        "printLocation" => "Printing/viewReceipt/",
    ),

);