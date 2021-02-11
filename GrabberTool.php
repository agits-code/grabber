<?php

error_reporting(E_STRICT | E_ALL);
class GrabberTool
{
    private static $path = "../../downloads/";
    private static $row;
    private static $username = 'hdblit-21';
    private static $password = 'J-!35XN^f$bCH%k#';
    public static $options = array(
        CURLOPT_AUTOREFERER => true,
        CURLOPT_COOKIEFILE => '',
        CURLOPT_HTTPAUTH =>CURLAUTH_DIGEST,
        CURLOPT_FOLLOWLOCATION => true
    );

    public static function fetchContent($url)
    {
        if (($curl = curl_init($url)) == false) {
            throw new Exception("curl_init error for url $url.");
        }
        // echo "Reading $url ... ";
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(

            'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.198 Safari/537.36',
            'Sec-Fetch-Dest: document'));
        curl_setopt_array($curl, array(
            CURLOPT_HTTPAUTH => CURLAUTH_DIGEST,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_VERBOSE => 1
        ));
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
        $content = self::fetchContent($url);

            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($content);
            libxml_clear_errors();
            $xpath = new DOMXpath($dom);


        foreach ($xpath->query("//tr") as $element)
        {

            $els = $element->getElementsByTagName('td');

            if(is_object($els->item(0))) {

                self::$row['name'] = $els->item(0)->nodeValue;
            }
            if(is_object($els->item(1))) {

                self::$row['date'] = date("Y-m-d H:i:s",strtotime($els->item(1)->nodeValue));

            }
            if(is_object($els->item(2))) {
                self::$row['code'] = str_replace("\"", "",$els->item(2)->nodeValue);

            }
            if(is_object($els->item(3))) {
                self::$row['size'] = $els->item(3)->nodeValue;

            }
            if(is_object($els->item(4))) {
                self::$row['link'] = "https://assoc-datafeeds-eu.amazon.com/datafeed/".($els->item(4)->firstChild->getAttribute('href'));

            }

           if(self::$row) {
               //TODO scrivere elemento nel DB

               $riga[] = self::$row;
           }
        }
        return $riga;

    }



    public static function downloadFile($file_gz_url) {

        $fileName = self::$path.basename($file_gz_url); //./../downloads/it_standardized_camera_mp_20210201_29.delta.csv.gz"


        if (($curl = curl_init($file_gz_url)) === false) {
            throw new Exception("curl_init error for url $file_gz_url.");
        }
        curl_setopt_array($curl, self::$options);
        curl_setopt($curl, CURLOPT_USERPWD, self::$username.":".self::$password);


        if (($fp = fopen($fileName, "wb")) === false) {
            throw new Exception("fopen error for filename $fileName");
        }
        curl_setopt($curl, CURLOPT_FILE, $fp);


        if (curl_exec($curl) === false) {
            fclose($fp);
            unlink($fileName);
            throw new Exception("curl_exec error for url $file_gz_url.");
        } elseif (isset($targetDir)) {
            $eurl = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
            preg_match('#^.*/(.+)$#', $eurl, $match);
            fclose($fp);
            rename($fileName, "$targetDir{$match[1]}");
            $fileName = $targetDir($match[1]);



        } else {

            fclose($fp);
        }

        curl_close($curl);

    }

    public static function decompressGz ($file_gz)
    {

        $file_name = self::$path.$file_gz;


// Raising this value may increase performance
        $buffer_size = 4096; // read 4kb at a time
        $out_file_name = str_replace('.gz', '', $file_name);

// Open our files (in binary mode)
        $file = gzopen($file_name, 'rb');
        $out_file = fopen($out_file_name, 'wb');

// Keep repeating until the end of the input file
        while (!gzeof($file)) {
            // Read buffer-size bytes
            // Both fwrite and gzread and binary-safe
            fwrite($out_file, gzread($file, $buffer_size));
        }
        echo "decompress Done :".$out_file_name;
// Files are done, close files
        fclose($out_file);
        gzclose($file);
        return $out_file_name;
    }


    public static function csvReader($file_csv,$db)
    {
        $filename = "getFeed?filename=".str_replace('.gz', '', $file_csv);;

        $file = self::$path . $filename;

        if ( ! is_readable( $file ) ) {
            die( 'Il file non è leggibile oppure non esiste!' );
        }

        if (filesize($file) === intval($db->getPointer('myfiles',$file_csv))){
            echo "File: ".$file_csv." is read";

            $db->setRead($file_csv);
        }

        $h = fopen($file, "r");

        fseek($h,($db->getPointer('myfiles',$file_csv)));

        $startTime = time();
        while (($data =fgetcsv($h, 4000)) !== FALSE)
        {
            $the_big_array[ftell($h)] = $data;
            $pos = ftell($h);
            $db->setPointer($file_csv,intval($pos));
            //var_dump($the_big_array[ftell($h)]);
            var_dump($file_csv."----------".$pos."||||||||".filesize($file)."\n");

            if (time()-$startTime>1) break;
        }

        fclose($h);
    }
}


