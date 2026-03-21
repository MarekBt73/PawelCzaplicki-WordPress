<?php
/**
 * Template Name: Polityka prywatności
 * Description: Strona z polityką prywatności w stylu Protokół 17:00™, z użyciem bieżącego headera i footera motywu.
 *
 * @package PawelCzaplicki
 */

declare(strict_types=1);

get_header();
?>

<section class="pt-32 md:pt-40 pb-12 bg-slate-50 border-b border-gray-200">
	<div class="max-w-4xl mx-auto px-6 md:px-12">
		<p class="text-sm font-bold uppercase tracking-[0.15em] mb-4 policy-kicker">
			<?php esc_html_e( 'Informacje prawne', 'pawelczaplicki' ); ?>
		</p>
		<h1 class="text-4xl md:text-5xl font-bold text-slate-900 tracking-tight policy-title">
			<?php the_title(); ?>
		</h1>
		<p class="text-gray-500 mt-4">
			<?php esc_html_e( 'Ostatnia aktualizacja:', 'pawelczaplicki' ); ?>
			<?php echo esc_html( date_i18n( 'F Y' ) ); ?>
		</p>
	</div>
</section>

<article class="py-16 md:py-20">
	<div class="max-w-4xl mx-auto px-6 md:px-12 policy-content">
		<p>
			Niniejsza Polityka Prywatności określa zasady przetwarzania i ochrony danych osobowych
			przekazanych przez Użytkowników w związku z korzystaniem z usług i funkcjonalności strony
			internetowej Protokół 17:00™.
		</p>

		<h2>1. Administrator Danych</h2>
		<p>
			Administratorem danych osobowych zawartych w serwisie jest
			<strong>[Twoja Nazwa Firmy/Imię i Nazwisko]</strong>
			z siedzibą w <strong>[Adres Siedziby]</strong>,
			NIP: <strong>[Twój NIP]</strong>,
			REGON: <strong>[Twój REGON]</strong>.
			W sprawach związanych z ochroną danych można kontaktować się poprzez e-mail:
			<a href="mailto:kontakt@protokol17.pl">kontakt@protokol17.pl</a>.
		</p>

		<h2>2. Cele przetwarzania danych</h2>
		<p>
			Dane osobowe Użytkowników zbierane poprzez formularze (np. formularz kontaktowy)
			przetwarzane są wyłącznie w celach, dla których zostały udostępnione:
		</p>
		<ul>
			<li>obsługa zapytań ofertowych kierowanych przez formularz kontaktowy,</li>
			<li>realizacja usług doradczych i audytów (Protokół 17:00™),</li>
			<li>przesyłanie informacji handlowych (wyłącznie po uzyskaniu wyraźnej zgody).</li>
		</ul>

		<h2>3. Podstawa prawna i okres przechowywania</h2>
		<p>
			Przetwarzanie danych odbywa się na podstawie:
		</p>
		<ul>
			<li>zgody Użytkownika – art. 6 ust. 1 lit. a RODO,</li>
			<li>konieczności wykonania umowy lub podjęcia działań przed jej zawarciem – art. 6 ust. 1 lit. b RODO,</li>
			<li>prawnie uzasadnionego interesu Administratora – art. 6 ust. 1 lit. f RODO.</li>
		</ul>
		<p>
			Dane przechowywane są do momentu odwołania zgody, zrealizowania zapytania lub do upływu
			terminu przedawnienia ewentualnych roszczeń wynikających z przepisów prawa.
		</p>

		<h2>4. Prawa Użytkownika</h2>
		<p>Każdy Użytkownik posiada prawo do:</p>
		<ul>
			<li>dostępu do treści swoich danych oraz ich sprostowania,</li>
			<li>usunięcia danych („prawo do bycia zapomnianym”) lub ograniczenia ich przetwarzania,</li>
			<li>przenoszenia danych do innego administratora,</li>
			<li>wniesienia sprzeciwu wobec przetwarzania danych w oparciu o prawnie uzasadniony interes,</li>
			<li>
				cofnięcia zgody w dowolnym momencie bez wpływu na zgodność z prawem przetwarzania,
				którego dokonano na podstawie zgody przed jej cofnięciem.
			</li>
		</ul>
		<p>
			Użytkownik ma również prawo wniesienia skargi do Prezesa Urzędu Ochrony Danych Osobowych,
			jeżeli uzna, że przetwarzanie danych narusza przepisy RODO.
		</p>

		<h2>5. Pliki cookies (ciasteczka)</h2>
		<p>
			Strona wykorzystuje pliki cookies niezbędne do prawidłowego funkcjonowania serwisu,
			a także w celach statystycznych i analitycznych.
		</p>
		<ul>
			<li>
				Użytkownik może samodzielnie zarządzać ustawieniami plików cookies w swojej
				przeglądarce internetowej (blokowanie, ograniczanie, usuwanie).
			</li>
			<li>
				Pliki cookies wykorzystywane są m.in. do zbierania anonimowych danych statystycznych
				dotyczących sposobu korzystania z serwisu.
			</li>
			<li>
				Ciasteczka nie służą do identyfikacji konkretnych osób i co do zasady nie pozwalają
				na ustalenie tożsamości Użytkownika.
			</li>
		</ul>

		<h2>6. Odbiorcy danych i bezpieczeństwo</h2>
		<p>
			Administrator dokłada wszelkich starań, aby chronić dane Użytkowników przed
			nieuprawnionym dostępem, utratą lub zniszczeniem, stosując odpowiednie środki
			techniczne i organizacyjne.
		</p>
		<ul>
			<li>
				Dane mogą być powierzane jedynie zaufanym podmiotom, takim jak dostawca hostingu,
				dostawcy usług IT czy narzędzi analitycznych – na podstawie zawartych umów
				powierzenia przetwarzania danych osobowych.
			</li>
			<li>
				Dane nie są transferowane poza Europejski Obszar Gospodarczy, chyba że wynika to
				z użycia konkretnego narzędzia spełniającego wymogi RODO (np. standardowe klauzule
				umowne, dodatkowe środki bezpieczeństwa).
			</li>
		</ul>

		<h2>7. Zmiany Polityki Prywatności</h2>
		<p>
			Administrator zastrzega sobie prawo do wprowadzania zmian w niniejszej Polityce
			Prywatności w przypadku zmiany przepisów prawa, wprowadzenia nowych funkcjonalności
			serwisu lub zmian technologicznych.
		</p>
		<p>
			O wszelkich istotnych zmianach Użytkownicy będą informowani w sposób widoczny na
			stronie internetowej. Aktualna wersja Polityki Prywatności jest zawsze dostępna
			na tej podstronie.
		</p>
	</div>
</article>

<?php
get_footer();
