#!/usr/bin/php
<?php

// Change these to your needs.
$fileIn = "/tmp/file.txt";
$fileOut = "/tmp/file.csv";
$outputLineSeparator="\r\n";

// You don't need to modify these unless you need to modify the behaviour.
$contents = file_get_contents($fileIn);

$linesIn = explode("\n", $contents);
$linesOutRaw = array();
$linesOut = array();

$keys=array();
$entries = array();
$section="";


function csvLine($keys, $values) {
    // Take an array line, and match it up to the keys consistently.
    $out = '';
    
    $first = true;
    foreach ($keys as $key) {
        $separator = ($first)?'':',';
        $first = false;
        
        $value = (isset($values[$key]))?$values[$key]:'';
        $out .= "$separator\"$value\"";
    }
    
    return $out;
}


// Decode the input file and store it in memory.
foreach ($linesIn as $line) {
    $cleanLine=trim($line);
    if ($cleanLine != "") {
        if ($cleanLine == '---') { // Is a new entry.
            $entries['section'] = $section;
            if (count($entries)) $linesOutRaw[] = $entries;
            
            $entries = array();
        }
        elseif (strpos($cleanLine, ':', 0) > 0) { // Is a key.
            $part = explode(': ', $cleanLine);
            if (!isset($keys[$part[0]])) $keys[$part[0]] = $part[0];
            
            $value = (isset($part[1]))?$part[1]:'';
            $entries[$part[0]] = $value;
        }
        else { // Is a section name.
            echo count($entries)."\n";
            if (count($entries)) $linesOutRaw[] = $entries;
            $entries = array();
            
            $section = $cleanLine;
            $keys['section'] = 'section';
            $entries['section'] = $section;
        }
    }
}


// Create the CSV lines.
$linesOut[] = csvLine($keys, $keys);

foreach ($linesOutRaw as $entry) {
    $linesOut[] = csvLine($keys, $entry);
}



// Do something with it.
$output = implode($outputLineSeparator, $linesOut);

echo "$output\n";

?>
