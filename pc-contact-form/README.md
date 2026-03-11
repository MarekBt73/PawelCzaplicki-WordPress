# PC Contact Form

Customowa wtyczka WordPress dostarczająca bezpieczny formularz kontaktowy bez zapisywania wiadomości w bazie danych.

## Instalacja

To repozytorium nie zawiera `wp-content/`, więc na środowisku WordPress:

1. Skopiuj katalog `pc-contact-form/` do `wp-content/plugins/pc-contact-form/`
2. Aktywuj wtyczkę w panelu WP → Wtyczki

## Konfiguracja

Panel WP → Ustawienia → **PC Contact Form**

- **E-mail administratora**: adres, na który mają przychodzić zgłoszenia
- **Link do polityki (RODO)**: opcjonalny URL wyświetlany przy zgodzie
- **E-mail nadawcy (From)**: adres używany w nagłówku From dla maili z formularza
- **Podpis nadawcy (stopka)**: podpis dodawany w potwierdzeniu dla nadawcy

## Użycie

W treści strony wstaw shortcode:

`[pc_contact_form]`

Formularz zawiera pola:

- Imię (wymagane)
- E-mail (wymagane)
- Telefon (opcjonalnie)
- Treść (wymagana, maks. 300 znaków)
- Zgoda RODO/marketing (wymagana, z opcjonalnym linkiem do polityki)

## Bezpieczeństwo (w skrócie)

- Nonce (`wp_nonce_field`) + obsługa przez `admin-post.php`
- Honeypot + pole timestamp (prosta heurystyka botów)
- Limit wysyłek per IP (transient)
- Brak zapisu wiadomości w bazie

