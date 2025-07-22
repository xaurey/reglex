
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="Reglex est un extracteur de jurisprudences depuis un fichier texte, renvoyant ensuite vers le texte des décisions identifiées">
		<meta name="author" content="Xavier Aurey">
		<title>REGLEX - Extracteur de jurisprudences</title>
        <!-- Favicon-->
		<link rel="apple-touch-icon-precomposed" sizes="57x57" href="images/icons/apple-touch-icon-57x57.png" />
		<link rel="apple-touch-icon-precomposed" sizes="114x114" href="images/icons/apple-touch-icon-114x114.png" />
		<link rel="apple-touch-icon-precomposed" sizes="72x72" href="images/icons/apple-touch-icon-72x72.png" />
		<link rel="apple-touch-icon-precomposed" sizes="144x144" href="images/icons/apple-touch-icon-144x144.png" />
		<link rel="apple-touch-icon-precomposed" sizes="120x120" href="images/icons/apple-touch-icon-120x120.png" />
		<link rel="apple-touch-icon-precomposed" sizes="152x152" href="images/icons/apple-touch-icon-152x152.png" />
		<link rel="icon" type="image/png" href="images/icons/favicon-32x32.png" sizes="32x32" />
		<link rel="icon" type="image/png" href="images/icons/favicon-16x16.png" sizes="16x16" />
		<meta name="application-name" content="REGLEX - Extracteur de jurisprudences"/>
		<meta name="msapplication-TileColor" content="#D14B0E" />
		<meta name="msapplication-TileImage" content="images/icons/mstile-144x144.png" />

		<!-- Bootstrap core CSS -->
		<link href="css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" type="text/css" />
		<link href="css/styles.css" rel="stylesheet" crossorigin="anonymous">

	</head>

	<body>
        
		<!-- Navigation-->
        <nav class="navbar navbar-light bg-light static-top d-flex flex-wrap justify-content-center">
            <div class="container">
				<a class="navbar-brand" href="#!">
					<img src="images/logo_reglex.png" height="80" width="80">
					<span>RegLex</span>
				</a>
				<ul class="topmenu nav">
                	<li class="nav-item"><a href="#divexemple" class="nav-link">Exemple</a></li>
                	<li class="nav-item"><a href="#author" class="nav-link">Concepteur</a></li>
                	<li class="nav-item"><a href="#contact" class="nav-link">Contact</a></li>
				</ul>
            </div>
        </nav>

		<!-- Masthead-->
        <header class="masthead">
            <div class="container position-relative">
                <div class="row justify-content-center">
                    <div class="col-9">
                        <div class="text-center text-white">
                            <!-- Page heading-->
                            <h1 class="mb-5">REGLEX - Extracteur de jurisprudences</h1>

							<?php if(isset($aff)): ?>
							<div class="row justify-content-center g-5">
								<div class="col-12">
									<?php print_r($aff); ?>
								</div>
							</div>
							<?php endif; ?>

							<!-- Form for File -->
                            <form id="fileform" method="POST" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col">
										<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
										<input class="form-control form-control-lg" type="file" name="fileinput" id="fileinput">
										<label for="fileupload" class="form-label">Fichier docx ou pdf, taille maximale : 1 Mo</label>
                                    </div>
								</div>

                                <div class="row padtop20">
                                    <div class="col">
										<button id="btnupload" class="w-100 btn btn-primary btn-lg" type="submit" name="uploadBtn">Analyser le fichier</button>

										<!-- Submit error message-->
										<?php if(isset($erreur)) : ?>
											<div class="col-12">
												<div class="text-center text-danger mb-3">
													<p><?php echo $erreur; ?></p>
												</div>
											</div>
										<?php endif; ?>
									</div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

		<!-- Results -->
		<section class="bg-light" id="divanalyse">
            <div class="container">
                <div class="row justify-content-center g-5 padtop20">
                    <div class="col-9">
						<h4 class="mb-3">Jurisprudences extraites</h4>
						<p>Jurisprudences classées dans l'ordre d'apparition dans le texte.</p>
						<div id="jptext"></div>
                    </div>
                </div>
            </div>
        </section>

		<!-- Icons Grid-->
        <section class="features-icons bg-light text-center">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="features-icons-item mx-auto mb-5 mb-lg-0 mb-lg-3">
                            <div class="features-icons-icon d-flex"><i class="bi-shield-lock m-auto text-primary"></i></div>
                            <h3>Sécurisé</h3>
                            <p class="lead mb-0">Protection de l'envoi par une connection Https (certificat SSL : SHA-256 avec chiffrement RSA)</p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="features-icons-item mx-auto mb-5 mb-lg-0 mb-lg-3">
                            <div class="features-icons-icon d-flex"><i class="bi-emoji-smile m-auto text-primary"></i></div>
                            <h3>Simple</h3>
                            <p class="lead mb-0">Aussi difficile à utiliser que de cliquer sur un bouton</p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="features-icons-item mx-auto mb-0 mb-lg-3">
                            <div class="features-icons-icon d-flex"><i class="bi-person-check m-auto text-primary"></i></div>
                            <h3>Respectueux des données</h3>
                            <p class="lead mb-0">Aucun fichier, ni aucune donnée ne sont conservés sur le serveur</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

		<!-- Image Showcases-->
		<section class="showcase">
            <div class="container-fluid p-0">
                <div class="row g-0">
                    <div class="col-lg-6 order-lg-2 text-white showcase-img" style="background-image: url('images/regex.png')"></div>
                    <div class="col-lg-6 order-lg-1 my-auto showcase-text">
                        <h2>Une application pour tous les juristes</h2>
                        <p class="lead mb-0">Cette petite application en <i>version Alpha</i> vous permet d'extraire les jurisprudences d'un fichier <b>Word .docx</b> (pas de .doc pour le moment) ou <b>PDF</b> (en version texte) et de renvoyer en lien vers le texte complet (si la décision est disponible en accès libre).</p>
                    </div>
                </div>
                <div class="row g-0">
                    <div class="col-lg-6 text-white showcase-img" style="background-image: url('images/api.png')"></div>
                    <div class="col-lg-6 my-auto showcase-text">
                        <h2>Une utilisation des données ouvertes</h2>
                        <p class="lead mb-0">Pour le droit interne, cette application se connecte aux API Legifrance, Judilibre (Cour de cassation) et ArianneWeb (Conseil d'Etat). Pour le droit européen, elle se connecte à l'API de la Cour européenne des droits de l'Homme, mais renvoie simplement à une recherche de l'affaire sur le site de la Cour de justice de l'Union européenne. Plus d'informations au sein de cet article : <a href="https://www.fondamentaux.org/2021/open-data-des-decisions-de-justice-et-api-des-cours-francaises-et-europeennes/">"Open data des décisions de justice et API des cours françaises et européennes"</a>.</p>
                    </div>
                </div>
            </div>
        </section>

		<!-- Example -->
		<section id="divexemple" class="bg-light exemple">
            <div class="container">
				<h2 class="mb-5 text-center">Exemple d'utilisation</h2>
                <div class="row justify-content-center g-5 padtop20">
					<div class="col-7">
						<h4 class="mb-3">Exemple de texte</h4>
						<p>Pour les manifestations, l’obligation d’une déclaration préalable implique que le rassemblement soit prévisible et organisé, comme le souligne d’ailleurs l’article L211-2 et la jurisprudence de la Cour de cassation (Cass., crim., 9 février 2016, n° 14-82234). De plus, en dehors d’un rassemblement répondant à la définition d’un attroupement les autorités ne peuvent prendre des mesures qu’à l’encontre des organisateurs. Les tribunaux et cours le rappellent en effet régulièrement (CAA de Lyon, Formation plénière, 24 juin 2003, n° 98LY01551, Société des autoroutes Paris-Rhin-Rhône). Pour les critères d’interdiction, l’appel à la violence directement formulé par les organisateurs d’une manifestation est sûrement le plus évident et ne pose pas de problème particulier au regard de l’objectif légitime de protection de l’ordre public (CE, 12 oct. 1983, n° 41410, Commune de Vertou). Mais que dire de toute autre manifestation où « eu égard au fondement et au but de la manifestation et à ses motifs portés à la connaissance du public » (CE, 5 janv. 2007, ord. réf., n° 300311, Association Solidarité des Français), aucune atteinte potentielle à l’ordre public n’est à mettre au crédit des organisateurs ? Parler dans ce cas-là de « manifestation violente » ou « potentiellement violente », c’est assimiler la manifestation organisée et les « troubles par suite d’événements échappant au contrôle des organisateurs », pour reprendre une formule de la Cour européenne (CrEDH, 15 octobre 2015, n° 37553/05, Kudrevičius et autres c. Lituanie, § 94).</p>
					</div>
					<div class="col-5">
						<h4 class="mb-3">Exemple de résultat</h4>
						<ol>
							<li><a href="https://www.courdecassation.fr/decision/5fd9445286e9ed2b4a373fde">Cass., crim., 9 février 2016, n° 14-82234</a></li>
							<li><a href="https://www.legifrance.gouv.fr/ceta/id/CETATEXT000007470086">CAA de Lyon, Formation plénière, 24 juin 2003, n° 98LY01551</a></li>
							<li><a href="https://www.legifrance.gouv.fr/ceta/id/CETATEXT000007689878">CE, 12 oct. 1983, n° 41410</a></li>
							<li><a href="https://www.legifrance.gouv.fr/ceta/id/CETATEXT000018259403">CE, 5 janv. 2007, ord. réf., n° 300311</a></li>
							<li><a href="http://hudoc.echr.coe.int/fre?i=001-158232">CrEDH, 15 octobre 2015, n° 37553/05</a></li>
						</ol>
					</div>
                </div>
            </div>
        </section>

		<!-- Author -->
		<section id="author" class="testimonials">
			<div class="container">
				<h2 class="mb-5 text-center">Concepteur de RegLex</h2>
				<div class="row">
					<div class="col-lg-4 text-center">
						<div class="testimonial-item mx-auto mb-5 mb-lg-0">
							<img class="img-fluid rounded-circle mb-3" src="images/aurey.jpg" alt="Xavier Aurey" />
						</div>
					</div>
					<div class="col-lg-8">
						<div class="mx-auto mb-5 mb-lg-0">
							<h3>Xavier Aurey</h3>
							<p class="font-weight-light mb-0">Docteur en droit, ancien avocat (omission au 1er avril 2025) et ancien maître de conférences à l’Université d’Essex au Royaume-Uni dans le cadre d’un double-diplôme en droit français et anglais. En parallèle, il est membre du <a href="https://www.cliniuqes-juridiques.org" target="_blank">Réseau des cliniques juridiques francophones</a> et travaille au développement de l’enseignement clinique du droit dans les pays francophones. Enfin, programmateur informatique à ses heures perdues, principalement autour des technologies web, il a développé plusieurs sites internet (<a href="https://www.crdh.fr">CRDH - Paris Human Rights</a>, <a href="https://www.fondamentaux.org">Fondamentaux</a>...).</p>
						</div>
					</div>
				</div>
			</div>
		</section>

		<!-- Call to Action-->
        <section class="call-to-action text-white text-center" id="contact">
            <div class="container position-relative">
                <div class="row justify-content-center">
                    <div class="col-xl-9">
                        <h3 class="mb-4">Commentaires et identification des problèmes</h3>
						<p>Pour tout commentaire, merci d'envoyer un email à reglex@fondamentaux.org. Si une jurisprudence n'est pas trouvée, merci de copier-coller le passage incriminé afin que je modifie l'outil.</p>
                    </div>
                </div>
            </div>
        </section>

		<!-- Footer-->
        <footer class="footer bg-light">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 h-100 text-center text-lg-start my-auto">
                        <p class="text-muted small mb-4 mb-lg-0">&copy; Xavier Aurey 2021 - <a href="https://www.fondamentaux.org">fondamentaux.org</a> - Aucun cookie n'est utilisé</p>
                    </div>
                    <div class="col-lg-6 h-100 text-center text-lg-end my-auto">
                        <ul class="list-inline mb-0">
                            <li class="list-inline-item me-4">
                                <a href="https://twitter.com/xaurey"><i class="bi-twitter fs-3"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>

		<div id="loader"></div>
		<script src="https://code.jquery.com/jquery.js"></script>
		<script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
		<script src="js/ajax.js" crossorigin="anonymous"></script>
	</body>
</html>