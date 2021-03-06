<?php
namespace App\Models;
use DOMDocument;
use DOMXPath;
use Exception;

error_reporting(E_STRICT | E_ALL);
class GrabberTool
{
    private static $path = "../../downloads/";
    private static $file_API_amazon;   //singolo file
    private static $username = 'hdblit-21';
    private static $password = 'J-!35XN^f$bCH%k#';
    public static $options = array(

        CURLOPT_AUTOREFERER => true,
        CURLOPT_COOKIEFILE => '',
        CURLOPT_HTTPAUTH => CURLAUTH_DIGEST,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_RETURNTRANSFER => true ,
        CURLOPT_VERBOSE => 1
    );

    public static function fetchContent($url)
    {
        if (($curl = curl_init($url)) === false) {
            throw new \RuntimeException("curl_init error for url $url.");
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER, array(

            'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.198 Safari/537.36',
            'Sec-Fetch-Dest: document'));
        curl_setopt_array($curl,self::$options);
        curl_setopt($curl, CURLOPT_USERPWD, self::$username . ":" . self::$password);

        $content = curl_exec($curl);
        if ($content === false) {
            throw new Exception("curl_exec error for url $url.");
        }
        curl_close($curl);


        $content = preg_replace('#\n+#', ' ', $content);
        $content = preg_replace('#\s+#', ' ', $content);

        return $content;
    }

    public static function getItems($url) {
        try {
            $content = self::fetchContent($url);
        } catch (Exception $e) {
        }

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($content);
        libxml_clear_errors();
        $xpath = new DOMXpath($dom);

        foreach ($xpath->query("//tr") as $element)
        {

            $els = $element->getElementsByTagName('td');

            if(is_object($els->item(0))) {

                self::$file_API_amazon['name'] = $els->item(0)->nodeValue;
            }
            if(is_object($els->item(1))) {

                self::$file_API_amazon['date'] =strtotime($els->item(1)->nodeValue);
            }
            if(is_object($els->item(2))) {
                self::$file_API_amazon['code'] = str_replace("\"", "",$els->item(2)->nodeValue);

            }
            if(is_object($els->item(3))) {
                self::$file_API_amazon['size'] = $els->item(3)->nodeValue;

            }
            if(is_object($els->item(4))) {
                self::$file_API_amazon['link'] = "https://assoc-datafeeds-eu.amazon.com/datafeed/".($els->item(4)->firstChild->getAttribute('href'));

            }

           if(self::$file_API_amazon) {


               $riga[] = self::$file_API_amazon;
           }
        }
        return $riga;

    }

    public static function downloadFile($file_id,$file_size,$file_link,$file_cursor) // FUNZIONA!!!!!!!!!
    {


       $cursor = intval($file_cursor);

        if ($cursor){
            $init =$cursor + 1;
        } else $init = 0;
        $end = (($file_size - $cursor) > 499999) ? ($init + 499999) : ($file_size);
        $range = "$init-$end";
        $fileName = self::$path.$file_id.basename($file_link);
        if (($curl = curl_init($file_link)) === false) {
            throw new Exception("curl_init error for url $file_link.");
        }
        curl_setopt_array($curl, self::$options);
        curl_setopt($curl, CURLOPT_USERPWD, self::$username.":".self::$password);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);//non cambia nulla
        curl_setopt($curl,CURLOPT_RANGE,$range);

        if (($fp = fopen($fileName, "ab")) === false) {
            throw new Exception("fopen error for filename $fileName");
        }
        curl_setopt($curl, CURLOPT_FILE, $fp);

        echo "$init : $end of $file_size\n";

