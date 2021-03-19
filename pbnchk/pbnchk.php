<?php

$lines = file_get_contents("php://input");
$lines =preg_split("/\r\n|\n|\r/", $lines);
$blanklinepat = "/^\s*$/";
$normalpat = '/^\s*\[\s*(\w*)\s*"(.*)"\w*]\w*$/';
$good_denom ="AKQJT98765432";
$blank_line_last = false;
foreach ($lines as $line) {
    if (preg_match($blanklinepat,$line)) {
        if ($blank_line_last) {
            continue;
        }
        $blank_line_last = true;
        $fail = false;
        if (!isset($board)) {
            $ev['line'][] = [1,"'Board' is not defined"];
            $fail = true;
        }
        if (!isset($dealer)) {
            $ev['line'][] = [1,"'Dealer' is not defined"];
            $fail = true;
        }
        if (!isset($vulnerable)) {
            $ev['line'][] = [1,"'Vulnerable' is not defined"];
            $fail = true;
        }
        if (!isset($deal)) {
            $ev['line'][] = [1,"'Deal' is not defined"];
            $fail = true;
        }
        if (!$fail) {
            $ev['line'][] = [0,"Board " . $board];
            $deck = null;
            if (strlen($deal) < 10) {
                $ev['line'][] = [1,"Deal line is too short: " . $deal];
            } else if (strpos("NESW",$deal[0]) === false) {
                $ev['line'][] = [1,"Deal value must start with N, S, E or W"];
            } else if ($deal[1] != ":") {
                $ev['line'][] = [1,"Second character of deal value must be ':'"];
            } else {
                $hands = explode(' ', substr($deal,2));
                if (count($hands) != 4) {
                    $ev['line'][] = [1,"Each deal must have four space separated hands"];
                } else {
                    foreach ($hands as $hand) {
                        $suits = explode('.', $hand);
                        if (count($suits) != 4) {
                            $ev['line'][] = [1,"Each hand must have four dot separated suits"]; 
                        }
                        $n = 0;
                        foreach ($suits as $suit) {
                            if ($suit) {
                                $suitname = "SHDC"[$n];
                                foreach (str_split($suit) as $card) {
                                    if (strpos($good_denom, $card) === false) {
                                        $ev['line'][] = [1, $card . " is not a valid denomination"];
                                    } else {
                                        if (isset($deck[$card][$suitname])) {
                                            $ev['line'][] = [1,"Duplicate " . $card . $suitname];
                                        }
                                        $deck[$card][$suitname] = true;
                                    }
                                }
                            }
                            $n++;
                        }
                    }
                }  
            }
            foreach (str_split("SHDC") as $suitname) {
                foreach (str_split($good_denom) as $card) {
                    if (!  isset($deck[$card][$suitname])) {
                        $ev['line'][] = [1,"Missing " . $card . $suitname];
                    }
                }
            }
        }
        $board = null;
        $dealer = null;
        $vulnerable = null;
        $deal = null;
    } else if ($line[0] === "%") {
        $blank_line_last = false;
        continue;
    } else if (preg_match($normalpat,$line,$matches)) {
        if ($matches[1] == "Board") {
            $board = $matches[2];
        } else if ($matches[1] == "Dealer") {
            $dealer = $matches[2];
        } else if ($matches[1] === "Vulnerable") {
            $vulnerable = $matches[2];
        } else if ($matches[1] === "Deal") {
            $deal = $matches[2];
        }
        $blank_line_last = false;
    } 
} 

echo json_encode($ev);
