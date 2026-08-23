<?php
/**
 * [AGENT_LOG]
 * ROLE      : Software Engineer Agent
 * PURPOSE   : Konfigurasi Core untuk modul Amandemen Invoice (370)
 * COMPLIANCE: ISO 9001 (Audit Trail)
 * LOG_EXPIRE: 2026-11-07
 * [/AGENT_LOG]
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$config["coTransaksiCore"] = array(
    "4822a" => array(
        "counters" => array(
            "stepCode|placeID",
            "stepCode|olehID",
            "stepCode|placeID|olehID",
            "stepCode|placeID|olehID|customerID",
            "stepCode|customerID",
            "stepCode|placeID|customerID",
            "stepCode|olehID|customerID",
        ),
        "formatNota" => "stepCode|placeID|customerID",
        "valueGates" => array(
            "master" => array(),
            "detail" => array(
                "qty" => "jml",
                "harga" => "harga",
            )
        )
    )
);
