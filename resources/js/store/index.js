import { createStore } from "vuex";

//Importation des modules gérant les données contenu dans l'entête de page

import economieModule from './modules/frontoffice/header/economie'

import internationalModule from './modules/frontoffice/header/international'

import societeModule from './modules/frontoffice/header/societe'

import politiqueModule from './modules/frontoffice/header/politique'

import rubriquesModule from './modules/frontoffice/header/rubriques'
  
import diasporaModule from './modules/frontoffice/header/diaspora'  

//Importation des modules gérant les données contenu dans le pied de page

import newsletterModule from './modules/frontoffice/footer/newsletter'

import articlesPopularsModule from './modules/frontoffice/footer/articlesPopulars'

import categoryPopularsModule from './modules/frontoffice/footer/categoryPopulars'

import tagsPopularsModule from './modules/frontoffice/footer/tagsPopulars' 
 
const store = createStore({
    modules:{

        //Déclaration des modules gérant les données contenu dans l'entête de page

        economie: economieModule,
        international: internationalModule,
        societe: societeModule,
        politique: politiqueModule,
        rubriques: rubriquesModule, 
        diaspora: diasporaModule, 

        //Déclaration des modules gérant les données contenu dans le pied de page

        newsletter: newsletterModule,
        articlesPopulars: articlesPopularsModule,
        categoryPopulars: categoryPopularsModule,
        tagsPopulars: tagsPopularsModule
    }
  });

  export default store;
