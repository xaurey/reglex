<?php

namespace Reglex;

class DilaAPI{

    private $provider;
    private $accesstoken;
    private $curl;

    public function __construct() {

        // URL de connexion aux API
        $this->url['juri']      = 'https://api.piste.gouv.fr/dila/legifrance/lf-engine-app/search';
        $this->url['ceta']      = 'https://api.piste.gouv.fr/dila/legifrance/lf-engine-app/search';
        $this->url['cons']      = 'https://api.piste.gouv.fr/dila/legifrance/lf-engine-app/search';
        $this->url['cass']      = 'https://api.piste.gouv.fr/cassation/judilibre/v1.0/search?query=';
        $this->url['cedh']      = 'https://hudoc.echr.coe.int/app/query/results?query=';
        $this->url['arianne']   = 'https://www.conseil-etat.fr/xsearch?';

        // URL d'affichage des JP
        $this->urlaff['juri']       = 'https://www.legifrance.gouv.fr/juri/id/';
        $this->urlaff['ceta']       = 'https://www.legifrance.gouv.fr/ceta/id/';
        $this->urlaff['cons']       = 'https://www.legifrance.gouv.fr/cons/id/';
        $this->urlaff['cass']       = 'https://www.courdecassation.fr/decision/';
        $this->urlaff['cedh']       = 'http://hudoc.echr.coe.int/fre?i=';
        $this->urlaff['arianne']    = 'http://www.conseil-etat.fr/fr/arianeweb/';

        // Type de requête suivant l'API
        $this->req['juri']      = 'post';
        $this->req['ceta']      = 'post';
        $this->req['cons']      = 'post';
        $this->req['cass']      = 'get';
        $this->req['arianne']   = 'get';
        $this->req['cedh']      = 'get';

        // Configuration et obtention d'un token d'accès au service PISTE
        $this->provider = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId'                => CLIENT_ID,
            'clientSecret'            => CLIENT_SECRET,
            'redirectUri'             => APP_URL,
            'urlAuthorize'            => 'https://oauth.piste.gouv.fr/api/oauth/authorize',
            'urlAccessToken'          => 'https://oauth.piste.gouv.fr/api/oauth/token',
            'urlResourceOwnerDetails' => 'https://oauth.piste.gouv.fr/api/oauth/resource'
        ]);

        try {
            $this->accesstoken = $this->provider->getAccessToken('client_credentials');

        } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
            // Gestion des erreurs
            exit($e->getMessage());

        }

    }

    private function curlpost($data, $fond) {
        
        // url de requête : si GET, alors concaténer l'url et $data
        $url = ($this->req[$fond] == 'get') ? $this->url[$fond] . $data : $this->url[$fond] ;

        // Headers de requête
        $headers = array(
            'Content-Type: application/json',
            'Accept: application/json'
        );

        // Access token pour PISTE
        if (in_array($fond, ['juri', 'ceta', 'cass', 'cons'])) $headers[] = 'Authorization: Bearer ' . $this->accesstoken ;

        // Initialisation de la session curl
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
        if ($fond == 'cedh') curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 0);

        // Gestion des Headers pour une connexion POST
        if ($this->req[$fond] == 'post') {
            curl_setopt($this->curl, CURLOPT_POST, true);
            // Conversion des données en JSON
            curl_setopt($this->curl, CURLOPT_POSTFIELDS,  json_encode($data));
        }

        // Exécution de la requêye curl
        $return['data'] = curl_exec($this->curl);
        $return['info'] = curl_getinfo($this->curl);
        $return['erreur'] = curl_error($this->curl);
        
        // Fermeture de la session curl
        curl_close($this->curl);

        return $return;
        
    }

    private function prepdata($data, $fond) {
        // Processus DILA
        if(in_array($fond, ['ceta', 'juri', 'cons'])) {

            $typechamp = array(
                'ceta'  => 'NUM_DEC',
                'cons'  => 'NUM_DEC',
                'juri'  => 'NUM_AFFAIRE'
            );
            
            // Correction de l'erreur de l'API Legifrance pour le Conseil d'Etat et le CC
            $fondapi = ($fond == 'ceta') ? 'cetat' : (($fond == 'cons') ? 'constit' : $fond);

            $return = [
                'fond' => strtoupper($fondapi),
                'recherche' => [
                    'champs' => array(), 
                    'operateur' => 'OU', 
                    'typePagination' => 'DEFAUT',
                    'pageNumber' => 1, 
                    'pageSize' => 20,
                    'sort' => 'DATE_ASC' 
                ]
            ];
            foreach($data as $d) {
                $return['recherche']['champs'][0]['criteres'][] = [
                    'typeRecherche' => 'EXACTE',
                    'valeur' => $d[1],
                    'operateur' => 'OU'
                ];
                $return['recherche']['champs'][0]['typeChamp'] = $typechamp[$fond];
                $return['recherche']['champs'][0]['operateur'] = 'OU';
            }
        }
        // Processus JUDILIBRE
        elseif ($fond == 'cass' ) {
            $return = $data[1];
        }
        // Processus Arianne
        elseif ($fond == 'arianne' ) {
            $return = 'advanced=1&type=json&SourceStr4=AW_DCE&SourceStr4=AW_DTC&SourceStr4=AW_DCA&text.add=&synonyms=true&scmode=smart&SkipCount=50&SkipFrom=0&sort=SourceDateTime1.desc,SourceStr5.desc';
            $return .= '&sourcecsv1=' . $data[1];
        }
        // Processus CEDH
        elseif ($fond == 'cedh' ) {
            $return = "contentsitename%3AECHR%20AND%20(NOT%20(doctype%3DPR%20OR%20doctype%3DHFCOMOLD%20OR%20doctype%3DHECOMOLD))%20AND%20((languageisocode%3D%22FRE%22))%20AND%20((documentcollectionid%3D%22JUDGMENTS%22)%20OR%20(documentcollectionid%3D%22DECISIONS%22))%20AND%20";

            $app = '';
            foreach($data as $d) {
                if($app != '') $app .= ' OR ';
                $app .= '(appno:"'. $d[1] . '")' ;
            }
            $app = '(' . $app . ')';
            $return .= str_replace(array(' ', '"', ':', '=', '/'), array('%20', '%22', '%3A', '%3D', '%2F'), $app);

            $return .= "&select=sharepointid,Rank,ECHRRanking,languagenumber,itemid,docname,doctype,application,appno,conclusion,importance,originatingbody,typedescription,kpdate,kpdateAsText,documentcollectionid,documentcollectionid2,languageisocode,extractedappno,isplaceholder,doctypebranch,respondent,advopidentifier,advopstatus,ecli,appnoparts,sclappnos&sort=&start=0&length=20&rankingModelId=22222222-ffff-0000-0000-000000000000";
        }

        return $return;
    }

    private function findurl($d, $urls, $fond) {
        
        $return = array();

        if($urls['info']['http_code'] == '200') {
            $urls = json_decode($urls['data'], true);
            $urls = ($fond != 'arianne') ? $urls['results'] : $urls['Documents'];

            foreach($d as $key => $e) {
                $return[$key]['case'] = $e[0];
                $return[$key]['number'] = $e[1];
                foreach($urls as $u){
                    if (in_array($fond, ['ceta', 'juri', 'cons'])) { $t = strtolower($u['titles'][0]['title']); }
                    elseif ($fond == 'cass') { $t = strtolower($u['number']); }
                    elseif ($fond == 'cedh') { $t = strtolower($u['columns']['appno']); }
                    elseif ($fond == 'arianne') { $t = strtolower($u['SourceStr5']); } 

                    $t = str_replace(array('/', '.', '-'), '', $t);
                    $v = str_replace(array('/', '.', '-', ' '), '', $e[1]);

                    if (in_array($fond, ['ceta', 'juri', 'cons'])) { $id = $u['titles'][0]['cid']; }
                    elseif ($fond == 'cass') { $id = $u['id']; }
                    elseif ($fond == 'cedh') { $id = $u['columns']['itemid']; }
                    elseif ($fond == 'arianne') { 
                        $cour = array(
                            'AW_DCE' => 'CE',
                            'AW_DCA' => 'CAA',
                            'AW_DTC' => 'TC'
                        );
                        $date = \DateTime::createFromFormat('Y-m-d H:i:s', $u['SourceDateTime1']);
                        $dateaff = $date->format('Y-m-d');
                        $id = $cour[$u['SourceStr4']] . '/decision/' . $dateaff . '/' . $u['SourceStr5'];
                    } 

                    if(stripos($t, $v) !== FALSE) {
                        $return[$key]['url'] = $this->urlaff[$fond] . $id;
                        break;
                    }
                }
                //Ajouter appel à JUDILIBRE et ARIANNEWEB pour les urls non trouvés sur Legifrance
                if(!isset($return[$key]['url']) AND in_array($fond, ['ceta', 'juri'])) {
                    $fondsec = array ('ceta' => 'arianne', 'juri' => 'cass');
                    $dsec[0] = $e;
                    $jpsec = $this->prepdata($e, $fondsec[$fond]);
                    $urlssec = $this->curlpost($jpsec, $fondsec[$fond]);
                    $retsec = $this->findurl($dsec, $urlssec, $fondsec[$fond]);
                    $return[$key] = $retsec[0];
                }
            }
        }
        else {
            $return['error'][$fond] = true;
        }
        return $return;
    }

    public function cases_url($data) {

        $return = array();
        // $k = fond
        // $d = cases du fond
        foreach($data as $k => $d) {
            if($k != 'cjue') {
                $jp = $this->prepdata($d, $k);
                $urls = $this->curlpost($jp, $k);
                $return = array_merge($return, $this->findurl($d, $urls, $k));
            }
            else {
                foreach($d as $key => $e) {
                    $return[$key]['case'] = $e[0];
                    $return[$key]['number'] = $e[1];
                    $jp = str_replace(array(' ', '"', ':', '=', '/'), array('%20', '%22', '%3A', '%3D', '%2F'), $e[1]);
                    $return[$key]['url'] = 'https://curia.europa.eu/juris/liste.jsf?mat=or&lgrec=fr&td=%3BALL&jur=C%2CT%2CF&page=1&pcs=Oor&nat=or&language=fr&num=' . $jp;
                }
            }

        }
        return $return;
    }
    
}