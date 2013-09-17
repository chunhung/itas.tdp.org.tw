<?php

$columns = array(
    '主導企業名稱',
    '計劃名稱',
    '企業網址',
    '計畫起訖時間',
    '計畫總經費',
    '計畫補助款',
    '計畫自籌款',
    'link',
);
$output = fopen('php://output', 'w');
fputs($output, "json,");
fputcsv($output, $columns);

foreach (glob("outputs/*") as $file) {
    $json = json_decode(file_get_contents($file));
    $values = array($file);
    foreach ($columns as $col) {
        $values[] = $json->{$col};
    }
    fputcsv($output, $values);
}
