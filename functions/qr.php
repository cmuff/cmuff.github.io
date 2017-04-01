<!–– assumes entry from website folder through functions folder-->
<script type="text/javascript" src="functions/qrcodejs/qrcode.min.js"></script>
<?php
/*
 * generates the function call needed to generate a qr code
 * uses the qrcodejs/qrcode.min.js file
 */
function genQR($id, $text, $width = 128, $height = 128,
               $colorDark = "#000000", $colorLight = "#ffffff", $correctLevel = "QRCode.CorrectLevel.H") {
	$QR='<script> 
		new QRCode(document.getElementById("' . $id . '"), {
            text: "' . $text . '",
            width: ' . $width . ',
            height: ' . $height . ',
            colorDark : "' . $colorDark . '",
            colorLight : "' . $colorLight . '",
            correctLevel : ' . $correctLevel . '
        });
		</script>';
	return $QR;
}
?>