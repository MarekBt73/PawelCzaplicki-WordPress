## TASK PLAN – wdrożenie pawelczaplicki.com (WordPress)

**Status procesu (aktualizacja: 2025-03-11)**  
Sekcje 1–2 i większość sekcji 3 są ukończone: motyw bazowy z Tailwindem, header/footer, front-page (rozbudowana), page.php, szablony /o-mnie, /badanie, /test oraz landing Protokół 17:00. Do zrobienia: kolejne landingi (wzorzec jest), szablon video + /dziekujemy, grafika (czapli.png), integracje (Calendly, Frontlead, MailerLite), konfiguracja SEO na środowisku, testy.

---

### 1. Środowisko / meta
- [x] Środowisko na serwerze (WordPress, PHP 8+, SSL) – **dostarczone poza tym projektem**
- [x] Określenie celu strony i oferty (plan z `plan strony pawelczaplicki.com.*`)

### 2. Motyw bazowy
- [x] Utworzenie katalogu motywu `theme-pawelczaplicki` w repo lokalnym
- [x] Dodanie pliku `style.css` z nagłówkiem motywu
- [x] Dodanie `functions.php` z:
  - [x] `add_theme_support( 'title-tag' )`
  - [x] rejestracją menu `primary` i `footer`
  - [x] enqueue stylów `tailwind.css`, `fonts.css` i `main.css`
- [x] Struktura katalogów `assets/css`, `assets/fonts`, `assets/img`
- [x] Podstawowa typografia i kolory (Mona Sans, #E7411D) w `assets/css/main.css`
- [x] Definicje `@font-face` dla Mona Sans w `assets/css/fonts.css`

### 3. Layout i szablony stron
- [x] `header.php` z logo Czaplicki’, `wp_head()` i menu głównym
- [x] `footer.php` ze stopką, menu w stopce i `wp_footer()`
- [x] `front-page.php`:
  - [x] Hero z killer speech i CTA „Umów rozmowę strategiczną”
  - [x] Sekcja „Dla kogo” / „W czym mogę pomóc” (rozszerzona o: liczby, projekty, doświadczenie, trzy obszary pomocy, kontakt)
- [x] Uniwersalny szablon `page.php` dla zwykłych stron
- [x] Dedykowany szablon strony `/o-mnie`
- [x] Dedykowany szablon `/badanie` (opis RMP + CTA → Calendly)
- [x] Dedykowany szablon `/test` z osadzoną ankietą Frontlead (obsługa przez treść strony)
- [x] Landing page: `page-protokol-17.php` (Protokół 17:00™) – wzorzec do kolejnych landingów
- [ ] Ewentualne kolejne landingi (template part / pattern na bazie Protokół 17)
- [ ] Szablon strony video + strona podziękowania (`/dziekujemy`)

### 4. Grafika i styl inspirowany `czapli.png`
- [ ] Dodanie tła/elementów graficznych (linie, akcenty) do `assets/img`
- [ ] Wpięcie motywów graficznych w sekcje hero i wybrane bloki

### 5. Integracje
- [ ] Wpięcie linków/embeddów Calendly w CTA (główna, badanie, video, landingi)
- [ ] Wpięcie kodu ankiety Frontlead w szablonie `/test`
- [ ] Wpięcie formularza MailerLite (stopka / strona newslettera)

### 6. SEO – plugin `SEO Custom NewLifeColor`
- [x] Analiza pluginu `seo-costom-newlifecolor` (wymagania, funkcje)
- [ ] Podpięcie pluginu na środowisku WP (katalog `wp-content/plugins`)
- [ ] Konfiguracja danych firmy i domyślnych ustawień SEO
- [ ] Ustalenie propozycji meta tytułów/opisów dla kluczowych stron

### 7. Testy i wydajność (po stronie środowiska)
- [ ] Sprawdzenie poprawności działania szablonów na desktop i mobile
- [ ] Test ścieżek użytkownika (główna → CTA, badanie, test, landingi, video)
- [ ] Podstawowe testy Lighthouse / PageSpeed

