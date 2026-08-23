<?php

/**
 * Created by PhpStorm.
 * User: thomas
 * Date: 07/08/2018
 * Time: 21.34
 */
class MdlSetting extends MdlMother
{
    protected $tableName = "settings";

    public function __construct()
    {
        parent::__construct();
    }

    public function settingInv(){

    }

    public function callInvoicing(){
        $condites = array(
            "jenis" => "onoff",
            "untuk" => "history_invoice",
        );
        $existingData  = $this->lookupByCondition($condites)->row();
        showLast_query("hijau");

        return $existingData;
    }

    public function updateInvoicing(){

        $existingData =  $this->callInvoicing();

        if($existingData){
            $nilai = $existingData->nilai;
            $nilai_new = $nilai == 0 ? 1 : 0;

            $updCondites = array(
                "id" => $existingData->id,
            );
            $updDatas = array(
                "nilai" => $nilai_new,
                "oleh_nama" => my_name()
            );


            $this->updateData($updCondites,$updDatas);
            showLast_query("merah");
        }
        else{
            $condites = array(
                "jenis" => "onoff",
                "untuk" => "history_invoice",
            );
            $datas = $condites;
            $datas['nilai'] = 1;
            $datas['status'] = 1;
            $this->addData($datas);
            showLast_query("hijau");
        }

        return $nilai_new;
    }
}