        if (curl_exec($curl) === false) {
            fclose($fp);
            unlink($fileName);
            throw new Exception("curl_exec error for url $file_link.");
  //      } elseif (isset($targetDir)) {
  //          $eurl = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
  //          preg_match('#^.*/(.+)$#', $eurl, $match);
  //          fclose($fp);
  //          rename($fileName, "$targetDir{$match[1]}");
        } else {
            fclose($fp);
        }
        curl_close($curl);
        return $end;
    }



    public static function decompressGz ($file_id,$file_link,$db) // ok
    {

        $file_name = self::$path.$file_id.basename($file_link);
        if (( $file = gzopen($file_name, 'rb')) === false) {
            throw new Exception("fopen error for filename $file_name");
        }


        $buffer_size = 4096; // read 4kb at a time
        $out_file_name = str_replace('.gz', '', $file_name);

// Open files (in binary mode)
        $out_file = fopen($out_file_name, 'wb');

// Keep repeating until the end of the input file
        while (!gzeof($file)) {
            // Read buffer-size bytes
            // Both fwrite and gzread and binary-safe
            fwrite($out_file, gzread($file, $buffer_size));
        }
        $db->query("UPDATE myfiles SET decompressed=true WHERE ID='$file_id';");
// Files are done, close files
        fclose($out_file);
        gzclose($file);
        return $out_file_name;
    }


    public static function csvReader($file_id,$file_name,$db) // ok
    {
        $file_csv = $file_name;
        $filename = $file_id."getFeed?filename=".str_replace('.gz', '', $file_csv);

        $file = self::$path . $filename;

        if ( ! is_readable( $file ) ) {
            die( 'Il file non è leggibile oppure non esiste!' );
        }
        $getCursor = $db->query_first("SELECT pointer from myfiles where ID='$file_id';");
        $cursor = intval($getCursor->pointer);

        if (filesize($file) === $cursor){
            echo "Complete ->\n";

            $db->query("UPDATE myfiles SET isread=true WHERE ID='$file_id';");
        }

        $h = fopen($file, "r");

        fseek($h,$cursor);

        $startTime = time();
        while (($data =fgetcsv($h)) !== FALSE)
        {

            $asin = (isset($data[0])) ? $data[0] : '';
            $titleStr = (isset($data[2])) ? $data[2] : '';
            $title= str_replace('\'', '-', $titleStr);
            $price_amazon= (isset($data[5])) ? $data[5] : '';
            $price_tp = (isset($data[40])) ? $data[40]: '';
            $price_string = ($price_amazon) ?: $price_tp;
            $priceStr = str_replace(',', '.', $price_string);
           // $price = filter_var($priceStr, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $price = (filter_var($priceStr, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION)) ? filter_var($priceStr, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : 0;
           echo $price;
            $operation = (isset($data[172])) ? $data[172] : '';
            $merchantStr = (isset($data[9])) ? $data[9] : '';
            $merchant = str_replace('\'', '-', $merchantStr);
            $url = (isset($data[10])) ? $data[10] : '';
            $product_group = (isset($data[142])) ? $data[142] : '';
            $brandStr = (isset($data[173])) ? $data[173] : '';
            $brand = str_replace('\'', '-', $brandStr);
            $productStr = (isset($data[174])) ? $data[174] : '';
            $product= str_replace('\'', '-', $productStr);

          //  echo $asin."-".$brand."-".$product."-".$title."-".$price."-".$url."-".$merchant."-".$product_group."<hr>";
           // echo $asin."-".$brand."-".$product."-".$title."-".$price."<hr>";
            #####################################################################################
            ###       scrivere nel db prodotti                                          #######
           $getAsin = $db->query_first("SELECT * FROM amazoncatalog WHERE asin='$asin';");
          // echo $getAsin;
            if($getAsin AND $asin !== "asin") {
                switch ($operation) {
                    case 'UPDATE':
                        echo "da modificare";
                        break;
                    case 'REMOVE' :
                        echo " da cancellare";
                        break;
                    default :
                        echo " da aggiornare";
                        break;
                }
            } else if($asin !== "asin"){

                switch ($operation) {
                    case 'ADD':
                      //  echo $asin . " da aggiungere";
                        break;
                    default :
                        $db->query("INSERT INTO amazoncatalog (asin,brand,product,title,price) VALUES ('{$asin}','{$brand}','{$product}','{$title}','{$price}');");

                        break;
                }
            }

            #####################################################################################

            $pos = ftell($h);
            $db->query("UPDATE myfiles SET pointer='$pos' WHERE ID='$file_id';");
            $now = time();
            $db->query("UPDATE myfiles SET updated= '$now' WHERE ID='$file_id';");

            if ((time()-$startTime>1) or (filesize($file) === intval($pos))) break;
        }
        echo $cursor." - ".((isset($pos)) ? $pos : filesize($file))." of ".filesize($file);
        fclose($h);
    }
}


