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
	 	<link rel="stylesheet" href="../../assets/css/loader.css">


        <!--  JS Popper jQuery -->
        <script src="../../assets/js/jquery-3.3.1.min.js"></script>
        <!--Mustache-->
        <script src="../../assets/js/gui/popper.min.js" ></script>
        <script src="../../assets/js/gui/bootstrap.min.js"></script>
        <!-- pace -->
        <script src="../../assets/js/gui/pace.min.js"></script>

        <noscript>
            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">Attenzione!</h4>
                <p>Per avere a disposizione tutte le funzionalità di questo sito è necessario abilitare Javascript, prima di continuare.<br/> Grazie, </p>
                <strong class="ml-5">Il Team Pippo.Swe</strong>
            </div>
        </noscript>
	<script type="text/javascript">
		$(document).ready(function() {
			var key_set=false;
			var pro_set=false;

			   $.ajax({
				url: "/api/config/read/",
				type: "GET",
				success:function(data){

					if(data.length === 0){
						$("#alert-no-project").html("<div class='alert alert-success alert-dismissible fade show' role='alert'><h4 class='alert-heading'>Benvenuto!</h4><p>Sembra che sia la prima volta che accedi ad AJarvis</p><hr><p class='mb-0'>Configura AJarvis<i class='fa fa-bolt pl-2'></i>.</div>");
					}
					else{
						key_set=true;
						$(".active-nonactive-key").addClass("d-none");
					}

					if(key_set){
						$.ajax({
				            url: "/api/project/",
				            type: "GET",
				            success: function(data) {
				                if(data.length === 0){
				                	$("#alert-no-project").html("<div class='alert alert-success alert-dismissible fade show' role='alert'><h4 class='alert-heading'>Benvenuto!</h4><p>Crea il tuo primo progetto con AJarvis</p><hr><p class='mb-0'>La prima cosa da fare è creare un progetto, altrimenti non è possibile registrare uno standup. </div>");
				                }

				                else{
				                	pro_set=true;
				                	$(".active-nonactive-pro").addClass("d-none");

				                	if(key_set && pro_set){
										$(".time-off").addClass("d-none");
										window.location.replace("/static/home/index.html");
									}
				                }

				            }

				        });
					}else{
						$(".active-nonactive-pro").addClass("d-none");
					}
					
				}

			});

		}); //docready

	</script>


</head>
<body class="bodyIndex">

		<div class="container">
				<div id="overlay"></div>
			<div class="text-center">
				<img class="img-logo" src="../../assets/images/logo.png" alt="AJarvis">
			</div>
			<div id="alert-no-project"></div>
			<div class="timeline time-off">
				<div class="entry entry active-nonactive-key">
					<div class="title">
						<h3>Configura AJarvis</h3>
					</div>
					<div class="body-custom">
						<p>Se è la prima volta che accedi configura l'applicazione</p>
						<a id="btn-insert" class="btn btn-success" href="/static/config/index.html?first_time=true" role="button"><i class="fa fa-wrench mr-2" aria-hidden="true"></i>Configura AJarvis</a>
					</div>
				</div>
				<div class="entry active-nonactive-pro">
					<div class="title">
						<h3>Crea progetto</h3>
					</div>
					<div class="body-custom">
						<p>Se è la prima volta che accedi crea il tuo primo progetto</p>
						<a id="btn-insert" class="btn btn-success" href="/static/project/index.html" role="button"><i class="ion-ios-plus-outline mr-2"></i>Nuovo progetto</a>
					</div>
				</div>
			</div>
		</div>
</body>
</html>