<?php
include("simple_html_dom.php");

$base = 'https://www.leaseweb.com/dedicated-servers/instant-delivery';

$curl = curl_init();
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($curl, CURLOPT_URL, $base);
curl_setopt($curl, CURLOPT_REFERER, $base);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
$str = curl_exec($curl);
curl_close($curl);

$html_base = new simple_html_dom();
$html_base->load($str);

$i=0;
foreach($html_base->find('table tr') as $e){
  $line = array();
  foreach($e->find('td') as $f){
    $t = trim($f->innertext);
    $t = str_replace('<div class="gray">',' ',$t);
    $t = str_replace('</div>',' ',$t);
    $t = trim($t);

    if ($i%23==0) {
      $t = trim(str_replace('compare-',' ',$f->first_child()->getAttribute("name")));
      $line["id"] = $t;
    }
    if ($i%23==1) {
      $line["cpu"] = $t;
    }
    if ($i%23==2) {
      $line["ram"] = $t;
    }
    if ($i%23==3) {
      $line["hdd"] = $t;
    }
    if ($i%23==4) {
      $line["bandwidth"] = $t;
    }
    if ($i%23==5) {
      $line["location"] = $t;
    }
    if ($i%23==6) {

      /**/
      $id = strpos($t, 'data-regular-price="');
      if ($id) {
        $t1=substr($t,$id+strlen('data-regular-price="'));
        $pos = strpos($t1,'"');
        $para = substr($t1,0,$pos);
        $t = $para;
      }
      $line["price"] = $t;
    }
    $i++;
  }
  if (array_key_exists('location', $line) && strpos($line['location'],'Amsterdam')!== false)
    $arr[] = $line;
}

$html_base->clear();
unset($html_base);

echo json_encode($arr);
