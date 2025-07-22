<?php
namespace Reglex;

class Reader{

    private $filename;

    public function __construct($filePath, $ext) {
        $this->filename = $filePath;
        $this->ext = $ext;
    }

    private function read_doc() {
        if($fileHandle = fopen($this->filename, "r") !== false) {

            $headers = @fread($fileHandle, 0xA00);

            return $headers;

            // 1 = (ord(n)*1) ; Document has from 0 to 255 characters
            $n1 = ( ord($headers[0x21C]) - 1 );
            // 1 = ((ord(n)-8)*256) ; Document has from 256 to 63743 characters
            $n2 = ( ( ord($headers[0x21D]) - 8 ) * 256 );
            // 1 = ((ord(n)*256)*256) ; Document has from 63744 to 16775423 characters
            $n3 = ( ( ord($headers[0x21E]) * 256 ) * 256 );
            // 1 = (((ord(n)*256)*256)*256) ; Document has from 16775424 to 4294965504 characters
            $n4 = ( ( ( ord($headers[0x21F]) * 256 ) * 256 ) * 256 );
            // Total length of text in the document
            $textLength = ($n1 + $n2 + $n3 + $n4);

            //$line = @fread($fileHandle, filesize($this->filename));
            $line = @fread($fileHandle, $textLength);
            $lines = explode(chr(0x0D),$line);
            return $line;
            $outtext = "";
            foreach($lines as $thisline)
            {
                $pos = strpos($thisline, chr(0x00));
                if (($pos !== FALSE)||(strlen($thisline)==0))
                {
                } else {
                    $outtext .= $thisline." ";
                }
            }
            
            return mb_convert_encoding($outtext, "UTF8", "Windows-1252");
        }
        else {
            return 'error';
        }
    }

    private function read_docx(){

        // Create new ZIP archive
        $zip = new \ZipArchive;
        $content = '';

        // Open received archive file
        if (true === $zip->open($this->filename)) {
            // If done, search for the data file in the archive

            for($i = 0; $i < $zip->numFiles; $i++) {
                if (in_array($zip->getNameIndex($i), array("word/document.xml", "word/footnotes.xml", "word/endnotes.xml"))) {
                    // Read content
                    $content .= $zip->getFromIndex($i);
                }
            }

            $zip->close();
        }
        else {
            return false;
        }

        $content = str_replace('</w:r></w:p></w:tc><w:tc>', " ", $content);
        $content = str_replace('</w:r></w:p>', "\r\n", $content);
        $content = strip_tags($content);

        return $content;
    }

    public function convertToText() {

        if(isset($this->filename) && !file_exists($this->filename)) {
            return array('error'=>"File does not exist");
        }

        $file_ext = $this->ext;

        if( $file_ext == "docx" || $file_ext == "pdf")
        {
            //if($file_ext == "doc") {
            //    $return = $this->read_doc();
            //    return $return;
            if($file_ext == "docx") {
                $return = $this->read_docx();
            } elseif($file_ext == "pdf") {
                $parser = new \Smalot\PdfParser\Parser();
                try {
                    $pdf = $parser->parseFile($this->filename);
                    $return = $pdf->getText();
                }
                catch(\Exception $e) {
                    return array('error'=> $e->getMessage());
                }
            }
            //$return = mb_convert_case($return, MB_CASE_LOWER, "UTF-8");
            $return = preg_replace('/\t+/', '', $return);
            $return = preg_replace('/[\xA0\xc2\s]+/u', ' ', $return);
            $return = str_replace('  ', ' ', $return);
            $return = str_replace(' ,', ',', $return);
            $return = str_replace([' - ', '- ', ' -'], '-', $return);
            $return = str_replace(' .', '.', $return);
            $return = str_replace([' / ', ' /', '/ '], '/', $return);
            return $return;
        } else {
            return array('error'=>"Invalid File Type");
        }
    }
}
?>