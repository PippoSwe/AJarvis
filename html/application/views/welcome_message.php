<?php
	
?>
<!DOCTYPE html>
<html lang="it">
<head>
	<meta charset="UTF-8">
	<title>Welcome to AJarvis</title>
	<!--#include virtual="/assets/tpl/meta.html"-->
	<meta charset="utf-8">
        <!-- Bootstap CSS -->
        <link rel="stylesheet" href="../../assets/css/bootstrap.min.css" >
        <link rel="stylesheet" href="../../assets/css/pippo.css">
	 	<link rel="stylesheet" href="../../assets/css/ionicons.min.css">
	 	<link rel="stylesheet" href="../../assets/css/font-awesome.min.css">


        <!--  JS Popper jQuery -->
        <script src="../../assets/js/jquery-3.3.1.min.js"></script>
        <!--Mustache-->
        <script src="../../assets/js/gui/popper.min.js" ></script>
        <script src="../../assets/js/gui/bootstrap.min.js"></script>

        <noscript>
            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">Attenzione!</h4>
                <p>Per avere a disposizione tutte le funzionalità di questo sito è necessario abilitare Javascript, prima di continuare.<br/> Grazie, </p>
                <strong class="ml-5">Il Team Pippo.Swe</strong>
            </div>
        </noscript>
	<script type="text/javascript">
		$(document).ready(function() {
			$.ajax({
	            url: "/api/project/",
	            type: "GET",
	            success: function(data) {
	                if(data.length < 0)
	                	$("#alert-no-project").html("<div class='alert alert-success alert-dismissible fade show' role='alert'><h4 class='alert-heading'>Benvenuto!</h4><p>Sembra che sia la prima volta che accedi ad AJarvis</p><hr><p class='mb-0'>La prima cosa da fare è creare un progetto, altrimenti non è possibile registrare uno standup. <a class='nav-link' href='/static/project/index.html'>Clicca qui per inserire un progetto</a></p><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>");
	            },
	            error: function(xhr, status, text) {
	                alert("nonva");
	            }
	        });
		});
	</script>
</head>
<body class="bodyIndex">
<div class="container">
	<div class="text-center">
		<img class="img-logo" src="../../assets/images/logo.png" alt="AJarvis">
	</div>
	<div class="timeline">
		<div class="entry">
			<div class="title">
				<h3>Configura AJarvis</h3>
			</div>
			<div class="body-custom">
				<p>Se è la prima volta che accedi configura l'applicazione</p>
				<a id="btn-insert" class="btn btn-success" href="#" role="button"><i class="fa fa-wrench mr-2" aria-hidden="true"></i>Configura AJarvis</a>
			</div>
		</div>
		<div class="entry">
			<div class="title">
				<h3>Crea progetto</h3>
			</div>
			<div class="body-custom">
				<p>Se è la prima volta che accedi crea il tuo primo progetto</p>
				<a id="btn-insert" class="btn btn-success" href="#" role="button"><i class="ion-ios-plus-outline mr-2"></i>Nuovo progetto</a>
			</div>
		</div>
		<div class="entry">
			<div class="title">
				<h3>Aggiungi membri</h3>
			</div>
			<div class="body-custom">
				<p>Aggiungi i membri dell'azienda per una migliore gestione dei tuoi progetti</p>
				<a id="btn-insert" class="btn btn-success" href="#" role="button"><i class="ion-ios-plus-outline mr-2"></i>Aggiungi membri</a>
			</div>
		</div>
		
	</div>
	
	<div id="alert-no-project"></div>
</div>
	
	<!--<footer class="footer">
			<div class="container">
					<span class="text-muted">inserire le tecnologie utilizzate</span>
			</div>
	</footer>-->
</body>
</html>