@extends('layouts.base')

@section('title') Accueil @endsection

@section('content')

    @include('includes.header')

    <main>
         <section class="py-0">
            <div class="container"> 
                @include('sectionHomePage.alaUne')
            </div>
            <div class="container">  
                <togoactualite-home-page></togoactualite-home-page>
            </div>
            <div class="container">  
                <politique-home-page></politique-home-page>
            </div>
            <div class="container">  
                <education-home-page></education-home-page>
            </div>
            <div class="container">  
                <societe-home-page></societe-home-page>
            </div>
            <div class="container">
                <div class=" row mt-3"> 
                    <opinion-home-page></opinion-home-page>
                    <faits-divers-home-page></faits-divers-home-page>
                </div>
            </div>
            @include('sectionHomePage.touteLActualite')
        </section>
    </main>
    <br>
    @include('includes.footer')

@endsection
