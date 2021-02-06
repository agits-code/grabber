<?php

error_reporting(E_STRICT | E_ALL);
class GrabberTool
{
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
                //TODO escludere file xml
              /*  $pattern = '/\.(.*?).gz/s';

                if (preg_match($pattern, $els->item(0)->nodeValue, $risultato)) {
                    $extension = $risultato[1];

                }
                    if ($extension == "xml")  continue; */
                self::$row['name'] = $els->item(0)->nodeValue;
            }
            if(is_object($els->item(1))) {
                //date("jS F, Y", strtotime("11.12.10"));
                self::$row['date'] = date("Y-m-d H:i:s",strtotime($els->item(1)->nodeValue));

            }
            if(is_object($els->item(2))) {
                self::$row['code'] = str_replace("\"", "",$els->item(2)->nodeValue);

            }
            if(is_object($els->item(3))) {
                self::$row['size'] = $els->item(3)->nodeValue;

            }
            if(is_object($els->item(4))) {
                self::$row['link'] = "https://assoc-datafeeds-eu.amazon.com/datafeed/getFeed?filename=".($els->item(4)->firstChild->getAttribute('href'));

            }

           if(self::$row) {
               //TODO scrivere elemento nel DB

               $riga[] = self::$row;
           }
        }
        return $riga;

    }

    public static function downloadFile($file_gz_url, $verbose = false) {
        // TODO : costruire $url x download
        $fileName = basename($file_gz_url);
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
            $fileName = "$targetDir{$match[1]}";

        } else {
            $verbose = true;
            fclose($fp);
        }

        curl_close($curl);
        if ($verbose === true) {
            echo "Done.\n";
        }
        return $fileName;
    }

    public static function decompressGz ($file_gz)
    {

        $file_name = $file_gz;


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

    public static function csvReader ($file_csv)
    {

       // $filename = self::decompressGz($url);
        $filename = $file_csv;
        $filenamepointer = 'pointer.txt';
// Percorso da cui prelevare il file
        $path = '';

// File completo di percorso
        $file = $path . $filename;
        $file_pointer = $path.$filenamepointer;
// Controllo se il file è leggibile
        if ( ! is_readable( $file ) ) {
            die( 'Il file non è leggibile oppure non esiste!' );
        }

        $source_pointer = fopen($file_pointer, 'r' );

        $posizioni = fread($source_pointer,4000);
        fclose($source_pointer);
        $posizione = explode(",",$posizioni);


        $pointer =intval($posizione[0]);
        if (filesize($file) === $pointer){
            echo "ok";
            //TODO scrivere valore nella colonna POINTER
        }
// apro file
        $h = fopen($file, "r");
        if ( ! is_readable( $file_pointer ) ) {
            die( 'Il file non è leggibile oppure non esiste!' );
        }
        fseek($h,$pointer);
        //$line_read = 0;

        $startTime = time();
        while (($data =fgetcsv($h, 4000)) !== FALSE)
        {


                $the_big_array[ftell($h)] = $data;



               $pos = ftell($h).",";

            $add_pos = fopen($file_pointer,"w+");
           fwrite($add_pos,$pos);

               //$line_read++;
               var_dump($the_big_array[ftell($h)]);

            fclose($add_pos);
           // if ($line_read>10) break;
            if (time()-$startTime>1) break;
        }


        fclose($h);

    }
}


