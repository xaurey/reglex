<?php
namespace Reglex;

class Regex{

    public function regjp($text) {
        $text = str_replace('  ', ' ', $text);
        $regjp = "/(?<juri>(?<![éèêëàäöùü])\b(?:c(?=ed|re|ou|on|e\b(?! s)|[hicrajp .])|(?:t(?=rib|[pagi .]))|(?:s(?=oc)))(?> ?[-a-zéè.'’]+ ?){0,10})(?:[\(\[][0-9a-zêéàâè]+[\)\]])?,?(?:(?:\s[0-9a-z êéàâèùûôöëäç.]+)?,)?\s?(?<date>(?:\d{1,2}[er]{0,2}\s[jfmasond][a-zéû.]+\s\d{2,4})|(?:\d{1,2}[-\/]\d{1,2}[-\/]\d{2,4})),?\s(?:[-a-zÀ-ÖÙ-öù-ÿĀ-žḀ-ỿ\/. '’]{3,100}[, ]?){0,1}\s?[a-zé.° ,]{0,14}(?<affaire>(?:c-|t-)?\d+[-.a-z0-9\/]*\d)/i";
        preg_match_all( $regjp, $text, $matches, PREG_SET_ORDER );
        return $matches;
    }

    public function cases_organise($data) {
        $cetat = '/\b[ct][. ]{0,2}(?:e|.*tat|.*con|.*adm|.*a[. ]{0,2}a)|t[. ]{0,2}[ca]\b/i';
        $cetat_num = '/(\d+[!.-a-z0-9\/]{4,20})/i';
        $juri_num = '/\d+[-\/]\d{2,}\.?\d+/i';
        $constit = '/\bc(?!a)(?:[^r]*\b)?c(?!r|i)/i';
        $constit_num = '/\d+\s?-\s?\d+/i';
        $cedh = "/\bc.+h(?:omme)?\b/i";
        $cedh_num = '/\d+\/\d{2}/i';
        $cjue = '/\b[ct][. ]{0,2}(?:j[ .]?[uc][ .]?e|.*euro)/i';
        $cjue_num = '/[ct]-\d+/i';
        $cpi = '';
        $return = array();

        foreach($data as $k => $d) {

            if(preg_match($cedh, $d['juri'])){
                if(preg_match($cedh_num, $d['affaire'])){
                    $return['cedh'][$k] = array($d[0],$d['affaire']);
                }
            }
            elseif(preg_match($constit, $d['juri'])) {
                if(preg_match($constit_num, $d['affaire'])){
                    $return['cons'][$k] = array($d[0],$d['affaire']);
                }
            }
            elseif(preg_match($cjue, $d['juri'])) {
                if(preg_match($cjue_num, $d['affaire'])){
                    $return['cjue'][$k] = array($d[0],$d['affaire']);
                }
            }
            elseif(preg_match($cetat, $d['juri'])) {
                if(preg_match($cetat_num, $d['affaire'])){
                    $return['ceta'][$k] = array($d[0],$d['affaire']);
                }
            }
            else{
                // Toutes celles qui restent sont considérées comme faisant partie de JURI
                if(preg_match($juri_num, $d['affaire'])){
                    $return['juri'][$k] = array($d[0],$d['affaire']);
                }
            }
        }

        return $return;
    }
}
?>