<?php

/**
 * Created by PhpStorm.
 * User: aziz
 * Date: 6/2/2019
 * Time: 7:54 PM
 */
class StockLocker extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
    }
    function viewCurrentLockers(){
        $cabID=$this->session->login['cabang_id'];
        $whID=$this->session->login['gudang_id'];
        $this->load->model("Mdls/MdlLockerStock");
        $q=isset($_GET['q'])&&strlen($_GET['q'])?$_GET['q']:"";
        $lo=new MdlLockerStock();
        $lo->addFilter("cabang_id='$cabID'");
        $lo->addFilter("gudang_id='$whID'");
        $this->db->where("transaksi_id='0'");
        $this->db->group_start();
            $this->db->where("state='active'");
            $this->db->or_where("state='hold'");
        $this->db->group_end();

        if(strlen($q)>0){
            $tmp=$lo->lookupByKeyword($q)->result();
        }else{
            $tmp=$lo->lookupAll()->result();
        }


//        cekbiru($this->db->last_query());
        $items=array();
        $states=array(
            "active",
            "hold",
        );
        $stocks=array();
        $stockItems=array();
        if(sizeof($tmp)>0){
            foreach($tmp as $row){

                if(!array_key_exists($row->produk_id,$items)){
                    $items[$row->produk_id]=$row->nama;
                }
                if(!isset($stocks[$row->produk_id])){
                    $stocks[$row->produk_id]=array();
                }
                if(!isset($stocks[$row->produk_id][$row->state])){
                    $stocks[$row->produk_id][$row->state]=0;
                }
                $stocks[$row->produk_id][$row->state]+=$row->jumlah;
            }
        }

//        arrprint($stocks);

        $headerFields=array();
        if(sizeof($items)>0){
            $tmpItem=array();
            $headerFields=array("name"=>"item name");
            foreach($items as $iID=>$iName){
                $tmpItem['id']=$iID;
                $tmpItem['name']=$iName;
                foreach($states as $stName){
                    $tmpItem[$stName]=isset($stocks[$iID][$stName])?$stocks[$iID][$stName]:0;
                    $headerFields[$stName]=$stName;
                }
                $tmpItem['link']="";
                $stockItems[]=$tmpItem;
            }
        }


//        $data=array(
//            "states"=>$states,
//            "items"=>$items,
//            "stocks"=>$stocks,
//        );
        $data = array(
            "mode"         => "saldo",
            "title"        => "active stocks",
            "subTitle"     => "current stocks".(strlen($q)>0?" matched '$q'":""),
            "items"        => $stockItems,
            //            "headerFields" => $balConfig['viewedColumns'],
            "headerFields" => $headerFields,
            "thisPage"=>base_url().get_class($this)."/".$this->uri->segment(2),
            "thisURL"=>base_url().get_class($this)."/".$this->uri->segment(2)."?",
            "q"=>$q,

            //            "inspectTarget_mutasi" => base_url() . "Ledger/viewMoves_l2/$relName/$rekName/",


        );
        $this->load->view("ledger", $data);

    }
}