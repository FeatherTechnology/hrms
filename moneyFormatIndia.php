<?php
//Format number in Indian Format
function moneyFormatIndia($num1)
{
    if ($num1 === '' || $num1 === null) {
        return '';
    }

    $negative = false;

    if ($num1 < 0) {
        $negative = true;
        $num1 = abs($num1);
    }

    // split decimal part
    $parts = explode('.', $num1);
    $int = $parts[0];
    $decimal = isset($parts[1]) ? '.' . $parts[1] : '';

    if (strlen($int) > 3) {

        $lastthree = substr($int, -3);
        $restunits = substr($int, 0, -3);

        $restunits = (strlen($restunits) % 2 == 1)
            ? "0" . $restunits
            : $restunits;

        $expunit = str_split($restunits, 2);
        $explrestunits = "";

        for ($i = 0; $i < sizeof($expunit); $i++) {
            if ($i == 0) {
                $explrestunits .= (int)$expunit[$i] . ",";
            } else {
                $explrestunits .= $expunit[$i] . ",";
            }
        }

        $int = $explrestunits . $lastthree;
    }

    $result = $int . $decimal;

    if ($negative) {
        $result = "-" . $result;
    }

    return $result;
}
?>
