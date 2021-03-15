<?php

$contents = file_get_contents("php://input");
$lines = preg_split("/\r\n|\n|\r/", $contents);
$header = str_getcsv(array_shift($lines));
$width = count($header);
$ebu_num = "National number";
$last_name ="Last name";
$wanted = ["BBO username", "First name", $last_name, $ebu_num];
$status = "Left club reason";
foreach ($wanted as $fname) {
    $header_pos[] = array_search($fname, $header);
}
$status_pos = array_search($status, $header);
$ebu_pos = array_search($ebu_num,$header);
$last_pos = array_search($last_name, $header);

$ev["bt"] = '#names,,,' . "\n";

$incl = 0;
$excl = 0;
$mt = 0;
foreach ($lines as $line) {
    $csv = str_getcsv($line);
    if (count($csv) != $width) {
        $mt++;
    } else if (($csv[$status_pos] == "Passed away") || ($csv[$ebu_pos] == 88888888) || ! $csv[$ebu_pos] || ! $csv[$last_pos]) {
        $excl++;
    } else {
        $ev["bt"] .= ($csv[$header_pos[0]] . "," . $csv[$header_pos[1]] . "," . $csv[$last_pos] . "," . $csv[$ebu_pos] . "\n");
        $incl ++;
    }
}

$ev["warnings"][] = strval($excl) . " entries were excluded";
$ev["warnings"][] = strval($incl) . " entries were included";
$ev["warnings"][] = strval($mt) . " lines skipped";
echo json_encode($ev);
return;

