<?php
$xml = new XMLReader();
$xml->open("php://input");
#$xml->open("test.xml");
$in_player = false;

while ($xml -> read()) {
    if ($xml->name == "PLAYER") {
        if ($xml->nodeType == XMLReader::ELEMENT) {
            $in_player = true;
            $player_name = "";
            $ebu_num = "";
        } elseif ($xml->nodeType == XMLReader::END_ELEMENT) {
            $in_player = false;
            if ("" == $ebu_num) $ev['issues'][] = $player_name . " has no ebu number";
        }
    } elseif ($xml->name == "PLAYER_NAME" AND $in_player AND $xml->nodeType == XMLReader::ELEMENT ) {
        $player_name = $xml->readString();
    } elseif  ($xml->name == "NATIONAL_ID_NUMBER" AND $in_player AND $xml->nodeType == XMLReader::ELEMENT) {
        $ebu_num = $xml->readString();
    } elseif ($xml->name == "EVENT_DESCRIPTION" AND $xml->nodeType == XMLReader::ELEMENT ) {
        $ev['name'] = $xml->readString();
    } elseif ($xml->name == "DATE" AND $xml->nodeType == XMLReader::ELEMENT ) {
        $ev['date'] = $xml->readString();
    }
}
echo json_encode($ev);
         
