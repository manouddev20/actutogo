<?php
use App\Models\Publication;
  function tendances()
    {
        $articles_count = Publication::where('status', 1)->where("publications.type_publication_id", 1)->count();

        if($articles_count === 0){

            return $articles_count;

        }else{

            
            $tendances = Publication::where('status', 1)->where("publications.type_publication_id", 1)->where("publications.deja_citer", 0)->orderBy('date_publish', 'desc')->take(3)->get();

            return  $tendances;

        }
    }

    
