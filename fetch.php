<?php

for ($i = 0; $i < 3000; $i ++) {
    $url = 'http://itas.tdp.org.tw/content/application/itas/plan_apply/guest-cnt-browse.php?cnt_id=' . $i;
    error_log($url);
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $content = curl_exec($curl);
    curl_close($curl);

    $doc = new DOMDocument;
    @$doc->loadHTML($content);

    $doc_name = null;
    foreach ($doc->getElementsByTagName('span') as $span_dom) {
        if ($span_dom->getAttribute('class') == 'doc_name') {
            $doc_name = $span_dom->nodeValue;
            break;
        }
    }

    if (is_null($doc_name) or $doc_name == '') {
        continue;
    }

    $ret = array();
    $ret['計劃名稱'] = $doc_name;
    $ret['link'] = $url;

    foreach ($doc->getElementsByTagName('tr') as $tr_dom) {
        $td_node_count = 0;
        foreach ($tr_dom->childNodes as $childnode) {
            if ($childnode->nodeName == 'td') {
                $td_node_count ++;
            }
        }
        if ($td_node_count != 3) {
            continue;
        }
        if ($tr_dom->getElementsByTagName('td')->item(0)->getElementsByTagName('img')->length != 1) {
            continue;
        }
        if (!$tr_dom->getElementsByTagName('td')->item(1)->nodeValue) {
            continue;
        }

        if (in_array($tr_dom->getElementsByTagName('td')->item(1)->nodeValue, array('計畫概述', '計畫效益'))) {
            $ret[$tr_dom->getElementsByTagName('td')->item(1)->nodeValue] = $doc->saveHTML($tr_dom->getElementsByTagName('td')->item(2));
        } else {
            $ret[$tr_dom->getElementsByTagName('td')->item(1)->nodeValue] = $tr_dom->getElementsByTagName('td')->item(2)->nodeValue;
        }
    }
    file_put_contents(__DIR__ . '/outputs/' . $i . '.json', json_encode($ret, JSON_UNESCAPED_UNICODE));
}
