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
<body>
	<nav class="navbar navbar-dark bg-dark navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<a class="navbar-brand" href="#home">AJarvis</a>
			</div><!--
			<button class="navbar-toggler hidden-md-up pull-xs-right" data-target="#collapsenav" data-toggle="collapse" type="button">☰	</button>-->
			<!--<div class="collapse navbar-collapse" id="navbarSupportedContent">-->
			<div class="nav navbar" id="navbarSupportedContent">
				<ul class="list-inline">
					<li class="list-inline-item"><a class="nav-link" href="/static/recorder/index.html">Registrazione</a></li>
					<li class="list-inline-item"><a class="nav-link" href="/static/project/index.html">Progetti</a></li>
					<li class="list-inline-item"><a class="nav-link" href="/static/member/index.html">Membri</a></li>
					<li class="list-inline-item"><a class="nav-link" href="/static/keyword/index.html">Keyword</a></li>
					<li class="list-inline-item"><a class="nav-link" href="/static/config/index.html">Configurazione</a></li>
				</ul>
			</div>
		</div>
	</nav>
	<div id="alert-no-project">
	</div>
	<div class="bg-danger w-100">descrizione ajarvis</div>
	<footer class="footer">    
	    <div class="container">
	        <span class="text-muted">inserire le tecnologie utilizzate</span>
	    </div>

	</footer>
</body>
<style type="text/css">
	.footer {
        position: absolute;
        bottom: 0;
        background-color: #000;
        width: 100%;
        height: 80px;
        line-height: 80px;
    }
</style>
</html>