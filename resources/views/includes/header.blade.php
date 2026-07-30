<!-- Offcanvas START -->

<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasMenu">
	<div class="offcanvas-header justify-content-end">
		<button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
	</div>
	<div class="offcanvas-body d-flex flex-column pt-0">
		<div>
			<a class="navbar-brand" href="/">
				<img class="navbar-brand-item light-mode-item" src="/assets/images/logo.png" style="height: 85px; width: 185px; object-fit: cover;" alt="logo togoactualite">
			</a> <br>
			<!-- Nav START -->
			<ul class="nav d-block flex-column my-4">
				<li class="nav-item h5">
					<a class="nav-link" href="/">Accueil</a>
				</li>
				<li class="nav-item h5">
					<a class="nav-link" href="/about">A propos</a>
				</li>
                <li class="nav-item h5">
					<a class="nav-link" href="/privacy">Confidentialité</a>
				</li>
                <li class="nav-item h5">
					<a class="nav-link" href="/terms">Conditions d'utilisations</a>
				</li>
				<li class="nav-item h5">
					<a class="nav-link" href="/contact">Contactez Nous</a>
				</li>
				<li class="nav-item h5">
                    <off-canvas></off-canvas>
                </li>
 
                <li>
                    <transfer></transfer>
                </li>
			</ul>
			<!-- Nav END -->
		</div>
		<div class="mt-auto pb-3">
			<!-- Address -->
				<p class="text-body mb-2 fw-bold">
                    <b><u><a href="https://togoactu.com">TogoActu</a></u></b>, votre nouvelle source d’info moderne pour tout savoir sur le Togo et l’actualité mondiale.
                    Restez informé en temps réel avec des contenus <b>fiables</b>, <b>clairs</b> et <b>accessibles</b> à tout moment.
                </p>
				<p>Email: <a href="mailto:contact@togoactualite.com" class="text-reset"><u>contact@togoactualite.com</u></a></p>
				<p>Heures de services:
					Lundi à Vendredi de  9:30am à 6:30 pm
					<br>
				</p>
		</div>
	</div>
</div>
<!-- Offcanvas END -->
<header>
<!-- Navbar top -->
<div class="navbar-top d-none d-lg-block">
	<div class="container">
		<div class="row d-flex align-items-center my-2">
			<!-- Top bar left -->
			<div class="col-sm-9 d-sm-flex align-items-center">
				<!-- Title -->
				<div class="me-3">
					<span class="badge bg-primary p-2 px-3 text-white">Tendances:</span>
				</div>
				<!-- Slider -->
				<div class="tiny-slider arrow-end arrow-xs arrow-bordered arrow-round arrow-md-none">
					<div class="tiny-slider-inner"
						data-autoplay="true"
						data-hoverpause="true"
						data-gutter="0"
						data-arrow="true"
						data-dots="false"
						data-items="1">
						<!-- Slider items -->
						@for ($i=0; $i<= count(tendances()) - 1; $i++ )
							<div> <a href="/{{ tendances()[$i]['slug'] }}" class="text-reset btn-link">{!! tendances()[$i]['title_truncate'] !!}</a></div> 
						@endfor
					</div>
				</div>
			</div>
	
			<!-- Top bar right -->
			<div class="col-sm-3">
				<ul class="list-inline mb-0 text-center text-sm-end">
					<li class="list-inline-item">
						<span>{{ \Carbon\Carbon::now()->isoFormat('LL') }}</span>
					</li> 
				</ul>
			</div>
		</div>
		<!-- Divider -->
		<div class="border-bottom border-2 border-primary opacity-1"></div>
	</div>
</div>
<div class="container">
	<div class="d-sm-flex justify-content-sm-between align-items-sm-center my-2">
		<!-- Logo START -->
		<div class="container">
			<div class="row g-4 mt-2">
				<div class="d-sm-flex justify-content-sm-between align-items-sm-center my-2">
				<!-- Logo START -->
				<a class="navbar-brand d-block" href="/">
					<img class="navbar-brand-item light-mode-item" src="/assets/images/logo.png" alt="logo togoactualite" style="height: 85px; width: 185px; object-fit: cover;">

				</a>
				 
			</div>
			 
			</div>
		</div>
	</div>
