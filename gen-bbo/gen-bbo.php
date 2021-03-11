<?php

$contents = file_get_contents("php://input");
$lines = preg_split("/\r\n|\n|\r/", $contents);
$header = str_getcsv(array_shift($lines));
$width = count($header);
$wanted = ["BBO username", "First name", "Last name", "National number"];
$status = "Left club reason";
foreach ($wanted as $fname) {
    $header_pos[] = array_search($fname, $header);
}
$status_pos = array_search($status, $header);

$ev["bt"] = '#names,,,' . "\n";

$incl = 0;
$excl = 0;
$mt = 0;
foreach ($lines as $line) {
    $csv = str_getcsv($line);
    if (count($csv) != $width) {
        $mt++;
    } else if (($csv[$status_pos] == "Passed away") || ($csv[$header_pos[3]] == 88888888)) {
        $excl++;
    } else {
        $ev["bt"] .= ($csv[$header_pos[0]] . "," . $csv[$header_pos[1]] . "," . $csv[$header_pos[2]] . "," . $csv[$header_pos[3]] . "\n");
        $incl ++;
    }
}

$ev["warnings"][] = strval($excl) . " entries were excluded";
$ev["warnings"][] = strval($incl) . " entries were included";
$ev["warnings"][] = strval($mt) . " lines skipped";
echo json_encode($ev);
return;

