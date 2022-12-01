<?php
include 'phpQuery-onefile.php';

$locations = ["andhra-pradesh","assam","bihar","chandigarh","chhattisgarh","delhi","goa","gujarat","haryana",
                "himachal-pradesh","jammu-and-kashmir","jharkhand","karnataka","kerala","madhya-pradesh",
                "maharashtra","odisha","puducherry","punjab","rajasthan","tamil-nadu","telangana","uttar-pradesh",
                "uttarakhand","west-bengal"];

$names = [];
$addresses = [];
$phone_numbers = [];
$dealer_names = [];
$indexes = [];

for($i = 0; $i < count($locations); $i++){
    $url = "https://dealers.skoda-auto.co.in/location/".$locations[$i];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $resp = curl_exec($ch);

    $document = phpQuery::newDocument($resp);
    
    foreach( $document->find('ul.pagination li') as $item){
        $index = $document->find('a',$item)->text();
        array_push($indexes, $index);
    }
   
    if(count($indexes)==0){
        echo "scraping ".$locations[$i]." ";
        
        foreach($document->find('div.outlet-list div.store-info-box') as $item){
            $title = trim($document->find('ul div.info-text', $item)->text());
            $address = trim($document->find('li.outlet-address', $item)->text());
            $number = trim($document->find('li.outlet-phone', $item)->text());
            array_push($names, $title);
            array_push($addresses, $address);
            array_push($phone_numbers, $number);
        }

    } elseif (count($indexes)==4){
        $index = 3;
        echo "scraping ".$locations[$i]." ";
        for ($j = 1; $j < $index; $j++){
            $url = $url."?&page=".$j;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $resp = curl_exec($ch);
            $document = phpQuery::newDocument($resp);
            foreach($document->find('div.outlet-list div.store-info-box') as $item){
                $title = trim($document->find('ul div.info-text', $item)->text());
                $address = trim($document->find('li.outlet-address', $item)->text());
                $number = trim($document->find('li.outlet-phone', $item)->text());
                array_push($names, $title);
                array_push($addresses, $address);
                array_push($phone_numbers, $number);
            }   
    
        }

    } else { 
        $index = 4;
        echo "scraping ".$locations[$i]." ";
        for ($j = 1; $j < $index; $j++){
            $url = $url."?&page=".$j;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $resp = curl_exec($ch);
            $document = phpQuery::newDocument($resp);
            foreach($document->find('div.outlet-list div.store-info-box') as $item){
                $title = trim($document->find('ul div.info-text', $item)->text());
                $address = trim($document->find('li.outlet-address', $item)->text());
                $number = trim($document->find('li.outlet-phone', $item)->text());
                array_push($names, $title);
                array_push($addresses, $address);
                array_push($phone_numbers, $number);
            }   
    
        }

    }
    
}                

for ($i = 0; $i < count($names); $i++){
    $str = preg_replace("/[\r\n]*/","",$names[$i]);
    $matches = [];
    $pattern = "/(?<=\s\s\s).*(?=East|West|North|South|CNCR|)/i";
    preg_match($pattern, $str, $matches);
    $dealer_name = trim($matches[0]);
    array_push($dealer_names, $dealer_name);
}

$file = fopen('dealers.csv', 'w');
fputcsv($file, array("Dealer Name", "Address", "Phone", "Location"));
echo "writing";
$final_array = [];
for($i = 0; $i < count($dealer_names); $i++){
    array_push($final_array, array($dealer_names[$i],$addresses[$i], $phone_numbers[$i]));
}
foreach($final_array as $final){
    fputcsv($file, $final);
}




























?>