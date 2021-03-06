<?php

function endsWithIgnoreCase( $haystack, $needle ) {
    $length = strlen( $needle );
    if( !$length ) {
        return true;
    }
    return substr( strtoupper($haystack), -$length ) === strtoupper($needle);
}

$arr = preg_split("/\r\n|\n|\r/", $_POST["names"]);

foreach ($arr as $value) {
    $eles = explode(",",$value);
    $given = trim($eles[1]);
    $surname = trim($eles[2]);
    $ebu = trim($eles[3]);
    $surnames[$ebu] = $surname;
    $names[$ebu] = $given . " " . $surname;
}
$xml = new XMLReader();
$xml->XML($_POST["xml"]);

$in_player = false;
$ebus = [];
while ($xml -> read()) {
    if ($xml->name == "PLAYER") {
        if ($xml->nodeType == XMLReader::ELEMENT) {
            $in_player = true;
            $player_name = "";
            $ebu_num = "";
        } elseif ($xml->nodeType == XMLReader::END_ELEMENT) {
            $in_player = false;
            if ("" == $ebu_num) $ev['issues'][] = $player_name . " has no ebu number";
            elseif (array_key_exists($ebu_num,$ebus)) $ev['issues'][] = "Checking " . $player_name . " finds ebu number " . $ebu_num . " already in use by " . $ebus[$ebu_num];
            elseif (! array_key_exists($ebu_num,$surnames) || ! endsWithIgnoreCase($player_name, $surnames[$ebu_num])) $ev['issues'][] = $player_name . " does not have ebu number " . $ebu_num;
            elseif ($names[$ebu_num] !== $player_name) $ev["warnings"][] = $player_name . " is different from Pianola's " . $names[$ebu_num];
            $ebus[$ebu_num] = $player_name;
        }
    } elseif ($xml->name == "PLAYER_NAME" AND $in_player AND $xml->nodeType == XMLReader::ELEMENT ) {
        $player_name = $xml->readString();
    } elseif  ($xml->name == "NATIONAL_ID_NUMBER" AND $in_player AND $xml->nodeType == XMLReader::ELEMENT) {
        $ebu_num = $xml->readString();
    } elseif ($xml->name == "EVENT_DESCRIPTION" AND $xml->nodeType == XMLReader::ELEMENT ) {
        $ev['name'] = $xml->readString();
    } elseif ($xml->name == "DATE" AND $xml->nodeType == XMLReader::ELEMENT ) {
        $ev['date'] = $xml->readString();
    } elseif ($xml->name == "P2P_CHARGE_RATE" AND $xml->nodeType == XMLReader::ELEMENT ) {
        $ev['ums'] = $xml->readString();
    }
}
echo json_encode($ev);
