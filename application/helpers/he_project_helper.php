<?php

//by: chepy
//date: 10 Sept 2025

function generateShowButton($fase_urut, $prodID, $enabled) {
    $btnJs = "
        var tmpIdForm=top.$('#komposisi_fase_biaya_tambahan_{$fase_urut}{$prodID}');
        var idform=$(tmpIdForm).attr('idform');
        $(tmpIdForm).attr('onclick','document.getElementById(\"'+idform+'\").submit();');
        $(tmpIdForm).prop('disabled', " . ($enabled ? "false" : "true") . ");
        $(tmpIdForm)." . ($enabled ? "addClass('btn-success');" : "removeClass('btn-success');");

    return $btnJs;
}

// Helper untuk hitung subtotal
function getSubtotal($fase_urut, $prodID, $jml, $harga, $value = null) {
    if (isset($_SESSION["NEW_TAMBAHAN"]["komposisi_fase_biaya_tambahan"][$fase_urut][$prodID]["subtotal"])) {
        return round($_SESSION["NEW_TAMBAHAN"]["komposisi_fase_biaya_tambahan"][$fase_urut][$prodID]["subtotal"]);
    }
    if ($value !== null) {
        return round($_SESSION["NEW_TAMBAHAN"]["komposisi_fase_biaya_tambahan"][$fase_urut][$prodID]["jml"] * $value);
    }
    return round($jml * $harga);
}

// Helper untuk cetak output
function printOutputJs($fase_urut, $extraJs, $subTotal, $showButton) {
    $outputJs = base64_encode("
        top.$('#komposisi_fase_biaya_tambahan_{$fase_urut}subtotal').html(addCommas($subTotal));
        $extraJs
        $showButton
    ");
    echo $outputJs;
}



?>