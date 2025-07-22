<?php 

require "conf.php";
require "../vendor/autoload.php";
require "dilaapi.php";
require "reader.php";
require "regex.php";
require "upload.php";


use Reglex\DilaAPI;
use Reglex\Reader;
use Reglex\Regex;
use Reglex\Upload;

$upload = new Upload();
$file = $upload->uploadFile();

//Récupérer le texte du fichier
if (isset($file['error'])){
	?>
	<div class="alert alert-danger">
		<?php print_r($file['error']); ?>
	</div>
	<?php
}
else {
	$conv = new Reader($file['file'], $file['ext']);
	$text = $conv->convertToText();

	// Gestion des erreurs de fichiers
	if(is_array($text)) {
		if (stripos($text['error'], 'Secured') !== false) {
			$text['error'] = "RegLex ne prend malheureusement pas en charge les PDF protégés.";
		}
		?>
		<div class="alert alert-danger">

			<?php echo $text['error']; ?>
		</div>
		<?php
	}
	// Gestion des fichiers images ou si pas de JP trouvée
	elseif ($text == '') {
		?>
		<div class="alert alert-warning">
			Le fichier est vide ou ne contient que des éléménts scannés.
		</div>
		<?php
	}
	// Programme continue sinon
	else { 

		// Parser les jurisprudences
		$regex = new Regex();
		$matches = $regex->regjp($text);
		if (preg_last_error() !== PREG_NO_ERROR) {
			echo array_flip(array_filter(get_defined_constants(true)['pcre'], function ($value) {
				return substr($value, -6) === '_ERROR';
			}, ARRAY_FILTER_USE_KEY))[preg_last_error()];
		}

		// Organiser les jurisprudences par fonds de recherche
		$cases = $regex->cases_organise($matches);
		

		// Récupérer les liens sur Legifrance
		$oauth = new DilaAPI();
		$urls = $oauth->cases_url($cases);

		if(isset($urls['error']['cetat'])){
			?>
			<div class="alert alert-danger">
				Erreur de connexion à l'API Legifrance
			</div>
			<?php
		}
		elseif(count($urls) == 0) {
			?>
			<div class="alert alert-warning">
				Aucune décision trouvée.
			</div>
			<?php
		}
		else {
			echo '<ol>';
			ksort($urls);
			$oldcase = '';
			foreach($urls as $u){
				if($oldcase != $u['case']) { 
					if(isset($u['url'])){
						echo '<li><a href="'. $u['url'] . '" target="_blank">' . $u['case'] .'</a></li>';
					}
					else {
						echo '<li>' . $u['case'] .' - pas de lien trouvé </li>';
					}
					$oldcase = $u['case'];
				}
			}
			echo '</ol>';
		}
	}
}
?>