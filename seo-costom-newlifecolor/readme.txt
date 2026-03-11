=== SEO Custom NewLifeColor ===
Contributors: marekbecht
Tags: seo, schema.org, local business, structured data, json-ld, open graph, twitter cards
Requires at least: 6.0
Tested up to: 6.4
Requires PHP: 8.0
Stable tag: 1.0.0
License: GPL-2.0+
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Zaawansowany plugin SEO z pełnym wsparciem Schema.org dla lokalnych usług. Generuje meta tagi, Open Graph, Twitter Cards oraz strukturalne dane JSON-LD.

== Description ==

**SEO Custom NewLifeColor** to kompleksowe rozwiązanie SEO stworzone specjalnie dla firm świadczących usługi lokalne, takie jak malowanie, projektowanie wnętrz, remonty i inne.

= Główne funkcje =

* **LocalBusiness Schema** - Pełne wsparcie dla danych strukturalnych firmy lokalnej
* **Service Schema** - Oznaczenie usług zgodne ze Schema.org
* **Meta Tagi SEO** - Tytuł, opis, canonical URL
* **Open Graph** - Integracja z Facebook i LinkedIn
* **Twitter Cards** - Wsparcie dla kart Twitter
* **FAQ Schema** - Strony z pytaniami i odpowiedziami
* **Article Schema** - Dla wpisów blogowych
* **ImageGallery Schema** - Auto-detekcja obrazów z treści
* **VideoObject Schema** - Wsparcie dla YouTube i Vimeo
* **BreadcrumbList Schema** - Automatyczne ścieżki nawigacji

= Funkcje panelu administracyjnego =

* Strona ustawień globalnych (Ustawienia → SEO Custom NLC)
* Meta box w edytorze postów/stron z zakładkami:
  * **SEO** - Tytuł, opis, słowo kluczowe, canonical
  * **Schema** - Wybór typu schema, FAQ repeater
  * **Social** - Open Graph i Twitter Card
* Podgląd snippetu Google w czasie rzeczywistym
* Liczniki znaków dla tytułu i opisu
* Media uploader dla logo i obrazów OG

= Bezpieczeństwo =

* Pełna sanityzacja danych wejściowych
* Weryfikacja nonce dla formularzy
* Escapowanie wszystkich outputów
* Sprawdzanie uprawnień użytkownika

== Installation ==

1. Pobierz plugin i rozpakuj do katalogu `/wp-content/plugins/seo-costom-newlifecolor`
2. Aktywuj plugin przez menu 'Wtyczki' w WordPress
3. Przejdź do Ustawienia → SEO Custom NLC i skonfiguruj dane firmy
4. Dodaj meta SEO do swoich stron i wpisów

= Wymagania minimalne =

* WordPress 6.0 lub nowszy
* PHP 8.0 lub nowszy

== Frequently Asked Questions ==

= Jak przetestować Schema.org? =

Po skonfigurowaniu pluginu możesz przetestować dane strukturalne używając:
* [Google Rich Results Test](https://search.google.com/test/rich-results)
* [Schema Markup Validator](https://validator.schema.org/)

= Czy plugin jest kompatybilny z Gutenberg? =

Tak, plugin jest w pełni kompatybilny z edytorem blokowym Gutenberg oraz klasycznym edytorem.

= Jak dodać FAQ do strony? =

1. Edytuj stronę lub wpis
2. W meta boxie SEO Custom NLC wybierz zakładkę "Schema"
3. Wybierz typ Schema: "Strona FAQ"
4. Dodaj pytania i odpowiedzi używając przycisku "+ Dodaj pytanie"

= Jak ustawić godziny otwarcia? =

1. Przejdź do Ustawienia → SEO Custom NLC
2. W sekcji "Dane Firmy" znajdź tabelę "Godziny otwarcia"
3. Zaznacz dni w które firma jest otwarta
4. Ustaw godziny otwarcia i zamknięcia dla każdego dnia

= Jak znaleźć współrzędne GPS? =

1. Otwórz Google Maps
2. Znajdź swoją lokalizację
3. Kliknij prawym przyciskiem myszy
4. Skopiuj współrzędne (pierwszy to latitude, drugi to longitude)

== Changelog ==

= 1.0.0 =
* Pierwsza wersja pluginu
* Wsparcie dla LocalBusiness Schema
* Wsparcie dla Service Schema
* Meta tagi SEO (title, description, canonical)
* Open Graph tags
* Twitter Cards
* FAQ Schema
* Article/BlogPosting Schema
* ImageGallery Schema
* VideoObject Schema (YouTube, Vimeo)
* BreadcrumbList Schema
* Strona ustawień globalnych
* Meta box w edytorze
* Podgląd snippetu Google
* Liczniki znaków
* Media uploader

== Upgrade Notice ==

= 1.0.0 =
Pierwsza wersja pluginu. Bezpieczna instalacja.

== Screenshots ==

1. Strona ustawień globalnych
2. Meta box SEO w edytorze - zakładka SEO
3. Meta box SEO w edytorze - zakładka Schema
4. Meta box SEO w edytorze - zakładka Social
5. Podgląd snippetu Google
6. Przykładowy output JSON-LD

== Developer Documentation ==

= Filtry =

Plugin udostępnia następujące filtry dla deweloperów:

* `seo_nlc_meta_title` - Modyfikacja tytułu SEO
* `seo_nlc_meta_description` - Modyfikacja opisu meta
* `seo_nlc_schema_graph` - Modyfikacja @graph Schema.org
* `seo_nlc_og_tags` - Modyfikacja tagów Open Graph
* `seo_nlc_twitter_tags` - Modyfikacja tagów Twitter
* `seo_nlc_supported_post_types` - Dodanie własnych post types
* `seo_nlc_should_render` - Kontrola renderowania SEO
* `seo_nlc_local_business_schema` - Modyfikacja LocalBusiness
* `seo_nlc_service_schema` - Modyfikacja Service
* `seo_nlc_article_schema` - Modyfikacja Article

= Przykład użycia filtra =

`
add_filter( 'seo_nlc_meta_title', function( $title, $post_id ) {
    // Dodaj sufiks do tytułu
    return $title . ' - Najlepsza firma w mieście';
}, 10, 2 );
`

= Namespace =

Wszystkie klasy pluginu używają namespace `SeoCustomNLC`.

= Stałe =

* `SEO_NLC_VERSION` - Wersja pluginu
* `SEO_NLC_PATH` - Ścieżka do katalogu pluginu
* `SEO_NLC_URL` - URL katalogu pluginu

== Support ==

W przypadku problemów lub pytań:
* Utwórz issue na GitHub
* Skontaktuj się przez stronę NewLifeColor.pl
