@extends('layouts.base')

@section('title') Rechercher une publication @endsection

@section('content')


    @include('includes.header')

        <!-- **************** MAIN CONTENT START **************** -->
        <main style="margin-top: -30px; margin-bottom: -30px">

            @if ($search==false)

                <!-- =======================Inner intro START -->
                <section class="pt-3">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-9 mx-auto text-center py-4">
                                
                    
                                <h2 class="display-5">Rechercher une publication</h2>

                                <!-- Search -->
                                <div class="row">
                                    <div class="col-sm-12 col-md-6 col-lg-10 mx-auto">
                                        <form class="input-group mt-4" method="GET">
                                            <input class="form-control form-control-lg border-success" name="query" type="search" placeholder="Effectuez une recherche..." aria-label="Search" value="{{ old('query') }}">
                                            <!-- 🕵️ Bot trap (caché) -->
                                            <input type="text" name="website" style="display:none">
                                            <!-- ⏱️ Timestamp -->
                                            <input type="hidden" name="form_time" value="{{ time() }}">
                                            <button class="btn btn-success btn-lg m-0" type="submit">
                                            <i class="bi bi-search fs-4"> </i>
                                        </form>
                                       
                                    </div>
                                </div>
                                <br>
                                @if ($errors->any())
                                    <h6 class="form-text text-danger mt-1">
                                        {{ $errors->first('query') }}
                                    </h6>
                                @endif
                            </div>
                        </div>
                    </div>
                </section>

                <section class="position-relative pt-0">
                    <div class="container">
                        <div class="row">
                            <!-- Main Post START -->
                            <div class="col-lg-9 mx-auto">

                                <h2><i class="bi bi-symmetry-vertical me-2"></i>Publications à la une</h2>
                    
                                @foreach ($articles as $article)

                                <!-- Card item START -->
                                <div class="card border rounded-3 up-hover p-4 mb-2">
                                    <div class="row g-3">
                                        @if($article->image_cover_url)
                                            <div class="col-sm-8">
                                                <!-- Categories -->
                                                <a href="/{{ $article->category_slug }}" class="badge text-bg-danger mb-2"><i class="fas fa-circle me-2 small fw-bold"></i> {{ $article->category_name }} </a>

                                                <!-- Title -->
                                                <h5 class="card-title">
                                                    <a href="/{{ $article->slug }}" class="btn-link text-reset stretched-link">{!! $article->title !!}</a>
                                                </h5>

                                                <div>
                                                    {!! $article->truncate_content !!}
                                                </div>
                                                <!-- Card info -->
                                                <ul class="nav nav-divider align-items-center text-uppercase small mt-2">
                                                    <li class="nav-item">
                                                        <div class="nav-link">
                                                            <div class="d-flex align-items-center position-relative">
                                                                <span class="ms-3"><a href="/authors/{{ $article->author_slug }}" class="stretched-link text-reset btn-link"> {{ strtoupper($article->author_name) }} </a></span>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li class="nav-item">{{ date('d/m/Y', strtotime($article->date_publish)) }}</li>
                                                </ul>
                                            </div>
                                            <!-- Image -->
                                            <div class="col-sm-4">
                                                <img class="rounded-3" src="{{ $article->image_cover_url }}" style="height: 220px; width: 700px ;object-fit:cover" alt="{{ $article->title }}">
                                            </div>
                                        @else
                                            <div class="col-sm-12">
                                                <!-- Categories -->
                                                <a href="/{{ $article->category_slug }}" class="badge text-bg-danger mb-2"><i class="fas fa-circle me-2 small fw-bold"></i> {{ $article->category_name }} </a>

                                                <!-- Title -->
                                                <h5 class="card-title">
                                                    <a href="/{{ $article->slug }}" class="btn-link text-reset stretched-link">{!! $article->title !!}</a>
                                                </h5>

                                                <div>
                                                    {!! $article->truncate_content !!}
                                                </div>
                                                <!-- Card info -->
                                                <ul class="nav nav-divider align-items-center text-uppercase small mt-2">
                                                    <li class="nav-item">
                                                        <div class="nav-link">
                                                            <div class="d-flex align-items-center position-relative">
                                                                <span class="ms-3"><a href="/authors/{{ $article->author_slug }}" class="stretched-link text-reset btn-link"> {{ strtoupper($article->author_name) }} </a></span>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li class="nav-item">{{ date('d/m/Y', strtotime($article->date_publish)) }}</li>
                                                </ul>
                                            </div> 
                                        @endif
                                    </div>
                                </div>
                                <!-- Card item END -->

                                @endforeach
                     
                            </div>
                        </div>
                    </div>
                </section>

            @else

                <!-- =======================Inner intro START -->
                <section class="pt-3">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-9 mx-auto text-center py-4">
                                <span>Résultats de recherche pour</span>
                                <h2 class="display-5"> {{ $search }} </h2>
                                <span class="lead"> @if ($count == 0) Aucune publication trouvée @elseif ($count == 1) {{ $count }} publication trouvée @else {{ $count }} publications trouvées @endif </span>
                                <!-- Search -->
                                <div class="row">
                                    <div class="col-sm-12 col-md-6 col-lg-10 mx-auto">
                                        <form class="input-group mt-4" method="GET">
                                            <input class="form-control form-control-lg border-success" type="search" placeholder="Effectuez une recherche..." aria-label="Search">
                                            <button class="btn btn-success btn-lg m-0" type="submit">
                                            <i class="bi bi-search fs-4"> </i>
                                        </form>
                                        <br>
                                        
                                    </div>
                                </div>
                                @if ($errors->any())
                                    <h6 class="form-text text-danger mt-1">
                                        {{ $errors->first('query') }}
                                    </h6>
                                @endif
                            </div>
                        </div>
                    </div>
                </section>

                <section class="position-relative pt-0">
                    <div class="container">
                        <div class="row">
                            <!-- Main Post START -->
                            <div class="col-lg-9 mx-auto">

                                <h2><i class="bi bi-symmetry-vertical me-2"></i>@if ($count == 0) Aucune publication trouvée @elseif ($count == 1) {{ $count }} publication trouvée @else {{ $count }} publications trouvées @endif </h2>
                    
                                @if ($count !== 0)
                                    @foreach ($articles as $article)

                                        <!-- Card item START -->
                                        <div class="card border rounded-3 up-hover p-4 mb-2">
                                            <div class="row g-3">
                                                @if($article->image_cover_url)
                                                    <div class="col-sm-8">
                                                        <!-- Categories -->
                                                        <a href="/{{ $article->category_slug }}" class="badge text-bg-danger mb-2"><i class="fas fa-circle me-2 small fw-bold"></i> {{ $article->category_name }} </a>
        
                                                        <!-- Title -->
                                                        <h5 class="card-title">
                                                            <a href="/{{ $article->slug }}" class="btn-link text-reset stretched-link">{!! $article->title !!}</a>
                                                        </h5>
        
                                                        <div>
                                                            {!! $article->truncate_content !!}
                                                        </div>
                                                        <!-- Card info -->
                                                        <ul class="nav nav-divider align-items-center text-uppercase small mt-2">
                                                            <li class="nav-item">
                                                                <div class="nav-link">
                                                                    <div class="d-flex align-items-center position-relative">
                                                                        <span class="ms-3"><a href="/authors/{{ $article->author_slug }}" class="stretched-link text-reset btn-link"> {{ strtoupper($article->author_name) }} </a></span>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                            <li class="nav-item">{{ date('d/m/Y', strtotime($article->date_publish)) }}</li>
                                                        </ul>
                                                    </div>
                                                    <!-- Image -->
                                                    <div class="col-sm-4">
                                                        <img class="rounded-3" src="{{ $article->image_cover_url }}" style="height: 220px; width: 700px ;object-fit:cover" alt="{{ $article->title }}">
                                                    </div>
                                                @else
                                                    <div class="col-sm-12">
                                                        <!-- Categories -->
                                                        <a href="/{{ $article->category_slug }}" class="badge text-bg-danger mb-2"><i class="fas fa-circle me-2 small fw-bold"></i> {{ $article->category_name }} </a>
        
                                                        <!-- Title -->
                                                        <h5 class="card-title">
                                                            <a href="/{{ $article->slug }}" class="btn-link text-reset stretched-link">{!! $article->title !!}</a>
                                                        </h5>
        
                                                        <div>
                                                            {!! $article->truncate_content !!}
                                                        </div>
                                                        <!-- Card info -->
                                                        <ul class="nav nav-divider align-items-center text-uppercase small mt-2">
                                                            <li class="nav-item">
                                                                <div class="nav-link">
                                                                    <div class="d-flex align-items-center position-relative">
                                                                        <span class="ms-3"><a href="/authors/{{ $article->author_slug }}" class="stretched-link text-reset btn-link"> {{ strtoupper($article->author_name) }} </a></span>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                            <li class="nav-item">{{ date('d/m/Y', strtotime($article->date_publish)) }}</li>
                                                        </ul>
                                                    </div> 
                                                @endif
                                            </div>
                                        </div>
                                        <!-- Card item END -->

                                    @endforeach
                        
                                @else
                                <section class="overflow-hidden">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-md-9 text-center mx-auto my-0 my-md-5 py-0 py-lg-5 position-relative z-index-9">
                                                <!-- SVG shape START -->
                                                <figure class="position-absolute top-50 start-50 translate-middle opacity-7 z-index-n9">
                                                    <svg width="650" height="379" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"  viewBox="0 0 510 297">
                                                        <g>
                                                        <path class="fill-primary opacity-1" d="M121,147.4c0,6-4.8,10.8-10.8,10.8H47.6c-6,0-10.8-4.8-10.8-10.8v-11.5c0-6,4.8-10.8,10.8-10.8h62.6
                                                        c6,0,10.8,4.8,10.8,10.8V147.4z"/>
                                                        <path class="fill-primary opacity-1" d="M179.4,90.2c0,6-4.8,10.8-10.8,10.8h-62.6c-6,0-10.8-4.8-10.8-10.8V78.7c0-6,4.8-10.8,10.8-10.8h62.6
                                                        c6,0,10.8,4.8,10.8,10.8V90.2z"/>
                                                        <path class="fill-primary opacity-1" d="M459.1,26.3c0,6-4.8,10.8-10.8,10.8h-62.6c-6,0-10.8-4.8-10.8-10.8V14.8c0-6,4.8-10.8,10.8-10.8h62.6
                                                        c6,0,10.8,4.8,10.8,10.8V26.3z"/>
                                                        <path class="fill-primary opacity-1" d="M422.1,66.9c0,6-4.8,10.8-10.8,10.8h-62.6c-6,0-10.8-4.8-10.8-10.8V55.3c0-6,4.8-10.8,10.8-10.8h62.6
                                                        c6,0,10.8,4.8,10.8,10.8V66.9z"/>
                                                        <path class="fill-primary opacity-1" d="M275.8,282.6c0,5.9-4.8,10.8-10.8,10.8h-62.6c-6,0-10.8-4.8-10.8-10.8v-11.5c0-6,4.8-10.8,10.8-10.8h62.6
                                                        c6,0,10.8,4.8,10.8,10.8V282.6z"/>
                                                        <path class="fill-primary opacity-1" d="M87.7,42.9c0,5.9-4.8,10.8-10.8,10.8H14.3c-6,0-10.8-4.8-10.8-10.8V31.4c0-6,4.8-10.8,10.8-10.8h62.6
                                                        c6,0,10.8,4.8,10.8,10.8V42.9z"/>
                                                        <path class="fill-primary opacity-1" d="M505.9,123.4c0,6-4.8,10.8-10.8,10.8h-62.6c-6,0-10.8-4.8-10.8-10.8v-11.5c0-6,4.8-10.8,10.8-10.8h62.6
                                                        c6,0,10.8,4.8,10.8,10.8V123.4z"/>
                                                        <path class="fill-primary opacity-1" d="M482.5,204.9c0,5.9-4.8,10.8-10.8,10.8h-62.6c-6,0-10.8-4.8-10.8-10.8v-11.5c0-6,4.8-10.8,10.8-10.8h62.6
                                                        c5.9,0,10.8,4.8,10.8,10.8V204.9z"/>
                                                        <path class="fill-primary opacity-1" d="M408.3,258.8c0,5.9-4.8,10.8-10.8,10.8H335c-6,0-10.8-4.8-10.8-10.8v-11.5c0-6,4.8-10.8,10.8-10.8h62.6
                                                        c6,0,10.8,4.8,10.8,10.8V258.8z"/>
                                                        <path class="fill-primary opacity-1" d="M147,252.5c0,5.9-4.8,10.8-10.8,10.8H73.6c-6,0-10.8-4.8-10.8-10.8V241c0-5.9,4.8-10.8,10.8-10.8h62.6
                                                        c6,0,10.8,4.8,10.8,10.8V252.5z"/>
                                                        </g>
                                                    </svg>
                                                </figure>
                                                <h1 class="display-1 text-primary">Aucun</h1>
                                                <p>Aucune publication n'a été trouvée dans cette recherche</p>
                                                <a href="/" class="btn btn-danger-soft mt-3"><i class="fas fa-long-arrow-alt-left me-3"></i>Page d'acceuil</a>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                    
                                @endif

                            </div>
                
                        </div>
                    </div>
                </section>

            @endif
        </main>

    @include('includes.footer')

@endsection
