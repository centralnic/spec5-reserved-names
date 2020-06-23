<?php
chdir("Net_IDNA2");
require('Net/IDNA2.php');
$idn = new Net_IDNA2;
$doc= new DOMDocument();
chdir("../..");
$doc->load('registry.xml.in');

$registries = $doc->getElementsByTagName('registry');
for ($i = 0 ; $i < $registries->length ; $i++) {
    $registry = $registries->item($i);
    $file = $registry->getAttribute('id').'.txt';
    if (!file_exists($file)) {
        continue;
    }

    $lines = array_map('trim', file($file));
    foreach ($lines as $line) {
        $line = preg_replace('/\s*#.*/', '', $line);
        if (empty($line)) {
            continue;
        }
        $record = $doc->createElement('record');
        $name = $doc->createElement('name', $line);
        if (preg_match('/^xn--/', $line)) {
            $label = $doc->createElement('label', $line);
        }
        else {
            $label= $doc->createElement('label',$idn->encode($line));
        }
        $record->appendChild($name);
        $record->appendChild($label);
        $registry->appendChild($record);
    }
}

print $doc->saveXML();
