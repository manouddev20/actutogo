@extends('layouts.base')

@section('title') Toutes les catégories @endsection

@section('content')

    @include('includes.header')

    <main style="margin-top: -20px">
        <!-- =======================Author list START -->
        <section class="py-4">
            <div class="container">
            <!-- Author list title START -->
                <div class="row g-4 pb-4">
                    <div class="col-12">
                        <div class="d-sm-flex justify-content-between align-items-center">
                            <h1 class="mb-sm-0 h2">Toutes les catégories</h1>
                        </div>
                    </div>
                </div>
                <!-- Author list title START -->
                <div class="row g-4">
                    <div class="col-12">
                        <!-- Card START -->
                        <div class="card border">
                            <!-- Card header START -->
                            <div class="card-header border-bottom p-3">
                                <!-- Search and select START -->
                                <div class="row g-3 align-items-center justify-content-between">
                                    <!-- Search bar -->
                                    <div class="col-md-12">
                                        <form class="rounded position-relative" method="GET">
                                            <input class="form-control form-control-lg bg-transparent" name="query" type="search" placeholder="Rechercher une catégorie ..." aria-label="Search" value="{{ old('query') }}">
                                            <!-- 🕵️ Bot trap (caché) -->
                                            <input type="text" name="website" style="display:none">
                                            <!-- ⏱️ Timestamp -->
                                            <input type="hidden" name="form_time" value="{{ time() }}">
                                            <button class="btn bg-transparent border-0 px-2 py-0 position-absolute top-50 end-0 translate-middle-y" type="submit"><i class="fas fa-search fs-6 "></i></button>
                                        </form>
                                    </div>
                                    <!-- Tab buttons -->
                                    @if ($errors->any())
                                        <h6 class="form-text text-danger mt-1">
                                            {{ $errors->first('query') }}
                                        </h6>
                                    @endif

                                </div>
                                <!-- Search and select END -->
                            </div>
                            <!-- Card header END -->

                            <!-- Card body START -->
                            <div class="card-body p-3 pb-0">
                                <!-- Tabs content START -->
                                <div class="tab-content py-0 my-0">
                                    <div class="tab-pane fade show active" id="nav-grid-tab">
                                        <div class="row g-4">

                                            @foreach ($categories as $category)
                                                <div class="col-md-6 col-xl-4">
                                                    <!-- Category item START -->
                                                    <div class="card border h-100">
                                                        <!-- Card header -->
                                                        <div class="card-header border-bottom p-3">
                                                            <div class="d-flex align-items-center">
                                                                <h5 class="mb-0 ms-3"> {{ $category->name }} </h5>
                                                            </div>
                                                        </div>

                                                        <!-- Card body START -->
                                                        <div class="card-body p-3">
                                                            <p> {{ $category->slug }} </p>

                                                            <!-- Followers and Post -->
                                                            <div class="d-flex text-start align-items-center mt-3">
                                                                <div class="icon-md bg-light text-body rounded-circle flex-shrink-0">
                                                                    <i class="bi bi-file-earmark-text-fill fa-fw"></i>
                                                                </div>
                                                                <div class="ms-2">
                                                                    @if ($category->count_publications == 0)
                                                                        <h5 class="mb-0">Aucune publication</h5>
                                                                    @else
                                                                        <h5 class="mb-0"> {{ $category->count_publications }} </h5>
                                                                        <h6 class="mb-0 fw-light">publications totales</h6>
                                                                    @endif

                                                                </div>
                                                            </div>

                                                        </div>
                                                        <!-- Card body END -->

                                                        <!-- Card footer -->
                                                        <div class="card-footer border-top text-center p-3">
                                                            <a href="/{{$category->slug}}" class="btn btn-primary-soft w-100 mb-0">Voir ces publications</a>
                                                        </div>
                                                    </div>
                                                    <!-- Category item END -->
                                                </div>
                                            @endforeach
                                        </div> <!-- Row END -->
                                    </div>
                                    <!-- Tabs content item END -->

                                </div>
                                <!-- Tabs content END -->
                            </div>
                            <!-- Card body END -->

                            <!-- Card Footer START -->
                            <div class="card-footer p-3">
                                <!-- Pagination START -->
                                <div class="d-sm-flex justify-content-sm-between align-items-sm-center">
                                    <!-- Content -->

                                    <!-- Pagination -->
                                    {{ $categories->links() }}
                                </div>
                                <!-- Pagination END -->
                            </div>
                            <!-- Card Footer END -->
                        </div>
                        <!-- Card END -->
                    </div>


                </div>
            </div>
        </section>
        <!-- =======================Author list END -->
    </main>

    @include('includes.footer')

@endsection
