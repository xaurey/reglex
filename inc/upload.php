<?php
namespace Reglex;

class Upload{

    public function uploadFile() {
        
        if (isset($_FILES['fileinput']) && $_FILES['fileinput']['error'] === UPLOAD_ERR_OK) {

            if ($_FILES['fileinput']['size'] < 500000) {
            

                // Check MIME Type
                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                if (false === $ext = array_search(
                        $finfo->file($_FILES['fileinput']['tmp_name']),
                        array(
                            'pdf' => 'application/pdf',  
                            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                        ),
                        true)
                ) {
                    // Error Type not accepted
                    $return['error'] = 'Type de fichier non supporté';
                }
                else {
                    $return['ext'] = $ext;
                    
                    $return['file'] = $_FILES['fileinput']['tmp_name'];
                }
            }
            else {
                $return['error'] = 'Fichier trop volumineux (500 Ko maximum).';
            }
        }
        else {
            $return['error'] = ($_FILES['fileinput']['error'] == 2) ? 'Fichier trop volumineux (500 Ko maximum).' :'Il y a eu une erreur dans l\'envoi du fichier.';
        }
        return $return;
    }
}