</div>
<!-- Navbar START -->
<div class="navbar-sticky header-static">
	<nav class="navbar navbar-light navbar-expand-lg">
		<div class="container">
			<div class="w-100 bg-light d-flex">

				<!-- Responsive navbar toggler -->
				<button class="navbar-toggler me-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
					<span class="text-muted h6 ps-3">MENU</span>
					<span class="navbar-toggler-icon"></span>
				</button>

				<!-- Main navbar START -->
				<div class="collapse navbar-collapse" id="navbarCollapse">
					<ul class="navbar-nav navbar-nav-scroll navbar-lh-sm" >

						<li class="nav-item">
							<a style="font-size: 12px" class="nav-link  " href="/"  id="homeMenu">ACCUEIL</a>
						</li>

						<!-- Nav item 2 Pages -->
					<li class="nav-item dropdown dropdown-fullwidth">
						<a style="font-size: 12px" class="nav-link dropdown-toggle" href="#" id="pagesMenu" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">ECONOMIE</a>
						<ul class="dropdown-menu" aria-labelledby="pagesMenu">
                            <economie-header></economie-header>
						</ul>
					</li>

                    <li class="nav-item dropdown dropdown-fullwidth">
						<a style="font-size: 12px" class="nav-link dropdown-toggle" href="#" id="pagesMenu" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">INTERNATIONAL</a>
						<ul class="dropdown-menu" aria-labelledby="pagesMenu">
                            <international-header></international-header>
						</ul>
					</li>

                    <li class="nav-item dropdown dropdown-fullwidth">
						<a style="font-size: 12px" class="nav-link dropdown-toggle" href="#" id="pagesMenu" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">POLITIQUE</a>
						<ul class="dropdown-menu" aria-labelledby="pagesMenu">
                            <politique-header></politique-header>
						</ul>
					</li>

					<li class="nav-item dropdown dropdown-fullwidth">
						<a style="font-size: 12px" class="nav-link dropdown-toggle" href="#" id="pagesMenu" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">SOCIETE</a>
						<ul class="dropdown-menu" aria-labelledby="pagesMenu">
                            <societe-header></societe-header>
						</ul>
					</li>

					<li class="nav-item dropdown dropdown-fullwidth">
						<a style="font-size: 12px" class="nav-link dropdown-toggle" href="#" id="pagesMenu" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">DIASPORA</a>
						<ul class="dropdown-menu" aria-labelledby="pagesMenu">
                            <diaspora-header></diaspora-header>
						</ul>
					</li>

					<li class="nav-item dropdown dropdown-fullwidth">
						<a style="font-size: 12px" class="nav-link dropdown-toggle" href="#" id="pagesMenu" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">RUBRIQUES</a>
						<ul class="dropdown-menu" aria-labelledby="pagesMenu">
                            <rubriques-header></rubriques-header>
						</ul>
					</li>
						 
					</ul>
				</div>
				<!-- Main navbar END -->

				<!-- Nav right START -->
				<div class="nav flex-nowrap align-items-center me-2">

					<in-second-menu></in-second-menu>

					<div class="nav-item dropdown nav-search dropdown-toggle-icon-none">
						<a class="nav-link pe-0 dropdown-toggle" role="button" href="#" id="navSearch" data-bs-toggle="dropdown" aria-expanded="false">
							<i class="bi bi-search fs-4"> </i>
						</a>
						<div class="dropdown-menu dropdown-menu-end shadow rounded p-2" aria-labelledby="navSearch">
						<form class="input-group" action="/search-posts" method="GET">
							<input class="form-control border-success" name="query" type="search" placeholder="Recherche..." aria-label="Search">
							<!-- 🕵️ Bot trap (caché) -->
							<input type="text" name="website" style="display:none">
							<!-- ⏱️ Timestamp -->
							<input type="hidden" name="form_time" value="{{ time() }}">
							<button class="btn btn-success m-0" type="submit"><i class="bi bi-search"> </i></button>
						</form>
						</div>
					</div>


					<!-- Offcanvas menu toggler -->
					<div class="nav-item">
						<a style="font-size: 12px" class="nav-link  pe-0" data-bs-toggle="offcanvas" href="#offcanvasMenu" role="button" aria-controls="offcanvasMenu">
							<i class="bi bi-text-right rtl-flip fs-2" data-bs-target="#offcanvasMenu"> </i>
						</a>
					</div>
				</div>
				<!-- Nav right END -->
			</div>
		</div>
	</nav>
</div>
<!-- Navbar END -->
</header>