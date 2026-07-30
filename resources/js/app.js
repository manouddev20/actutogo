import './bootstrap';

//Importation des librairies js

import { createApp } from 'vue'

import VueSweetalert2 from 'vue-sweetalert2';

import 'sweetalert2/dist/sweetalert2.min.css';

import store from './store/index';
 
//Importation, déclaration et chargement des composants inclus dans le header
 
import EconomieHeader from './components/frontoffice/header/economie.vue'
 
import InternationalHeader from './components/frontoffice/header/international.vue'

import PolitiqueHeader from './components/frontoffice/header/politique.vue'

import SocieteHeader from './components/frontoffice/header/societe.vue'

import RubriquesHeader from './components/frontoffice/header/rubriques.vue'

import DiasporaHeader from './components/frontoffice/header/diaspora.vue'

//Importation, déclaration et chargement des composants inclus dans le footer

import NewsletterFooter from './components/frontoffice/footer/newsletter.vue'
 
import CategoryPopularsFooter from './components/frontoffice/footer/categoryPopulars.vue'

import TagsPopularsFooter from './components/frontoffice/footer/tagsPopulars.vue'
 
const app = createApp({})
 
app.component('EconomieHeader', EconomieHeader)

app.component('SocieteHeader', SocieteHeader)

app.component('DiasporaHeader', DiasporaHeader)

app.component('RubriquesHeader', RubriquesHeader)
 
app.component('InternationalHeader', InternationalHeader)

app.component('PolitiqueHeader', PolitiqueHeader)

app.component('NewsletterFooter', NewsletterFooter)

app.component('CategoryPopulars', CategoryPopularsFooter)

app.component('TagsPopulars', TagsPopularsFooter)
 
app.use(VueSweetalert2).use(store).mount('#app')