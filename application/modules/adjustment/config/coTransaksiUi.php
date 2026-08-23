<?php
//region urusan tanggal-menanggal
// date_default_timezone_set('asia/jakarta');
// $date = new DateTime(date("Y-m-d")); // Y-m-d
// $date->add(new DateInterval('P30D'));
//$date->format('Y-m-d') . "\n";
//endregion

//tambahin filter "461ro untuk selectornota taxes 681
$config["coTransaksiUi"] = array(
    "999" => array(
        "icon" => "fa fa-cube",
        "label" => "adjustment journaling",
        "place" => "center",//=> "center",
        "steps" => array(
            1 => array(
                "label" => "adjustment journaling",
                "actionLabel" => "make adjustment",
                "source" => "",
                "target" => "999",
                "userGroup" => "root",
                "stateLabel" => "done",
                "stateColor" => "#dd3300",
                "stateCaption" => "made by",
            ),
        ),
//        "template" => "template/transaksi_nopihak2.html",
        "template" => "template/transaksi_nopihak4.html",

        "selectorModel" => "MdlRekeningKredit",
        "selectorSrcModel" => "MdlRekeningKredit",
//        "selectorModel" => "MdlRekeningDebet",
//        "selectorSrcModel" => "MdlRekeningDebet",

        "selectorModel2" => "MdlRekeningKredit",
        "selectorSrcModel2" => "MdlRekeningKredit",
//        "selectorModel2" => "MdlRekeningDebet",
//        "selectorSrcModel2" => "MdlRekeningDebet",

//        "selectorModel3" => "MdlRekeningKredit",
//        "selectorSrcModel3" => "MdlRekeningKredit",
//        "selectorModel3" => "MdlRekeningDebet",
//        "selectorSrcModel3" => "MdlRekeningDebet",

//        "selectorModel4" => "MdlRekeningKredit",
//        "selectorSrcModel4" => "MdlRekeningKredit",
//        "selectorModel4" => "MdlRekeningDebet",
//        "selectorSrcModel4" => "MdlRekeningDebet",


        "selectedPrice" => array(),
        "lockerCheck" => array(),
        "selectorFilters" => array(//            "bank.cabang_id=placeID",
        ),
        "selectorCaller" => "_selectorItem/selectItem",// bikin shopping cart background
        "selectorCaller2" => "_selectorItem/selectItem2",// bikin shopping cart background
        "selectorCaller3" => "_selectorItem/selectItem3",// bikin shopping cart background
        "selectorCaller4" => "_selectorItem/selectItem4",// bikin shopping cart background
        "selectorLabel" => "from account",
        "selectorLabel2" => "to account",
        "selectorParamFields" => array(
            "id" => "id",
            "name" => "name",
        ),
        "selectorViewedFields" => array(
            "name",
            "defPosition",
        ),
        "selectorProcessor" => "_processSelectRekeningAdjustment/select",
        "selectorProcessor2" => "_processSelectRekeningAdjustment/select2",
        "selectorProcessor3" => "_processSelectRekeningAdjustment/select3",
        "selectorProcessor4" => "_processSelectRekeningAdjustment/select4",
        "editHandlerMethod" => "edit",

        "pihakModel" => "MdlGudang",
        "pihakCaller" => "_selectorPihak/selectPihak",
        "pihakLabel" => "gudang",
        "pihakFilters" => array(
            "cabang_id=cabang_id",
            "id<>gudang_id",
        ),
        "pihakProcessor" => "_processPihak/select",

        "shortHistoryFields" => array(
            "jenis_label" => "activity",
            "dtime" => "date",
            //            "cabang2_nama" => "recipient",
            "nomer" => "receipt number",
            "oleh_nama" => "person",
        ),
        "selectorFields" => array("id", "nama"),
        "pihakFields" => array("id", "nama"),
        "shoppingCart" => array(
            "initPrices" => "beli",
        ),

        "shoppingCartFields" => array(
            1 => array(
                "nama" => "source account",

            ),
            2 => array(
                "nama" => "source account",

            ),
        ),
        "shoppingCartFields2" => array(
            1 => array(
                "nama" => "target account",

            ),
            2 => array(
                "nama" => "target account",

            ),
        ),
        "shoppingCartFieldSrc" => array(
            "name" => "name",
            "nama" => "nama",

        ),
        "shoppingCartNumFields" => array(
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
        "shoppingCartNumFields2" => array(
            1 => array(
                "harga" => "receiving amount",
                "jml" => "qty",
            ),
            2 => array(
                "harga" => "receiving amount",
                "jml" => "qty",
            ),
        ),
        "shoppingCartNoteEnabled" => false,
        "shoppingCartAvoidRemove" => true,

        "shoppingCartEditableFields" => array(
            1 => array(
                "jml",
                "debet",
                "kredit",
            ),
        ),
        "shoppingCartAmountValue" => array(
            1 => "jml*harga",
            2 => "jml*harga",
        ),
        "receiptElements" => array(
            "extern1" => array(
                "elementType" => "dataModel",
                "inputType" => "radio",
                "label" => "source sub-account",
                "mdlName" => "MdlExtern",
                "mdlFilter" => array("relName=srcRel"),
                "key" => "extern_id",
                "labelSrc" => "extern_id/extern_nama",
                "usedFields" => array(
                    "extern_nama" => "account name",
                ),
                "noValidate" => true,
                "editPoints" => array(1),
            ),

            "extern2" => array(
                "elementType" => "dataModel",
                "inputType" => "radio",
//                "inputType" => "hidden",
                "label" => "target sub-account",
                "mdlName" => "MdlExtern",
                "mdlFilter" => array(
                    "relName=targetRel",
                ),
                "key" => "extern_id",
                "labelSrc" => "extern_id/extern_nama",
                "usedFields" => array(
                    "extern_nama" => "account name",
                ),
                "editPoints" => array(1),
                "noValidate" => true,
            ),

            //klo bukan project matikan, jika project harus di nyalakan
//            "extern_project" => array(
//                "elementType" => "dataModel",
//                "inputType" => "radio",
////                "inputType" => "hidden",
//                "label" => "target project",
//                "mdlName" => "MdlProdukProject",
//                "mdlFilter" => array(
//
//                ),
//                "key" => "id",
//                "labelSrc" => "id/nama",
//                "usedFields" => array(
//                    "id" => "id project",
//                    "nama" => "nama project",
//                ),
//                "editPoints" => array(1),
//                "noValidate" => true,
//            ),

//            "extern3" => array(
//                "elementType" => "dataModel",
//                "inputType" => "radio",
//                "label" => "target 3 sub-account",
//                "mdlName" => "MdlExtern",
//                "mdlFilter" => array("relName=target3Rel"),
//                "key" => "extern_id",
//                "labelSrc" => "extern_id/extern_nama",
//                "usedFields" => array(
//                    "extern_nama" => "account name",
//                ),
//                "noValidate" => true,
//                "editPoints" => array(1),
//            ),
//
//            "extern4" => array(
//                "elementType" => "dataModel",
//                "inputType" => "radio",
//                "label" => "target 4 sub-account",
//                "mdlName" => "MdlExtern",
//                "mdlFilter" => array("relName=target4Rel"),
//                "key" => "extern_id",
//                "labelSrc" => "extern_id/extern_nama",
//                "usedFields" => array(
//                    "extern_nama" => "account name",
//                ),
//                "noValidate" => true,
//                "editPoints" => array(1),
//            ),
        ),
        "previewCtr" => "Create",
    ),

    "999_1" => array(
        "icon" => "fa fa-cube",
        "label" => "adjustment journaling",
        "place" => "center",//=> "center",
        "steps" => array(
            1 => array(
                "label" => "adjustment journaling",
                "actionLabel" => "make adjustment",
                "source" => "",
                "target" => "999",
                "userGroup" => "root",
                "stateLabel" => "done",
                "stateColor" => "#dd3300",
                "stateCaption" => "made by",
            ),
        ),
        "template" => "template/transaksi_nopihak.html",
        //        "selectorModel"    => "MdlRekeningKredit",
        //        "selectorSrcModel" => "MdlRekeningKredit",
        //        "selectorModel" => "MdlRekeningDebet",
        //        "selectorSrcModel" => "MdlRekeningDebet",
        "selectorModel" => "MdlRekeningDebetKredit",
        "selectorSrcModel" => "MdlRekeningDebetKredit",

        "selectedPrice" => array(),
        "lockerCheck" => array(),
        "selectorFilters" => array(//            "bank.cabang_id=placeID",
        ),
        "selectorCaller" => "_selectorItem/selectItem",// bikin shopping cart background
        //        "selectorCaller2"      => "_selectorItem/selectItem2",// bikin shopping cart background
        "selectorLabel" => "from account",
        "selectorLabel2" => "to account",
        "selectorParamFields" => array(
            "id" => "id",
            "name" => "name",
        ),
        "selectorViewedFields" => array(
            "name",
            "defPosition",
        ),
        "selectorProcessor" => "_processSelectRekeningAdjustment/select",
        "selectorProcessor2" => "_processSelectRekeningAdjustment/select2",
        "editHandlerMethod" => "edit",

        "pihakModel" => "MdlGudang",
        "pihakCaller" => "_selectorPihak/selectPihak",
        "pihakLabel" => "gudang",
        "pihakFilters" => array(
            "cabang_id=cabang_id",
            "id<>gudang_id",
        ),
        "pihakProcessor" => "_processPihak/select",

        "shortHistoryFields" => array(
            "jenis_label" => "activity",
            "dtime" => "date",
            //            "cabang2_nama" => "recipient",
            "nomer" => "receipt number",
            "oleh_nama" => "person",
        ),
        "selectorFields" => array("id", "nama"),
        "pihakFields" => array("id", "nama"),
        "shoppingCart" => array(
            "initPrices" => "beli",
        ),

        "shoppingCartFields" => array(
            1 => array(
                "nama" => "source account",

            ),
            2 => array(
                "nama" => "source account",

            ),
        ),
        "shoppingCartFields2" => array(
            1 => array(
                "nama" => "target account",

            ),
            2 => array(
                "nama" => "target account",

            ),
        ),
        "shoppingCartFieldSrc" => array(
            "name" => "name",
            "nama" => "nama",

        ),
        "shoppingCartNumFields" => array(
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
        "shoppingCartNumFields2" => array(
            1 => array(
                "harga" => "receiving amount",
                "jml" => "qty",
            ),
            2 => array(
                "harga" => "receiving amount",
                "jml" => "qty",
            ),
        ),
        "shoppingCartNoteEnabled" => false,
        "shoppingCartAvoidRemove" => false,
        "shoppingCartSumFields" => array(
            1 => array(
                //                "tagihan" => "amount remains to pay",
                //                "ppn" => "vat",
                //                "nett2" => "total",
            ),
        ),
        //        "shoppingCartPairedItem"  => array(
        //            "enabled"   => true,
        //            "mdlName"   => "MdlBankAccount",
        //            "mdlFilter" => array(
        //                "cabang_id=placeID"
        //            ),
        //            "srcKey"    => "id",
        //            "srcLabel"  => array("nama"),
        //            "mdlFilter" => array("id=id"),
        //        ),

        //        "shoppingCartPairedSelectedItem" => array(
        //            "enabled" => true,
        //            "mdlName" => "ComRekeningPembantuKas",
        //            "srcKey" => "extern_id",
        //            "srcLabel" => array("nama"),
        //            "mdlFilter" => array(
        //                "cabang_id=placeID",
        //                "periode=forever",
        //                "rekening=kas",
        //                ),
        //        ),

        "shoppingCartEditableFields" => array(
            1 => array(
                "jml",
                "debet",
                "kredit",
            ),
        ),
        "shoppingCartAmountValue" => array(
            1 => "jml*harga",
            2 => "jml*harga",
        ),
        "receiptElements" => array(
            "extern1" => array(
                "elementType" => "dataModel",
                "inputType" => "radio",
                "label" => "source sub-account",
                "mdlName" => "MdlExtern",
                "mdlFilter" => array("relName=srcRel"),
                "key" => "extern_id",
                "labelSrc" => "extern_id/extern_nama",
                "usedFields" => array(
                    "extern_nama" => "account name",
                ),
                "editPoints" => array(1),
            ),

            //            "extern2" => array(
            //                "elementType" => "dataModel",
            //                "inputType"   => "radio",
            //                "label"       => "target sub-account",
            //                "mdlName"     => "MdlExtern",
            //                "mdlFilter"   => array("relName=targetRel"),
            //                "key"         => "extern_id",
            //                "labelSrc"    => "extern_nama",
            //                "usedFields"  => array(
            //                    "extern_nama" => "account name",
            //                ),
            //                "editPoints"  => array(1),
            //            ),
        ),

        "previewCtr" => "Create",

    ),

    //    "999_0" => array(
    //        "icon"     => "fa fa-cube",
    //        "label"    => "adjustment journaling",
    //        "place"    => "center",//=> "center",
    //        "steps"    => array(
    //            1 => array(
    //                "label"        => "adjustment journaling",
    //                "actionLabel"  => "make adjustment",
    //                "source"       => "",
    //                "target"       => "999_0",
    //                "userGroup"    => "root",
    //                "stateLabel"   => "done",
    //                "stateColor"   => "#dd3300",
    //                "stateCaption" => "made by",
    //            ),
    //        ),
    //        "template" => "application/template/transaksi_nopihak.html",
    //
    //        "selectorModel"    => "MdlRekeningDebetKredit",
    //        "selectorSrcModel" => "MdlRekeningDebetKredit",
    //
    //        "selectedPrice"        => array(),
    //        "lockerCheck"          => array(),
    //        "selectorFilters"      => array(//            "bank.cabang_id=placeID",
    //        ),
    //        "selectorCaller"       => "Selectors/_selectorItem/selectItem",// bikin shopping cart background
    //        //        "selectorCaller2"      => "Selectors/_selectorItem/selectItem2",// bikin shopping cart background
    //        "selectorLabel"        => "from account",
    //        "selectorLabel2"       => "to account",
    //        "selectorParamFields"  => array(
    //            "id"   => "id",
    //            "name" => "name",
    //        ),
    //        "selectorViewedFields" => array(
    //            "name",
    //            "defPosition",
    //        ),
    //        "selectorProcessor"    => "Selectors/_processSelectRekeningAdjustment/select",
    //        "selectorProcessor2"   => "Selectors/_processSelectRekeningAdjustment/select2",
    //        "editHandlerMethod"    => "edit",
    //
    //        "pihakModel"     => "MdlGudang",
    //        "pihakCaller"    => "Selectors/_selectorPihak/selectPihak",
    //        "pihakLabel"     => "gudang",
    //        "pihakFilters"   => array(
    //            "cabang_id=cabang_id",
    //            "id<>gudang_id",
    //        ),
    //        "pihakProcessor" => "Selectors/_processPihak/select",
    //
    //        "shortHistoryFields" => array(
    //            "jenis_label" => "activity",
    //            "dtime"       => "date",
    //            //            "cabang2_nama" => "recipient",
    //            "nomer"       => "receipt number",
    //            "oleh_nama"   => "person",
    //        ),
    //        "selectorFields"     => array("id", "nama"),
    //        "pihakFields"        => array("id", "nama"),
    //        "shoppingCart"       => array(
    //            "initPrices" => "beli",
    //        ),
    //
    //        "shoppingCartFields"      => array(
    //            1 => array(
    //                "nama" => "source account",
    //
    //            ),
    //            2 => array(
    //                "nama" => "source account",
    //
    //            ),
    //        ),
    //        "shoppingCartFields2"     => array(
    //            1 => array(
    //                "nama" => "target account",
    //
    //            ),
    //            2 => array(
    //                "nama" => "target account",
    //
    //            ),
    //        ),
    //        "shoppingCartFieldSrc"    => array(
    //            "name" => "name",
    //            "nama" => "nama",
    //
    //        ),
    //        "shoppingCartNumFields"   => array(
    //            1 => array(
    //                "jml"    => "(don't change)",
    //                "debet"  => "debet",
    //                "kredit" => "kredit",
    //            ),
    //            2 => array(
    //                "jml"    => "(don't change)",
    //                "debet"  => "debet",
    //                "kredit" => "kredit",
    //            ),
    //        ),
    //        "shoppingCartNumFields2"  => array(
    //            1 => array(
    //                "harga" => "receiving amount",
    //                "jml"   => "qty",
    //            ),
    //            2 => array(
    //                "harga" => "receiving amount",
    //                "jml"   => "qty",
    //            ),
    //        ),
    //        "shoppingCartNoteEnabled" => false,
    //        "shoppingCartAvoidRemove" => false,
    //        "shoppingCartSumFields"   => array(
    //            1 => array(
    //                //                "tagihan" => "amount remains to pay",
    //                //                "ppn" => "vat",
    //                //                "nett2" => "total",
    //            ),
    //        ),
    //        //        "shoppingCartPairedItem"  => array(
    //        //            "enabled"   => true,
    //        //            "mdlName"   => "MdlBankAccount",
    //        //            "mdlFilter" => array(
    //        //                "cabang_id=placeID"
    //        //            ),
    //        //            "srcKey"    => "id",
    //        //            "srcLabel"  => array("nama"),
    //        //            "mdlFilter" => array("id=id"),
    //        //        ),
    //
    //        //        "shoppingCartPairedSelectedItem" => array(
    //        //            "enabled" => true,
    //        //            "mdlName" => "ComRekeningPembantuKas",
    //        //            "srcKey" => "extern_id",
    //        //            "srcLabel" => array("nama"),
    //        //            "mdlFilter" => array(
    //        //                "cabang_id=placeID",
    //        //                "periode=forever",
    //        //                "rekening=kas",
    //        //                ),
    //        //        ),
    //
    //        "shoppingCartEditableFields" => array(
    //            1 => array(
    //                "jml",
    //                "debet",
    //                "kredit",
    //            ),
    //        ),
    //        "shoppingCartAmountValue"    => array(
    //            1 => "jml*harga",
    //            2 => "jml*harga",
    //        ),
    //        "receiptElements"            => array(
    //            //            "extern1" => array(
    //            //                "elementType" => "dataModel",
    //            //                "inputType" => "radio",
    //            //                "label" => "source sub-account",
    //            //                "mdlName" => "MdlExtern",
    //            //                "mdlFilter" => array("relName=srcRel"),
    //            //                "key" => "extern_id",
    //            //                "labelSrc" => "extern_nama",
    //            //                "usedFields" => array(
    //            //                    "extern_nama" => "account name",
    //            //                ),
    //            //                "editPoints" => array(1),
    //            //            ),
    //
    //            //            "extern2" => array(
    //            //                "elementType" => "dataModel",
    //            //                "inputType"   => "radio",
    //            //                "label"       => "target sub-account",
    //            //                "mdlName"     => "MdlExtern",
    //            //                "mdlFilter"   => array("relName=targetRel"),
    //            //                "key"         => "extern_id",
    //            //                "labelSrc"    => "extern_nama",
    //            //                "usedFields"  => array(
    //            //                    "extern_nama" => "account name",
    //            //                ),
    //            //                "editPoints"  => array(1),
    //            //            ),
    //        ),
    //
    //
    //    ),
    //
    //    //  config penyesuaian piutang, hutang, kas. pokoknya selain produk
    //    "888_1" => array(
    //        "icon" => "fa fa-cube",
    //        "label" => "penyesuaian non produk (tambah)",
    //        "place" => "center",//=> "center",
    //        "steps" => array(
    //            1 => array(
    //                "label" => "penyesuaian non produk (tambah)",
    //                "actionLabel" => "adjust",
    //                "source" => "",
    //                "target" => "888_1",
    //                "userGroup" => "root",
    //                "stateLabel" => "done",
    //                "stateColor" => "#dd3300",
    //                "stateCaption" => "made by",
    //            ),
    //        ),
    //        "template" => "application/template/transaksi.html",
    //        "selectorModel" => "MdlPembantu",
    //        "selectorSrcModel" => "MdlPembantu",
    //        "selectedPrice" => array(),
    //        "lockerCheck" => array(),
    //        "selectorFilters" => array(
    //            //            "cabang_id=placeID",
    //            //            "rekening=pihakID",
    //        ),
    //        "selectorCaller" => "Selectors/_selectorItem/selectItem",// bikin shopping cart background
    //        "selectorLabel" => "from account",
    //        "selectorParamFields" => array(
    //            "id" => "id",
    //            "name" => "name",
    //        ),
    //        "selectorViewedFields" => array(
    //            "name",
    //        ),
    //        "selectorProcessor" => "Selectors/_processSelectRekeningImporter/select",
    //        "editHandlerMethod" => "edit",
    //
    //        "pihakModel" => "MdlRekeningDebetKredit",
    //        "pihakName" => true,
    //        //        "pihakMainNota" => true,
    //        "pihakCaller" => "Selectors/_selectorPihak/selectPihak",
    //        "pihakLabel" => "rekening name",
    //        "pihakMainValueSrc" => array(
    //            "defPosition" => "defPosition",
    //        ),
    //        "pihakFilters" => array(
    //            "cabang_id=cabang_id",
    //            "id<>gudang_id",
    //        ),
    //        "pihakProcessor" => "Selectors/_processPihak/select",
    //
    //        "shortHistoryFields" => array(
    //            "jenis_label" => "activity",
    //            "dtime" => "date",
    //            "nomer" => "receipt number",
    //            "oleh_nama" => "person",
    //        ),
    //        "selectorFields" => array("id", "name"),
    //        "pihakFields" => array("id", "name"),
    //        "shoppingCart" => array(
    //            "initPrices" => "beli",
    //        ),
    //
    //        "shoppingCartFields" => array(
    //            1 => array(
    //                "nama" => "source account",
    //
    //            ),
    //            2 => array(
    //                "nama" => "source account",
    //
    //            ),
    //        ),
    //        //        "shoppingCartFields2" => array(
    //        //            1 => array(
    //        //                "nama" => "target account",
    //        //
    //        //            ),
    //        //            2 => array(
    //        //                "nama" => "target account",
    //        //
    //        //            ),
    //        //        ),
    //        "shoppingCartFieldSrc" => array(
    //            "name" => "name",
    //            "nama" => "nama",
    //        ),
    //        "shoppingCartNumFields" => array(
    //            1 => array(
    //                "jml" => "(don't change)",
    //                //                "debet" => "debet",
    //                //                "kredit" => "kredit",
    //                "harga" => "harga",
    //            ),
    //            //            2 => array(
    //            //                "jml" => "(don't change)",
    //            ////                "debet" => "debet",
    //            ////                "kredit" => "kredit",
    //            //                "harga" => "harga",
    //            //            ),
    //        ),
    //        //        "shoppingCartNumFields2" => array(
    //        //            1 => array(
    //        //                "harga" => "receiving amount",
    //        //                "jml" => "qty",
    //        //            ),
    //        //            2 => array(
    //        //                "harga" => "receiving amount",
    //        //                "jml" => "qty",
    //        //            ),
    //        //        ),
    //        "shoppingCartNoteEnabled" => false,
    //        "shoppingCartAvoidRemove" => false,
    //        "shoppingCartSumFields" => array(
    //            1 => array(
    //                "harga" => "total",
    //                //                "ppn" => "vat",
    //                //                "nett2" => "total",
    //            ),
    //        ),
    //        //        "shoppingCartPairedItem"  => array(
    //        //            "enabled"   => true,
    //        //            "mdlName"   => "MdlBankAccount",
    //        //            "mdlFilter" => array(
    //        //                "cabang_id=placeID"
    //        //            ),
    //        //            "srcKey"    => "id",
    //        //            "srcLabel"  => array("nama"),
    //        //            "mdlFilter" => array("id=id"),
    //        //        ),
    //
    //        //        "shoppingCartPairedSelectedItem" => array(
    //        //            "enabled" => true,
    //        //            "mdlName" => "ComRekeningPembantuKas",
    //        //            "srcKey" => "extern_id",
    //        //            "srcLabel" => array("nama"),
    //        //            "mdlFilter" => array(
    //        //                "cabang_id=placeID",
    //        //                "periode=forever",
    //        //                "rekening=kas",
    //        //                ),
    //        //        ),
    //
    //        "shoppingCartEditableFields" => array(
    //            1 => array(
    //                "jml",
    //                "debet",
    //                "kredit",
    //                "harga",
    //            ),
    //        ),
    //        "shoppingCartAmountValue" => array(
    //            1 => "jml*harga",
    //            2 => "jml*harga",
    //        ),
    //        "receiptElements" => array(
    //            //            "position" => array(
    //            //                "elementType" => "dataModel",
    //            //                "inputType" => "radio",
    //            //                "label" => "account position",
    //            //                "mdlName" => "MdlPosition",
    //            ////                "mdlName" => "MdlRekeningDebetKredit",
    //            ////                "mdlFilter" => array("relName=srcRel"),
    //            //                "key" => "id",
    //            //                "labelSrc" => "name",
    //            //                "usedFields" => array(
    //            //                    "name" => "position",
    //            //                ),
    //            //                "editPoints" => array(1),
    //            //            ),
    //        ),
    //    ),
    //    "888_2" => array(
    //        "icon" => "fa fa-cube",
    //        "label" => "penyesuaian non produk (kurang)",
    //        "place" => "branch",//=> "center",
    //        "steps" => array(
    //            1 => array(
    //                "label" => "penyesuaian non produk (kurang)",
    //                "actionLabel" => "adjust",
    //                "source" => "",
    //                "target" => "888_2",
    //                "userGroup" => "root",
    //                "stateLabel" => "done",
    //                "stateColor" => "#dd3300",
    //                "stateCaption" => "made by",
    //            ),
    //        ),
    //        "template" => "application/template/transaksi.html",
    //        "selectorModel" => "MdlPembantu",
    //        "selectorSrcModel" => "MdlPembantu",
    //        "selectedPrice" => array(),
    //        "lockerCheck" => array(),
    //        "selectorFilters" => array(
    //            //            "cabang_id=placeID",
    //            //            "rekening=pihakID",
    //        ),
    //        "selectorCaller" => "Selectors/_selectorItem/selectItem",// bikin shopping cart background
    //        "selectorLabel" => "from account",
    //        "selectorParamFields" => array(
    //            "id" => "id",
    //            "name" => "name",
    //        ),
    //        "selectorViewedFields" => array(
    //            "name",
    //        ),
    //        "selectorProcessor" => "Selectors/_processSelectRekeningImporter/select",
    //        "editHandlerMethod" => "edit",
    //
    //        "pihakModel" => "MdlRekeningDebetKredit",
    //        "pihakName" => true,
    //        "pihakCaller" => "Selectors/_selectorPihak/selectPihak",
    //        "pihakLabel" => "rekening name",
    //        "pihakMainValueSrc" => array(
    //            "defPosition" => "defPosition",
    //        ),
    //        "pihakFilters" => array(
    //            "cabang_id=cabang_id",
    //            "id<>gudang_id",
    //        ),
    //        "pihakProcessor" => "Selectors/_processPihak/select",
    //
    //        "shortHistoryFields" => array(
    //            "jenis_label" => "activity",
    //            "dtime" => "date",
    //            "nomer" => "receipt number",
    //            "oleh_nama" => "person",
    //        ),
    //        "selectorFields" => array("id", "nama"),
    //        "pihakFields" => array("id", "nama"),
    //        "shoppingCart" => array(
    //            "initPrices" => "beli",
    //        ),
    //
    //        "shoppingCartFields" => array(
    //            1 => array(
    //                "nama" => "source account",
    //
    //            ),
    //            2 => array(
    //                "nama" => "source account",
    //
    //            ),
    //        ),
    //        //        "shoppingCartFields2" => array(
    //        //            1 => array(
    //        //                "nama" => "target account",
    //        //
    //        //            ),
    //        //            2 => array(
    //        //                "nama" => "target account",
    //        //
    //        //            ),
    //        //        ),
    //        "shoppingCartFieldSrc" => array(
    //            "name" => "name",
    //            "nama" => "nama",
    //        ),
    //        "shoppingCartNumFields" => array(
    //            1 => array(
    //                "jml" => "(don't change)",
    //                //                "debet" => "debet",
    //                //                "kredit" => "kredit",
    //                "harga" => "harga",
    //            ),
    //            //            2 => array(
    //            //                "jml" => "(don't change)",
    //            ////                "debet" => "debet",
    //            ////                "kredit" => "kredit",
    //            //                "harga" => "harga",
    //            //            ),
    //        ),
    //        //        "shoppingCartNumFields2" => array(
    //        //            1 => array(
    //        //                "harga" => "receiving amount",
    //        //                "jml" => "qty",
    //        //            ),
    //        //            2 => array(
    //        //                "harga" => "receiving amount",
    //        //                "jml" => "qty",
    //        //            ),
    //        //        ),
    //        "shoppingCartNoteEnabled" => false,
    //        "shoppingCartAvoidRemove" => false,
    //        "shoppingCartSumFields" => array(
    //            1 => array(
    //                //                "tagihan" => "amount remains to pay",
    //                //                "ppn" => "vat",
    //                //                "nett2" => "total",
    //            ),
    //        ),
    //        //        "shoppingCartPairedItem"  => array(
    //        //            "enabled"   => true,
    //        //            "mdlName"   => "MdlBankAccount",
    //        //            "mdlFilter" => array(
    //        //                "cabang_id=placeID"
    //        //            ),
    //        //            "srcKey"    => "id",
    //        //            "srcLabel"  => array("nama"),
    //        //            "mdlFilter" => array("id=id"),
    //        //        ),
    //
    //        //        "shoppingCartPairedSelectedItem" => array(
    //        //            "enabled" => true,
    //        //            "mdlName" => "ComRekeningPembantuKas",
    //        //            "srcKey" => "extern_id",
    //        //            "srcLabel" => array("nama"),
    //        //            "mdlFilter" => array(
    //        //                "cabang_id=placeID",
    //        //                "periode=forever",
    //        //                "rekening=kas",
    //        //                ),
    //        //        ),
    //
    //        "shoppingCartEditableFields" => array(
    //            1 => array(
    //                "jml",
    //                "debet",
    //                "kredit",
    //                "harga",
    //            ),
    //        ),
    //        "shoppingCartAmountValue" => array(
    //            1 => "jml*harga",
    //            2 => "jml*harga",
    //        ),
    //        "receiptElements" => array(
    //            //            "position" => array(
    //            //                "elementType" => "dataModel",
    //            //                "inputType" => "radio",
    //            //                "label" => "account position",
    //            //                "mdlName" => "MdlPosition",
    //            ////                "mdlName" => "MdlRekeningDebetKredit",
    //            ////                "mdlFilter" => array("relName=srcRel"),
    //            //                "key" => "id",
    //            //                "labelSrc" => "name",
    //            //                "usedFields" => array(
    //            //                    "name" => "position",
    //            //                ),
    //            //                "editPoints" => array(1),
    //            //            ),
    //        ),
    //    ),
    //    //  config penyesuaian produk. pokoknya selain piutang, hutang, kas

    "777_1" => array(
        "icon" => "fa fa-cube",
        "label" => "adjustment product inventory (tambah)",
        "place" => "branch",//=> "center",
        "steps" => array(
            1 => array(
                "label" => "adjustment product inventory (tambah)",
                "actionLabel" => "adjust",
                "source" => "",
                "target" => "777_1",
                "userGroup" => "root",
                "stateLabel" => "done",
                "stateColor" => "#dd3300",
                "stateCaption" => "made by",
            ),
        ),
        "template" => "template/transaksi.html",
        "selectorModel" => "MdlPembantu",
        "selectorSrcModel" => "MdlPembantu",
        "selectedPrice" => array(),
        "lockerCheck" => array(),
        "selectorFilters" => array(
            //            "cabang_id=placeID",
            //            "rekening=pihakID",
        ),
        "selectorCaller" => "_selectorItem/selectItem",// bikin shopping cart background
        "selectorLabel" => "from account",
        "selectorParamFields" => array(
            "id" => "id",
            "name" => "name",
        ),
        "selectorViewedFields" => array(
            "name",
        ),
        "selectorProcessor" => "_processSelectRekeningImporter/select",
        "editHandlerMethod" => "edit",

        "pihakModel" => "MdlRekeningDebetKredit",
        "pihakName" => true,
        //        "pihakMainNota" => true,
        "pihakCaller" => "_selectorPihak/selectPihak",
        "pihakLabel" => "rekening name",
        "pihakMainValueSrc" => array(
            "defPosition" => "defPosition",
        ),
        "pihakFilters" => array(
            "cabang_id=cabang_id",
            "id<>gudang_id",
        ),
        "pihakProcessor" => "_processPihak/select",

        "shortHistoryFields" => array(
            "jenis_label" => "activity",
            "dtime" => "date",
            "nomer" => "receipt number",
            "oleh_nama" => "person",
        ),
        "selectorFields" => array("id", "nama"),
        "pihakFields" => array("id", "nama"),
        "shoppingCart" => array(
            "initPrices" => "beli",
        ),

        "shoppingCartFields" => array(
            1 => array(
                "nama" => "source account",

            ),
            2 => array(
                "nama" => "source account",

            ),
        ),
        "shoppingCartFieldSrc" => array(
            "name" => "name",
            "nama" => "nama",
        ),
        "shoppingCartNumFields" => array(
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
        "shoppingCartNoteEnabled" => false,
        "shoppingCartAvoidRemove" => false,
        "shoppingCartSumFields" => array(
            1 => array(
                //                "tagihan" => "amount remains to pay",
                //                "ppn" => "vat",
                //                "nett2" => "total",
            ),
        ),
        "shoppingCartEditableFields" => array(
            1 => array(
                "jml",
                "debet",
                "kredit",
                "harga",
            ),
        ),
        "shoppingCartAmountValue" => array(
            1 => "jml*harga",
            2 => "jml*harga",
        ),
        "receiptElements" => array(
            "opAccount" => array(
                "elementType" => "dataModel",
                "inputType" => "radio",
                "label" => "opposite account",
                "mdlName" => "MdlRekeningDebetKredit",
                "mdlFilter" => array(
                    "name<>pihakName",
                ),
                "key" => "id",
                "labelSrc" => "name",
                "usedFields" => array(
                    "name" => "name",
                ),
                "editPoints" => array(1, 2, 3, 4),
            ),
        ),
        "previewCtr" => "Create",
    ),
    "7778" => array(
        "icon" => "fa fa-cube",
        "label" => "adjustment product inventory (tambah)",
        "place" => "branch",//=> "center",
        "steps" => array(
            1 => array(
                "label" => "adjustment product inventory (tambah)",
                "actionLabel" => "adjust",
                "source" => "",
                "target" => "7778",
                "userGroup" => "root",
                "stateLabel" => "done",
                "stateColor" => "#dd3300",
                "stateCaption" => "made by",
            ),
        ),

    ),

    //    "777_2" => array(
    //        "icon" => "fa fa-cube",
    //        "label" => "adjustment for produk (kurang)",
    //        "place" => "branch",//=> "center",
    //        "steps" => array(
    //            1 => array(
    //                "label" => "adjustment for produk (kurang)",
    //                "actionLabel" => "adjust",
    //                "source" => "",
    //                "target" => "777_2",
    //                "userGroup" => "root",
    //                "stateLabel" => "done",
    //                "stateColor" => "#dd3300",
    //                "stateCaption" => "made by",
    //            ),
    //        ),
    //        "template" => "application/template/transaksi.html",
    //        "selectorModel" => "MdlPembantu",
    //        "selectorSrcModel" => "MdlPembantu",
    //        "selectedPrice" => array(),
    //        "lockerCheck" => array(),
    //        "selectorFilters" => array(
    ////            "cabang_id=placeID",
    ////            "rekening=pihakID",
    //        ),
    //        "selectorCaller" => "Selectors/_selectorItem/selectItem",// bikin shopping cart background
    //        "selectorLabel" => "from account",
    //        "selectorParamFields" => array(
    //            "id" => "id",
    //            "name" => "name",
    //        ),
    //        "selectorViewedFields" => array(
    //            "name",
    //        ),
    //        "selectorProcessor" => "Selectors/_processSelectRekeningImporter/select",
    //        "editHandlerMethod" => "edit",
    //
    //        "pihakModel" => "MdlRekeningDebetKredit",
    //        "pihakCaller" => "Selectors/_selectorPihak/selectPihak",
    //        "pihakLabel" => "rekening name",
    //        "pihakMainValueSrc" => array(
    //            "defPosition" => "defPosition",
    //        ),
    //        "pihakFilters" => array(
    //            "cabang_id=cabang_id",
    //            "id<>gudang_id",
    //        ),
    //        "pihakProcessor" => "Selectors/_processPihak/select",
    //
    //        "shortHistoryFields" => array(
    //            "jenis_label" => "activity",
    //            "dtime" => "date",
    //            "nomer" => "receipt number",
    //            "oleh_nama" => "person",
    //        ),
    //        "selectorFields" => array("id", "nama"),
    //        "pihakFields" => array("id", "nama"),
    //        "shoppingCart" => array(
    //            "initPrices" => "beli",
    //        ),
    //
    //        "shoppingCartFields" => array(
    //            1 => array(
    //                "nama" => "source account",
    //
    //            ),
    //            2 => array(
    //                "nama" => "source account",
    //
    //            ),
    //        ),
    ////        "shoppingCartFields2" => array(
    ////            1 => array(
    ////                "nama" => "target account",
    ////
    ////            ),
    ////            2 => array(
    ////                "nama" => "target account",
    ////
    ////            ),
    ////        ),
    //        "shoppingCartFieldSrc" => array(
    //            "name" => "name",
    //            "nama" => "nama",
    //        ),
    //        "shoppingCartNumFields" => array(
    //            1 => array(
    //                "jml" => "(don't change)",
    ////                "debet" => "debet",
    ////                "kredit" => "kredit",
    //                "harga" => "harga",
    //            ),
    ////            2 => array(
    ////                "jml" => "(don't change)",
    //////                "debet" => "debet",
    //////                "kredit" => "kredit",
    ////                "harga" => "harga",
    ////            ),
    //        ),
    ////        "shoppingCartNumFields2" => array(
    ////            1 => array(
    ////                "harga" => "receiving amount",
    ////                "jml" => "qty",
    ////            ),
    ////            2 => array(
    ////                "harga" => "receiving amount",
    ////                "jml" => "qty",
    ////            ),
    ////        ),
    //        "shoppingCartNoteEnabled" => false,
    //        "shoppingCartAvoidRemove" => false,
    //        "shoppingCartSumFields" => array(
    //            1 => array(
    ////                "tagihan" => "amount remains to pay",
    ////                "ppn" => "vat",
    ////                "nett2" => "total",
    //            ),
    //        ),
    //        //        "shoppingCartPairedItem"  => array(
    //        //            "enabled"   => true,
    //        //            "mdlName"   => "MdlBankAccount",
    //        //            "mdlFilter" => array(
    //        //                "cabang_id=placeID"
    //        //            ),
    //        //            "srcKey"    => "id",
    //        //            "srcLabel"  => array("nama"),
    //        //            "mdlFilter" => array("id=id"),
    //        //        ),
    //
    //        //        "shoppingCartPairedSelectedItem" => array(
    //        //            "enabled" => true,
    //        //            "mdlName" => "ComRekeningPembantuKas",
    //        //            "srcKey" => "extern_id",
    //        //            "srcLabel" => array("nama"),
    //        //            "mdlFilter" => array(
    //        //                "cabang_id=placeID",
    //        //                "periode=forever",
    //        //                "rekening=kas",
    //        //                ),
    //        //        ),
    //
    //        "shoppingCartEditableFields" => array(
    //            1 => array(
    //                "jml",
    //                "debet",
    //                "kredit",
    //                "harga",
    //            ),
    //        ),
    //        "shoppingCartAmountValue" => array(
    //            1 => "jml*harga",
    //            2 => "jml*harga",
    //        ),
    //        "receiptElements" => array(
    //            "opAccount" => array(
    //                "elementType" => "dataModel",
    //                "inputType" => "radio",
    //                "label" => "opposite account",
    //                "mdlName" => "MdlRekeningDebetKredit",
    //                "mdlFilter" => array(
    //                    "name<>pihakName",
    //                ),
    //                "key" => "id",
    //                "labelSrc" => "name",
    //                "usedFields" => array(
    //                    "name" => "name",
    //                ),
    //                "editPoints" => array(1, 2, 3, 4),
    //            ),
    //        ),
    //    ),
    //    //  config penyesuaian supplies. pokoknya selain piutang, hutang, kas
    //    "666_1" => array(
    //        "icon" => "fa fa-cube",
    //        "label" => "adjustment for supplies (tambah)",
    //        "place" => "branch",//=> "center",
    //        "steps" => array(
    //            1 => array(
    //                "label" => "adjustment for supplies (tambah)",
    //                "actionLabel" => "adjust",
    //                "source" => "",
    //                "target" => "666_1",
    //                "userGroup" => "root",
    //                "stateLabel" => "done",
    //                "stateColor" => "#dd3300",
    //                "stateCaption" => "made by",
    //            ),
    //        ),
    //        "template" => "application/template/transaksi.html",
    //        "selectorModel" => "MdlPembantu",
    //        "selectorSrcModel" => "MdlPembantu",
    //        "selectedPrice" => array(),
    //        "lockerCheck" => array(),
    //        "selectorFilters" => array(
    ////            "cabang_id=placeID",
    ////            "rekening=pihakID",
    //        ),
    //        "selectorCaller" => "Selectors/_selectorItem/selectItem",// bikin shopping cart background
    //        "selectorLabel" => "from account",
    //        "selectorParamFields" => array(
    //            "id" => "id",
    //            "name" => "name",
    //        ),
    //        "selectorViewedFields" => array(
    //            "name",
    //        ),
    //        "selectorProcessor" => "Selectors/_processSelectRekeningImporter/select",
    //        "editHandlerMethod" => "edit",
    //
    //        "pihakModel" => "MdlRekeningDebetKredit",
    //        "pihakCaller" => "Selectors/_selectorPihak/selectPihak",
    //        "pihakLabel" => "rekening name",
    //        "pihakMainValueSrc" => array(
    //            "defPosition" => "defPosition",
    //        ),
    //        "pihakFilters" => array(
    //            "cabang_id=cabang_id",
    //            "id<>gudang_id",
    //        ),
    //        "pihakProcessor" => "Selectors/_processPihak/select",
    //
    //        "shortHistoryFields" => array(
    //            "jenis_label" => "activity",
    //            "dtime" => "date",
    //            "nomer" => "receipt number",
    //            "oleh_nama" => "person",
    //        ),
    //        "selectorFields" => array("id", "nama"),
    //        "pihakFields" => array("id", "nama"),
    //        "shoppingCart" => array(
    //            "initPrices" => "beli",
    //        ),
    //
    //        "shoppingCartFields" => array(
    //            1 => array(
    //                "nama" => "source account",
    //
    //            ),
    //            2 => array(
    //                "nama" => "source account",
    //
    //            ),
    //        ),
    //        "shoppingCartFieldSrc" => array(
    //            "name" => "name",
    //            "nama" => "nama",
    //        ),
    //        "shoppingCartNumFields" => array(
    //            1 => array(
    //                "jml" => "(don't change)",
    ////                "debet" => "debet",
    ////                "kredit" => "kredit",
    //                "harga" => "harga",
    //            ),
    ////            2 => array(
    ////                "jml" => "(don't change)",
    //////                "debet" => "debet",
    //////                "kredit" => "kredit",
    ////                "harga" => "harga",
    ////            ),
    //        ),
    //        "shoppingCartNoteEnabled" => false,
    //        "shoppingCartAvoidRemove" => false,
    //        "shoppingCartSumFields" => array(
    //            1 => array(
    ////                "tagihan" => "amount remains to pay",
    ////                "ppn" => "vat",
    ////                "nett2" => "total",
    //            ),
    //        ),
    //        "shoppingCartEditableFields" => array(
    //            1 => array(
    //                "jml",
    //                "debet",
    //                "kredit",
    //                "harga",
    //            ),
    //        ),
    //        "shoppingCartAmountValue" => array(
    //            1 => "jml*harga",
    //            2 => "jml*harga",
    //        ),
    //        "receiptElements" => array(
    //            "opAccount" => array(
    //                "elementType" => "dataModel",
    //                "inputType" => "radio",
    //                "label" => "opposite account",
    //                "mdlName" => "MdlRekeningDebetKredit",
    //                "mdlFilter" => array(
    //                    "name<>pihakName",
    //                ),
    //                "key" => "id",
    //                "labelSrc" => "name",
    //                "usedFields" => array(
    //                    "name" => "name",
    //                ),
    //                "editPoints" => array(1, 2, 3, 4),
    //            ),
    //        ),
    //    ),
    //    "666_2" => array(
    //        "icon" => "fa fa-cube",
    //        "label" => "adjustment for supplies (kurang)",
    //        "place" => "branch",//=> "center",
    //        "steps" => array(
    //            1 => array(
    //                "label" => "adjustment for supplies (kurang)",
    //                "actionLabel" => "adjust",
    //                "source" => "",
    //                "target" => "666_2",
    //                "userGroup" => "root",
    //                "stateLabel" => "done",
    //                "stateColor" => "#dd3300",
    //                "stateCaption" => "made by",
    //            ),
    //        ),
    //        "template" => "application/template/transaksi.html",
    //        "selectorModel" => "MdlPembantu",
    //        "selectorSrcModel" => "MdlPembantu",
    //        "selectedPrice" => array(),
    //        "lockerCheck" => array(),
    //        "selectorFilters" => array(
    ////            "cabang_id=placeID",
    ////            "rekening=pihakID",
    //        ),
    //        "selectorCaller" => "Selectors/_selectorItem/selectItem",// bikin shopping cart background
    //        "selectorLabel" => "from account",
    //        "selectorParamFields" => array(
    //            "id" => "id",
    //            "name" => "name",
    //        ),
    //        "selectorViewedFields" => array(
    //            "name",
    //        ),
    //        "selectorProcessor" => "Selectors/_processSelectRekeningImporter/select",
    //        "editHandlerMethod" => "edit",
    //
    //        "pihakModel" => "MdlRekeningDebetKredit",
    //        "pihakCaller" => "Selectors/_selectorPihak/selectPihak",
    //        "pihakLabel" => "rekening name",
    //        "pihakMainValueSrc" => array(
    //            "defPosition" => "defPosition",
    //        ),
    //        "pihakFilters" => array(
    //            "cabang_id=cabang_id",
    //            "id<>gudang_id",
    //        ),
    //        "pihakProcessor" => "Selectors/_processPihak/select",
    //
    //        "shortHistoryFields" => array(
    //            "jenis_label" => "activity",
    //            "dtime" => "date",
    //            "nomer" => "receipt number",
    //            "oleh_nama" => "person",
    //        ),
    //        "selectorFields" => array("id", "nama"),
    //        "pihakFields" => array("id", "nama"),
    //        "shoppingCart" => array(
    //            "initPrices" => "beli",
    //        ),
    //
    //        "shoppingCartFields" => array(
    //            1 => array(
    //                "nama" => "source account",
    //
    //            ),
    //            2 => array(
    //                "nama" => "source account",
    //
    //            ),
    //        ),
    //        "shoppingCartFieldSrc" => array(
    //            "name" => "name",
    //            "nama" => "nama",
    //        ),
    //        "shoppingCartNumFields" => array(
    //            1 => array(
    //                "jml" => "(don't change)",
    ////                "debet" => "debet",
    ////                "kredit" => "kredit",
    //                "harga" => "harga",
    //            ),
    ////            2 => array(
    ////                "jml" => "(don't change)",
    //////                "debet" => "debet",
    //////                "kredit" => "kredit",
    ////                "harga" => "harga",
    ////            ),
    //        ),
    //        "shoppingCartNoteEnabled" => false,
    //        "shoppingCartAvoidRemove" => false,
    //        "shoppingCartSumFields" => array(
    //            1 => array(
    ////                "tagihan" => "amount remains to pay",
    ////                "ppn" => "vat",
    ////                "nett2" => "total",
    //            ),
    //        ),
    //        "shoppingCartEditableFields" => array(
    //            1 => array(
    //                "jml",
    //                "debet",
    //                "kredit",
    //                "harga",
    //            ),
    //        ),
    //        "shoppingCartAmountValue" => array(
    //            1 => "jml*harga",
    //            2 => "jml*harga",
    //        ),
    //        "receiptElements" => array(
    //            "opAccount" => array(
    //                "elementType" => "dataModel",
    //                "inputType" => "radio",
    //                "label" => "opposite account",
    //                "mdlName" => "MdlRekeningDebetKredit",
    //                "mdlFilter" => array(
    //                    "name<>pihakName",
    //                ),
    //                "key" => "id",
    //                "labelSrc" => "name",
    //                "usedFields" => array(
    //                    "name" => "name",
    //                ),
    //                "editPoints" => array(1, 2, 3, 4),
    //            ),
    //        ),
    //    ),
    //  DEVELOPMENT ONLY

);


