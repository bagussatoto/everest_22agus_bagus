<?php


class ToolRepair2 extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->masterConfigUi = $this->config->item("heTransaksi_ui");
        $this->load->helper("he_angka");
    }

    function index()
    {
        $arrTools = array(
            "kas" => "viewUnsyncedKas",
            "produk" => "viewUnsyncedProduk",
            "produk rakitan" => "viewUnsyncedProdukRakitan",
            "supplies" => "viewUnsyncedSupplies",
            "valas" => "viewUnsyncedValas",
        );

//        foreach ($arrTools as $key => $value) {
//            echo "<div>";
//            echo "<h3>";
//            echo "<a href='" . base_url() . get_class($this) . "/$value' target='_blank'>:: $key ::</a>";
//            echo "</h3>";
//            echo "</div>";
//        }
    }

    //-------------------------
    public function patchProject()
    {
        $this->load->helper("he_mass_table");
        $this->load->model("MdlTransaksi");
        $this->load->model("Coms/ComTransaksiProject");
        $arrComProject = array();
//        array(
//            "comName" => "TransaksiProject",
//            "loop" => array(
//                "project" => "grandTotal",
//            ),
//            "static" => array(
//                "cabang_id" => "placeID",
//                "cabang_nama" => "placeName",
//                "extern_id" => "projectID",
//                "extern_nama" => "projectName",
//                "terbayar" => "grandTotal",
//            ),
//            "reversable" => true,
//            "srcGateName" => "main",
//            "srcRawGateName" => "main",
//        ),

        $tr = New MdlTransaksi();
        $tr->addFilter("trash_4='0'");
        $tr->addFilter("jenis='588st'");
        $trTmp = $tr->lookupAll()->result();
        cekBiru(count($trTmp));
        if (sizeof($trTmp) > 0) {
            foreach ($trTmp as $trSpec) {
                $trid = $trSpec->id;
                $trreg = New MdlTransaksi();
                $trreg->setFilters(array());
                $trreg->addFilter("transaksi_id='$trid'");
                $trreg->setJointSelectFields("transaksi_id, main");
                $tmpReg = $trreg->lookupDataRegistries()->result();
                $main = blobDecode($tmpReg[0]->main);
                $arrComProject[$trid] = array(
                    "loop" => array(
                        "project" => $main["grandTotal"],
                    ),
                    "static" => array(
                        "cabang_id" => $main["placeID"],
                        "cabang_nama" => $main["placeName"],
                        "extern_id" => $main["projectID"],
                        "extern_nama" => $main["projectName"],
                        "terbayar" => $main["grandTotal"],
                        "transaksi_id" => $trSpec->id,
                        "transaksi_no" => $trSpec->nomer,
                        "dtime" => $trSpec->dtime,
                        "fulldate" => $trSpec->fulldate,
                    ),
                );
            }
        }
//        arrPrintCyan($arrComProject);


        $this->db->trans_start();

        if (sizeof($arrComProject) > 0) {
            foreach ($arrComProject as $trid => $spec) {
                $cp = New ComTransaksiProject();
                $cp->pair($spec);
                $cp->exec();
            }
        }

        mati_disini("---SETOP--- " . __LINE__);
        $this->db->trans_complete() or die("Gagal saat berusaha  commit transaction!");
        cekHijau("<h3>DONE...</h3>");
    }


    public function patchPenerimaanPenjualanTunai_OLD()
    {
        $date1 = "2024-07-01";
        $date2 = "2024-12-31";

        $this->load->model("MdlTransaksi");
        $tr = New MdlTransaksi();
        $tr->addFilter("jenis='4464'");
//        $tr->addFilter("trash_4='0'");
        $tr->addFilter("date(dtime)>='$date1'");
        $tr->addFilter("date(dtime)<='$date2'");
        $trTmp = $tr->lookupAll()->result();
        showLast_query("biru");
        cekBiru(count($trTmp));


        $this->db->trans_start();


        if (sizeof($trTmp) > 0) {
            foreach ($trTmp as $trSpec) {
                $trid = $trSpec->id;
                $trreg = New MdlTransaksi();
                $trreg->setFilters(array());
                $trreg->addFilter("transaksi_id='$trid'");
                $trreg->setJointSelectFields("transaksi_id, main");
                $tmpReg = $trreg->lookupDataRegistries()->result();
                $main = blobDecode($tmpReg[0]->main);
                $nilai_bayar = $main["nilai_bayar"];

                $tr = New MdlTransaksi();
                $where = array(
                    "id" => $trid,
                );
                $data = array(
                    "transaksi_nilai" => $nilai_bayar,
                    "transaksi_net" => $nilai_bayar,
                );
                $tr->setFilters(array());
                $tr->updateData($where, $data);
                showLast_query("orange");
            }
        }

//        mati_disini("---SETOP--- " . __LINE__);
        $this->db->trans_complete() or die("Gagal saat berusaha  commit transaction!");
        cekHijau("<h3>DONE...</h3>");


    }

    public function patchPenerimaanPenjualanTunai()
    {
//        $date1 = "2024-01-01";
//        $date2 = "2024-06-31";
//        $date1 = "2024-07-01";
//        $date2 = "2024-12-31";
        $date1 = "2025-01-01";
        $date2 = "2025-12-31";

        $this->load->model("MdlTransaksi");
        $tr = New MdlTransaksi();
        $tr->addFilter("jenis in ('4464','749')");
//        $tr->addFilter("trash_4='0'");
        $tr->addFilter("date(dtime)>='$date1'");
        $tr->addFilter("date(dtime)<='$date2'");
        $trTmp = $tr->lookupAll()->result();
        showLast_query("biru");
        cekBiru(count($trTmp));


        $this->db->trans_start();


        if (sizeof($trTmp) > 0) {
            foreach ($trTmp as $trSpec) {
                $trid = $trSpec->id;
                $trreg = New MdlTransaksi();
                $trreg->setFilters(array());
                $trreg->addFilter("transaksi_id='$trid'");
                $trreg->setJointSelectFields("transaksi_id, main");
                $tmpReg = $trreg->lookupDataRegistries()->result();
                $main = blobDecode($tmpReg[0]->main);
                $nilai_bayar = $main["nilai_bayar"];

                $tr = New MdlTransaksi();
                $where = array(
                    "id" => $trid,
                );
                $data = array(
                    "transaksi_nilai" => $nilai_bayar,
                    "transaksi_net" => $nilai_bayar,
                    //----
                    "bank_id" => isset($main["cash_account__folders"]) ? $main["cash_account__folders"] : 0,
                    "bank_nama" => isset($main["cash_account__folders_nama"]) ? $main["cash_account__folders_nama"] : 0,
                    "bank_rekening_id" => isset($main["cash_account"]) ? $main["cash_account"] : 0,
                    "bank_rekening_nama" => isset($main["cash_account__label"]) ? $main["cash_account__label"] : 0,
                );
                $tr->setFilters(array());
                $tr->updateData($where, $data);
                showLast_query("orange");
            }
        }

//        mati_disini("---SETOP--- " . __LINE__);
        $this->db->trans_complete() or die("Gagal saat berusaha  commit transaction!");
        cekHijau("<h3>DONE...</h3>");


    }


    public function run_susulanJurnal()
    {

        $this->load->model("MdlTransaksi");
        $this->load->model("CustomCounter");
        $this->load->helper("he_mass_table");
        $startDate = dtimeNow();


        $getTrID = (isset($_GET['tr_id']) && ($_GET['tr_id'] > 0)) ? $_GET['tr_id'] : 0;
        $addJudul = "";

        $tr = New MdlTransaksi();
        $tr->setSortBy(
            array(
                "kolom" => "id",
                "mode" => "ASC",
            )
        );
        $this->db->limit(1);

        $getTrID = 879252;

        // bila ada trID dari URL, maka ini adalah cek manual, tidak boleh close commit !!!
        if ($getTrID > 0) {
            $tr->addFilter("id='$getTrID'");

            $addJudul = "<br>cek manual";
        }
        else {
            $tr->addFilter("cli='0'");
        }

        $trTmp = $tr->lookupAll()->result();
        cekHere($this->db->last_query() . "<br>" . sizeof($trTmp));
//        mati_disini(__LINE__);


        if (sizeof($trTmp) > 0) {
            $trID_cli = $trTmp[0]->id;
            $trTmpCabangID = $trTmp[0]->cabang_id;
            $kolom = array(
                "trID" => "id",
                "jenisTr" => "jenis",
                "jenisTrMaster" => "jenis_master",
                "jenisTrTop" => "jenis_top",
                "nomer" => "nomer",
                "nomerTop" => "nomer_top",
                "dtime" => "dtime",
                "fulldate" => "fulldate",
                "stepNumber" => "step_number",
                "indexRegistry" => "indexing_registry",
                "olehID" => "oleh_id",
                "olehNama" => "oleh_nama",
            );

            $arrKolomTrans = array();
            foreach ($kolom as $key => $val) {
                $arrKolomTrans[$key] = isset($trTmp[0]->$val) ? $trTmp[0]->$val : NULL;
            }

            $reg = New MdlTransaksi();
            $key = "indexRegistry";
            $index_reg = blobDecode($arrKolomTrans[$key]);
            $reg->setFilters(array());
//            $reg->addFilter("id in ('" . implode("','", $index_reg) . "')");
            $reg->addFilter("transaksi_id='" . $trTmp[0]->id . "'");
            $regTmp = $reg->lookupDataRegistries()->result();
            $registryGates = array();
            foreach ($regTmp as $regSpec) {
                foreach ($regSpec as $key_reg => $val_reg) {
                    if ($key_reg != "transaksi_id") {
                        $registryGates[$key_reg] = blobDecode($val_reg);
                    }
                }
            }

//cekHitam(":: cetak REGISTRY ::");
//            arrPrintWebs($registryGates["items8_sum"]);
//mati_disini();
//            arrprint($arrKolomTrans);
//            arrPrint($registryGates["items"]);
//             mati_disini();
            $jenisTr = $arrKolomTrans['jenisTr'];
            $jenisTrMaster = $arrKolomTrans['jenisTrMaster'];
            $fulldate = $arrKolomTrans['fulldate'];
            $dtime = $arrKolomTrans['dtime'];
            $stepNumber = $arrKolomTrans['stepNumber'];
            $insertNum = $tmpNomorNota = $arrKolomTrans['nomer'];
            $olehNama = $arrKolomTrans['olehNama'];
            $insertID = $transaksiID = $arrKolomTrans['trID'];
            /*---------------------- jenismaster untuk gerbang utama masuk modul, jenisTr adalah targetnya */
            /*------end*/
            $configCore = loadConfigModulJenis_he_misc($jenisTrMaster, "coTransaksiCore");
            $configUi = loadConfigModulJenis_he_misc($jenisTrMaster, "coTransaksiUi");
            $configLayout = loadConfigModulJenis_he_misc($jenisTrMaster, "coTransaksiLayout");

            cekHitam(":: jenisTrMaster-> $jenisTrMaster :: jenisTr-> $jenisTr :: [trID_cli: $trID_cli]");

            $cliComponent = "components";

            $pakai_ini = 0;
            if ($pakai_ini == 1) {

                //region BUILD TABEL DATABASE OTOMATIS
                $buildTablesDetail = isset($configCore[$cliComponent][$jenisTr]['detail']) ? $configCore[$cliComponent][$jenisTr]['detail'] : array();
//arrPrintWebs($buildTablesDetail);
                if (sizeof($buildTablesDetail) > 0) {
                    foreach ($buildTablesDetail as $buildTablesDetail_specs) {
//arrPrintWebs($buildTablesDetail_specs);
                        $buildTablesDetail_specs_result = $buildTablesDetail_specs;
                        $srcGateName = $buildTablesDetail_specs['srcGateName'];
                        $srcRawGateName = $buildTablesDetail_specs['srcRawGateName'];
//                    cekHitam(__LINE__ . ":: $srcGateName");
                        if (isset($registryGates[$srcGateName]) && sizeof($registryGates[$srcGateName]) > 0) {
                            foreach ($registryGates[$srcGateName] as $itemSpec) {

//                            arrPrintWebs($itemSpec);
                                $mdlName = $buildTablesDetail_specs['comName'];
//                            cekBiru("== $srcGateName == $mdlName ==");
                                if (substr($mdlName, 0, 1) == "{") {
                                    $mdlName = trim($mdlName, "{");
                                    $mdlName = trim($mdlName, "}");
                                    $mdlName = str_replace($mdlName, $itemSpec[$mdlName], $mdlName);
                                }

//cekBiru("== $mdlName ==");
                                if (isset($buildTablesDetail_specs['loop'])) {
                                    foreach ($buildTablesDetail_specs['loop'] as $key => $val) {
//cekKuning(":: $key => $val ::");
                                        unset($buildTablesDetail_specs_result['loop']);
                                        if (substr($key, 0, 1) == "{") {
                                            $key = trim($key, "{");
                                            $key = trim($key, "}");
                                            $key = str_replace($key, $itemSpec[$key], $key);
                                        }
                                        $buildTablesDetail_specs_result['loop'][$key] = $val;
//                                cekHitam("LINE: " . __LINE__ . " ::sini bukan??  akan build tabel detail $key");
                                    }
                                }

//arrPrintWebs($buildTablesDetail_specs_result['loop']);
//                        cekHere($mdlName . " == " . $srcGateName);
                                $mdlName = "Com" . $mdlName;
                                $this->load->model("Coms/" . $mdlName);
                                $m = new $mdlName();
                                if (method_exists($m, "getTableNameMaster")) {
                                    if (sizeof($m->getTableNameMaster())) {
//                                cekMerah(":: $mdlName ::");
//                                arrPrintWebs($buildTablesDetail_specs_result);
                                        $m->buildTables($buildTablesDetail_specs_result);
                                    }
                                }
                            }

                        }
                        else {
//                        cekHere("TESTSTST");
                        }
                    }
                }
                else {
                    cekMerah(":: TIDAK ADA CONFIG cliComponent");
                }
                //endregion

            }


            $this->db->trans_start();

            $paramPatchers = $this->config->item('heTransaksi_paramPatchers') != null ? $this->config->item('heTransaksi_paramPatchers') : array();
            $paramForceFillers = $this->config->item('heTransaksi_paramForceFillers') != null ? $this->config->item('heTransaksi_paramForceFillers') : array();
            $validateSubComponent = $this->config->item('heTransaksi_validateComponentDetail') != null ? $this->config->item('heTransaksi_validateComponentDetail') : array();
            $paramForceFillersJenisTR = $this->config->item('heTransaksi_paramForceFillers_jenisTR') != null ? $this->config->item('heTransaksi_paramForceFillers_jenisTR') : array();

            $pakai_ini = 0;
            if ($pakai_ini == 1) {

                //region ----------subcomponents by cli
                $componentGate['detail'] = array();
                $componentConfig['master'] = array();
                $componentConfig['detail'] = array();
                if (isset($configCore['relativeComponets']) && $configCore['relativeComponets'] == true) {
                    $iterator = isset($registryGates['revert']['jurnal']['detail']) ? $registryGates['revert']['jurnal']['detail'] : array();
                    $revertedTarget = $registryGates['main']['pihakExternID'];
                    $componentConfig['detail'] = $iterator;
                    $iteratorMaster = $componentConfig['master'] = isset($registryGates['revert']['jurnal']['master']) ? $registryGates['revert']['jurnal']['master'] : array();
                }
                else {
                    $iterator = isset($configCore[$cliComponent][$jenisTr]['detail']) ? $configCore[$cliComponent][$jenisTr]['detail'] : array();
                    $componentConfig['detail'] = $iterator;
                    $iteratorMaster = $componentConfig['master'] = isset($configCore[$cliComponent][$jenisTr]['master']) ? $configCore[$cliComponent][$jenisTr]['master'] : array();

                    $revertedTarget = "";

                }
                $subComModel = array();
                if (sizeof($iterator) > 0) {
//                arrPrintKuning($iterator);
                    $compValidators = ($this->config->item('transaksi_value_required_components') != null) ? $this->config->item('transaksi_value_required_components') : array();
                    $filterNeeded = false;

                    $arrRekeningLoop = array();

//                if (in_array($mdlName, $compValidators)) {//perlu validasi filter
//                    $filterNeeded = true;
//                }
                    foreach ($iterator as $cCtr => $tComSpec) {
                        $comName_orig = $comName = $tComSpec['comName'];
                        $srcGateName = $tComSpec['srcGateName'];
                        $loopRequire = isset($tComSpec['loopRequire']) ? $tComSpec['loopRequire'] : false;
                        $srcRawGateName = $tComSpec['srcRawGateName'];

                        echo "sub-component: $comName, $srcGateName, initializing values <br>";

                        $tmpOutParams[$cCtr] = array();
                        if (isset($registryGates[$srcGateName]) && sizeof($registryGates[$srcGateName]) > 0) {

                            foreach ($registryGates[$srcGateName] as $id => $dSpec) {
                                $comName = $comName_orig;
                                if (substr($comName, 0, 1) == "{") {
                                    $comName = trim($comName, "{");
                                    $comName = trim($comName, "}");
                                    $comName = str_replace($comName, $registryGates[$srcGateName][$id][$comName], $comName);
                                    $tComSpec['comName'] = $comName;
                                    $iterator[$cCtr]['comName'] = $comName;
                                }
//                        $subComModel[$comName] = $comName;
                                $filterNeeded = false;
                                $mdlName = "Com" . ucfirst($comName);
                                if (in_array($mdlName, $compValidators)) {//perlu validasi filter
                                    $filterNeeded = true;
                                }


                                $subParams = array();
                                if (isset($tComSpec['loop'])) {
                                    foreach ($tComSpec['loop'] as $key => $value) {
                                        if (substr($key, 0, 1) == "{") {
                                            $key = trim($key, "{");
                                            $key = trim($key, "}");
                                            $key = str_replace($key, $registryGates[$srcGateName][$id][$key], $key);
                                        }

                                        $subComModel[$key] = $comName;

                                        $realValue = makeValue($value, $registryGates[$srcGateName][$id], $registryGates[$srcGateName][$id], 0);

                                        if (strlen($key) > 1) {
                                            $subParams['loop'][$key] = $realValue;
                                        }
                                        else {
                                            $subParams['loop'] = array();
                                        }

                                        // =================== =================== ===================
                                        if (!isset($arrRekeningLoop[$dSpec[$tComSpec['static']['cabang_id']]][$key])) {
                                            $arrRekeningLoop[$dSpec[$tComSpec['static']['cabang_id']]][$key] = 0;
                                        }
                                        $arrRekeningLoop[$dSpec[$tComSpec['static']['cabang_id']]][$key] += $realValue;
                                        if ($realValue != 0) {
                                            cekUngu(":: cetak loop $key => $realValue ::");
                                        }

                                        if ($filterNeeded) {
                                            if ($subParams['loop'][$key] == 0) {
                                                unset($subParams['loop'][$key]);

                                                // =================== =================== ===================
                                            }
                                        }
                                    }
                                }
                                if (isset($tComSpec['static'])) {
                                    foreach ($tComSpec['static'] as $key => $value) {

                                        $realValue = makeValue($value, $registryGates[$srcGateName][$id], $registryGates[$srcGateName][$id], 0);
//                                    $subParams['static'][$key] = $realValue;
                                        $subParams['static'][$key] = trim($realValue);
//                                cekKuning("STATIC: $key diisi dengan $realValue");
                                    }
                                    if (isset($paramPatchers[$comName]) && sizeof($paramPatchers[$comName]) > 0) {
                                        foreach ($paramPatchers[$comName] as $k => $v) {
                                            if (!isset($subParams['static'][$k])) {
                                                $subParams['static'][$k] = isset($$v) ? $$v : "_v";
                                                cekOrange("fill :: $comName :: $k ($v) => " . $subParams['static'][$k]);
                                            }
                                        }
                                    }
                                    if (isset($paramForceFillers[$comName]) && sizeof($paramForceFillers[$comName]) > 0) {

                                        $jenis = $registryGates['main']['jenis'];
                                        foreach ($paramForceFillers[$comName] as $k => $v) {
                                            $subParams['static'][$k] = isset($$v) ? $$v : "_v";
                                            cekOrange("fillforce :: $comName :: $k ($v) => " . $subParams['static'][$k]);
                                        }
                                    }
//                                arrPrintWebs($paramForceFillersJenisTR[$comName]);
//                                cekMerah($jenisTrMaster);
                                    // tambahan custom gerbang saat simpan transaksi, tidak bisa ditambahkan di coTransaksiCore/coTransaksiValues
                                    if (isset($paramForceFillersJenisTR[$comName][$jenisTrMaster]) && sizeof($paramForceFillersJenisTR[$comName][$jenisTrMaster]) > 0) {
                                        foreach ($paramForceFillersJenisTR[$comName][$jenisTrMaster] as $k => $v) {
                                            $subParams['static'][$k] = isset($$v) ? $$v : "_v";
                                            cekorange(":: $k diisikan dengan " . $subParams['static'][$k]);
                                        }
                                    }
                                    $subParams['static']["fulldate"] = $fulldate;
                                    $subParams['static']["dtime"] = $dtime;
                                    $subParams['static']["keterangan"] = $configUi['steps'][$stepNumber]['label'] . " nomor " . $tmpNomorNota . " oleh " . $olehNama;
                                    //------
                                    $subParams['static']["reference_id"] = isset($dSpec["referenceID"]) ? $dSpec["referenceID"] : "";
                                    $subParams['static']["reference_nomer"] = isset($dSpec["referenceNomer"]) ? $dSpec["referenceNomer"] : "";
                                    $subParams['static']["reference_jenis"] = isset($dSpec["jenisTr_reference"]) ? $dSpec["jenisTr_reference"] : "";
                                    $subParams['static']["reference_id_top"] = isset($dSpec["referenceID_top"]) ? $dSpec["referenceID_top"] : "";
                                    $subParams['static']["reference_nomer_top"] = isset($dSpec["referenceNomer_top"]) ? $dSpec["referenceNomer_top"] : "";
                                    $subParams['static']["reference_jenis_top"] = isset($dSpec["pihakExternMasterID"]) ? $dSpec["pihakExternMasterID"] : "";
                                    //------
                                    if (strlen($revertedTarget) > 1) {
                                        $subParams['static']['reverted_target'] = $revertedTarget;
                                    }
                                }
                                if (sizeof($subParams) > 0) {
                                    if ($filterNeeded) {
                                        if (isset($subParams['loop']) && !empty($subParams['loop'])) {
                                            $tmpOutParams[$cCtr][] = $subParams;
                                        }
                                    }
                                    else {
                                        if (empty($subParams['loop']) && $loopRequire == true) {
                                            unset($tmpOutParams[$cCtr]);
                                        }
                                        else {
                                            $tmpOutParams[$cCtr][] = $subParams;
                                        }
                                    }
                                }
                            }

                            $componentGate['detail'][$cCtr] = $subParams;
                        }

                    }
//                arrPrintKuning($tmpOutParams);
                    $it = 0;
                    foreach ($iterator as $cCtr => $tComSpec) {
                        $it++;
                        $comName = $tComSpec['comName'];
                        $srcGateName = $tComSpec['srcGateName'];
                        $srcRawGateName = $tComSpec['srcRawGateName'];
                        if (isset($registryGates[$srcGateName]) && sizeof($registryGates[$srcGateName]) > 0) {
                            foreach ($registryGates[$srcGateName] as $id => $dSpec) {
                                if (substr($comName, 0, 1) == "{") {
                                    $comName = trim($comName, "{");
                                    $comName = trim($comName, "}");
                                    $comName = str_replace($comName, $registryGates[$srcGateName][$id][$comName], $comName);
//                            $tComSpec['comName'] = $comName;
//                            $iterator[$cCtr]['comName'] = $comName;
//
//
                                }
                            }
                        }
                        else {
                            $comName = NULL;
                        }
                        cekHere("::::: $comName ::::: $srcGateName :::::");


                        echo __LINE__ . " sub $cCtr component #$it: $comName, sending values**** <br>";

                        if ($comName != NULL) {
//cekHere(":: $comName ::");
                            $mdlName = "Com" . ucfirst($comName);
                            $this->load->model("Coms/" . $mdlName);
                            $m = new $mdlName();

                            if (isset($tmpOutParams[$cCtr]) && sizeof($tmpOutParams[$cCtr]) > 0) {
                                $tobeExecuted = true;
                            }
                            else {
                                $tobeExecuted = false;
                            }

                            if ($tobeExecuted) {
                                $m->pair($tmpOutParams[$cCtr]) or die("Tidak berhasil memasang  values pada komponen: $comName/" . $jenisTrMaster . "/" . __FUNCTION__ . "/" . __LINE__);
                                $m->exec() or die("Gagal saat berusaha  exec values pada komponen: $comName/" . $jenisTrMaster . "/" . __FUNCTION__ . "/" . __LINE__);
                            }
                            else {
                                cekBiru("sub-komponem $comName tidak memenuhi syarat untuk ditulis");
                            }

                        }
                    }

                    cekMerah("HAHAHA");
                    $pakai_ini = 0;
                    if ($pakai_ini == 1) {
                        // region baca jurnal rekening besar
                        $jn = New ComJurnal();
                        $jn->addFilter("transaksi_id='$transaksiID'");
                        $jnTmp = $jn->lookupAll()->result();
//                    arrPrint($jnTmp);
                        $arrJurnal = array();
                        if (sizeof($jnTmp) > 0) {
                            foreach ($jnTmp as $ii => $spec) {
                                $defPosition = detectRekDefaultPosition($spec->rekening);
                                switch ($defPosition) {
                                    case "debet":
                                        $arrJurnal[$spec->cabang_id][$spec->rekening] = $spec->debet > 0 ? $spec->debet : $spec->kredit * -1;
                                        break;
                                    case "kredit":
                                        $arrJurnal[$spec->cabang_id][$spec->rekening] = $spec->kredit > 0 ? $spec->kredit : $spec->debet * -1;
                                        break;
                                    default:
                                        mati_disini("tidak menemukan default posisi rekening...");
                                        break;
                                }
                            }
                        }
                        // endregion

                        cekHere("cetak array jurnal");
                        arrPrint($arrJurnal);

                        cekHere("cetak rek loop");
                        arrPrint($arrRekeningLoop);


                        if (sizeof($arrJurnal) > 0) {
                            if (sizeof($arrRekeningLoop) > 0) {
                                foreach ($arrRekeningLoop as $cabang_id => $loopSpec) {
                                    foreach ($loopSpec as $rekening => $rekValue) {
                                        if (array_key_exists($rekening, $arrJurnal[$cabang_id])) {
                                            if (floor($rekValue) != floor($arrJurnal[$cabang_id][$rekening])) {
                                                mati_disini("nilai $rekening, jurnal: " . floor($arrJurnal[$cabang_id][$rekening]) . ", akumulasi pembantu: " . floor($rekValue));
                                            }
                                            else {
                                                cekHijau(":: COCOK ::");
                                            }
                                        }
                                    }
                                }
                            }
                        }


                    }


                    // validasi rekening besar vs rekening pembantu
                    validateBalancesComparison($trTmpCabangID, $componentGate, $componentConfig, "detail", $transaksiID, $tmpNomorNota);

                }
                else {
                    cekMerah("subcomponents [detail] is not set");
                }

                arrPrint($iteratorMaster);
                if (sizeof($iteratorMaster) > 0) {
                    $compValidators = ($this->config->item('transaksi_value_required_components') != null) ? $this->config->item('transaksi_value_required_components') : array();
                    $componentConfig['master'] = $iteratorMaster;
                    $cCtr = 0;
                    foreach ($iteratorMaster as $cCtr => $tComSpec) {
                        $cCtr++;
                        $comName = $tComSpec['comName'];
                        if (substr($comName, 0, 1) == "{") {
                            $comName = trim($comName, "{");
                            $comName = trim($comName, "}");
                            $comName = str_replace($comName, $registryGates[$srcGateName][$comName], $comName);
                        }
                        $srcGateName = $tComSpec['srcGateName'];
                        $srcRawGateName = $tComSpec['srcRawGateName'];
                        echo "component # $cCtr: $comName<br>";

                        $dSpec = $registryGates[$srcGateName];
                        $tmpOutParams = array();
                        if (isset($tComSpec['loop'])) {
                            foreach ($tComSpec['loop'] as $key => $value) {
                                if (substr($key, 0, 1) == "{") {
                                    $key = trim($key, "{");
                                    $key = trim($key, "}");
                                    $key = str_replace($key, $registryGates[$srcGateName][$key], $key);
                                }
                                $realValue = makeValue($value, $registryGates[$srcGateName], $registryGates[$srcGateName], 0);
                                $tmpOutParams['loop'][$key] = $realValue;
                            }
                        }
                        if (isset($tComSpec['static'])) {
                            foreach ($tComSpec['static'] as $key => $value) {

                                $realValue = makeValue($value, $registryGates[$srcGateName], $registryGates[$srcGateName], 0);
                                $tmpOutParams['static'][$key] = $realValue;

                            }
                            if (!isset($tmpOutParams['static']["transaksi_id"])) {
                                $tmpOutParams['static']["transaksi_id"] = $insertID;
                            }
                            if (!isset($tmpOutParams['static']["transaksi_no"])) {
                                $tmpOutParams['static']["transaksi_no"] = $insertNum;
                            }
                            $tmpOutParams['static']["urut"] = $cCtr;
                            $tmpOutParams['static']["fulldate"] = $fulldate;
                            $tmpOutParams['static']["dtime"] = $dtime;
                            $tmpOutParams['static']["keterangan"] = $configUi['steps'][$stepNumber]['label'] . " nomor " . $tmpNomorNota . " oleh " . $olehNama;


                        }

                        if (isset($tComSpec['static2'])) {
                            //cekHere("DISINI OIII");
                            foreach ($tComSpec['static2'] as $key => $value) {

                                $realValue = makeValue($value, $registryGates[$srcGateName][$cCtr], $registryGates[$srcGateName][$cCtr], 0);
                                $tmpOutParams['static2'][$key] = $realValue;

                            }
                            if (!isset($tmpOutParams['static2']["transaksi_id"])) {
                                $tmpOutParams['static2']["transaksi_id"] = $insertID;
                            }
                            if (!isset($tmpOutParams['static2']["transaksi_no"])) {
                                $tmpOutParams['static2']["transaksi_no"] = $insertNum;
                            }

                            $tmpOutParams['static2']["fulldate"] = $fulldate;
                            $tmpOutParams['static2']["dtime"] = $dtime;
                            $tmpOutParams['static2']["keterangan"] = $configUi['steps'][$stepNumber]['label'] . " nomor " . $tmpNomorNota . " oleh " . $olehNama;


                        }


                        $mdlName = "Com" . ucfirst($comName);
                        $this->load->model("Coms/" . $mdlName);
                        $m = new $mdlName();

                        //===filter value nol, jika harus difilter
                        $tobeExecuted = true;

                        if (in_array($mdlName, $compValidators)) {

                            $loopParams = isset($tmpOutParams['loop']) ? $tmpOutParams['loop'] : array();
                            if (sizeof($loopParams) > 0) {
                                foreach ($loopParams as $key => $val) {
                                    cekmerah("$comName : $key = $val ");
                                    if ($val == 0) {
                                        unset($tmpOutParams['loop'][$key]);
                                    }
                                }
                            }
                            if (sizeof($tmpOutParams['loop']) < 1) {
                                $tobeExecuted = false;
                            }

                        }

                        if ($tobeExecuted) {
                            $m->pair($tmpOutParams) or die("Tidak berhasil memasang  values pada komponen: $comName/" . $this->jenisTr . "/" . __FUNCTION__ . "/" . __LINE__);
                            $m->exec() or die("Gagal saat berusaha  exec values pada komponen: $comName/" . $this->jenisTr . "/" . __FUNCTION__ . "/" . __LINE__);
                        }

                        $componentGate['master'][$cCtr] = $tmpOutParams;
                    }
                }
                else {
                    cekHitam("TIDAK ADA CORE MASTER");
                }


                //endregion

            }

            arrPrintCyan($registryGates["main"]);

            $pakai_ini = 1;
            if ($pakai_ini == 1) {
                $jenisTr_con = $jenisTrMaster;
//                $registryGates["main"]["nilai_bayar"] = $registryGates["main"]["nilai_cash"];
//                if (isset($configCore['rejectComponent'][$jenisTr_con]['master'])) {
//                    $iterator = $configCore['rejectComponent'][$jenisTr_con]['master'];
//                }
                $iterator = array(
                    array(
                        "comName" => "RekeningPembantuProduk",
                        "loop" => array(
//                            "1010030030" => "sub_produk_rel_harga",//persediaan produk
//                            "1010030030" => "sub_produk_rel_harga_persediaan",//persediaan produk
                            "1010030030" => "sub_diskon_supplier_nilai_netto",//persediaan produk
                        ),
                        "static" => array(
                            "cabang_id" => "placeID",
                            "extern_id" => "extern_id",
                            "extern_nama" => "extern_nama",
                            "produk_qty" => "qty",
//                            "produk_nilai" => "produk_rel_harga",
//                            "produk_nilai" => "produk_rel_harga_persediaan",
                            "produk_nilai" => "diskon_supplier_nilai_netto",
                            "gudang_id" => "gudangID",
                            "jenis" => "jenisTr",
                            "transaksi_no" => "nomer",
                            "supplierID" => "pihakID",
                        ),
                        "srcGateName" => "items5_sum",
                        "srcRawGateName" => "items5_sum",
                    ),
//                    array(
//                        "comName" => "FifoAverage",
//                        "loop" => array(
//                            "1010030030" => "sub_diskon_supplier_nilai_netto",//persediaan produk
//                        ),
//                        "static" => array(
//                            "jenis" => ".produk",
//                            "jml" => "qty",
//                            "produk_id" => "id",
////                            "hpp" => "produk_rel_harga",
////                            "jml_nilai" => "sub_produk_rel_harga",
////                            "hpp_riil" => "produk_rel_harga",
////                            "jml_nilai_riil" => "produk_rel_harga",
//                            "hpp" => "produk_rel_harga_persediaan",
//                            "jml_nilai" => "sub_produk_rel_harga_persediaan",
//                            "hpp_riil" => "produk_rel_harga_persediaan",
//                            "jml_nilai_riil" => "sub_produk_rel_harga_persediaan",
//
//                            "ppv_riil" => "ppv",
//                            "ppv_nilai_riil" => "sub_ppv",
//                            "hpp_nppv" => "hpp_nppv",
//                            "jml_nilai_nppv" => "sub_hpp_nppv",
//                            "nama" => "name",
//                            "cabang_id" => "placeID",
//                            "gudang_id" => "gudangID",
//                            "ppn_in" => "ppn",
//                            "ppn_in_nilai" => "sub_ppn",
//                            "suppliers_id" => "pihakID",
//                            "suppliers_nama" => "pihakName",
//                            "produk_jenis" => ".lokal",
//                        ),
//                        "srcGateName" => "items5_sum",
//                        "srcRawGateName" => "items5_sum",
//                    ),

                );
                if (sizeof($iteratorrrr) > 0) {
                    $componentConfig['master'] = $iterator;
                    $it = 0;
                    $compValidators = ($this->config->item('transaksi_value_required_components') != null) ? $this->config->item('transaksi_value_required_components') : array();
                    foreach ($iterator as $cCtr => $tComSpec) {
                        $it++;
                        $comName = $tComSpec['comName'];
                        $srcGateName = $tComSpec['srcGateName'];
                        $srcRawGateName = $tComSpec['srcRawGateName'];
                        cekHere("component #$it: $comName :: $srcGateName <br>");

                        $dSpec = $registryGates[$srcGateName];
                        $tmpOutParams = array();
                        if (isset($tComSpec['loop'])) {
                            foreach ($tComSpec['loop'] as $key => $value) {
                                if (substr($key, 0, 1) == "{") {
                                    $key = trim($key, "{");
                                    $key = trim($key, "}");
                                    $key = str_replace($key, $registryGates[$srcGateName][$key], $key);
                                }
                                $realValue = makeValue($value, $registryGates[$srcGateName], $registryGates[$srcGateName], 0);
                                if ($key != null) {
                                    $tmpOutParams['loop'][$key] = $realValue;
                                }

                            }
                        }
                        if (isset($tComSpec['static'])) {
                            foreach ($tComSpec['static'] as $key => $value) {

                                $realValue = makeValue($value, $registryGates[$srcGateName], $registryGates[$srcGateName], 0);
                                $tmpOutParams['static'][$key] = $realValue;
                                cekHijau(":: NORMAL :: $key => $realValue ::");
                            }
                            if (isset($paramPatchers[$comName]) && sizeof($paramPatchers[$comName]) > 0) {
                                cekHijau(":: masuk ke PATCHER ::");
                                foreach ($paramPatchers[$comName] as $k => $v) {
                                    cekHijau(":: ada yang mau di-PATCHER ::");
                                    arrPrint($tmpOutParams['static']);
                                    if (!isset($tmpOutParams['static'][$k])) {
                                        $tmpOutParams['static'][$k] = isset($$v) ? $$v : "_v";
                                        cekHijau(":: PATCHER :: $key => $realValue ::");
                                    }

                                }
                            }
                            else {
                                cekMerah(":: TIDAK TERMASUK PATCHER ::");
                            }
                            if (isset($paramForceFillers[$comName]) && sizeof($paramForceFillers[$comName]) > 0) {
                                $jenis = $registryGates['main']['jenis'];
                                foreach ($paramForceFillers[$comName] as $k => $v) {
                                    $tmpOutParams['static'][$k] = isset($$v) ? $$v : "_v";
                                    cekHijau(":: FORCEFILL :: $key => $realValue ::");
                                }
                            }
                            $tmpOutParams['static']["urut"] = $cCtr;
                            $tmpOutParams['static']["fulldate"] = $fulldate;
                            $tmpOutParams['static']["dtime"] = $dtime;
                            $tmpOutParams['static']["keterangan"] = $this->configUi[$jenisTr]['steps'][$stepNumber]['label'] . " nomor " . $tmpNomorNota . " oleh " . $olehNama;

                            $tmpOutParams['static']["rejection"] = true;

                        }
                        if (isset($tComSpec['static2'])) {
                            foreach ($tComSpec['static2'] as $key => $value) {

                                $realValue = makeValue($value, $registryGates[$srcGateName], $registryGates[$srcGateName], 0);
                                $tmpOutParams['static2'][$key] = $realValue;

                            }
                            if (isset($paramPatchers[$comName]) && sizeof($paramPatchers[$comName]) > 0) {
                                foreach ($paramPatchers[$comName] as $k => $v) {
                                    if (!isset($subParams['static'][$k])) {
                                        $subParams['static'][$k] = isset($$v) ? $$v : "_v";
                                    }
                                }
                            }
                            if (isset($paramForceFillers[$comName]) && sizeof($paramForceFillers[$comName]) > 0) {
                                $jenis = $registryGates['main']['jenis'];
                                foreach ($paramForceFillers[$comName] as $k => $v) {
                                    $subParams['static'][$k] = isset($$v) ? $$v : "_v";
                                }
                            }
                            $tmpOutParams['static2']["fulldate"] = $fulldate;
                            $tmpOutParams['static2']["dtime"] = $dtime;
                            $tmpOutParams['static2']["keterangan"] = $this->configUi[$jenisTr]['steps'][$stepNumber]['label'] . " nomor " . $tmpNomorNota . " oleh " . $olehNama;


                        }

                        $mdlName = "Com" . ucfirst($comName);
                        $this->load->model("Coms/" . $mdlName);
                        $m = new $mdlName();
                        //===filter value nol, jika harus difilter
                        $tobeExecuted = true;

                        if (in_array($mdlName, $compValidators)) {

                            $loopParams = isset($tmpOutParams['loop']) ? $tmpOutParams['loop'] : array();
                            if (sizeof($loopParams) > 0) {
                                foreach ($loopParams as $key => $val) {
                                    cekmerah("$comName : $key = $val ");
                                    if ($val == 0) {
                                        unset($tmpOutParams['loop'][$key]);
                                    }
                                }
                            }
                            if (sizeof($tmpOutParams['loop']) < 1) {
                                $tobeExecuted = false;
                            }

                        }

                        if ($tobeExecuted) {
                            cekBiru("kiriman komponen $comName");
                            arrPrint($tmpOutParams);
                            $m->pair($tmpOutParams) or die("Tidak berhasil memasang  values pada komponen: $comName/" . $jenisTr . "/" . __FUNCTION__ . "/" . __LINE__);
                            $m->exec() or die("Gagal saat berusaha  exec values pada komponen: $comName/" . $jenisTr . "/" . __FUNCTION__ . "/" . __LINE__);
                        }
                        else {
                            cekBiru("komponem $comName tidak memenuhi syarat untuk ditulis");
                        }

                    }

                    $pakai_ini = 0;
                    if ($pakai_ini == 1) {
                        $this->load->model("Mdls/MdlPaymentUangMuka");
                        $l = new MdlPaymentUangMuka();
                        $l->addFilter("extern_id='" . $registryGates['main']['pihakID'] . "'");
                        $l->addFilter("extern_label2='customer'");
                        $l->addFilter("label='uang muka konsumen'");
                        $l->addFilter("cabang_id='" . $registryGates['main']['placeID'] . "'");
                        $tmpUm = $l->lookupAll()->result();
                        if (sizeof($tmpUm) > 0) {
                            $preTagihan = $tmpUm[0]->tagihan;
                            $preSisa = $tmpUm[0]->sisa;
                            $newTahigan = $preTagihan + $nilai_bayar;
                            $newsisa = $preSisa + $nilai_bayar;
                            $update = array(
                                "tagihan" => $newTahigan,
                                "sisa" => $newsisa,
                            );
                            $where = array(
//                        "extern_id" => $registryGates['main'][$externSrc['id']],
//                        "extern_label2" => $externSrc['extLabel'],//pembeda vendor dan customer lihat di heTransaksi_misc ->uang muka
                                "id" => $tmpUm[0]->id,
                            );
                            $tr->updateUangMukaSrc($where, $update);
                        }
                        else {
                            //insertbaru brooo
                            $tr->writeUangMukaSrc($insertID, array(
                                "jenis" => $stepCode,
                                "target_jenis" => $uangMukaSrcConfig['jenisTarget'],
                                "reference_jenis" => $uangMukaSrcConfig['jenisSrc'],
                                "extern_id" => $registryGates['main']['pihakID'],
                                "extern_nama" => $registryGates['main']['pihakName'],
                                "nomer" => "",
                                "note" => "",
                                "label" => "uang muka konsumen",
                                "tagihan" => $nilai_bayar,
                                "terbayar" => 0,
                                "sisa" => $nilai_bayar,
                                "cabang_id" => $registryGates['main']['placeID'],
                                "cabang_nama" => $registryGates['main']['placeName'],
                                "oleh_id" => $this->session->login['id'],
                                "oleh_nama" => $this->session->login['nama'],
                                "dtime" => date("Y-m-d H:i:s"),
                                "fulldate" => date("Y-m-d"),
                                "extern_label2" => "customer",
                            ));
                        }
                        cekMerah($this->db->last_query());
                    }

                    // transaksi pembayarannya...
                    if ($paymentSrc[0]->transaksi_ref_id > 0) {
                        $tr = New MdlTransaksi();
                        $tr->setFilters(array());
                        $where = array(
                            "id" => $paymentSrc[0]->transaksi_ref_id,
                        );
                        $data = array(
                            "trash_4" => 1,
                            "cancel_id" => my_id(),
                            "cancel_name" => my_name(),
                            "cancel_dtime" => date("Y-m-d H:i:s"),
                            "deskripsi" => "reject $transaksiJenisLabel_reference nomer $transaksiNomer_reference",
                        );
                        $tr->updateData($where, $data);
                        showLast_query("orange");
                    }

                }
                else {
                    cekKuning("reject components iterator");
                }
                if (sizeof($iterator) > 0) {

                    $compValidators = ($this->config->item('transaksi_value_required_components') != null) ? $this->config->item('transaksi_value_required_components') : array();
                    $filterNeeded = false;
                    $arrRekeningLoop = array();
                    foreach ($iterator as $cCtr => $tComSpec) {
                        $comName_orig = $comName = $tComSpec['comName'];
                        $srcGateName = $tComSpec['srcGateName'];
                        $loopRequire = isset($tComSpec['loopRequire']) ? $tComSpec['loopRequire'] : false;
                        $srcRawGateName = $tComSpec['srcRawGateName'];
                        echo "sub-component: $comName, $srcGateName, initializing values <br>";
                        $tmpOutParams[$cCtr] = array();
                        if (isset($registryGates[$srcGateName]) && sizeof($registryGates[$srcGateName]) > 0) {
                            foreach ($registryGates[$srcGateName] as $id => $dSpec) {
                                $comName = $comName_orig;
                                if (substr($comName, 0, 1) == "{") {
                                    $comName = trim($comName, "{");
                                    $comName = trim($comName, "}");
                                    $comName = str_replace($comName, $registryGates[$srcGateName][$id][$comName], $comName);
                                    $tComSpec['comName'] = $comName;
                                    $iterator[$cCtr]['comName'] = $comName;
                                }
                                $filterNeeded = false;
                                $mdlName = "Com" . ucfirst($comName);
                                if (in_array($mdlName, $compValidators)) {//perlu validasi filter
                                    $filterNeeded = true;
                                }
                                $subParams = array();
                                if (isset($tComSpec['loop'])) {
                                    foreach ($tComSpec['loop'] as $key => $value) {
                                        if (substr($key, 0, 1) == "{") {
                                            $key = trim($key, "{");
                                            $key = trim($key, "}");
                                            $key = str_replace($key, $registryGates[$srcGateName][$id][$key], $key);
                                        }

                                        $subComModel[$key] = $comName;

                                        $realValue = makeValue($value, $registryGates[$srcGateName][$id], $registryGates[$srcGateName][$id], 0);

                                        if (strlen($key) > 1) {
                                            $subParams['loop'][$key] = $realValue;
                                        }
                                        else {
                                            $subParams['loop'] = array();
                                        }

                                        // =================== =================== ===================
                                        if (!isset($arrRekeningLoop[$dSpec[$tComSpec['static']['cabang_id']]][$key])) {
                                            $arrRekeningLoop[$dSpec[$tComSpec['static']['cabang_id']]][$key] = 0;
                                        }
                                        $arrRekeningLoop[$dSpec[$tComSpec['static']['cabang_id']]][$key] += $realValue;
                                        if ($realValue != 0) {
                                            cekUngu(":: cetak loop $key => $realValue ::");
                                        }

                                        if ($filterNeeded) {
                                            if ($subParams['loop'][$key] == 0) {
                                                unset($subParams['loop'][$key]);

                                                // =================== =================== ===================
                                            }
                                        }
                                    }
                                }
                                if (isset($tComSpec['static'])) {
                                    foreach ($tComSpec['static'] as $key => $value) {

                                        $realValue = makeValue($value, $registryGates[$srcGateName][$id], $registryGates[$srcGateName][$id], 0);
//                                    $subParams['static'][$key] = $realValue;
                                        $subParams['static'][$key] = trim($realValue);
//                                cekKuning("STATIC: $key diisi dengan $realValue");
                                    }
                                    if (isset($paramPatchers[$comName]) && sizeof($paramPatchers[$comName]) > 0) {
                                        foreach ($paramPatchers[$comName] as $k => $v) {
                                            if (!isset($subParams['static'][$k])) {
                                                $subParams['static'][$k] = isset($$v) ? $$v : "_v";
                                                cekOrange("fill :: $comName :: $k ($v) => " . $subParams['static'][$k]);
                                            }
                                        }
                                    }
                                    if (isset($paramForceFillers[$comName]) && sizeof($paramForceFillers[$comName]) > 0) {

                                        $jenis = $registryGates['main']['jenis'];
                                        foreach ($paramForceFillers[$comName] as $k => $v) {
                                            $subParams['static'][$k] = isset($$v) ? $$v : "_v";
                                            cekOrange("fillforce :: $comName :: $k ($v) => " . $subParams['static'][$k]);
                                        }
                                    }

                                    // tambahan custom gerbang saat simpan transaksi, tidak bisa ditambahkan di coTransaksiCore/coTransaksiValues
                                    if (isset($paramForceFillersJenisTR[$comName][$jenisTrMaster]) && sizeof($paramForceFillersJenisTR[$comName][$jenisTrMaster]) > 0) {
                                        foreach ($paramForceFillersJenisTR[$comName][$jenisTrMaster] as $k => $v) {
                                            $subParams['static'][$k] = isset($$v) ? $$v : "_v";
                                            cekorange(":: $k diisikan dengan " . $subParams['static'][$k]);
                                        }
                                    }
                                    $subParams['static']["fulldate"] = $fulldate;
                                    $subParams['static']["dtime"] = $dtime;
                                    $subParams['static']["keterangan"] = $configUi['steps'][$stepNumber]['label'] . " nomor " . $tmpNomorNota . " oleh " . $olehNama;
                                    //------
                                    $subParams['static']["reference_id"] = isset($dSpec["referenceID"]) ? $dSpec["referenceID"] : "";
                                    $subParams['static']["reference_nomer"] = isset($dSpec["referenceNomer"]) ? $dSpec["referenceNomer"] : "";
                                    $subParams['static']["reference_jenis"] = isset($dSpec["jenisTr_reference"]) ? $dSpec["jenisTr_reference"] : "";
                                    $subParams['static']["reference_id_top"] = isset($dSpec["referenceID_top"]) ? $dSpec["referenceID_top"] : "";
                                    $subParams['static']["reference_nomer_top"] = isset($dSpec["referenceNomer_top"]) ? $dSpec["referenceNomer_top"] : "";
                                    $subParams['static']["reference_jenis_top"] = isset($dSpec["pihakExternMasterID"]) ? $dSpec["pihakExternMasterID"] : "";
                                    //------
                                    if (strlen($revertedTarget) > 1) {
                                        $subParams['static']['reverted_target'] = $revertedTarget;
                                    }
                                }
                                if (sizeof($subParams) > 0) {
                                    if ($filterNeeded) {
                                        if (isset($subParams['loop']) && !empty($subParams['loop'])) {
                                            $tmpOutParams[$cCtr][] = $subParams;
                                        }
                                    }
                                    else {
                                        if (empty($subParams['loop']) && $loopRequire == true) {
                                            unset($tmpOutParams[$cCtr]);
                                        }
                                        else {
                                            $tmpOutParams[$cCtr][] = $subParams;
                                        }
                                    }
                                }
                            }
                            arrPrintHitam($subParams);
                            $componentGate['detail'][$cCtr] = $subParams;
                        }
                    }
                    arrPrintPink($tmpOutParams);
                    $it = 0;
                    foreach ($iterator as $cCtr => $tComSpec) {
                        $it++;
                        $comName = $tComSpec['comName'];
                        $srcGateName = $tComSpec['srcGateName'];
                        $srcRawGateName = $tComSpec['srcRawGateName'];
                        if (isset($registryGates[$srcGateName]) && sizeof($registryGates[$srcGateName]) > 0) {
                            foreach ($registryGates[$srcGateName] as $id => $dSpec) {
                                if (substr($comName, 0, 1) == "{") {
                                    $comName = trim($comName, "{");
                                    $comName = trim($comName, "}");
                                    $comName = str_replace($comName, $registryGates[$srcGateName][$id][$comName], $comName);
//                            $tComSpec['comName'] = $comName;
//                            $iterator[$cCtr]['comName'] = $comName;
//
//
                                }
                            }
                        }
                        else {
                            $comName = NULL;
                        }
                        cekHere("::::: $comName ::::: $srcGateName :::::");


                        echo __LINE__ . " sub $cCtr component #$it: $comName, sending values**** <br>";

                        if ($comName != NULL) {

                            $mdlName = "Com" . ucfirst($comName);
                            $this->load->model("Coms/" . $mdlName);
                            $m = new $mdlName();

                            if (isset($tmpOutParams[$cCtr]) && sizeof($tmpOutParams[$cCtr]) > 0) {
                                $tobeExecuted = true;
                                cekUngu("MASUK TRUE");
                            }
                            else {
                                $tobeExecuted = false;
                                cekUngu("MASUK FALSE");
                            }

                            if ($tobeExecuted) {
                                $m->pair($tmpOutParams[$cCtr]) or die("Tidak berhasil memasang  values pada komponen: $comName/" . $jenisTrMaster . "/" . __FUNCTION__ . "/" . __LINE__);
                                $m->exec() or die("Gagal saat berusaha  exec values pada komponen: $comName/" . $jenisTrMaster . "/" . __FUNCTION__ . "/" . __LINE__);
                            }
                            else {
                                cekBiru("sub-komponem $comName tidak memenuhi syarat untuk ditulis");
                            }

                        }
                    }

                }
                else {
                    cekMerah("subcomponents [detail] is not set");
                }
            }

            $stopDate = dtimeNow();


            cekHitam("--- MULAI VALIDATOR ---");
            $this->load->library("Validator");
            $vdt = New Validator();

            mati_disini("...cek MANUAL cli transaksi... rekening pembantu masuk disini (component detail)<br>start: $startDate<br>stop: $stopDate<br>butuh waktu: " . timeDiff($startDate, $stopDate));


            cekHijau("...tes cli transaksi... rekening pembantu masuk disini (component detail)<br>start: $startDate<br>stop: $stopDate<br>butuh waktu: " . timeDiff($startDate, $stopDate));
//            mati_disini("...tes cli transaksi... rekening pembantu masuk disini (component detail)<br>start: $startDate<br>stop: $stopDate<br>butuh waktu: " . timeDiff($startDate, $stopDate));


            $this->db->trans_complete() or die("Gagal saat berusaha  commit transaction!");
        }
        else {
            $stopDate = dtimeNow();
            cekMerah(":: TIDAK ADA yang perlu di-CLI-kan ::
                    <br>start: $startDate<br>stop: $stopDate<br>butuh waktu: " . timeDiff($startDate, $stopDate));
        }

    }

    // patch insert ke tabel transaksi_efaktur
    public function run_patchFaktur()
    {
        $this->load->model("MdlTransaksi");
        $t = new MdlTransaksi();
        $t->setfilters(array());
        $t->addFilter("link_id='0'");
        $t->addFilter("trash_4='0'");
        $t->addFilter("jenis in ('110')");
        $t->addFilter("gunggungan_mode='1'");
        $tTmp = $t->lookupJoined_OLD()->result();
        showLast_query("kuning");
        cekHere(count($tTmp));

        $this->db->trans_start();


        foreach ($tTmp as $spec) {
//            arrPrint($spec);
            $data = array(
                "transaksi_id" => $spec->transaksi_id,
                "nomer" => $spec->nomer,
                "dtime" => $spec->dtime,
                "oleh_id" => $spec->oleh_id,
                "oleh_nama" => $spec->oleh_nama,
                "produk_id" => $spec->sub_referensi_id_4,
                "produk_nama" => $spec->sub_referensi_nama_4,
                "pihak_id" => $spec->sub_pihak_id,
                "pihak_nama" => $spec->sub_pihak_nama,
                "efaktur" => $spec->efaktur,
                "date_faktur" => $spec->efaktur_dtime,
                "jumlah" => 1,
            );
            $this->db->insert('transaksi_efaktur', $data);
            showLast_query("hijau");
//            break;
        }


//        mati_disini("...cek MANUAL cli transaksi... ");
        $this->db->trans_complete() or mati_disini("Gagal saat berusaha  commit transaction!");

    }

    public function run_patchFakturUpdate()
    {
        $this->load->model("MdlTransaksi");
        $t = new MdlTransaksi();
        $t->setfilters(array());
        $t->addFilter("link_id='0'");
        $t->addFilter("trash_4='1'");
        $t->addFilter("jenis in ('110')");
//        $t->addFilter("gunggungan_mode='1'");
        $tTmp = $t->lookupAll()->result();
        showLast_query("kuning");
        cekHere(count($tTmp));

        $this->db->trans_start();


        foreach ($tTmp as $spec) {
//            $data = array(
//                "transaksi_id" => $spec->transaksi_id,
//                "nomer" => $spec->nomer,
//                "dtime" => $spec->dtime,
//                "oleh_id" => $spec->oleh_id,
//                "oleh_nama" => $spec->oleh_nama,
//                "produk_id" => $spec->sub_referensi_id_4,
//                "produk_nama" => $spec->sub_referensi_nama_4,
//                "pihak_id" => $spec->sub_pihak_id,
//                "pihak_nama" => $spec->sub_pihak_nama,
//                "efaktur" => $spec->efaktur,
//                "date_faktur" => $spec->efaktur_dtime,
//                "jumlah" => 1,
//            );
//            $this->db->insert('transaksi_efaktur', $data);
//            showLast_query("hijau");
            $where = array(
                "transaksi_id" => $spec->id,
            );
            $this->db->where($where);
            $tmp = $this->db->get('transaksi_efaktur')->result();
            showLast_query("biru");
            if (sizeof($tmp) > 0) {
                $data_update = array(
                    "jumlah" => "0",
                );
                $this->db->where('transaksi_id', $spec->id);
                $this->db->update('transaksi_efaktur', $data_update);
                showLast_query("orange");
            }

//            break;
        }


        mati_disini("...cek MANUAL cli transaksi... ");
        $this->db->trans_complete() or mati_disini("Gagal saat berusaha  commit transaction!");

    }


    public function run_biayaProjectSusulan()
    {
        $this->load->model("MdlTransaksi");
        $this->load->model("Coms/ComJurnal");
        $this->load->model("CustomCounter");

        $arrTrid = array(
//            677294,
//            677298,
//            680509,
//            681802,
//            681806,
            683575
        );

        $this->db->trans_start();


        $tr = New MdlTransaksi();
        $tr->addFilter("id in ('" . implode("','", $arrTrid) . "')");
        $trTmp = $tr->lookupAll()->result();
        if (sizeof($trTmp) > 0) {
            foreach ($trTmp as $spec) {
                $trID_cli = $spec->id;
                $trTmpCabangID = $spec->cabang_id;
                $kolom = array(
                    "trID" => "id",
                    "jenisTr" => "jenis",
                    "jenisTrMaster" => "jenis_master",
                    "jenisTrTop" => "jenis_top",
                    "nomer" => "nomer",
                    "nomerTop" => "nomer_top",
                    "dtime" => "dtime",
                    "fulldate" => "fulldate",
                    "stepNumber" => "step_number",
                    "indexRegistry" => "indexing_registry",
                    "olehID" => "oleh_id",
                    "olehNama" => "oleh_nama",
                );
                $arrKolomTrans = array();
                foreach ($kolom as $key => $val) {
                    $arrKolomTrans[$key] = isset($spec->$val) ? $spec->$val : NULL;
                }


                $reg = New MdlTransaksi();
                $reg->setFilters(array());
                $reg->addFilter("transaksi_id=$trID_cli");
                $regTmp = $reg->lookupDataRegistries()->result();
                $registryGates = array();
                foreach ($regTmp as $regSpec) {
                    foreach ($regSpec as $key_reg => $val_reg) {
                        if ($key_reg != "transaksi_id") {
                            $registryGates[$key_reg] = blobDecode($val_reg);
                        }
                    }
                }

                if (sizeof($registryGates["items2"]) > 0) {
                    $biaya_tambahan = 0;
                    foreach ($registryGates["items2"] as $ii => $iiSpec) {
                        foreach ($iiSpec as $iii => $iiiSpec) {
                            $registryGates["items2"][$ii][$iii]["harga_tambahan"] = $iiiSpec["harga"];
                            $registryGates["items2"][$ii][$iii]["biaya_tambahan"] = $iiiSpec["harga"] * $iiiSpec["jml"];
                            $biaya_tambahan += $iiiSpec["harga"] * $iiiSpec["jml"];
                        }
                    }
                    $registryGates["main"]["piutang_tambah"] = ($registryGates["main"]["type_pelaksana_txt"] == "vendor") ? $biaya_tambahan : 0;
                }

                $jenisTr = $arrKolomTrans['jenisTr'];
                $jenisTrMaster = $arrKolomTrans['jenisTrMaster'];
                $fulldate = $arrKolomTrans['fulldate'];
                $dtime = $arrKolomTrans['dtime'];
                $stepNum = $stepNumber = $arrKolomTrans['stepNumber'];
                $insertNum = $tmpNomorNota = $arrKolomTrans['nomer'];
                $olehID = $arrKolomTrans['olehID'];
                $olehNama = $arrKolomTrans['olehNama'];
                $insertID = $transaksiID = $arrKolomTrans['trID'];
                /*---------------------- jenismaster untuk gerbang utama masuk modul, jenisTr adalah targetnya */
                /*------end*/
                $configCore = loadConfigModulJenis_he_misc($jenisTrMaster, "coTransaksiCore");
                $configUi = loadConfigModulJenis_he_misc($jenisTrMaster, "coTransaksiUi");
                $configLayout = loadConfigModulJenis_he_misc($jenisTrMaster, "coTransaksiLayout");

                $paramPatchers = $this->config->item('heTransaksi_paramPatchers') != null ? $this->config->item('heTransaksi_paramPatchers') : array();
                $paramForceFillers = $this->config->item('heTransaksi_paramForceFillers') != null ? $this->config->item('heTransaksi_paramForceFillers') : array();
                $validateSubComponent = $this->config->item('heTransaksi_validateComponentDetail') != null ? $this->config->item('heTransaksi_validateComponentDetail') : array();
                $paramForceFillersJenisTR = $this->config->item('heTransaksi_paramForceFillers_jenisTR') != null ? $this->config->item('heTransaksi_paramForceFillers_jenisTR') : array();
                $cliComponent = "components";

                cekHitam(":: jenisTrMaster-> $jenisTrMaster :: jenisTr-> $jenisTr :: [trID_cli: $trID_cli]");

                arrPrint($configCore);
                arrPrintWebs($registryGates["items2"]);

                $componentGate['detail'] = array();
                $componentConfig['master'] = array();
                $componentConfig['detail'] = array();
                if (isset($configCore['relativeComponets']) && $configCore['relativeComponets'] == true) {
                    $iterator = isset($registryGates['revert']['jurnal']['detail']) ? $registryGates['revert']['jurnal']['detail'] : array();
                    $revertedTarget = $registryGates['main']['pihakExternID'];
                    $componentConfig['detail'] = $iterator;
                    $iteratorMaster = $componentConfig['master'] = isset($registryGates['revert']['jurnal']['master']) ? $registryGates['revert']['jurnal']['master'] : array();
                }
                else {
                    $iterator_sub = isset($configCore[$cliComponent][$jenisTr]['sub_detail']) ? $configCore[$cliComponent][$jenisTr]['sub_detail'] : array();
                    $iterator = isset($configCore[$cliComponent][$jenisTr]['detail']) ? $configCore[$cliComponent][$jenisTr]['detail'] : array();
                    $componentConfig['detail'] = $iterator;
                    $iteratorMaster = $componentConfig['master'] = isset($configCore[$cliComponent][$jenisTr]['master']) ? $configCore[$cliComponent][$jenisTr]['master'] : array();
                    $revertedTarget = "";
                }

                $subComModel = array();
                // region komponent detail
                if (sizeof($iterator) > 0) {
                    $compValidators = ($this->config->item('transaksi_value_required_components') != null) ? $this->config->item('transaksi_value_required_components') : array();
                    $filterNeeded = false;
                    $arrRekeningLoop = array();
                    foreach ($iterator as $cCtr => $tComSpec) {
                        $comName_orig = $comName = $tComSpec['comName'];
                        $srcGateName = $tComSpec['srcGateName'];
                        $loopRequire = isset($tComSpec['loopRequire']) ? $tComSpec['loopRequire'] : false;
                        $srcRawGateName = $tComSpec['srcRawGateName'];

                        echo "sub-component: $comName, $srcGateName, initializing values <br>";

                        $tmpOutParams[$cCtr] = array();
                        if (isset($registryGates[$srcGateName]) && sizeof($registryGates[$srcGateName]) > 0) {
                            foreach ($registryGates[$srcGateName] as $id => $dSpec) {
                                $comName = $comName_orig;
                                if (substr($comName, 0, 1) == "{") {
                                    $comName = trim($comName, "{");
                                    $comName = trim($comName, "}");
                                    $comName = str_replace($comName, $registryGates[$srcGateName][$id][$comName], $comName);
                                    $tComSpec['comName'] = $comName;
                                    $iterator[$cCtr]['comName'] = $comName;
                                }

                                $filterNeeded = false;
                                $mdlName = "Com" . ucfirst($comName);
                                if (in_array($mdlName, $compValidators)) {//perlu validasi filter
                                    $filterNeeded = true;
                                }

                                $subParams = array();
                                if (isset($tComSpec['loop'])) {
                                    foreach ($tComSpec['loop'] as $key => $value) {
                                        if (substr($key, 0, 1) == "{") {
                                            $key = trim($key, "{");
                                            $key = trim($key, "}");
                                            $key = str_replace($key, $registryGates[$srcGateName][$id][$key], $key);
                                        }

                                        $subComModel[$key] = $comName;

                                        $realValue = makeValue($value, $registryGates[$srcGateName][$id], $registryGates[$srcGateName][$id], 0);

                                        if (strlen($key) > 1) {
                                            $subParams['loop'][$key] = $realValue;
                                        }
                                        else {
                                            $subParams['loop'] = array();
                                        }

                                        // =================== =================== ===================
                                        if (!isset($arrRekeningLoop[$dSpec[$tComSpec['static']['cabang_id']]][$key])) {
                                            $arrRekeningLoop[$dSpec[$tComSpec['static']['cabang_id']]][$key] = 0;
                                        }
                                        $arrRekeningLoop[$dSpec[$tComSpec['static']['cabang_id']]][$key] += $realValue;
                                        if ($realValue != 0) {
                                            cekUngu(":: cetak loop $key => $realValue ::");
                                        }

                                        if ($filterNeeded) {
                                            if ($subParams['loop'][$key] == 0) {
                                                unset($subParams['loop'][$key]);

                                                // =================== =================== ===================
                                            }
                                        }
                                    }
                                }
                                if (isset($tComSpec['static'])) {
                                    foreach ($tComSpec['static'] as $key => $value) {

                                        $realValue = makeValue($value, $registryGates[$srcGateName][$id], $registryGates[$srcGateName][$id], 0);
//                                    $subParams['static'][$key] = $realValue;
                                        $subParams['static'][$key] = trim($realValue);
//                                cekKuning("STATIC: $key diisi dengan $realValue");
                                    }
                                    if (isset($paramPatchers[$comName]) && sizeof($paramPatchers[$comName]) > 0) {
                                        foreach ($paramPatchers[$comName] as $k => $v) {
                                            if (!isset($subParams['static'][$k])) {
                                                $subParams['static'][$k] = isset($$v) ? $$v : "_v";
                                                cekOrange("fill :: $comName :: $k ($v) => " . $subParams['static'][$k]);
                                            }
                                        }
                                    }
                                    if (isset($paramForceFillers[$comName]) && sizeof($paramForceFillers[$comName]) > 0) {

                                        $jenis = $registryGates['main']['jenis'];
                                        foreach ($paramForceFillers[$comName] as $k => $v) {
                                            $subParams['static'][$k] = isset($$v) ? $$v : "_v";
                                            cekOrange("fillforce :: $comName :: $k ($v) => " . $subParams['static'][$k]);
                                        }
                                    }
//                                arrPrintWebs($paramForceFillersJenisTR[$comName]);
//                                cekMerah($jenisTrMaster);
                                    // tambahan custom gerbang saat simpan transaksi, tidak bisa ditambahkan di coTransaksiCore/coTransaksiValues
                                    if (isset($paramForceFillersJenisTR[$comName][$jenisTrMaster]) && sizeof($paramForceFillersJenisTR[$comName][$jenisTrMaster]) > 0) {
                                        foreach ($paramForceFillersJenisTR[$comName][$jenisTrMaster] as $k => $v) {
                                            $subParams['static'][$k] = isset($$v) ? $$v : "_v";
                                            cekorange(":: $k diisikan dengan " . $subParams['static'][$k]);
                                        }
                                    }
                                    $subParams['static']["fulldate"] = $fulldate;
                                    $subParams['static']["dtime"] = $dtime;
                                    $subParams['static']["keterangan"] = $configUi['steps'][$stepNumber]['label'] . " nomor " . $tmpNomorNota . " oleh " . $olehNama;
                                    //------
                                    $subParams['static']["reference_id"] = isset($dSpec["referenceID"]) ? $dSpec["referenceID"] : "";
                                    $subParams['static']["reference_nomer"] = isset($dSpec["referenceNomer"]) ? $dSpec["referenceNomer"] : "";
                                    $subParams['static']["reference_jenis"] = isset($dSpec["jenisTr_reference"]) ? $dSpec["jenisTr_reference"] : "";
                                    $subParams['static']["reference_id_top"] = isset($dSpec["referenceID_top"]) ? $dSpec["referenceID_top"] : "";
                                    $subParams['static']["reference_nomer_top"] = isset($dSpec["referenceNomer_top"]) ? $dSpec["referenceNomer_top"] : "";
                                    $subParams['static']["reference_jenis_top"] = isset($dSpec["pihakExternMasterID"]) ? $dSpec["pihakExternMasterID"] : "";
                                    //------
                                    if (strlen($revertedTarget) > 1) {
                                        $subParams['static']['reverted_target'] = $revertedTarget;
                                    }
                                }
                                if (sizeof($subParams) > 0) {
                                    if ($filterNeeded) {
                                        if (isset($subParams['loop']) && !empty($subParams['loop'])) {
                                            $tmpOutParams[$cCtr][] = $subParams;
                                        }
                                    }
                                    else {
                                        if (empty($subParams['loop']) && $loopRequire == true) {
                                            unset($tmpOutParams[$cCtr]);
                                        }
                                        else {
                                            $tmpOutParams[$cCtr][] = $subParams;
                                        }
                                    }
                                }
                            }

                            $componentGate['detail'][$cCtr] = $subParams;
                        }

                    }
                    $it = 0;
                    foreach ($iterator as $cCtr => $tComSpec) {
                        $it++;
                        $comName = $tComSpec['comName'];
                        $srcGateName = $tComSpec['srcGateName'];
                        $srcRawGateName = $tComSpec['srcRawGateName'];
                        if (isset($registryGates[$srcGateName]) && sizeof($registryGates[$srcGateName]) > 0) {
                            foreach ($registryGates[$srcGateName] as $id => $dSpec) {
                                if (substr($comName, 0, 1) == "{") {
                                    $comName = trim($comName, "{");
                                    $comName = trim($comName, "}");
                                    $comName = str_replace($comName, $registryGates[$srcGateName][$id][$comName], $comName);
//                            $tComSpec['comName'] = $comName;
//                            $iterator[$cCtr]['comName'] = $comName;
//
//
                                }
                            }
                        }
                        else {
                            $comName = NULL;
                        }
                        cekHere("::::: $comName ::::: $srcGateName :::::");


                        echo __LINE__ . " sub $cCtr component #$it: $comName, sending values**** <br>";

                        if ($comName != NULL) {
//cekHere(":: $comName ::");
                            $mdlName = "Com" . ucfirst($comName);
                            $this->load->model("Coms/" . $mdlName);
                            $m = new $mdlName();

                            if (isset($tmpOutParams[$cCtr]) && sizeof($tmpOutParams[$cCtr]) > 0) {
                                $tobeExecuted = true;
                            }
                            else {
                                $tobeExecuted = false;
                            }

                            if ($tobeExecuted) {
                                $m->pair($tmpOutParams[$cCtr]) or die("Tidak berhasil memasang  values pada komponen: $comName/" . $jenisTrMaster . "/" . __FUNCTION__ . "/" . __LINE__);
                                $m->exec() or die("Gagal saat berusaha  exec values pada komponen: $comName/" . $jenisTrMaster . "/" . __FUNCTION__ . "/" . __LINE__);
                            }
                            else {
                                cekBiru("sub-komponem $comName tidak memenuhi syarat untuk ditulis");
                            }

                        }
                    }
                }
                else {
                    cekMerah("subcomponents [detail] is not set");
                }
                // endregion komponent detail

                // region komponent sub_detail
                if (sizeof($iterator_sub) > 0) {
                    $compValidators = ($this->config->item('transaksi_value_required_components') != null) ? $this->config->item('transaksi_value_required_components') : array();
                    $filterNeeded = false;
                    $arrRekeningLoop = array();
                    foreach ($iterator_sub as $cCtr => $tComSpec) {
                        $comName_orig = $comName = $tComSpec['comName'];
                        $srcGateName = $tComSpec['srcGateName'];
                        $loopRequire = isset($tComSpec['loopRequire']) ? $tComSpec['loopRequire'] : false;
                        $srcRawGateName = $tComSpec['srcRawGateName'];
                        echo "sub-component: $comName, $srcGateName, initializing values <br>";
                        $tmpOutParams[$cCtr] = array();
                        if (isset($registryGates[$srcGateName]) && sizeof($registryGates[$srcGateName]) > 0) {

                            foreach ($registryGates[$srcGateName] as $id => $ddSpec) {
                                foreach ($ddSpec as $dID => $dSpec) {
                                    $comName = $comName_orig;
                                    if (substr($comName, 0, 1) == "{") {
                                        $comName = trim($comName, "{");
                                        $comName = trim($comName, "}");
                                        $comName = str_replace($comName, $registryGates[$srcGateName][$id][$comName], $comName);
                                        $tComSpec['comName'] = $comName;
                                        $iterator[$cCtr]['comName'] = $comName;
                                    }
                                    $filterNeeded = false;
                                    $mdlName = "Com" . ucfirst($comName);
                                    if (in_array($mdlName, $compValidators)) {//perlu validasi filter
                                        $filterNeeded = true;
                                    }


                                    $subParams = array();
                                    if (isset($tComSpec['loop'])) {
                                        foreach ($tComSpec['loop'] as $key => $value) {
                                            if (substr($key, 0, 1) == "{") {
                                                $key = trim($key, "{");
                                                $key = trim($key, "}");
                                                $key = str_replace($key, $registryGates[$srcGateName][$id][$dID][$key], $key);
                                            }

                                            $subComModel[$key] = $comName;

                                            $realValue = makeValue($value, $registryGates[$srcGateName][$id][$dID], $registryGates[$srcGateName][$id][$dID], 0);

                                            if (strlen($key) > 1) {
                                                $subParams['loop'][$key] = $realValue;
                                            }
                                            else {
                                                $subParams['loop'] = array();
                                            }

                                            // =================== =================== ===================
                                            if (!isset($arrRekeningLoop[$dSpec[$tComSpec['static']['cabang_id']]][$key])) {
                                                $arrRekeningLoop[$dSpec[$tComSpec['static']['cabang_id']]][$key] = 0;
                                            }
                                            $arrRekeningLoop[$dSpec[$tComSpec['static']['cabang_id']]][$key] += $realValue;
                                            if ($realValue != 0) {
                                                cekUngu(":: cetak loop $key => $realValue ::");
                                            }

                                            if ($filterNeeded) {
                                                if ($subParams['loop'][$key] == 0) {
                                                    unset($subParams['loop'][$key]);

                                                    // =================== =================== ===================
                                                }
                                            }
                                        }
                                    }
                                    if (isset($tComSpec['static'])) {
                                        foreach ($tComSpec['static'] as $key => $value) {

                                            $realValue = makeValue($value, $registryGates[$srcGateName][$id][$dID], $registryGates[$srcGateName][$id][$dID], 0);
                                            $subParams['static'][$key] = $realValue;
//                                cekKuning("STATIC: $key diisi dengan $realValue");
                                        }
                                        if (isset($paramPatchers[$comName]) && sizeof($paramPatchers[$comName]) > 0) {
                                            foreach ($paramPatchers[$comName] as $k => $v) {
                                                if (!isset($subParams['static'][$k])) {
                                                    $subParams['static'][$k] = isset($$v) ? $$v : "_v";
                                                    cekOrange("fill :: $comName :: $k ($v) => " . $subParams['static'][$k]);
                                                }
                                            }
                                        }
                                        if (isset($paramForceFillers[$comName]) && sizeof($paramForceFillers[$comName]) > 0) {

                                            $jenis = $registryGates['main']['jenis'];
                                            foreach ($paramForceFillers[$comName] as $k => $v) {
                                                $subParams['static'][$k] = isset($$v) ? $$v : "_v";
                                                cekOrange("fillforce :: $comName :: $k ($v) => " . $subParams['static'][$k]);
                                            }
                                        }
                                        $subParams['static']["fulldate"] = $fulldate;
                                        $subParams['static']["dtime"] = $dtime;
                                        $subParams['static']["keterangan"] = $configUi['steps'][$stepNumber]['label'] . " nomor " . $tmpNomorNota . " oleh " . $olehNama;
                                        //------
                                        $subParams['static']["reference_id"] = isset($dSpec["referenceID"]) ? $dSpec["referenceID"] : "";
                                        $subParams['static']["reference_nomer"] = isset($dSpec["referenceNomer"]) ? $dSpec["referenceNomer"] : "";
                                        $subParams['static']["reference_jenis"] = isset($dSpec["jenisTr_reference"]) ? $dSpec["jenisTr_reference"] : "";
                                        $subParams['static']["reference_id_top"] = isset($dSpec["referenceID_top"]) ? $dSpec["referenceID_top"] : "";
                                        $subParams['static']["reference_nomer_top"] = isset($dSpec["referenceNomer_top"]) ? $dSpec["referenceNomer_top"] : "";
                                        $subParams['static']["reference_jenis_top"] = isset($dSpec["pihakExternMasterID"]) ? $dSpec["pihakExternMasterID"] : "";
                                        //------
                                        if (strlen($revertedTarget) > 1) {
                                            $subParams['static']['reverted_target'] = $revertedTarget;
                                        }
                                    }
                                    if (sizeof($subParams) > 0) {
                                        if ($filterNeeded) {
                                            if (isset($subParams['loop']) && !empty($subParams['loop'])) {
                                                $tmpOutParams[$cCtr][] = $subParams;
                                            }
                                        }
                                        else {
                                            if (empty($subParams['loop']) && $loopRequire == true) {
                                                unset($tmpOutParams[$cCtr]);
                                            }
                                            else {
                                                $tmpOutParams[$cCtr][] = $subParams;
                                            }
                                        }
                                    }

                                }
                                $componentGate['sub_detail'][$cCtr] = $subParams;
                            }


                        }

                    }
                    $it = 0;
                    foreach ($iterator_sub as $cCtr => $tComSpec) {
                        $it++;
                        $comName = $tComSpec['comName'];
                        $srcGateName = $tComSpec['srcGateName'];
                        $srcRawGateName = $tComSpec['srcRawGateName'];
                        if (isset($registryGates[$srcGateName]) && sizeof($registryGates[$srcGateName]) > 0) {
                            foreach ($registryGates[$srcGateName] as $id => $ddSpec) {
                                foreach ($ddSpec as $ixx => $dSpec) {
                                    if (substr($comName, 0, 1) == "{") {
                                        $comName = trim($comName, "{");
                                        $comName = trim($comName, "}");
                                        $comName = str_replace($comName, $registryGates[$srcGateName][$id][$ixx][$comName], $comName);
                                    }
                                }
                            }
                        }
                        else {
                            $comName = NULL;
                        }
                        cekHere("::::: $comName :::::");


                        echo __LINE__ . " sub $cCtr component #$it: $comName, sending values**** <br>";

                        if ($comName != NULL) {
                            cekHere(":: $comName ::");
                            $mdlName = "Com" . ucfirst($comName);
                            $this->load->model("Coms/" . $mdlName);
                            $m = new $mdlName();
                            if (isset($tmpOutParams[$cCtr]) && sizeof($tmpOutParams[$cCtr]) > 0) {
                                $tobeExecuted = true;
                            }
                            else {
                                $tobeExecuted = false;
                            }
                            arrPrintPink($tmpOutParams[$cCtr]);
                            if ($tobeExecuted) {
                                $m->pair($tmpOutParams[$cCtr]) or die("Tidak berhasil memasang  values pada komponen: $comName/" . $jenisTrMaster . "/" . __FUNCTION__ . "/" . __LINE__);
                                $m->exec() or die("Gagal saat berusaha  exec values pada komponen: $comName/" . $jenisTrMaster . "/" . __FUNCTION__ . "/" . __LINE__);
                            }
                            else {
                                cekBiru("sub-komponem $comName tidak memenuhi syarat untuk ditulis");
                            }
                        }
                    }
                    // validasi rekening besar vs rekening pembantu
//                    validateBalancesComparison($trTmpCabangID, $componentGate, $componentConfig, "detail", $transaksiID, $tmpNomorNota);

                }
                else {
                    cekMerah("subcomponents [sub_detail] is not set");
                }
                // endregion komponent detail

                // region komponent master
                if (sizeof($iteratorMaster) > 0) {
                    $compValidators = ($this->config->item('transaksi_value_required_components') != null) ? $this->config->item('transaksi_value_required_components') : array();
                    $componentConfig['master'] = $iteratorMaster;
                    $cCtr = 0;
                    foreach ($iteratorMaster as $cCtr => $tComSpec) {
                        $cCtr++;
                        $comName = $tComSpec['comName'];
                        if (substr($comName, 0, 1) == "{") {
                            $comName = trim($comName, "{");
                            $comName = trim($comName, "}");
                            $comName = str_replace($comName, $registryGates[$srcGateName][$comName], $comName);
                        }
                        $srcGateName = $tComSpec['srcGateName'];
                        $srcRawGateName = $tComSpec['srcRawGateName'];
                        echo "component # $cCtr: $comName<br>";

                        $dSpec = $registryGates[$srcGateName];
                        $tmpOutParams = array();
                        if (isset($tComSpec['loop'])) {
                            foreach ($tComSpec['loop'] as $key => $value) {
                                if (substr($key, 0, 1) == "{") {
                                    $key = trim($key, "{");
                                    $key = trim($key, "}");
                                    $key = str_replace($key, $registryGates[$srcGateName][$key], $key);
                                }
                                $realValue = makeValue($value, $registryGates[$srcGateName], $registryGates[$srcGateName], 0);
                                $tmpOutParams['loop'][$key] = $realValue;
                            }
                        }
                        if (isset($tComSpec['static'])) {
                            foreach ($tComSpec['static'] as $key => $value) {

                                $realValue = makeValue($value, $registryGates[$srcGateName], $registryGates[$srcGateName], 0);
                                $tmpOutParams['static'][$key] = $realValue;

                            }
                            if (!isset($tmpOutParams['static']["transaksi_id"])) {
                                $tmpOutParams['static']["transaksi_id"] = $insertID;
                            }
                            if (!isset($tmpOutParams['static']["transaksi_no"])) {
                                $tmpOutParams['static']["transaksi_no"] = $insertNum;
                            }
                            $tmpOutParams['static']["urut"] = $cCtr;
                            $tmpOutParams['static']["fulldate"] = $fulldate;
                            $tmpOutParams['static']["dtime"] = $dtime;
                            $tmpOutParams['static']["keterangan"] = $configUi['steps'][$stepNumber]['label'] . " nomor " . $tmpNomorNota . " oleh " . $olehNama;


                        }
                        if (isset($tComSpec['static2'])) {
                            //cekHere("DISINI OIII");
                            foreach ($tComSpec['static2'] as $key => $value) {

                                $realValue = makeValue($value, $registryGates[$srcGateName][$cCtr], $registryGates[$srcGateName][$cCtr], 0);
                                $tmpOutParams['static2'][$key] = $realValue;

                            }
                            if (!isset($tmpOutParams['static2']["transaksi_id"])) {
                                $tmpOutParams['static2']["transaksi_id"] = $insertID;
                            }
                            if (!isset($tmpOutParams['static2']["transaksi_no"])) {
                                $tmpOutParams['static2']["transaksi_no"] = $insertNum;
                            }

                            $tmpOutParams['static2']["fulldate"] = $fulldate;
                            $tmpOutParams['static2']["dtime"] = $dtime;
                            $tmpOutParams['static2']["keterangan"] = $configUi['steps'][$stepNumber]['label'] . " nomor " . $tmpNomorNota . " oleh " . $olehNama;


                        }

                        $mdlName = "Com" . ucfirst($comName);
                        $this->load->model("Coms/" . $mdlName);
                        $m = new $mdlName();
                        //===filter value nol, jika harus difilter
                        $tobeExecuted = true;

                        if (in_array($mdlName, $compValidators)) {

                            $loopParams = isset($tmpOutParams['loop']) ? $tmpOutParams['loop'] : array();
                            if (sizeof($loopParams) > 0) {
                                foreach ($loopParams as $key => $val) {
                                    cekmerah("$comName : $key = $val ");
                                    if ($val == 0) {
                                        unset($tmpOutParams['loop'][$key]);
                                    }
                                }
                            }
                            if (sizeof($tmpOutParams['loop']) < 1) {
                                $tobeExecuted = false;
                            }

                        }

                        if ($tobeExecuted) {
                            $m->pair($tmpOutParams) or die("Tidak berhasil memasang  values pada komponen: $comName/" . $this->jenisTr . "/" . __FUNCTION__ . "/" . __LINE__);
                            $m->exec() or die("Gagal saat berusaha  exec values pada komponen: $comName/" . $this->jenisTr . "/" . __FUNCTION__ . "/" . __LINE__);
                        }

                        $componentGate['master'][$cCtr] = $tmpOutParams;
                    }
                }
                else {
                    cekHitam("TIDAK ADA CORE MASTER");
                }
                // endregion komponent master


                //region nulis paymentSource
                $stepCode = $configUi['steps'][$stepNum]['target'];
                $paymentSources = $this->config->item("payment_source");
                if (array_key_exists($stepCode, $paymentSources)) {
                    $payConfigs = isset($paymentSources[$stepCode][$stepNum]) ? $paymentSources[$stepCode][$stepNum] : array();
                    if (sizeof($payConfigs) > 0) {
                        foreach ($payConfigs as $paymentSrcConfig) {
                            $valueLabel = isset($paymentSrcConfig['label_key']) ? $paymentSrcConfig['label_key'] : $paymentSrcConfig['label'];
                            $valueSrc = $paymentSrcConfig['valueSrc'];
                            $externSrc = $paymentSrcConfig['externSrc'];
                            $valueAdd = isset($registryGates['main'][$paymentSrcConfig['addValueValidator']]) ? $registryGates['main'][$paymentSrcConfig['addValueValidator']] : 0;
                            if (isset($paymentSrcConfig['model'])) {
                                $mdlName = $paymentSrcConfig['model'];
                                $this->load->model("Mdls/$mdlName");
                                $pMdl = New $mdlName();
                                $pTmpMdl = $pMdl->lookupAll()->result();
                                $pTmpMdlResult = array();
                                if (sizeof($pTmpMdl) > 0) {
                                    foreach ($pTmpMdl as $pTmpMdlSpec) {
                                        $pTmpMdlResult[$pTmpMdlSpec->id] = $pTmpMdlSpec;
                                    }
                                }
                            }
                            else {
                                $pTmpMdlResult = array();
                            }

                            if (isset($registryGates['main'][$valueSrc]) && $registryGates['main'][$valueSrc] > 0) {
                                if (isset($externSrc['extern_label2'])) {
                                    //cek ada isinya atau kosong
                                    $cek = strlen($registryGates['main'][$externSrc['extern_label2']]) > 4 ? "" : matiHere("jenis biaya tidak dikenali " . __LINE__);//
                                }
                                //region cek duplikasi paymentsource
                                $tr->setFilters(array());
                                $tr->addFilter("transaksi_id='$insertID'");
                                $tr->addFilter("target_jenis='" . $paymentSrcConfig['jenisTarget'] . "'");
                                // $tr->addFilter("target_jenis='759'");
                                $validateIsInserted = $tr->lookUpAllPaymentSrc()->result();
                                if (sizeof($validateIsInserted) > 0) {
                                    matiHEre("Gagal menulis transaksi. Silahkan relogin untuk membersihkan sesi demi menghindari duplikasi data, dan coba kembali transaksi yang gagal");
                                }
                                //endregion

                                //-----------------------
                                cekHitam("valuelabel: $valueLabel, valueSrc: $valueSrc");
//                                $this->load->helper("he_payment_source");
                                //                        paymentSource($this->jenisTr, $componentJurnal, $registryGates['main'], $valueLabel, $valueSrc, $valueAdd);
                                //-----------------------

                                $arrPymSrc = array(
                                    "jenis" => $stepCode,
                                    "target_jenis" => $paymentSrcConfig['jenisTarget'],
                                    "reference_jenis" => $paymentSrcConfig['jenisSrc'],
                                    "extern_id" => isset($registryGates['main'][$externSrc['id']]) ? $registryGates['main'][$externSrc['id']] : "",
                                    "extern_nama" => isset($registryGates['main'][$externSrc['nama']]) ? $registryGates['main'][$externSrc['nama']] : "",
                                    "nomer" => $tmpNomorNota,
                                    "label" => $paymentSrcConfig['label'],
                                    "tagihan" => $registryGates['main'][$valueSrc],
                                    "terbayar" => 0,
                                    "sisa" => $registryGates['main'][$valueSrc],
                                    "cabang_id" => isset($externSrc['cabang_id']) && isset($registryGates['main'][$externSrc['cabang_id']]) ? $registryGates['main'][$externSrc['cabang_id']] : $registryGates['main']['placeID'],
                                    "cabang_nama" => isset($externSrc['cabang_nama']) && isset($registryGates['main'][$externSrc['cabang_nama']]) ? $registryGates['main'][$externSrc['cabang_nama']] : $registryGates['main']['placeName'],
                                    "oleh_id" => $olehID,
                                    "oleh_nama" => $olehNama,
                                    "dtime" => $dtime,
                                    "fulldate" => $fulldate,
                                    "valas_id" => isset($externSrc['valasId']) && isset($registryGates['main'][$externSrc['valasId']]) ? $registryGates['main'][$externSrc['valasId']] : '',
                                    "valas_nama" => isset($externSrc['valasLabel']) && isset($registryGates['main'][$externSrc['valasLabel']]) ? $registryGates['main'][$externSrc['valasLabel']] : '',
                                    "valas_nilai" => isset($externSrc['valasValue']) && isset($registryGates['main'][$externSrc['valasValue']]) ? $registryGates['main'][$externSrc['valasValue']] : '',
                                    "tagihan_valas" => isset($externSrc['valasTagihan']) && isset($registryGates['main'][$externSrc['valasTagihan']]) ? $registryGates['main'][$externSrc['valasTagihan']] : '',
                                    "terbayar_valas" => 0,
                                    "sisa_valas" => isset($externSrc['valasSisa']) && isset($registryGates['main'][$externSrc['valasSisa']]) ? $registryGates['main'][$externSrc['valasSisa']] : '',
                                    "extern_label2" => (isset($externSrc['extern_label2']) && ($registryGates['main'][$externSrc['extern_label2']])) ? $registryGates['main'][$externSrc['extern_label2']] : "",
                                    "dpp_ppn" => (isset($externSrc['dpp_ppn']) && ($registryGates['main'][$externSrc['dpp_ppn']])) ? $registryGates['main'][$externSrc['dpp_ppn']] : 0,
                                    "ppn" => (isset($externSrc['ppn']) && ($registryGates['main'][$externSrc['ppn']])) ? $registryGates['main'][$externSrc['ppn']] : 0,
                                    "ppn_approved" => (isset($externSrc['ppn_approved']) && ($registryGates['main'][$externSrc['ppn_approved']])) ? $registryGates['main'][$externSrc['ppn_approved']] : 0,
                                    "ppn_sisa" => (isset($externSrc['ppn']) && ($registryGates['main'][$externSrc['ppn']])) ? $registryGates['main'][$externSrc['ppn']] : "",
                                    "ppn_status" => (isset($externSrc['ppn_status'])) ? $externSrc['ppn_status'] : 0,
                                    "extern_nilai2" => (isset($externSrc['extern_nilai2']) && ($registryGates['main'][$externSrc['extern_nilai2']])) ? $registryGates['main'][$externSrc['extern_nilai2']] : 0,
                                    "extern_date2" => (isset($externSrc['extern_date2']) && ($registryGates['main'][$externSrc['extern_date2']])) ? $registryGates['main'][$externSrc['extern_date2']] : "",
                                    "pph_23" => (isset($externSrc['pph_23']) && ($registryGates['main'][$externSrc['pph_23']])) ? $registryGates['main'][$externSrc['pph_23']] : "",
                                    "npwp" => (isset($externSrc['npwp']) && ($registryGates['main'][$externSrc['npwp']])) ? $registryGates['main'][$externSrc['npwp']] : "",
                                    "project_id" => isset($externSrc['project_id']) && isset($registryGates['main'][$externSrc['project_id']]) ? $registryGates['main'][$externSrc['project_id']] : "",
                                    "project_nama" => isset($externSrc['project_nama']) && isset($registryGates['main'][$externSrc['project_nama']]) ? $registryGates['main'][$externSrc['project_nama']] : "",
                                    "extern2_id" => (isset($externSrc['extern2_id']) && ($registryGates['main'][$externSrc['extern2_id']])) ? $registryGates['main'][$externSrc['extern2_id']] : "",
                                    "extern2_nama" => (isset($externSrc['extern2_nama']) && ($registryGates['main'][$externSrc['extern2_nama']])) ? $registryGates['main'][$externSrc['extern2_nama']] : "",
                                    "ppn_pph_faktor" => (isset($externSrc['ppn_pph_faktor']) && ($registryGates['main'][$externSrc['ppn_pph_faktor']])) ? $registryGates['main'][$externSrc['ppn_pph_faktor']] : "",
                                    "extern_jenis" => (isset($externSrc['extern_jenis']) && ($registryGates['main'][$externSrc['extern_jenis']])) ? $registryGates['main'][$externSrc['extern_jenis']] : "",
                                    "extern_nilai3" => (isset($externSrc['extern_nilai3']) && ($registryGates['main'][$externSrc['extern_nilai3']])) ? $registryGates['main'][$externSrc['extern_nilai3']] : "",
                                    "extern_nilai4" => (isset($externSrc['extern_nilai4']) && ($registryGates['main'][$externSrc['extern_nilai4']])) ? $registryGates['main'][$externSrc['extern_nilai4']] : "",
                                    "extern3_id" => isset($externSrc['extern3_id']) && isset($registryGates['main'][$externSrc['extern3_id']]) ? $registryGates['main'][$externSrc['extern3_id']] : "",
                                    "extern3_nama" => isset($externSrc['extern3_nama']) && isset($registryGates['main'][$externSrc['extern3_nama']]) ? $registryGates['main'][$externSrc['extern3_nama']] : "",
                                    "extern4_id" => isset($externSrc['extern4_id']) && isset($registryGates['main'][$externSrc['extern4_id']]) ? $registryGates['main'][$externSrc['extern4_id']] : "",
                                    "extern4_nama" => isset($externSrc['extern4_nama']) && isset($registryGates['main'][$externSrc['extern4_nama']]) ? $registryGates['main'][$externSrc['extern4_nama']] : "",
                                    "extern5_id" => isset($externSrc['extern5_id']) && isset($registryGates['main'][$externSrc['extern5_id']]) ? $registryGates['main'][$externSrc['extern5_id']] : "",
                                    "extern5_nama" => isset($externSrc['extern5_nama']) && isset($registryGates['main'][$externSrc['extern5_nama']]) ? $registryGates['main'][$externSrc['extern5_nama']] : "",
                                    "payment_locked" => (isset($externSrc['payment_locked']) && ($registryGates['main'][$externSrc['payment_locked']])) ? $registryGates['main'][$externSrc['payment_locked']] : 0,
                                    "cash_account" => (isset($externSrc['cash_account']) && ($registryGates['main'][$externSrc['cash_account']])) ? $registryGates['main'][$externSrc['cash_account']] : 0,
                                    "cash_account_nama" => (isset($externSrc['cash_account_nama']) && ($registryGates['main'][$externSrc['cash_account_nama']])) ? $registryGates['main'][$externSrc['cash_account_nama']] : 0,
                                );
                                $tr->writePaymentSrc($insertID, $arrPymSrc);
                                showLast_query("merah");
                            }
                        }
                    }
                }
                else {
                    cekMerah("TIDAK nulis paymentSrc");
                }

                $addPaymentSource = isset($configUi['steps'][$stepNum]['additionalStep']['shippingService']) ? $configUi['steps'][$stepNum]['additionalStep']['shippingService'] : array();
                //endregion

                //region pembantu paymentsource
                $paymentPembantuSources = $this->config->item("payment_pembantu_Source");
                if (array_key_exists($stepCode, $paymentPembantuSources)) {
                    $payPembantuConfigs = isset($paymentPembantuSources[$stepCode][$stepNum]) ? $paymentPembantuSources[$stepCode][$stepNum] : array();
                    if (sizeof($payPembantuConfigs) > 0) {
                        foreach ($payPembantuConfigs as $paymentSrcConfig) {
                            $valueSrc = $paymentSrcConfig['valueSrc'];
                            $externSrc = $paymentSrcConfig['externSrc'];
                            $gate = $paymentSrcConfig['gate'];
                            foreach ($registryGates['items2'] as $pembantuData_tmp) {
                                foreach ($pembantuData_tmp as $pembantuData) {
                                    if (isset($pembantuData[$valueSrc]) && $pembantuData[$valueSrc] > 0) {
                                        $arrPymSrc = array(
                                            "jenis" => $stepCode,
                                            "target_jenis" => $paymentSrcConfig['jenisTarget'],
                                            "reference_jenis" => $paymentSrcConfig['jenisSrc'],
                                            "extern_id" => isset($pembantuData[$externSrc['id']]) ? $pembantuData[$externSrc['id']] : "",
                                            "extern_nama" => isset($pembantuData[$externSrc['nama']]) ? $pembantuData[$externSrc['nama']] : "",
                                            "nomer" => $tmpNomorNota,
                                            "label" => $paymentSrcConfig['label'],
                                            "tagihan" => $pembantuData[$valueSrc],
                                            "terbayar" => 0,
                                            "sisa" => $pembantuData[$valueSrc],
                                            "cabang_id" => $pembantuData['cabang_id'],
                                            "cabang_nama" => $pembantuData['cabang_nama'],
                                            "oleh_id" => $olehID,
                                            "oleh_nama" => $olehNama,
                                            "dtime" => $dtime,
                                            "fulldate" => $fulldate,
                                            "valas_id" => isset($externSrc['valasId']) && isset($pembantuData[$externSrc['valasId']]) ? $pembantuData[$externSrc['valasId']] : '',
                                            "valas_nama" => isset($externSrc['valasLabel']) && isset($pembantuData[$externSrc['valasLabel']]) ? $pembantuData[$externSrc['valasLabel']] : '',
                                            "valas_nilai" => isset($externSrc['valasValue']) && isset($pembantuData[$externSrc['valasValue']]) ? $pembantuData[$externSrc['valasValue']] : '',
                                            "tagihan_valas" => isset($externSrc['valasTagihan']) && isset($pembantuData[$externSrc['valasTagihan']]) ? $pembantuData[$externSrc['valasTagihan']] : '',
                                            "terbayar_valas" => 0,
                                            "sisa_valas" => isset($externSrc['valasSisa']) && isset($pembantuData[$externSrc['valasSisa']]) ? $pembantuData[$externSrc['valasSisa']] : '',
                                            //                            "extern_label2" => isset($pembantuData['pihakMainName']) ? $pembantuData['pihakMainName'] : "",
                                            "extern_label2" => (isset($externSrc['extern_label2']) && ($pembantuData[$externSrc['extern_label2']])) ? $pembantuData[$externSrc['extern_label2']] : "",
                                            "ppn" => (isset($externSrc['ppn']) && ($pembantuData[$externSrc['ppn']])) ? $pembantuData[$externSrc['ppn']] : "",
                                            "ppn_approved" => (isset($externSrc['ppn_approved']) && ($pembantuData[$externSrc['ppn_approved']])) ? $pembantuData[$externSrc['ppn_approved']] : 0,
                                            "ppn_sisa" => (isset($externSrc['ppn']) && ($pembantuData[$externSrc['ppn']])) ? $pembantuData[$externSrc['ppn']] : "",
                                            "ppn_status" => (isset($externSrc['ppn_status'])) ? $externSrc['ppn_status'] : 0,
                                            "extern_nilai2" => (isset($externSrc['extern_nilai2']) && ($pembantuData[$externSrc['extern_nilai2']])) ? $pembantuData[$externSrc['extern_nilai2']] : 0,
                                            "extern_date2" => (isset($externSrc['extern_date2']) && ($pembantuData[$externSrc['extern_date2']])) ? $pembantuData[$externSrc['extern_date2']] : "",
                                            "pph_23" => (isset($externSrc['pph_23']) && ($pembantuData[$externSrc['pph_23']])) ? $pembantuData[$externSrc['pph_23']] : "",
                                            "npwp" => (isset($externSrc['npwp']) && ($pembantuData[$externSrc['npwp']])) ? $pembantuData[$externSrc['npwp']] : "",
                                            "extern2_id" => (isset($externSrc['extern2_id']) && ($pembantuData[$externSrc['extern2_id']])) ? $pembantuData[$externSrc['extern2_id']] : "",
                                            "extern2_nama" => (isset($externSrc['extern2_nama']) && ($pembantuData[$externSrc['extern2_nama']])) ? $pembantuData[$externSrc['extern2_nama']] : "",
                                            "ppn_pph_faktor" => (isset($externSrc['ppn_pph_faktor']) && ($pembantuData[$externSrc['ppn_pph_faktor']])) ? $pembantuData[$externSrc['ppn_pph_faktor']] : "",
                                            "extern_jenis" => (isset($externSrc['extern_jenis']) && ($pembantuData[$externSrc['extern_jenis']])) ? $pembantuData[$externSrc['extern_jenis']] : "",
                                            "extern_nilai3" => (isset($externSrc['extern_nilai3']) && ($pembantuData[$externSrc['extern_nilai3']])) ? $pembantuData[$externSrc['extern_nilai3']] : "",
                                            "extern_nilai4" => (isset($externSrc['extern_nilai4']) && ($pembantuData[$externSrc['extern_nilai4']])) ? $pembantuData[$externSrc['extern_nilai4']] : "",
//                                    "npwp" => (isset($externSrc['npwp']) && ($pembantuData[$externSrc['npwp']])) ? $pembantuData[$externSrc['npwp']] : "",
                                            //                            "extern_nilai2" => (isset($externSrc['extern_nilai2']) && ($pembantuData[$externSrc['extern_nilai2']])) ? $pembantuData[$externSrc['extern_nilai2']] : "",
                                            "payment_locked" => (isset($externSrc['payment_locked']) && ($pembantuData[$externSrc['payment_locked']])) ? $pembantuData[$externSrc['payment_locked']] : 0,
                                            "cash_account" => (isset($externSrc['cash_account']) && ($pembantuData[$externSrc['cash_account']])) ? $pembantuData[$externSrc['cash_account']] : 0,
                                            "cash_account_nama" => (isset($externSrc['cash_account_nama']) && ($pembantuData[$externSrc['cash_account_nama']])) ? $pembantuData[$externSrc['cash_account_nama']] : 0,
                                        );
//                                        arrPrintWebs($arrPymSrc);
                                        $tr->writePaymentPembantuSrc($insertID, $arrPymSrc);
                                        showLast_query("pink");
                                    }
                                }

                            }

//                        cekMerah($this->db->last_query());
                        }

                    }

                }
                else {
                    cekMerah("TIDAK nulis paymentSrc");
                }
                //endregion


                validateAllBalances($trTmpCabangID);

//                break;
            }
        }

        cekHijau("...tes cli transaksi... rekening pembantu masuk disini (component detail)<br>start: $startDate<br>stop: $stopDate<br>butuh waktu: " . timeDiff($startDate, $stopDate));
        mati_disini("...tes cli transaksi... rekening pembantu masuk disini (component detail)<br>start: $startDate<br>stop: $stopDate<br>butuh waktu: " . timeDiff($startDate, $stopDate));
        $this->db->trans_complete() or die("Gagal saat berusaha  commit transaction!");

        cekHijau("...tes cli transaksi... rekening pembantu masuk disini (component detail)<br>start: $startDate<br>stop: $stopDate<br>butuh waktu: " . timeDiff($startDate, $stopDate));

    }

    public function run_susulanJurnal_2()
    {

        $this->load->model("MdlTransaksi");
        $this->load->model("CustomCounter");
        $this->load->helper("he_mass_table");
        $startDate = dtimeNow();


        $getTrID = (isset($_GET['tr_id']) && ($_GET['tr_id'] > 0)) ? $_GET['tr_id'] : 0;
        $addJudul = "";
//        $getTrID = "727781";
//        $getTrID = "727939";
//        $getTrID = "728077";
//        $getTrID = "728377";
        $getTrID = "728387";

        $tr = New MdlTransaksi();
        $tr->setSortBy(
            array(
                "kolom" => "id",
                "mode" => "ASC",
            )
        );
        $this->db->limit(1);

        // bila ada trID dari URL, maka ini adalah cek manual, tidak boleh close commit !!!
        if ($getTrID > 0) {
            $tr->addFilter("id='$getTrID'");

            $addJudul = "<br>cek manual";
        }
        else {
            $tr->addFilter("cli='0'");
            mati_disini(__LINE__ . " WAJIB tentukan transaksi_id");
        }

        $trTmp = $tr->lookupAll()->result();
        cekHere($this->db->last_query() . "<br>" . sizeof($trTmp));

        if (sizeof($trTmp) > 0) {
            $trID_cli = $trTmp[0]->id;
            $trTmpCabangID = $trTmp[0]->cabang_id;
            $kolom = array(
                "trID" => "id",
                "jenisTr" => "jenis",
                "jenisTrMaster" => "jenis_master",
                "jenisTrTop" => "jenis_top",
                "nomer" => "nomer",
                "nomerTop" => "nomer_top",
                "dtime" => "dtime",
                "fulldate" => "fulldate",
                "stepNumber" => "step_number",
                "indexRegistry" => "indexing_registry",
                "olehID" => "oleh_id",
                "olehNama" => "oleh_nama",
            );

            $arrKolomTrans = array();
            foreach ($kolom as $key => $val) {
                $arrKolomTrans[$key] = isset($trTmp[0]->$val) ? $trTmp[0]->$val : NULL;
            }

            $reg = New MdlTransaksi();
            $reg->setFilters(array());
            $reg->addFilter("transaksi_id='" . $trTmp[0]->id . "'");
            $regTmp = $reg->lookupDataRegistries()->result();
            $registryGates = array();
            foreach ($regTmp as $regSpec) {
                foreach ($regSpec as $key_reg => $val_reg) {
                    if ($key_reg != "transaksi_id") {
                        $registryGates[$key_reg] = blobDecode($val_reg);
                    }
                }
            }


            $this->jenisTr = $jenisTrTarget = $jenisTr = $jenis = $arrKolomTrans['jenisTr'];
            $jenisTrMaster = $arrKolomTrans['jenisTrMaster'];
            $fulldate = $arrKolomTrans['fulldate'];
            $dtime = $arrKolomTrans['dtime'];
            $stepNumber = $arrKolomTrans['stepNumber'];
            $insertNum = $tmpNomorNota = $transaksi_no = $arrKolomTrans['nomer'];
            $pelaku_transaksi_id = $olehID = $arrKolomTrans['olehID'];
            $pelaku_transaksi_nama = $olehNama = $arrKolomTrans['olehNama'];
            $insertID = $transaksiID = $transaksi_id = $arrKolomTrans['trID'];
            $stepNumCurrent = 1;
            $stepNum = 2;
            $ppnFactor = $registryGates["main"]["ppnFactor"];
            //---------------------
            $cCode = "_TR_" . $this->jenisTr;
            $_SESSION[$cCode] = array();
            $_SESSION[$cCode] = $registryGates;


            //gerbang yang kurang
            $arrGerbang = array(
                "biaya_cashback" => "harga",
                "hutang_pph23" => "nilai_pph23",
                "hutang_pph21" => "nilai_pph21",
                "valas_nonhutang_komisi_qty" => "valas__qty",
                "hutang_ke_pusat" => "valas_hpp",
                "piutang_cabang" => "valas_hpp",
            );
            foreach ($arrGerbang as $hasil => $sumber) {
                $_SESSION[$cCode]["main"][$hasil] = $_SESSION[$cCode]["main"][$sumber];
            }
            //---------------------
//            arrPrint($_SESSION[$cCode]["main"]);


            $configCoreMasterModulJenisBuilder = $configCore = loadConfigModulJenis_he_misc($jenisTrMaster, "coTransaksiCore");
            $configUiMasterModulJenis = $configUi = loadConfigModulJenis_he_misc($jenisTrMaster, "coTransaksiUi");
            $configLayoutMasterModulJenis = $configLayout = loadConfigModulJenis_he_misc($jenisTrMaster, "coTransaksiLayout");
            //----
            $configCoreMasterModulJenis = $configCore = array(
                "preProcessor" => array(
                    "16677" => array(
                        "master" => array(
                            // preprocc fifo stok valas
                            array(
                                "comName" => "FifoValasAverageMain",
                                "loop" => array(),
                                "static" => array(
                                    "cabang_id" => "placeID",
                                    "extern_id" => "valas",
                                    "extern_nama" => "valas__nama",
                                    "produk_qty" => "valas_nonhutang_komisi_qty",// jumlah stok valas yang dipakai || valas_nilai_bayar
                                    "gudang_id" => ".0",
                                    "cash_methode" => ".valas",// ditembak valas supaya bisa dijalankan
                                    //                            "cash_methode" => "cashMethodeOption",
                                ),
                                "srcGateName" => "main",
                                "srcRawGateName" => "main",
                            ),
                            array(
                                "comName" => "FifoValasMain",
                                "loop" => array(),
                                "static" => array(
                                    "cabang_id" => "placeID",
                                    "extern_id" => "valas",// harus ada isinya atau tidak boleh jalan fifonya
                                    "extern_nama" => "valas__nama",
                                    "produk_qty" => "valas_nonhutang_komisi_qty", // jumlah stok valas yang dipakai || valas_nilai_bayar
                                    "gudang_id" => ".0",
                                    "cash_methode" => ".valas",// ditembak valas supaya bisa dijalankan
                                ),
                                "resultParams" => array(
                                    "rsltItems" => array(
                                        "id" => "produk_id",
                                        "nama" => "nama",
                                        "name" => "nama",
                                        "jml" => "qty",
                                        "qty" => "qty",
                                        "valas_harga" => "hpp",
                                        "valas_hpp" => "hpp",
                                    ),
                                ),
                                "srcGateName" => "main",
                                "srcRawGateName" => "main",
                                "switchResultParams" => true,
                            ),

                            // inject selisih kurs
                            array(
                                "comName" => "SelisihKurs",
                                "loop" => array(),
                                "static" => array(
                                    "uang_muka_stock_valas" => "valas_hpp", // fifo valas
                                    "jenisTr" => "jenisTr",
                                    "cashMethodeOption" => ".valas",
                                    "additional" => "additional",
                                    "additional_value" => "additional_value",
                                    "nilai_entry" => "nilai_kas_cn",
                                ),
                                "srcGateName" => "main",
                                "srcRawGateName" => "main",
                            ),
                        ),
                        "detail" => array(),
                    ),
                ),
                "components" => array(
                    "16677" => array(
                        "master" => array(
                            // PUSAT, kas pusat dibawa ke cabang, saat cashback kas diberikan ke konsumen
                            array(
                                "comName" => "Jurnal",
                                "loop" => array(
                                    "1010060010" => "piutang_cabang",//piutang cabang
                                    "1010010010" => "-kas_pusat",//kas
                                    "1010010020" => "-valas_hpp",//valas
                                ),
                                "static" => array(
                                    "cabang_id" => "placeID",
                                    "jenis" => "jenisTr",
//                            "transaksi_no" => "nomer",
                                ),
                                "srcGateName" => "main",
                                "srcRawGateName" => "main",
                            ),
                            array(
                                "comName" => "Rekening",
                                "loop" => array(
                                    "1010060010" => "piutang_cabang",//piutang cabang
                                    "1010010010" => "-kas_pusat",//kas
                                    "1010010020" => "-valas_hpp",//valas
                                ),
                                "static" => array(
                                    "cabang_id" => "placeID",
                                    "jenis" => "jenisTr",
//                            "transaksi_no" => "nomer",
                                ),
                                "srcGateName" => "main",
                                "srcRawGateName" => "main",
                            ),
                            array(
                                "comName" => "RekeningPembantuKas",
                                "loop" => array(
                                    "1010010010" => "-kas_pusat",//kas
                                ),
                                "static" => array(
                                    "cabang_id" => "placeID",
                                    "extern_id" => "cash_account",// diisi id bank
                                    "extern_nama" => "cash_account__label",// diisi nama bank
                                    "jenis" => "jenisTr",
//                            "transaksi_no" => "nomer",
                                ),
                                "srcGateName" => "main",
                                "srcRawGateName" => "main",
                            ),
                            array(
                                "comName" => "RekeningPembantuAntarcabang",
                                "loop" => array(
                                    "1010060010" => "piutang_cabang",//piutang cabang
                                ),
                                "static" => array(
                                    "cabang_id" => "placeID",
                                    "extern_id" => "place2ID",
                                    "extern_nama" => "place2Name",
                                    "jenis" => "jenisTr",
//                            "transaksi_no" => "nomer",
                                ),
                                "srcGateName" => "main",
                                "srcRawGateName" => "main",
                            ),


                            // CABANG, terima kas dari pusat, saat cashback kas diberikan ke konsumen
                            array(
                                "comName" => "Jurnal",
                                "loop" => array(
                                    "2040010" => "hutang_ke_pusat",//hutang ke pusat
                                    "1010010010" => "kas_pusat",//kas
                                    "1010010020" => "valas_hpp",//valas
                                ),
                                "static" => array(
                                    "cabang_id" => "place2ID",
                                    "jenis" => "jenisTr",
//                            "transaksi_no" => "nomer",
                                ),
                                "srcGateName" => "main",
                                "srcRawGateName" => "main",
                            ),
                            array(
                                "comName" => "Rekening",
                                "loop" => array(
                                    "2040010" => "hutang_ke_pusat",//hutang ke pusat
                                    "1010010010" => "kas_pusat",//kas
                                    "1010010020" => "valas_hpp",//valas
                                ),
                                "static" => array(
                                    "cabang_id" => "place2ID",
                                    "jenis" => "jenisTr",
//                            "transaksi_no" => "nomer",
                                ),
                                "srcGateName" => "main",
                                "srcRawGateName" => "main",
                            ),
                            array(
                                "comName" => "RekeningPembantuKas",
                                "loop" => array(
                                    "1010010010" => "kas_pusat",//kas
                                ),
                                "static" => array(
                                    "cabang_id" => "place2ID",
                                    "extern_id" => "cash_account",// diisi id bank
                                    "extern_nama" => "cash_account__label",// diisi nama bank
                                    "jenis" => "jenisTr",
//                            "transaksi_no" => "nomer",
                                ),
                                "srcGateName" => "main",
                                "srcRawGateName" => "main",
                            ),
                            array(
                                "comName" => "RekeningPembantuAntarcabang",
                                "loop" => array(
                                    "2040010" => "hutang_ke_pusat",//hutang ke pusat
                                ),
                                "static" => array(
                                    "cabang_id" => "place2ID",
                                    "extern_id" => "placeID",
                                    "extern_nama" => "placeName",
                                    "jenis" => "jenisTr",
//                            "transaksi_no" => "nomer",
                                ),
                                "srcGateName" => "main",
                                "srcRawGateName" => "main",
                            ),

                            // CABANG, pemberian cashback ke konsumen
                            array(
                                "comName" => "Jurnal",
                                "loop" => array(
                                    "6010" => "biaya_cashback",//biaya usaha
                                    "2030010" => "hutang_pph21",//hutang pph21
                                    "2030030" => "hutang_pph23",//hutang pph23
                                    "1010010010" => "-kas_cabang",//kas
                                    "2010050" => "hutang_ke_konsumen",// hutang ke konsumen
                                    "2010120" => "hutang_komisi",// hutang komisi freelancer
                                    "1010010020" => "-valas_hpp",//valas
                                    "7010080" => "add_diskon_selisih_kurs",//laba(rugi) selisih kurs
                                ),
                                "static" => array(
                                    "cabang_id" => "place2ID",
                                    "jenis" => "jenisTr",
//                            "transaksi_no" => "nomer",
                                ),
                                "srcGateName" => "main",
                                "srcRawGateName" => "main",
                            ),
                            array(
                                "comName" => "Rekening",
                                "loop" => array(
                                    "6010" => "biaya_cashback",//biaya usaha
                                    "2030010" => "hutang_pph21",//hutang pph21
                                    "2030030" => "hutang_pph23",//hutang pph23
                                    "1010010010" => "-kas_cabang",//kas
                                    "2010050" => "hutang_ke_konsumen",// hutang ke konsumen
                                    "2010120" => "hutang_komisi",// hutang komisi freelancer
                                    "1010010020" => "-valas_hpp",//valas
                                    "7010080" => "add_diskon_selisih_kurs",//laba(rugi) selisih kurs
                                ),
                                "static" => array(
                                    "cabang_id" => "place2ID",
                                    "jenis" => "jenisTr",
//                            "transaksi_no" => "nomer",
                                ),
                                "srcGateName" => "main",
                                "srcRawGateName" => "main",
                            ),
                            array(
                                "comName" => "RekeningPembantuKas",
                                "loop" => array(
                                    "1010010010" => "-kas_cabang",// kas
                                ),
                                "static" => array(
                                    "cabang_id" => "place2ID",
                                    "extern_id" => "cash_account",// diisi id bank
                                    "extern_nama" => "cash_account__label",// diisi nama bank
                                    "jenis" => "jenisTr",
//                            "transaksi_no" => "nomer",
                                ),
                                "srcGateName" => "main",
                                "srcRawGateName" => "main",
                            ),
                            array(
                                "comName" => "RekeningPembantuCustomer",
                                "loop" => array(
                                    "2010050" => "hutang_ke_konsumen",// hutang ke konsumen
                                ),
                                "static" => array(
                                    "cabang_id" => "place2ID",
                                    "extern_id" => ".2010050050",
                                    "extern_nama" => ".Uang Muka Konsumen",
                                    "jenis" => "jenisTr",
                                ),
                                "srcGateName" => "main",
                                "srcRawGateName" => "main",
                            ),

                            // rekening pembantu hutang ke konsumen, uang muka tanpa relasi so (creditnote), konsumenID
                            array(
                                "comName" => "RekeningPembantuCustomerDetail",
                                "loop" => array(
                                    "2010050" => "hutang_ke_konsumen",// hutang ke konsumen
                                ),
                                "static" => array(
                                    "cabang_id" => "place2ID",
                                    "extern_id" => "pihakID",
                                    "extern_nama" => "pihakName",
                                    "extern2_id" => ".2010050050",
                                    "extern2_nama" => ".Uang Muka Konsumen",
                                    "jenis" => "jenisTr",
                                ),
                                "srcGateName" => "main",
                                "srcRawGateName" => "main",
                            ),
                            // pembantu biaya usaha, cashback
                            array(
                                "comName" => "RekeningPembantuBiayaUsahaMain",
                                "loop" => array(
                                    "6010" => "biaya_cashback",//biaya usaha
                                ),
                                "static" => array(
                                    "cabang_id" => "place2ID",
                                    "extern_id" => "pihakMainID",
                                    "extern_nama" => "pihakMainName",
                                    "jenis" => "jenisTr",
                                ),
                                "srcGateName" => "main",
                                "srcRawGateName" => "main",
                            ),


                            // CABANG, pindah hutang pph23 di cabang ke pusat
                            array(
                                "comName" => "Jurnal",
                                "loop" => array(
                                    "2040010" => "hutang_pph_total",//hutang ke pusat
                                    "2030010" => "-hutang_pph21",//hutang pph21
                                    "2030030" => "-hutang_pph23",//hutang pph23
                                ),
                                "static" => array(
                                    "cabang_id" => "place2ID",
                                    "jenis" => "jenisTr",
//                            "transaksi_no" => "nomer",
                                ),
                                "srcGateName" => "main",
                                "srcRawGateName" => "main",
                            ),
                            array(
                                "comName" => "Rekening",
                                "loop" => array(
                                    "2040010" => "hutang_pph_total",//hutang ke pusat
                                    "2030010" => "-hutang_pph21",//hutang pph21
                                    "2030030" => "-hutang_pph23",//hutang pph23
                                ),
                                "static" => array(
                                    "cabang_id" => "place2ID",
                                    "jenis" => "jenisTr",
//                            "transaksi_no" => "nomer",
                                ),
                                "srcGateName" => "main",
                                "srcRawGateName" => "main",
                            ),
                            array(
                                "comName" => "RekeningPembantuAntarcabang",
                                "loop" => array(
                                    "2040010" => "hutang_pph23",//hutang ke pusat
                                ),
                                "static" => array(
                                    "cabang_id" => "place2ID",
                                    "extern_id" => "placeID",
                                    "extern_nama" => "placeName",
                                    "jenis" => "jenisTr",
                                    "transaksi_no" => "nomer",
                                ),
                                "srcGateName" => "main",
                                "srcRawGateName" => "main",
                            ),
                            array(
                                "comName" => "RekeningPembantuAntarcabang",
                                "loop" => array(
                                    "2040010" => "hutang_pph21",//hutang ke pusat
                                ),
                                "static" => array(
                                    "cabang_id" => "place2ID",
                                    "extern_id" => "placeID",
                                    "extern_nama" => "placeName",
                                    "jenis" => "jenisTr",
                                    "transaksi_no" => "nomer",
                                ),
                                "srcGateName" => "main",
                                "srcRawGateName" => "main",
                            ),


                            // PUSAT, terima pindahan hutang pph23 dari cabang ke pusat
                            array(
                                "comName" => "Jurnal",
                                "loop" => array(
                                    "1010060010" => "hutang_pph_total",//piutang cabang
                                    "2030010" => "hutang_pph21",//hutang pph21
                                    "2030030" => "hutang_pph23",//hutang pph23
                                ),
                                "static" => array(
                                    "cabang_id" => "placeID",
                                    "jenis" => "jenisTr",
//                            "transaksi_no" => "nomer",
                                ),
                                "srcGateName" => "main",
                                "srcRawGateName" => "main",
                            ),
                            array(
                                "comName" => "Rekening",
                                "loop" => array(
                                    "1010060010" => "hutang_pph_total",//piutang cabang
                                    "2030010" => "hutang_pph21",//hutang pph21
                                    "2030030" => "hutang_pph23",//hutang pph23
                                ),
                                "static" => array(
                                    "cabang_id" => "placeID",
                                    "jenis" => "jenisTr",
//                            "transaksi_no" => "nomer",
                                ),
                                "srcGateName" => "main",
                                "srcRawGateName" => "main",
                            ),
                            array(
                                "comName" => "RekeningPembantuAntarcabang",
                                "loop" => array(
                                    "1010060010" => "hutang_pph23",//piutang cabang
                                ),
                                "static" => array(
                                    "cabang_id" => "placeID",
                                    "extern_id" => "place2ID",
                                    "extern_nama" => "place2Name",
                                    "jenis" => "jenisTr",
                                    "transaksi_no" => "nomer",
                                ),
                                "srcGateName" => "main",
                                "srcRawGateName" => "main",
                            ),
                            array(
                                "comName" => "RekeningPembantuAntarcabang",
                                "loop" => array(
                                    "1010060010" => "hutang_pph21",//piutang cabang
                                ),
                                "static" => array(
                                    "cabang_id" => "placeID",
                                    "extern_id" => "place2ID",
                                    "extern_nama" => "place2Name",
                                    "jenis" => "jenisTr",
                                    "transaksi_no" => "nomer",
                                ),
                                "srcGateName" => "main",
                                "srcRawGateName" => "main",
                            ),
                            array(
                                "comName" => "RekeningPembantuPphMain",
                                "loop" => array(
                                    "2030010" => "hutang_pph21",//hutang pph21
                                ),
                                "static" => array(
                                    "cabang_id" => "placeID",
                                    "jenis" => "jenisTr",
                                    "transaksi_no" => "nomer",
                                    "harga" => "hutang_pph21",
//                            "extern2_id" => ".9",
//                            "extern2_nama" => ".customer",
//                            "extern_id" => "customerID",// diisi customer
//                            "extern_nama" => "customerName",// diisi customer
                                    "extern2_id" => ".11",
                                    "extern2_nama" => ".freelancer",
                                    "extern_id" => "freelancerDetails",// diisi customer
                                    "extern_nama" => "freelancerDetails__nama",// diisi customer
                                ),
                                "srcGateName" => "main",
                                "srcRawGateName" => "main",
                            ),
                            array(
                                "comName" => "RekeningPembantuPphMain",
                                "loop" => array(
                                    "2030030" => "hutang_pph23",// hutang pph23
                                ),
                                "static" => array(
                                    "cabang_id" => "placeID",
                                    "extern_id" => "customerID",// diisi customer
                                    "extern_nama" => "customerName",// diisi customer
                                    "jenis" => "jenisTr",
                                    "transaksi_no" => "nomer",
                                    "harga" => "hutang_pph23",
                                    "extern2_id" => ".9",
                                    "extern2_nama" => ".customer",
                                ),
                                "srcGateName" => "main",
                                "srcRawGateName" => "main",
                            ),


//                            // CABANG, terima uang pembayaran PPh
//                            array(
//                                "comName" => "Jurnal",
//                                "loop" => array(
//                                    "1010010010" => "kas_masuk_pph",// kas
//                                    "6010" => "-nilai_biaya_usaha_pph",// biaya usaha
////                            "6030" => "-nilai_biaya_umum",// biaya umum
//                                ),
//                                "static" => array(
//                                    "cabang_id" => "place2ID",
//                                    "jenis" => "jenisTr",
//                                    // "transaksi_no" => "nomer",
//                                ),
//                                "srcGateName" => "main",
//                                "srcRawGateName" => "main",
//                            ),
//                            array(
//                                "comName" => "Rekening",
//                                "loop" => array(
//                                    "1010010010" => "kas_masuk_pph",// kas
//                                    "6010" => "-nilai_biaya_usaha_pph",// biaya usaha
////                            "6030" => "-nilai_biaya_umum",// biaya umum
//                                ),
//                                "static" => array(
//                                    "cabang_id" => "place2ID",
//                                    "jenis" => "jenisTr",
//                                    // "transaksi_no" => "nomer",
//                                ),
//                                "srcGateName" => "main",
//                                "srcRawGateName" => "main",
//                            ),
//                            array(
//                                "comName" => "RekeningPembantuKas",
//                                "loop" => array(
//                                    "1010010010" => "kas_masuk_pph",// kas
//                                ),
//                                "static" => array(
//                                    "cabang_id" => "place2ID",
//                                    "extern_id" => "cash_account_source",
//                                    "extern_nama" => "cash_account_source__nama",
//                                    "jenis" => "jenisTr",
//                                    // "transaksi_no" => "nomer",
//                                ),
//                                "srcGateName" => "main",
//                                "srcRawGateName" => "main",
//                            ),
//                            array(
//                                "comName" => "RekeningPembantuBiayaUsahaMain",
//                                "loop" => array(
//                                    "6010" => "-nilai_biaya_usaha_pph",// biaya usaha
//                                ),
//                                "static" => array(
//                                    "cabang_id" => "place2ID",
//                                    "extern_id" => "biayaDetails",//id dta biaya usaha
//                                    "extern_nama" => "biayaDetails__label",///nama data biaya usaha
//                                    "jenis" => "jenisTr",
//                                ),
//                                "srcGateName" => "main",
//                                "srcRawGateName" => "main",
//                            ),

                        ),
                        "detail" => array(
                            // pembantu biaya usaha, cashback penjualan, invoice
                            array(
                                "comName" => "RekeningPembantuBiayaUsahaSubItem",
                                "loop" => array(
                                    "6010" => "sub_harga",//biaya usaha
                                ),
                                "static" => array(
                                    "cabang_id" => "place2ID",
                                    "extern_id" => "id",
                                    "extern_nama" => "nama",
                                    "extern2_id" => "pihakMainID",
                                    "extern2_nama" => "pihakMainName",
                                    "extern3_id" => "customerID",
                                    "extern3_nama" => "customerName",
                                    "jenis" => "jenisTr",
                                ),
                                "srcGateName" => "items",
                                "srcRawGateName" => "items",
                            ),
                            // pembantu hutang komisi
                            array(
                                "comName" => "RekeningPembantuKomisiItem",
                                "loop" => array(
                                    "2010120" => "sub_nilai_kas_cn_detail",// hutang komisi freelancer
                                ),
                                "static" => array(
                                    "cabang_id" => "place2ID",
                                    "extern_id" => "id",
                                    "extern_nama" => "nama",
                                    "jenis" => "jenisTr",
                                ),
                                "srcGateName" => "items4_sum",
                                "srcRawGateName" => "items4_sum",
                            ),
                            // pembantu hutang komisi valas
                            array(
                                "comName" => "RekeningPembantuKomisiValasItem",
                                "loop" => array(
                                    "2010120" => "nilai_valas_detail",// hutang komisi valas freelancer
                                ),
                                "static" => array(
                                    "cabang_id" => "place2ID",
                                    "extern_id" => "valas",//valas id
                                    "extern_nama" => "valas__nama",// valas nama
                                    "extern2_id" => "id",// penerima komisi
                                    "extern2_nama" => "nama",// penerima komisi
                                    "jenis" => "jenisTr",
                                    "produk_qty" => "nilai_valas_qty_detail",
                                    "produk_nilai" => "nilai_valas_detail",
                                ),
                                "srcGateName" => "items4_sum",
                                "srcRawGateName" => "items4_sum",
                            ),

                            //DC/PUSAT bagian rekening pembantu valas (USD, GBP, SGD, dll)
                            array(
                                "comName" => "RekeningPembantuValas",
                                "loop" => array(
                                    "1010010020" => "-sub_valas_hpp",// valas
                                ),
                                "static" => array(
                                    "cabang_id" => "placeID",
                                    "extern_id" => "id",// id valas
                                    "extern_nama" => "nama",// nama valas
                                    "jenis" => "jenisTr",
                                    "qty" => "-qty",
                                    "produk_nilai" => "valas_hpp",
                                    "gudang_id" => "gudangID",
                                ),
                                "srcGateName" => "rsltItems",
                                "srcRawGateName" => "rsltItems",
                            ),

                            //CABANG bagian rekening pembantu valas (USD, GBP, SGD, dll)--------
                            array(
                                "comName" => "RekeningPembantuValas",
                                "loop" => array(
                                    "1010010020" => "sub_valas_hpp",// valas
                                ),
                                "static" => array(
                                    "cabang_id" => "place2ID",
                                    "extern_id" => "id",// id valas
                                    "extern_nama" => "nama",// nama valas
                                    "jenis" => "jenisTr",
                                    "qty" => "qty",
                                    "produk_nilai" => "valas_hpp",
                                    "gudang_id" => "gudang2ID",
                                ),
                                "srcGateName" => "rsltItems",
                                "srcRawGateName" => "rsltItems",
                            ),

                            //bagian rekening pembantu valas (USD, GBP, SGD, dll)
                            array(
                                "comName" => "RekeningPembantuValas",
                                "loop" => array(
                                    "1010010020" => "-sub_valas_hpp",// valas
                                ),
                                "static" => array(
                                    "cabang_id" => "place2ID",
                                    "extern_id" => "id",// id valas
                                    "extern_nama" => "nama",// nama valas
                                    "jenis" => "jenisTr",
                                    "qty" => "-qty",
                                    "produk_nilai" => "valas_hpp",
                                    "gudang_id" => "gudang2ID",
                                ),
                                "srcGateName" => "rsltItems",
                                "srcRawGateName" => "rsltItems",
                            ),
                        ),
                    ),
                ),
                "postProcessor" => array(
                    "16677" => array(
                        "master" => array(
                            // kas keluar dari pusat
                            array(
                                "comName" => "LockerValue",
                                "loop" => array(),
                                "static" => array(
                                    "cabang_id" => "placeID",
                                    "gudang_id" => ".0",
                                    "state" => ".active",
                                    "jenis" => ".kas",
                                    "produk_id" => "cash_account",
                                    "nama" => "cash_account__label",
                                    "nilai" => "-kas_pusat",
                                    "transaksi_id" => ".0",
                                    "oleh_id" => ".0",
                                ),
                                "srcGateName" => "main",
                                "srcRawGateName" => "main",
                            ),

                            // payment source creditnote konsumen
                            array(
                                "comName" => "PaymentUangMuka",
                                "loop" => array(),
                                "static" => array(
                                    "cabang_id" => "place2ID",
                                    "cabang_nama" => "placeName",
//                            "transaksi_id" => "uangMuka__transaksi_id",
                                    "jenis" => "uangMuka__jenis",
                                    "extern_id" => "pihakID",
                                    "extern_nama" => "pihakName",
                                    "label" => ".uang muka konsumen",
                                    "tambah" => "hutang_ke_konsumen",
                                    "extern_label2" => ".customer",//ini update untuk pembeda vemdor/ customer
                                ),
                                "reversable" => true,
                                "srcGateName" => "main",
                                "srcRawGateName" => "main",
                            ),

                            array(
                                "comName" => "LockerValue",
                                "loop" => array(),
                                "static" => array(
                                    "cabang_id" => "placeID",
                                    "gudang_id" => ".0",
                                    "state" => ".active",
                                    "jenis" => ".valas",
                                    "produk_id" => "valas",
                                    "nama" => "valas__nama",
                                    "nilai" => "-valas_nonhutang_komisi_qty",
                                    "transaksi_id" => ".0",
                                    "oleh_id" => ".0",
                                ),
                                "srcGateName" => "main",
                                "srcRawGateName" => "main",
                            ),
                            array(
                                "comName" => "LockerValue",
                                "loop" => array(),
                                "static" => array(
                                    "cabang_id" => "placeID",
                                    "gudang_id" => ".0",
                                    "state" => ".payment",
                                    "jenis" => ".valas",
                                    "produk_id" => "valas",
                                    "nama" => "valas__nama",
                                    "nilai" => "valas_nonhutang_komisi_qty",
                                    "transaksi_id" => ".0",
                                    "oleh_id" => ".0",
                                ),
                                "srcGateName" => "main",
                                "srcRawGateName" => "main",
                            ),


                        ),
                        "detail" => array(
                            // pembantu hutang komisi
                            array(
                                "comName" => "PaymentSourceBuilder",
                                "loop" => array(),
                                "static" => array(
                                    "cabang_id" => "placeID",
                                    "cabang_nama" => "placeName",
                                    "extern_id" => "id",
                                    "extern_nama" => "nama",
                                    "extern2_id" => ".11",
                                    "extern2_nama" => ".freelancer",
                                    "extern4_id" => "customerID",
                                    "extern4_nama" => "customerName",
                                    "extern5_id" => "place2ID",
                                    "extern5_nama" => "place2Name",
                                    "label" => ".hutang komisi",
                                    "target_jenis" => ".1488",
                                    "jenis" => "jenisTr",
                                    "reference_jenis" => "jenisTr",
                                    "oleh_id" => "olehID",
                                    "oleh_nama" => "olehName",
                                    "tagihan" => "sub_nilai_kas_cn_detail",
                                    "sisa" => "sub_nilai_kas_cn_detail",
                                    "tagihan_valas" => "sub_nilai_valas_qty_detail",
                                    "sisa_valas" => "sub_nilai_valas_qty_detail",
                                    "valas_id" => "valas",
                                    "valas_nama" => "valas__nama",
                                    "extern_nilai2" => "(sub_nilai_kas_cn_detail/sub_nilai_valas_qty_detail)",
                                ),
                                "srcGateName" => "items4_sum",
                                "srcRawGateName" => "items4_sum",
                            ),
                        ),
                    ),
                ),
            );
            //----

            cekHitam(":: jenisTrMaster-> $jenisTrMaster :: jenisTr-> $jenisTr :: [trID_cli: $trID_cli]");
//            arrPrintHitam($_SESSION[$cCode]["main"]);
//            mati_disini(__LINE__);

            $this->db->trans_start();


            // region preprocc

            //region pre-processors (master)
            if (isset($configCoreMasterModulJenis['preProcessor'][$jenisTrTarget]['master'])) {
                $iterator = isset($configCoreMasterModulJenis['preProcessor'][$jenisTrTarget]['master']) ? $configCoreMasterModulJenis['preProcessor'][$jenisTrTarget]['master'] : array();
                $itemNumLabels = isset($configUiMasterModulJenis['shoppingCartNumFields']) ? $configUiMasterModulJenis['shoppingCartNumFields'] : array();

                echo "ITEM NUM LABELS [$jenisTrTarget] []";
                $tmpOutParams = array();
                if (sizeof($iterator) > 0) {
                    echo "<script>top.writeProgress('PERSIAPAN PRE-PROCESSOR...', 'HEAD');</script>";
                    foreach ($iterator as $cCtr => $tComSpec) {
                        $comName = $tComSpec['comName'];
                        $srcGateName = $tComSpec['srcGateName'];
                        $srcRawGateName = $tComSpec['srcRawGateName'];
                        $resultParams = isset($tComSpec['resultParams']) ? $tComSpec['resultParams'] : array();
                        $switchResultParams = isset($tComSpec['switchResultParams']) ? $tComSpec['switchResultParams'] : false;

                        echo "master-preproc: $comName, initializing values <br>";
                        $tmpOutParams[$cCtr] = array();

                        $subParams = array();
                        if (isset($tComSpec['static'])) {
                            foreach ($tComSpec['static'] as $key => $value) {

                                $realValue = makeValue($value, $_SESSION[$cCode][$srcGateName], $_SESSION[$cCode][$srcGateName], 0);
                                $subParams['static'][$key] = $realValue;

                            }

                            if (isset($paramPatchers[$comName]) && sizeof($paramPatchers[$comName]) > 0) {
                                foreach ($paramPatchers[$comName] as $k => $v) {
                                    if (!isset($subParams['static'][$k])) {
                                        $subParams['static'][$k] = isset($$v) ? $$v : "_v";
                                    }
                                }
                            }
                            if (isset($paramForceFillers[$comName]) && sizeof($paramForceFillers[$comName]) > 0) {
                                $jenis = $_SESSION[$cCode]['main']['jenis'];
                                foreach ($paramForceFillers[$comName] as $k => $v) {
                                    $subParams['static'][$k] = isset($$v) ? $$v : "_v";
                                }
                            }

                            $subParams['static']["fulldate"] = date("Y-m-d");
                            $subParams['static']["dtime"] = date("Y-m-d H:i:s");
                            $subParams['static']["keterangan"] = $configUiMasterModulJenis['steps'][$stepNum]['label'] . " nomor " . $tmpNomorNota . " oleh " . $this->session->login['nama'];
                        }
                        if (sizeof($subParams) > 0) {
                            $tmpOutParams[$cCtr] = $subParams;
                        }

                        $mdlName = "Pre" . ucfirst($comName);
                        $this->load->model("Preprocs/" . $mdlName);
                        $m = new $mdlName($resultParams);

                        if (sizeof($tmpOutParams[$cCtr]) > 0) {
                            $tobeExecuted = true;
                        }
                        else {
                            $tobeExecuted = false;
                        }

                        if ($tobeExecuted) {
                            $m->pair($masterID, $tmpOutParams[$cCtr]) or die("Tidak berhasil memasang  values pada pre-processor: $comName/" . $this->jenisTr . "/" . __FUNCTION__ . "/" . __LINE__);
                            $gotParams = $m->exec();
//
                            cekbiru("gotparams dari $comName");
                            arrprint($gotParams);
//
                            if (sizeof($gotParams) > 0) {//==gotParams means result from preprocessor
                                //                                cekhijau("ada gotparam, sekarang mau replace");
                                foreach ($gotParams as $gateName => $gSpec) {

                                    if ($switchResultParams == true) {
                                        foreach ($gSpec as $id => $ggSpec) {
                                            if (!isset($_SESSION[$cCode][$gateName][$id])) {
                                                $_SESSION[$cCode][$gateName][$id] = array();
                                            }
                                            if (isset($_SESSION[$cCode][$gateName][$id])) {
                                                if (is_array($ggSpec) && sizeof($ggSpec) > 0) {
                                                    foreach ($ggSpec as $key => $val) {
                                                        $_SESSION[$cCode][$gateName][$id][$key] = $val;
                                                    }
                                                }
                                            }
                                            //cekMerah("REBUILDING VALUES..");
                                            if (sizeof($itemNumLabels) > 0) {
                                                //cekHijau("REBUILDING SUBS FOR ITEMS");
                                                foreach ($itemNumLabels as $key => $label) {
                                                    //cekHere("$id === $key => $label");
                                                    if (isset($_SESSION[$cCode][$gateName][$id][$key])) {
                                                        $_SESSION[$cCode][$gateName][$id]['sub_' . $key] = ($_SESSION[$cCode][$gateName][$id]['jml'] * $_SESSION[$cCode][$gateName][$id][$key]);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    else {

                                        if (isset($_SESSION[$cCode]['main'])) {
                                            if (is_array($gSpec) && sizeof($gSpec) > 0) {
                                                foreach ($gSpec as $key => $val) {
                                                    cekbiru("injecting param $key with $val");
                                                    $_SESSION[$cCode]['main'][$key] = $val;
                                                }
                                            }
                                        }
                                        //==inject gotParams to child gate
                                        if (isset($_SESSION[$cCode]['main'])) {
                                            if (is_array($gSpec) && sizeof($gSpec) > 0) {
                                                foreach ($gSpec as $key => $val) {
                                                    $_SESSION[$cCode]['main'][$key] = $val;
                                                }
                                            }
                                        }
                                    }

                                }
                            }
                            else {
                                //                                cekmerah("TIDAK ada gotparam, tidak perlu replace");
                            }

                        }
                        else {
                            //                            cekBiru("sub-komponem $comName tidak memenuhi syarat untuk ditulis");
                        }

                        $this->load->helper("he_value_builder");
                        fillValues_he_value_builder($this->jenisTr, $stepNumCurrent, $stepNum, $configCoreMasterModulJenis, $configUiMasterModulJenis, $configValuesMasterModulJenis, $ppnFactor);

                    }
                }
                else {
                    //cekKuning("sub-preproc is not set");
                }


                $this->load->helper("he_value_builder");
                fillValues_he_value_builder($this->jenisTr, $stepNumCurrent, $stepNum, $configCoreMasterModulJenisBuilder, $configUiMasterModulJenis, $configValuesMasterModulJenis, $ppnFactor);


            }
            else {
                echo("no processor defined. skipping preprocessor..<br>");
            }
            //endregion

            // endregion preprocc

            $registryGates = $_SESSION[$cCode];
//            arrPrintHitam($registryGates["main"]);
//            arrPrintPink($_SESSION[$cCode]["rsltItems"]);
            $rsltItems_valas = blobEncode($_SESSION[$cCode]["rsltItems"]);

            cekHitam("CETAK RSLTITEMS:");
            cekHitam($rsltItems_valas);
//            mati_disini(__LINE__ . " || STOP CEK DULU...");

            //region components
            $paramPatchers = $this->config->item('heTransaksi_paramPatchers') != null ? $this->config->item('heTransaksi_paramPatchers') : array();
            $paramForceFillers = $this->config->item('heTransaksi_paramForceFillers') != null ? $this->config->item('heTransaksi_paramForceFillers') : array();
            $validateSubComponent = $this->config->item('heTransaksi_validateComponentDetail') != null ? $this->config->item('heTransaksi_validateComponentDetail') : array();
            $paramForceFillersJenisTR = $this->config->item('heTransaksi_paramForceFillers_jenisTR') != null ? $this->config->item('heTransaksi_paramForceFillers_jenisTR') : array();

            $componentGate['detail'] = array();
            $componentConfig['master'] = array();
            $componentConfig['detail'] = array();
            $iterator = isset($configCore["components"][$jenisTr]['detail']) ? $configCore["components"][$jenisTr]['detail'] : array();
            $componentConfig['detail'] = $iterator;
            $iteratorMaster = $componentConfig['master'] = isset($configCore["components"][$jenisTr]['master']) ? $configCore["components"][$jenisTr]['master'] : array();
            $revertedTarget = "";
            $subComModel = array();
            if (sizeof($iterator) > 0) {
                $compValidators = ($this->config->item('transaksi_value_required_components') != null) ? $this->config->item('transaksi_value_required_components') : array();
                $filterNeeded = false;
                $arrRekeningLoop = array();
                foreach ($iterator as $cCtr => $tComSpec) {
                    $comName_orig = $comName = $tComSpec['comName'];
                    $srcGateName = $tComSpec['srcGateName'];
                    $loopRequire = isset($tComSpec['loopRequire']) ? $tComSpec['loopRequire'] : false;
                    $srcRawGateName = $tComSpec['srcRawGateName'];

                    echo "sub-component: $comName, $srcGateName, initializing values <br>";

                    $tmpOutParams[$cCtr] = array();
                    if (isset($registryGates[$srcGateName]) && sizeof($registryGates[$srcGateName]) > 0) {

                        foreach ($registryGates[$srcGateName] as $id => $dSpec) {
                            $comName = $comName_orig;
                            if (substr($comName, 0, 1) == "{") {
                                $comName = trim($comName, "{");
                                $comName = trim($comName, "}");
                                $comName = str_replace($comName, $registryGates[$srcGateName][$id][$comName], $comName);
                                $tComSpec['comName'] = $comName;
                                $iterator[$cCtr]['comName'] = $comName;
                            }

                            $filterNeeded = false;
                            $mdlName = "Com" . ucfirst($comName);
                            if (in_array($mdlName, $compValidators)) {//perlu validasi filter
                                $filterNeeded = true;
                            }
                            $subParams = array();
                            if (isset($tComSpec['loop'])) {
                                foreach ($tComSpec['loop'] as $key => $value) {
                                    if (substr($key, 0, 1) == "{") {
                                        $key = trim($key, "{");
                                        $key = trim($key, "}");
                                        $key = str_replace($key, $registryGates[$srcGateName][$id][$key], $key);
                                    }

                                    $subComModel[$key] = $comName;

                                    $realValue = makeValue($value, $registryGates[$srcGateName][$id], $registryGates[$srcGateName][$id], 0);

                                    if (strlen($key) > 1) {
                                        $subParams['loop'][$key] = $realValue;
                                    }
                                    else {
                                        $subParams['loop'] = array();
                                    }

                                    // =================== =================== ===================
                                    if (!isset($arrRekeningLoop[$dSpec[$tComSpec['static']['cabang_id']]][$key])) {
                                        $arrRekeningLoop[$dSpec[$tComSpec['static']['cabang_id']]][$key] = 0;
                                    }
                                    $arrRekeningLoop[$dSpec[$tComSpec['static']['cabang_id']]][$key] += $realValue;
                                    if ($realValue != 0) {
                                        cekUngu(":: cetak loop $key => $realValue ::");
                                    }

                                    if ($filterNeeded) {
                                        if ($subParams['loop'][$key] == 0) {
                                            unset($subParams['loop'][$key]);

                                            // =================== =================== ===================
                                        }
                                    }
                                }
                            }
                            if (isset($tComSpec['static'])) {
                                foreach ($tComSpec['static'] as $key => $value) {
                                    $realValue = makeValue($value, $registryGates[$srcGateName][$id], $registryGates[$srcGateName][$id], 0);
                                    $subParams['static'][$key] = trim($realValue);
                                }
                                if (isset($paramPatchers[$comName]) && sizeof($paramPatchers[$comName]) > 0) {
                                    foreach ($paramPatchers[$comName] as $k => $v) {
                                        if (!isset($subParams['static'][$k])) {
                                            $subParams['static'][$k] = isset($$v) ? $$v : "_v";
                                            cekOrange("[$jenis] fill :: $comName :: $k ($v) => " . $subParams['static'][$k]);
                                        }
                                    }
                                }
                                if (isset($paramForceFillers[$comName]) && sizeof($paramForceFillers[$comName]) > 0) {
                                    foreach ($paramForceFillers[$comName] as $k => $v) {
                                        $subParams['static'][$k] = isset($$v) ? $$v : "_v";
                                        cekOrange("[$jenis] fillforce :: $comName :: $k ($v) => " . $subParams['static'][$k]);
                                    }
                                }

                                // tambahan custom gerbang saat simpan transaksi, tidak bisa ditambahkan di coTransaksiCore/coTransaksiValues
                                if (isset($paramForceFillersJenisTR[$comName][$jenisTrMaster]) && sizeof($paramForceFillersJenisTR[$comName][$jenisTrMaster]) > 0) {
                                    foreach ($paramForceFillersJenisTR[$comName][$jenisTrMaster] as $k => $v) {
                                        $subParams['static'][$k] = isset($$v) ? $$v : "_v";
                                        cekorange(":: $k diisikan dengan " . $subParams['static'][$k]);
                                    }
                                }
                                $subParams['static']["fulldate"] = $fulldate;
                                $subParams['static']["dtime"] = $dtime;
                                $subParams['static']["keterangan"] = $configUi['steps'][$stepNumber]['label'] . " nomor " . $tmpNomorNota . " oleh " . $olehNama;
                                //------
                                $subParams['static']["reference_id"] = isset($dSpec["referenceID"]) ? $dSpec["referenceID"] : "";
                                $subParams['static']["reference_nomer"] = isset($dSpec["referenceNomer"]) ? $dSpec["referenceNomer"] : "";
                                $subParams['static']["reference_jenis"] = isset($dSpec["jenisTr_reference"]) ? $dSpec["jenisTr_reference"] : "";
                                $subParams['static']["reference_id_top"] = isset($dSpec["referenceID_top"]) ? $dSpec["referenceID_top"] : "";
                                $subParams['static']["reference_nomer_top"] = isset($dSpec["referenceNomer_top"]) ? $dSpec["referenceNomer_top"] : "";
                                $subParams['static']["reference_jenis_top"] = isset($dSpec["pihakExternMasterID"]) ? $dSpec["pihakExternMasterID"] : "";
                                //------
                                if (strlen($revertedTarget) > 1) {
                                    $subParams['static']['reverted_target'] = $revertedTarget;
                                }
                            }
                            if (sizeof($subParams) > 0) {
                                if ($filterNeeded) {
                                    if (isset($subParams['loop']) && !empty($subParams['loop'])) {
                                        $tmpOutParams[$cCtr][] = $subParams;
                                    }
                                }
                                else {
                                    if (empty($subParams['loop']) && $loopRequire == true) {
                                        unset($tmpOutParams[$cCtr]);
                                    }
                                    else {
                                        $tmpOutParams[$cCtr][] = $subParams;
                                    }
                                }
                            }
                        }

                        $componentGate['detail'][$cCtr] = $subParams;
                    }

                }
                $it = 0;
                foreach ($iterator as $cCtr => $tComSpec) {
                    $it++;
                    $comName = $tComSpec['comName'];
                    $srcGateName = $tComSpec['srcGateName'];
                    $srcRawGateName = $tComSpec['srcRawGateName'];
                    if (isset($registryGates[$srcGateName]) && sizeof($registryGates[$srcGateName]) > 0) {
                        foreach ($registryGates[$srcGateName] as $id => $dSpec) {
                            if (substr($comName, 0, 1) == "{") {
                                $comName = trim($comName, "{");
                                $comName = trim($comName, "}");
                                $comName = str_replace($comName, $registryGates[$srcGateName][$id][$comName], $comName);
                            }
                        }
                    }
                    else {
                        $comName = NULL;
                    }
                    cekHere("::::: $comName ::::: $srcGateName :::::");


                    echo __LINE__ . " sub $cCtr component #$it: $comName, sending values**** <br>";

                    if ($comName != NULL) {

                        $mdlName = "Com" . ucfirst($comName);
                        $this->load->model("Coms/" . $mdlName);
                        $m = new $mdlName();

                        if (isset($tmpOutParams[$cCtr]) && sizeof($tmpOutParams[$cCtr]) > 0) {
                            $tobeExecuted = true;
                        }
                        else {
                            $tobeExecuted = false;
                        }

                        if ($tobeExecuted) {
                            $m->pair($tmpOutParams[$cCtr]) or die("Tidak berhasil memasang  values pada komponen: $comName/" . $jenisTrMaster . "/" . __FUNCTION__ . "/" . __LINE__);
                            $m->exec() or die("Gagal saat berusaha  exec values pada komponen: $comName/" . $jenisTrMaster . "/" . __FUNCTION__ . "/" . __LINE__);
                        }
                        else {
                            cekBiru("sub-komponem $comName tidak memenuhi syarat untuk ditulis");
                        }

                    }
                }

                $pakai_ini = 0;
                if ($pakai_ini == 1) {
                    // region baca jurnal rekening besar
                    $jn = New ComJurnal();
                    $jn->addFilter("transaksi_id='$transaksiID'");
                    $jnTmp = $jn->lookupAll()->result();
//                    arrPrint($jnTmp);
                    $arrJurnal = array();
                    if (sizeof($jnTmp) > 0) {
                        foreach ($jnTmp as $ii => $spec) {
                            $defPosition = detectRekDefaultPosition($spec->rekening);
                            switch ($defPosition) {
                                case "debet":
                                    $arrJurnal[$spec->cabang_id][$spec->rekening] = $spec->debet > 0 ? $spec->debet : $spec->kredit * -1;
                                    break;
                                case "kredit":
                                    $arrJurnal[$spec->cabang_id][$spec->rekening] = $spec->kredit > 0 ? $spec->kredit : $spec->debet * -1;
                                    break;
                                default:
                                    mati_disini("tidak menemukan default posisi rekening...");
                                    break;
                            }
                        }
                    }
                    // endregion

                    cekHere("cetak array jurnal");
                    arrPrint($arrJurnal);

                    cekHere("cetak rek loop");
                    arrPrint($arrRekeningLoop);


                    if (sizeof($arrJurnal) > 0) {
                        if (sizeof($arrRekeningLoop) > 0) {
                            foreach ($arrRekeningLoop as $cabang_id => $loopSpec) {
                                foreach ($loopSpec as $rekening => $rekValue) {
                                    if (array_key_exists($rekening, $arrJurnal[$cabang_id])) {
                                        if (floor($rekValue) != floor($arrJurnal[$cabang_id][$rekening])) {
                                            mati_disini("nilai $rekening, jurnal: " . floor($arrJurnal[$cabang_id][$rekening]) . ", akumulasi pembantu: " . floor($rekValue));
                                        }
                                        else {
                                            cekHijau(":: COCOK ::");
                                        }
                                    }
                                }
                            }
                        }
                    }


                }

                // validasi rekening besar vs rekening pembantu
//                validateBalancesComparison($trTmpCabangID, $componentGate, $componentConfig, "detail", $transaksiID, $tmpNomorNota);

            }
            else {
                cekMerah("subcomponents [detail] is not set");
            }

            if (sizeof($iteratorMaster) > 0) {
                $compValidators = ($this->config->item('transaksi_value_required_components') != null) ? $this->config->item('transaksi_value_required_components') : array();
                $componentConfig['master'] = $iteratorMaster;
                $cCtr = 0;
                foreach ($iteratorMaster as $cCtr => $tComSpec) {
                    $cCtr++;
                    $comName = $tComSpec['comName'];
                    if (substr($comName, 0, 1) == "{") {
                        $comName = trim($comName, "{");
                        $comName = trim($comName, "}");
                        $comName = str_replace($comName, $registryGates[$srcGateName][$comName], $comName);
                    }
                    $srcGateName = $tComSpec['srcGateName'];
                    $srcRawGateName = $tComSpec['srcRawGateName'];
                    echo "component # $cCtr: $comName<br>";

                    $dSpec = $registryGates[$srcGateName];
                    $tmpOutParams = array();
                    if (isset($tComSpec['loop'])) {
                        foreach ($tComSpec['loop'] as $key => $value) {
                            if (substr($key, 0, 1) == "{") {
                                $key = trim($key, "{");
                                $key = trim($key, "}");
                                $key = str_replace($key, $registryGates[$srcGateName][$key], $key);
                            }
                            $realValue = makeValue($value, $registryGates[$srcGateName], $registryGates[$srcGateName], 0);
                            $tmpOutParams['loop'][$key] = $realValue;
                        }
                    }
                    if (isset($tComSpec['static'])) {
                        foreach ($tComSpec['static'] as $key => $value) {

                            $realValue = makeValue($value, $registryGates[$srcGateName], $registryGates[$srcGateName], 0);
                            $tmpOutParams['static'][$key] = $realValue;

                        }
                        if (!isset($tmpOutParams['static']["transaksi_id"])) {
                            $tmpOutParams['static']["transaksi_id"] = $insertID;
                        }
                        if (!isset($tmpOutParams['static']["transaksi_no"])) {
                            $tmpOutParams['static']["transaksi_no"] = $insertNum;
                        }
                        $tmpOutParams['static']["urut"] = $cCtr;
                        $tmpOutParams['static']["fulldate"] = $fulldate;
                        $tmpOutParams['static']["dtime"] = $dtime;
                        $tmpOutParams['static']["keterangan"] = $configUi['steps'][$stepNumber]['label'] . " nomor " . $tmpNomorNota . " oleh " . $olehNama;


                    }
                    if (isset($tComSpec['static2'])) {
                        //cekHere("DISINI OIII");
                        foreach ($tComSpec['static2'] as $key => $value) {

                            $realValue = makeValue($value, $registryGates[$srcGateName][$cCtr], $registryGates[$srcGateName][$cCtr], 0);
                            $tmpOutParams['static2'][$key] = $realValue;

                        }
                        if (!isset($tmpOutParams['static2']["transaksi_id"])) {
                            $tmpOutParams['static2']["transaksi_id"] = $insertID;
                        }
                        if (!isset($tmpOutParams['static2']["transaksi_no"])) {
                            $tmpOutParams['static2']["transaksi_no"] = $insertNum;
                        }

                        $tmpOutParams['static2']["fulldate"] = $fulldate;
                        $tmpOutParams['static2']["dtime"] = $dtime;
                        $tmpOutParams['static2']["keterangan"] = $configUi['steps'][$stepNumber]['label'] . " nomor " . $tmpNomorNota . " oleh " . $olehNama;


                    }

                    $mdlName = "Com" . ucfirst($comName);
                    $this->load->model("Coms/" . $mdlName);
                    $m = new $mdlName();

                    //===filter value nol, jika harus difilter
                    $tobeExecuted = true;
                    if (in_array($mdlName, $compValidators)) {

                        $loopParams = isset($tmpOutParams['loop']) ? $tmpOutParams['loop'] : array();
                        if (sizeof($loopParams) > 0) {
                            foreach ($loopParams as $key => $val) {
                                cekmerah("$comName : $key = $val ");
                                if ($val == 0) {
                                    unset($tmpOutParams['loop'][$key]);
                                }
                            }
                        }
                        if (sizeof($tmpOutParams['loop']) < 1) {
                            $tobeExecuted = false;
                        }

                    }
                    if ($tobeExecuted) {
                        $m->pair($tmpOutParams) or die("Tidak berhasil memasang  values pada komponen: $comName/" . $this->jenisTr . "/" . __FUNCTION__ . "/" . __LINE__);
                        $m->exec() or die("Gagal saat berusaha  exec values pada komponen: $comName/" . $this->jenisTr . "/" . __FUNCTION__ . "/" . __LINE__);
                    }
                    $componentGate['master'][$cCtr] = $tmpOutParams;
                }
            }
            else {
                cekHitam("TIDAK ADA CORE MASTER");
            }
            //endregion

//            mati_disini(__LINE__ . " || STOP CEK DULU...");

            // region post procc

            //region processing sub-post-processors, always
            $iterator = isset($configCore['postProcessor'][$jenisTrTarget]['detail']) ? $configCore['postProcessor'][$jenisTrTarget]['detail'] : array();
            if (sizeof($iterator) > 0) {
                $tmpOutParams = array();
                foreach ($iterator as $cCtr => $tComSpec) {
                    $comName = $tComSpec['comName'];
                    $srcGateName = $tComSpec['srcGateName'];
                    $srcRawGateName = $tComSpec['srcRawGateName'];
                    echo "sub-postProcessor: $comName, initializing values <br>";
                    echo "<script>top.writeProgress('MENYIAPKAN DATA SUB-PROCESSORS UNTUK DIKIRIM...', 'head');</script>";
                    $tmpOutParams[$cCtr] = array();
                    foreach ($_SESSION[$cCode][$srcGateName] as $cnt => $dSpec) {
                        $subParams = array();
                        if (isset($tComSpec['loop'])) {
                            foreach ($tComSpec['loop'] as $key => $value) {

                                $realValue = makeValue($value, $_SESSION[$cCode][$srcGateName][$cnt], $_SESSION[$cCode][$srcGateName][$cnt], 0);
                                $subParams['loop'][$key] = $realValue;

                            }
                        }
                        if (isset($tComSpec['static'])) {
                            foreach ($tComSpec['static'] as $key => $value) {

                                $realValue = makeValue($value, $_SESSION[$cCode][$srcGateName][$cnt], $_SESSION[$cCode][$srcGateName][$cnt], 0);
                                $subParams['static'][$key] = $realValue;
                                //                                cekBiru("$key diisi dengan $realValue");

                            }
                            if (isset($paramPatchers[$comName]) && sizeof($paramPatchers[$comName]) > 0) {
                                foreach ($paramPatchers[$comName] as $k => $v) {
                                    if (!isset($subParams['static'][$k])) {
                                        $subParams['static'][$k] = isset($$v) ? $$v : "_v";
                                    }
                                }
                            }
                            if (isset($paramForceFillers[$comName]) && sizeof($paramForceFillers[$comName]) > 0) {
                                $jenis = $_SESSION[$cCode]['main']['jenis'];
                                foreach ($paramForceFillers[$comName] as $k => $v) {
                                    $subParams['static'][$k] = isset($$v) ? $$v : "_v";
                                    //                                    cekorange(":: $k diisikan dengan " . $subParams['static'][$k]);
                                }
                            }
                            $subParams['static']["fulldate"] = $fulldate;
                            $subParams['static']["dtime"] = $dtime;
                            $subParams['static']["keterangan"] = $configUiMasterModulJenis['steps'][$stepNum]['label'] . " nomor " . $tmpNomorNota . " oleh " . $this->session->login['nama'];
                        }
                        if (sizeof($subParams) > 0) {
                            $tmpOutParams[$cCtr][] = $subParams;
                        }
                        echo "<script>top.writeProgress('" . isset($subParams['static']['name']) ? $subParams['static']['name'] : "" . " " . isset($subParams['static']['extern_nama']) ? $subParams['static']['extern_nama'] : "" . " " . isset($subParams['static']['nama']) ? $subParams['static']['nama'] : "" . "');</script>";
                    }
                }

                foreach ($iterator as $cCtr => $tComSpec) {
                    $comName = $tComSpec['comName'];
                    $srcGateName = $tComSpec['srcGateName'];
                    $srcRawGateName = $tComSpec['srcRawGateName'];
                    if (sizeof($tmpOutParams[$cCtr]) > 0) {

                        echo "sub-postProcessor: $comName, sending values <br>";
                        echo "<script>top.writeProgress('SENDING SUB-PROCESSORS ($comName)...', 'head');</script>";
                        $mdlName = "Com" . ucfirst($comName);
                        $this->load->model("Coms/" . $mdlName);
                        $m = new $mdlName();

                        $m->pair($tmpOutParams[$cCtr]) or die("Tidak berhasil memasang  values pada post-processor: $comName/" . $this->jenisTr . "/" . __FUNCTION__ . "/" . __LINE__);
                        $m->exec() or die("Gagal saat berusaha  exec values pada post-processor: $comName/" . $this->jenisTr . "/" . __FUNCTION__ . "/" . __LINE__);
//                    cekBiru($this->db->last_query());
                    }
                }
            }
            //endregion

            //region processing main-post-processors, always
            $iterator = isset($configCore['postProcessor'][$jenisTrTarget]['master']) ? $configCore['postProcessor'][$jenisTrTarget]['master'] : array();
            if (sizeof($iterator) > 0) {
                echo "<script>top.writeProgress('MEMPROSES MAIN-PROCESSORS...', 'head');</script>";
                $tmpOutParams = array();
                foreach ($iterator as $cCtr => $tComSpec) {
                    $comName = $tComSpec['comName'];
                    $srcGateName = $tComSpec['srcGateName'];
                    $srcRawGateName = $tComSpec['srcRawGateName'];
                    echo "post-processor: $comName<br>";

                    $dSpec = $_SESSION[$cCode][$srcGateName];
                    $tmpOutParams = array();
                    if (isset($tComSpec['loop'])) {
                        foreach ($tComSpec['loop'] as $key => $value) {

                            $realValue = makeValue($value, $_SESSION[$cCode][$srcGateName], $_SESSION[$cCode][$srcGateName], 0);
                            $tmpOutParams['loop'][$key] = $realValue;

                        }
                    }
                    if (isset($tComSpec['static'])) {
                        //cekHere("DISINI OIII");
                        foreach ($tComSpec['static'] as $key => $value) {

                            $realValue = makeValue($value, $_SESSION[$cCode][$srcGateName], $_SESSION[$cCode][$srcGateName], 0);
                            $tmpOutParams['static'][$key] = $realValue;

                        }
                        if (isset($paramPatchers[$comName]) && sizeof($paramPatchers[$comName]) > 0) {
                            foreach ($paramPatchers[$comName] as $k => $v) {
                                if (!isset($tmpOutParams['static'][$k])) {
                                    $tmpOutParams['static'][$k] = isset($$v) ? $$v : "_v";
                                    echo "<script>top.writeProgress(':: $key diisikan dengan " . $tmpOutParams['static'][$k] . ");</script>";
                                }
                            }
                        }
                        if (isset($paramForceFillers[$comName]) && sizeof($paramForceFillers[$comName]) > 0) {
                            $jenis = $_SESSION[$cCode]['main']['jenis'];
                            foreach ($paramForceFillers[$comName] as $k => $v) {
                                $tmpOutParams['static'][$k] = isset($$v) ? $$v : "_v";
                                echo "<script>top.writeProgress(':: $key diisikan dengan " . $tmpOutParams['static'][$k] . ");</script>";
                            }
                        }
                        $tmpOutParams['static']["fulldate"] = $fulldate;
                        $tmpOutParams['static']["dtime"] = $dtime;
                        $tmpOutParams['static']["keterangan"] = $configUiMasterModulJenis['steps'][$stepNum]['label'] . " nomor " . $tmpNomorNota . " oleh " . $this->session->login['nama'];
                    }
                    if (isset($tComSpec['static2'])) {
                        foreach ($tComSpec['static2'] as $key => $value) {

                            $realValue = makeValue($value, $_SESSION[$cCode][$srcGateName][$cCtr], $_SESSION[$cCode][$srcGateName][$cCtr], 0);
                            $tmpOutParams['static2'][$key] = $realValue;

                        }
                        if (isset($paramPatchers[$comName]) && sizeof($paramPatchers[$comName]) > 0) {
                            foreach ($paramPatchers[$comName] as $k => $v) {
                                if (!isset($subParams['static'][$k])) {
                                    $subParams['static'][$k] = isset($$v) ? $$v : "_v";
                                }
                            }
                        }
                        if (isset($paramForceFillers[$comName]) && sizeof($paramForceFillers[$comName]) > 0) {
                            $jenis = $_SESSION[$cCode]['main']['jenis'];
                            foreach ($paramForceFillers[$comName] as $k => $v) {
                                $subParams['static'][$k] = isset($$v) ? $$v : "_v";
                            }
                        }
                        $tmpOutParams['static2']["fulldate"] = $fulldate;
                        $tmpOutParams['static2']["dtime"] = $dtime;
                        $tmpOutParams['static2']["keterangan"] = $configUiMasterModulJenis['steps'][$stepNum]['label'] . " nomor " . $tmpNomorNota . " oleh " . $this->session->login['nama'];
                    }

                    //lgShowError("Ada kesalahan",);
                    $mdlName = "Com" . ucfirst($comName);
                    $this->load->model("Coms/" . $mdlName);
                    $m = new $mdlName();
                    $m->pair($tmpOutParams) or die("Tidak berhasil memasang  values pada post-processor: $comName/" . $this->jenisTr . "/" . __FUNCTION__ . "/" . __LINE__);
                    $m->exec() or die("Gagal saat berusaha  exec values pada post-processor: $comName/" . $this->jenisTr . "/" . __FUNCTION__ . "/" . __LINE__);
                }
            }
            //endregion

            // endregion post procc


            //region nulis paymentSource
            $stepCode = $configUiMasterModulJenis['steps'][$stepNum]['target'];
            $paymentSources = $this->config->item("payment_source");
            cekHere("[stepCode: $stepCode]");
            if (array_key_exists($stepCode, $paymentSources)) {
                $payConfigs = isset($paymentSources[$stepCode][$stepNum]) ? $paymentSources[$stepCode][$stepNum] : array();
                if (sizeof($payConfigs) > 0) {
                    foreach ($payConfigs as $paymentSrcConfig) {
                        $valueLabel = isset($paymentSrcConfig['label_key']) ? $paymentSrcConfig['label_key'] : $paymentSrcConfig['label'];
                        $valueSrc = $paymentSrcConfig['valueSrc'];
                        $externSrc = $paymentSrcConfig['externSrc'];
                        $valueAdd = isset($_SESSION[$cCode]['main'][$paymentSrcConfig['addValueValidator']]) ? $_SESSION[$cCode]['main'][$paymentSrcConfig['addValueValidator']] : 0;
                        if (isset($paymentSrcConfig['model'])) {
                            $mdlName = $paymentSrcConfig['model'];
                            $this->load->model("Mdls/$mdlName");
                            $pMdl = New $mdlName();
                            $pTmpMdl = $pMdl->lookupAll()->result();
                            $pTmpMdlResult = array();
                            if (sizeof($pTmpMdl) > 0) {
                                foreach ($pTmpMdl as $pTmpMdlSpec) {
                                    $pTmpMdlResult[$pTmpMdlSpec->id] = $pTmpMdlSpec;
                                }
                            }
                        }
                        else {
                            $pTmpMdlResult = array();
                        }
                        if (isset($_SESSION[$cCode]['main'][$valueSrc]) && $_SESSION[$cCode]['main'][$valueSrc] > 0) {
                            if (isset($externSrc['extern_label2'])) {
                                //cek ada isinya atau kosong
                                $cek = strlen($_SESSION[$cCode]['main'][$externSrc['extern_label2']]) > 4 ? "" : matiHere("jenis biaya tidak dikenali " . __LINE__);//
                            }
                            //region cek duplikasi paymentsource
                            $tr->setFilters(array());
                            $tr->addFilter("transaksi_id='$insertID'");
                            $tr->addFilter("target_jenis='" . $paymentSrcConfig['jenisTarget'] . "'");
                            $validateIsInserted = $tr->lookUpAllPaymentSrc()->result();
                            showLast_query("biru");
                            if (sizeof($validateIsInserted) > 0) {
                                matiHEre("Gagal menulis transaksi. Silahkan relogin untuk membersihkan sesi demi menghindari duplikasi data, dan coba kembali transaksi yang gagal");
                            }
                            //endregion


                            cekHitam("valuelabel: $valueLabel, valueSrc: $valueSrc");
                            $this->load->helper("he_payment_source");

                            $arrPymSrc = array(
                                "jenis" => $stepCode,
                                "target_jenis" => $paymentSrcConfig['jenisTarget'],
                                "reference_jenis" => $paymentSrcConfig['jenisSrc'],
                                "extern_id" => isset($_SESSION[$cCode]['main'][$externSrc['id']]) ? $_SESSION[$cCode]['main'][$externSrc['id']] : "",
                                "extern_nama" => isset($_SESSION[$cCode]['main'][$externSrc['nama']]) ? $_SESSION[$cCode]['main'][$externSrc['nama']] : "",
                                "nomer" => $tmpNomorNota2,
                                "label" => $paymentSrcConfig['label'],

                                "tagihan" => $_SESSION[$cCode]['main'][$valueSrc],
                                "terbayar" => 0,
                                "sisa" => $_SESSION[$cCode]['main'][$valueSrc],

//                                "cabang_id" => $_SESSION[$cCode]['main']['placeID'],
//                                "cabang_nama" => $_SESSION[$cCode]['main']['placeName'],
                                "cabang_id" => isset($_SESSION[$cCode]['main'][$externSrc['cabang_id']]) ? $_SESSION[$cCode]['main'][$externSrc['cabang_id']] : $_SESSION[$cCode]['main']['placeID'],
                                "cabang_nama" => isset($_SESSION[$cCode]['main'][$externSrc['cabangnama']]) ? $_SESSION[$cCode]['main'][$externSrc['cabang_nama']] : $_SESSION[$cCode]['main']['placeName'],
                                "oleh_id" => $pelaku_transaksi_id,
                                "oleh_nama" => $pelaku_transaksi_nama,
                                "dtime" => $dtime,
                                "fulldate" => $fulldate,
                                "valas_id" => isset($externSrc['valasId']) && isset($_SESSION[$cCode]['main'][$externSrc['valasId']]) ? $_SESSION[$cCode]['main'][$externSrc['valasId']] : '',
                                "valas_nama" => isset($externSrc['valasLabel']) && isset($_SESSION[$cCode]['main'][$externSrc['valasLabel']]) ? $_SESSION[$cCode]['main'][$externSrc['valasLabel']] : '',
                                "valas_nilai" => isset($externSrc['valasValue']) && isset($_SESSION[$cCode]['main'][$externSrc['valasValue']]) ? $_SESSION[$cCode]['main'][$externSrc['valasValue']] : '',

                                "tagihan_valas" => isset($externSrc['valasTagihan']) && isset($_SESSION[$cCode]['main'][$externSrc['valasTagihan']]) ? $_SESSION[$cCode]['main'][$externSrc['valasTagihan']] : '',
                                "terbayar_valas" => 0,
                                "sisa_valas" => isset($externSrc['valasSisa']) && isset($_SESSION[$cCode]['main'][$externSrc['valasSisa']]) ? $_SESSION[$cCode]['main'][$externSrc['valasSisa']] : '',

                                //                            "extern_label2" => isset($_SESSION[$cCode]['main']['pihakMainName']) ? $_SESSION[$cCode]['main']['pihakMainName'] : "",
                                "extern_label2" => (isset($externSrc['extern_label2']) && ($_SESSION[$cCode]['main'][$externSrc['extern_label2']])) ? $_SESSION[$cCode]['main'][$externSrc['extern_label2']] : "",

                                "dpp_ppn" => (isset($externSrc['dpp_ppn']) && ($_SESSION[$cCode]['main'][$externSrc['dpp_ppn']])) ? $_SESSION[$cCode]['main'][$externSrc['dpp_ppn']] : 0,
                                "ppn" => (isset($externSrc['ppn']) && ($_SESSION[$cCode]['main'][$externSrc['ppn']])) ? $_SESSION[$cCode]['main'][$externSrc['ppn']] : 0,
                                "ppn_approved" => (isset($externSrc['ppn_approved']) && ($_SESSION[$cCode]['main'][$externSrc['ppn_approved']])) ? $_SESSION[$cCode]['main'][$externSrc['ppn_approved']] : 0,
                                "ppn_sisa" => (isset($externSrc['ppn']) && ($_SESSION[$cCode]['main'][$externSrc['ppn']])) ? $_SESSION[$cCode]['main'][$externSrc['ppn']] : "",
                                "ppn_status" => (isset($externSrc['ppn_status'])) ? $externSrc['ppn_status'] : 0,
                                "extern_nilai2" => (isset($externSrc['extern_nilai2']) && ($_SESSION[$cCode]['main'][$externSrc['extern_nilai2']])) ? $_SESSION[$cCode]['main'][$externSrc['extern_nilai2']] : 0,
                                "extern_date2" => (isset($externSrc['extern_date2']) && ($_SESSION[$cCode]['main'][$externSrc['extern_date2']])) ? $_SESSION[$cCode]['main'][$externSrc['extern_date2']] : "",
                                "pph_23" => (isset($externSrc['pph_23']) && ($_SESSION[$cCode]['main'][$externSrc['pph_23']])) ? $_SESSION[$cCode]['main'][$externSrc['pph_23']] : "",

                                "npwp" => (isset($externSrc['npwp']) && ($_SESSION[$cCode]['main'][$externSrc['npwp']])) ? $_SESSION[$cCode]['main'][$externSrc['npwp']] : "",
//                                "extern2_id" => (isset($externSrc['extern2_id']) && ($_SESSION[$cCode]['main'][$externSrc['extern2_id']])) ? $_SESSION[$cCode]['main'][$externSrc['extern2_id']] : "",
//                                "extern2_nama" => (isset($externSrc['extern2_nama']) && ($_SESSION[$cCode]['main'][$externSrc['extern2_nama']])) ? $_SESSION[$cCode]['main'][$externSrc['extern2_nama']] : "",
                                "ppn_pph_faktor" => (isset($externSrc['ppn_pph_faktor']) && ($_SESSION[$cCode]['main'][$externSrc['ppn_pph_faktor']])) ? $_SESSION[$cCode]['main'][$externSrc['ppn_pph_faktor']] : "",
                                "extern_jenis" => (isset($externSrc['extern_jenis']) && ($_SESSION[$cCode]['main'][$externSrc['extern_jenis']])) ? $_SESSION[$cCode]['main'][$externSrc['extern_jenis']] : "",
                                "extern_nilai3" => (isset($externSrc['extern_nilai3']) && ($_SESSION[$cCode]['main'][$externSrc['extern_nilai3']])) ? $_SESSION[$cCode]['main'][$externSrc['extern_nilai3']] : "",
                                "extern_nilai4" => (isset($externSrc['extern_nilai4']) && ($_SESSION[$cCode]['main'][$externSrc['extern_nilai4']])) ? $_SESSION[$cCode]['main'][$externSrc['extern_nilai4']] : "",
                                "npwp" => (isset($externSrc['npwp']) && ($_SESSION[$cCode]['main'][$externSrc['npwp']])) ? $_SESSION[$cCode]['main'][$externSrc['npwp']] : "",
                                //                            "extern_nilai2" => (isset($externSrc['extern_nilai2']) && ($_SESSION[$cCode]['main'][$externSrc['extern_nilai2']])) ? $_SESSION[$cCode]['main'][$externSrc['extern_nilai2']] : "",
                                "payment_locked" => (isset($externSrc['payment_locked']) && ($_SESSION[$cCode]['main'][$externSrc['payment_locked']])) ? $_SESSION[$cCode]['main'][$externSrc['payment_locked']] : 0,
                                "cash_account" => (isset($externSrc['cash_account']) && ($_SESSION[$cCode]['main'][$externSrc['cash_account']])) ? $_SESSION[$cCode]['main'][$externSrc['cash_account']] : 0,
                                "cash_account_nama" => (isset($externSrc['cash_account_nama']) && ($_SESSION[$cCode]['main'][$externSrc['cash_account_nama']])) ? $_SESSION[$cCode]['main'][$externSrc['cash_account_nama']] : 0,

                                "extern2_id" => (isset($externSrc['extern2_id']) && ($_SESSION[$cCode]['main'][$externSrc['extern2_id']])) ? $_SESSION[$cCode]['main'][$externSrc['extern2_id']] : $externSrc['extern2_id'],
                                "extern2_nama" => (isset($externSrc['extern2_nama']) && ($_SESSION[$cCode]['main'][$externSrc['extern2_nama']])) ? $_SESSION[$cCode]['main'][$externSrc['extern2_nama']] : $externSrc['extern2_nama'],

                                "extern3_id" => isset($_SESSION[$cCode]['main'][$externSrc['extern3_id']]) ? $_SESSION[$cCode]['main'][$externSrc['extern3_id']] : "",
                                "extern3_nama" => isset($_SESSION[$cCode]['main'][$externSrc['extern3_nama']]) ? $_SESSION[$cCode]['main'][$externSrc['extern3_nama']] : "",
                                "extern4_id" => isset($_SESSION[$cCode]['main'][$externSrc['extern4_id']]) ? $_SESSION[$cCode]['main'][$externSrc['extern4_id']] : "",
                                "extern4_nama" => isset($_SESSION[$cCode]['main'][$externSrc['extern4_nama']]) ? $_SESSION[$cCode]['main'][$externSrc['extern4_nama']] : "",
                                "extern5_id" => isset($_SESSION[$cCode]['main'][$externSrc['extern5_id']]) ? $_SESSION[$cCode]['main'][$externSrc['extern5_id']] : "",
                                "extern5_nama" => isset($_SESSION[$cCode]['main'][$externSrc['extern5_nama']]) ? $_SESSION[$cCode]['main'][$externSrc['extern5_nama']] : "",
                                //----
                                "biaya_rekening" => makeValue($externSrc['biaya_rekening'], $_SESSION[$cCode]['main'], $_SESSION[$cCode]['main'], 0),
                                "biaya_rekening_label" => makeValue($externSrc['biaya_rekening_label'], $_SESSION[$cCode]['main'], $_SESSION[$cCode]['main'], 0),
                                "biaya_rekening_id" => makeValue($externSrc['biaya_rekening_id'], $_SESSION[$cCode]['main'], $_SESSION[$cCode]['main'], 0),
                                "biaya_rekening_id_label" => makeValue($externSrc['biaya_rekening_id_label'], $_SESSION[$cCode]['main'], $_SESSION[$cCode]['main'], 0),
                                "biaya_rekening2_id" => makeValue($externSrc['biaya_rekening2_id'], $_SESSION[$cCode]['main'], $_SESSION[$cCode]['main'], 0),
                                "biaya_rekening2_id_label" => makeValue($externSrc['biaya_rekening2_id_label'], $_SESSION[$cCode]['main'], $_SESSION[$cCode]['main'], 0),
                                "cabang2_id" => makeValue($externSrc['cabang2_id'], $_SESSION[$cCode]['main'], $_SESSION[$cCode]['main'], 0),
                                "cabang2_nama" => makeValue($externSrc['cabang2_nama'], $_SESSION[$cCode]['main'], $_SESSION[$cCode]['main'], 0),
                            );
                            arrPrintCyan($arrPymSrc);
                            $tr->writePaymentSrc($insertID, $arrPymSrc);
                            cekMerah($this->db->last_query());

                        }
                    }
                }
            }
            else {
                cekMerah("TIDAK nulis paymentSrc");
            }

            //endregion


            $stopDate = dtimeNow();
//            mati_disini(__LINE__ . " || STOP CEK DULU...");

            cekHitam("--- MULAI VALIDATOR ---");
            $this->load->library("Validator");
            $vdt = New Validator();

            // validasi lajur DC/PUSAT
            validateAllBalances("-1");

            // validasi lajur CABANG
            validateAllBalances("1");

//            mati_disini("...cek MANUAL cli transaksi... rekening pembantu masuk disini (component detail)<br>start: $startDate<br>stop: $stopDate<br>butuh waktu: " . timeDiff($startDate, $stopDate));


            cekHijau("...tes cli transaksi... rekening pembantu masuk disini (component detail)<br>start: $startDate<br>stop: $stopDate<br>butuh waktu: " . timeDiff($startDate, $stopDate));
//            mati_disini("...tes cli transaksi... rekening pembantu masuk disini (component detail)<br>start: $startDate<br>stop: $stopDate<br>butuh waktu: " . timeDiff($startDate, $stopDate));


            $this->db->trans_complete() or mati_disini("Gagal saat berusaha  commit transaction!");

            cekHijau("<h3> [$getTrID] SELESAI... </h3>");
        }
        else {
            $stopDate = dtimeNow();
            cekMerah(":: TIDAK ADA yang perlu di-CLI-kan ::
                    <br>start: $startDate<br>stop: $stopDate<br>butuh waktu: " . timeDiff($startDate, $stopDate));
        }

    }


    public function cek_cek()
    {
        $tbl_1 = "neraca";
        $tbl_2 = "_rek_master_cache";
        $periode = "bulanan";
        $thn = "2026";
        $bln = "04";
        $bln_cache = "05";
        $cabang_id = "34";


        $this->db->trans_start();


        // region cache---------------------
        $arrFilter = array(
            "periode" => $periode,
            "thn" => $thn,
            "bln" => $bln_cache,
            "cabang_id" => $cabang_id,
//            "status" => 1,
//            "trash" => 0,
        );
        $this->db->where($arrFilter);
        $query1 = $this->db->get($tbl_2)->result();
        foreach ($query1 as $spec) {
            $rekeningCache[$spec->rekening] = $spec;
            $rekenings[$spec->rekening] = $spec->rekening;
        }
        showLast_query("kuning");
        //endregion cache---------------------

        // region neraca
        $arrFilter = array(
            "periode" => $periode,
            "thn" => $thn,
            "bln" => $bln,
            "cabang_id" => $cabang_id,
            "status" => 1,
            "trash" => 0,
        );
        $this->db->where($arrFilter);
        $query0 = $this->db->get($tbl_1)->result();
        showLast_query("biru");
        foreach ($query0 as $spec) {
            $rekenings[$spec->rekening] = $spec->rekening;
            if (!array_key_exists($spec->rekening, $rekeningCache)) {
                $debet = $spec->debet;
                $kredit = $spec->kredit;
                $data = array(
                    'debet' => $spec->debet,
                    'kredit' => $spec->kredit,
                    'periode' => $periode,
                    'rekening' => $spec->rekening,
                    'cabang_id' => $spec->cabang_id,
                    'bln' => $bln_cache,
                    'thn' => $thn,
                );
                $this->db->insert($tbl_2, $data);
                showLast_query("hijau");
            }
        }
        // endregion neraca

        //region cache---------------------
        $debet_total = 0;
        $kredit_total = 0;
        $arrFilter = array(
            "periode" => $periode,
            "thn" => $thn,
            "bln" => $bln_cache,
            "cabang_id" => $cabang_id,
//            "status" => 1,
//            "trash" => 0,
        );
        $this->db->where($arrFilter);
        $query1 = $this->db->get($tbl_2)->result();
        foreach ($query1 as $spec) {
            $debet = $spec->debet;
            $kredit = $spec->kredit;

            $debet_total += $spec->debet;
            $kredit_total += $spec->kredit;
        }
        $selisih = $debet_total - $kredit_total;
        cekHitam("debet: $debet_total");
        cekHitam("kredit: $kredit_total");
        cekHitam("selisih: $selisih");
        showLast_query("kuning");
        //endregion cache


        mati_disini(__LINE__);
        $this->db->trans_complete() or mati_disini("Gagal saat berusaha  commit transaction!");

        cekHijau("<h3> [$getTrID] SELESAI... </h3>");
    }

    public function cekUMProject()
    {
        $this->load->model("MdlTransaksi");
        $jenis = "4467";
        $trIDs = array();


        $tr = New MdlTransaksi();
        $tr->addFilter("jenis='$jenis'");
        $trTmp = $tr->lookupAll()->result();
        if (sizeof($trTmp) > 0) {
            foreach ($trTmp as $iSpec) {
                $trIDs[$iSpec->id] = $iSpec->id;
            }

            $tr = New MdlTransaksi();
            $tr->setFilters(array());
            $tr->setJointSelectFields("transaksi_id, main");
            $tr->addFilter("transaksi_id in ('" . implode("','", $trIDs) . "')");
            $trReg = $tr->lookupDataRegistries()->result();
            showLast_query("biru");
//            arrPrint($trReg);
            foreach ($trReg as $regSpec) {
                $trid = $regSpec->transaksi_id;
                $main = blobDecode($regSpec->main);
                $arrDataUpdate[$trid] = array(
                    "ppn_nilai" => $main["ppn"],
                    "transaksi_net" => $main["dpp_ppn"],
                    "project_id" => $main["referensi_so_project"],
                    "project_nama" => $main["referensi_so_project__nama"],
                    "reference_jenis" => $main["referensi_um__ref_jenis"],
                    "reference_id" => $main["referensi_so__id"],
                    "reference_nomer" => $main["referensi_so__nomer"],
                );

            }
//            arrPrintWebs($arrDataUpdate);


        }


        $this->db->trans_start();


        if (sizeof($arrDataUpdate) > 0) {
            foreach ($arrDataUpdate as $trid => $data) {
                $tr = New MdlTransaksi();
                $tr->setFilters(array());
                $where = array(
                    "id" => $trid,
                );
                $tr->updateData($where, $data);
                showLast_query("orange");
            }
        }


        mati_disini("...tes cli transaksi... rekening pembantu masuk disini (component detail)");


        $this->db->trans_complete() or mati_disini("Gagal saat berusaha  commit transaction!");

        cekHijau("<h3> [$getTrID] SELESAI... </h3>");
    }

    public function genRekeningUMProject()
    {
        $this->load->model("Coms/ComRekeningPembantuCustomerProject");
        $this->load->model("MdlTransaksi");
        $rek = "2010050";
        $rek_sub = "2010050060";

        $pymSrc = New MdlTransaksi();
        $pymSrc->setFilters(array());
        $pymSrc->addFilter("sisa>10");
        $pymSrc->addFilter("label='uang muka konsumen'");
        $pymSrc->addFilter("jenis='4467'");
        $pymSrc->addFilter("project_id>'0'");
        $pymSrcTmp = $pymSrc->lookUpAllPaymentSrc()->result();
        showLast_query("biru");
        cekBiru(count($pymSrcTmp));

        $this->db->trans_start();

        if (sizeof($pymSrcTmp) > 0) {
            foreach ($pymSrcTmp as $ii => $spec) {
                // cek dulu, jika belum ada maka buatkan
                $crpp = New ComRekeningPembantuCustomerProject();
                $crpp->addFilter("extern2_id=" . $spec->project_id);
                $crpp->addFilter("extern_id=" . $spec->extern_id);
                $crpp->addFilter("cabang_id=" . $spec->cabang_id);
                $tmp = $crpp->fetchBalances($rek);
                showLast_query("kuning");
                if (sizeof($tmp) == 0) {
                    $data[$ii] = array(
                        "loop" => array(
                            "2010050" => $spec->sisa,// hutang ke konsumen
                        ),
                        "static" => array(
                            "cabang_id" => $spec->cabang_id,
                            "extern_id" => $spec->extern_id,
                            "extern_nama" => $spec->extern_nama,
                            "extern2_id" => $spec->project_id,// projectid
                            "extern2_nama" => $spec->project_nama,// projectnama
                            "jenis" => $spec->jenis,
                            "transaksi_no" => $spec->nomer,
                            "transaksi_id" => $spec->transaksi_id,
                            "dtime" => $spec->dtime,
                            "fulldate" => $spec->fulldate,
                            "oleh_id" => $spec->oleh_id,
                            "oleh_nama" => $spec->oleh_nama,
                        ),
                    );
                }
            }
            cekBiru(count($data));
//            mati_disini(__LINE__);
//            arrPrintCyan($data);
            if (sizeof($data) > 0) {
                foreach ($data as $dataspec) {
                    $crpp = New ComRekeningPembantuCustomerProject();
                    $crpp->pair($dataspec);
                    $crpp->exec();

                }
            }
        }


        mati_disini("...tes cli transaksi... rekening pembantu masuk disini (component detail)");

        $this->db->trans_complete() or mati_disini("Gagal saat berusaha  commit transaction!");

        cekHijau("<h3> [$getTrID] SELESAI... </h3>");
    }

}


