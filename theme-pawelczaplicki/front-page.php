<?php

declare(strict_types=1);

get_header();
?>

<!-- Globalne linie tła (zgodnie z moodboardem) -->
<div class="bg-lines-container">
	<svg class="absolute w-full h-full opacity-[0.8]" viewBox="0 0 1440 1024" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
		<path d="M-200,800 C400,600 800,100 1600,-100" stroke="#E7411D" stroke-width="0.75" />
		<circle cx="350" cy="550" r="3.5" fill="#E7411D"/>
		<path d="M-100,200 C300,500 900,900 1600,700" stroke="#E7411D" stroke-width="0.75" />
		<circle cx="1050" cy="805" r="3.5" fill="#E7411D"/>
		<path d="M1200,-200 C1100,300 1300,800 1500,1200" stroke="#E7411D" stroke-width="0.75" />
		<circle cx="1185" cy="400" r="3.5" fill="#E7411D"/>
	</svg>
</div>

<!-- 1. SEKCJA: HERO -->
<section class="relative pt-40 pb-20 lg:pt-28 lg:pb-32">
	<div class="max-w-7xl mx-auto px-6 md:px-12">
		<div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-start">
			<div class="lg:col-span-7">
				<h1 class="text-4xl md:text-5xl lg:text-[4rem] font-semi-expanded font-bold leading-[1.1] text-brand-red mb-10 tracking-tight">
					Doradztwo strategiczne dla właścicieli firm:<br>
					Łączę dane z Reiss Motivation Profile z wdrożeniem
					<a href="<?php echo esc_url( home_url( '/protokol-17-00/' ) ); ?>" class="text-brand-dark">
						Protokołu 17:00™
					</a>
					, aby firma działała sprawnie bez ciągłego nadzoru właściciela.
				</h1>
				<p class="text-xl md:text-2xl text-brand-dark font-normal leading-relaxed mb-12 max-w-2xl">
					Pomagam właścicielom firm przestać być wąskim gardłem swoich biznesów, wykorzystując Reiss Motivation Profile® i wdrażając Protokół 17:00™ – system decyzji i delegowania, dzięki któremu firma działa bez ciągłego nadzoru właściciela.
				</p>
				<a href="<?php echo esc_url( home_url( '/protokol-17-00/' ) ); ?>" class="cta-arrow-link inline-flex items-center text-lg font-bold text-brand-red transition-colors">
					Poznaj Protokół 17:00™
					<i data-lucide="arrow-right" class="arrow-icon ml-3 w-6 h-6 transition-transform duration-300"></i>
				</a>
			</div>
			<div class="lg:col-span-5 relative mt-12 lg:mt-0">
				<div class="aspect-[4/5] overflow-hidden bg-brand-light relative z-10 [&_img]:w-full [&_img]:h-full [&_img]:object-cover [&_img]:object-top">
					<?php if ( is_active_sidebar( 'hero-foto' ) ) : ?>
						<?php dynamic_sidebar( 'hero-foto' ); ?>
					<?php else : ?>
						<img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
							 alt="<?php esc_attr_e( 'Profesjonalne zdjęcie autora', 'pawelczaplicki' ); ?>"
							 class="w-full h-full object-cover object-top filter contrast-125 saturate-50">
					<?php endif; ?>
				</div>
				<div class="absolute -bottom-6 -right-6 text-brand-red opacity-30">
					<svg width="120" height="120" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
						<circle cx="60" cy="60" r="59.5" stroke="currentColor"/>
						<circle cx="60" cy="60" r="40.5" stroke="currentColor"/>
					</svg>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- 2. SEKCJA: PROBLEM -->
<section id="problem" class="py-32 bg-white relative">
	<div class="max-w-7xl mx-auto px-6 md:px-12">
		<div class="mb-20 max-w-3xl">
			<h2 class="text-4xl md:text-6xl font-semi-expanded font-bold text-brand-red leading-tight tracking-tight mb-8">
				Sukces firmy nie musi oznaczać Twojego wypalenia.
			</h2>
			<p class="text-2xl text-brand-dark font-normal">Czy rozpoznajesz te symptomy w swojej firmie?</p>
		</div>
		<div class="grid grid-cols-1 md:grid-cols-3 gap-12 mb-20">
			<div class="border-t border-brand-red pt-8">
				<h3 class="text-2xl font-semi-expanded font-bold text-brand-dark mb-4 tracking-tight">Jesteś wąskim gardłem</h3>
				<p class="text-gray-500 leading-relaxed text-lg">– każda decyzja musi przejść przez Twoje biurko.</p>
			</div>
			<div class="border-t border-brand-red pt-8">
				<h3 class="text-2xl font-semi-expanded font-bold text-brand-dark mb-4 tracking-tight">Fikcyjne delegowanie</h3>
				<p class="text-gray-500 leading-relaxed text-lg">– oddajesz zadania, ale i tak musisz kontrolować każdy krok.</p>
			</div>
			<div class="border-t border-brand-red pt-8">
				<h3 class="text-2xl font-semi-expanded font-bold text-brand-dark mb-4 tracking-tight">Szklany sufit</h3>
				<p class="text-gray-500 leading-relaxed text-lg">– czujesz, że Twój zespół ma potencjał, ale boi się brać odpowiedzialność.</p>
			</div>
		</div>
		<div class="max-w-4xl">
			<p class="text-3xl md:text-4xl font-semi-expanded font-bold text-brand-dark leading-snug">
				To nie jest kwestia braku umiejętności Twoich ludzi. <br>
				<span class="text-brand-red">To błąd w architekturze motywacyjnej.</span>
			</p>
		</div>
	</div>
</section>

<!-- 3. SEKCJA: ROZWIĄZANIE (METODOLOGIA) -->
<section id="rozwiazanie" class="py-32 bg-brand-light relative">
	<div class="bg-lines-container opacity-50">
		<svg class="absolute w-full h-full" viewBox="0 0 1440 800" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path d="M-100,600 C500,800 1000,200 1600,400" stroke="#E7411D" stroke-width="0.5" />
		</svg>
	</div>
	<div class="max-w-7xl mx-auto px-6 md:px-12 relative z-10">
		<div class="mb-24">
			<h2 class="text-5xl md:text-7xl font-semi-expanded font-bold text-brand-red leading-tight tracking-tight max-w-4xl">
				Jak pracujemy? System nad intuicją.
			</h2>
		</div>
		<div class="space-y-0 border-y border-gray-200">
			<div class="grid grid-cols-1 md:grid-cols-12 gap-8 py-12 border-b border-gray-200 group hover:bg-white transition-colors duration-300">
				<div class="md:col-span-4 flex items-center">
					<i data-lucide="activity" class="w-10 h-10 text-brand-red opacity-50 mr-6"></i>
					<h3 class="text-3xl font-semi-expanded font-bold text-brand-dark">Diagnostyka RMP®</h3>
				</div>
				<div class="md:col-span-8 flex items-center">
					<p class="text-gray-600 text-lg leading-relaxed">
						Robimy "rentgen" motywacji 16 obszarów życiowych każdego kluczowego menedżera.
					</p>
				</div>
			</div>
			<div class="grid grid-cols-1 md:grid-cols-12 gap-8 py-12 border-b border-gray-200 group hover:bg-white transition-colors duration-300">
				<div class="md:col-span-4 flex items-center">
					<i data-lucide="map" class="w-10 h-10 text-brand-red opacity-50 mr-6"></i>
					<h3 class="text-3xl font-semi-expanded font-bold text-brand-dark">Mapa Decyzyjna</h3>
				</div>
				<div class="md:col-span-8 flex items-center">
					<p class="text-gray-600 text-lg leading-relaxed">
						Projektujemy procesy tak, by trafiały w naturalne predyspozycje pracowników.
					</p>
				</div>
			</div>
			<div class="grid grid-cols-1 md:grid-cols-12 gap-8 py-12 group hover:bg-white transition-colors duration-300">
				<div class="md:col-span-4 flex items-center">
					<i data-lucide="power" class="w-10 h-10 text-brand-red opacity-50 mr-6"></i>
					<h3 class="text-3xl font-semi-expanded font-bold text-brand-dark">Wdrożenie Autonomii</h3>
				</div>
				<div class="md:col-span-8 flex items-center">
					<p class="text-gray-600 text-lg leading-relaxed">
						Instalujemy mechanizmy, które pozwalają firmie działać bez Twojej obecności 24/7.
					</p>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- 4. SEKCJA: OFERTA -->
<section id="oferta" class="py-32 bg-white">
	<div class="max-w-7xl mx-auto px-6 md:px-12">
		<div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mt-12">
			<div class="p-12 border-2 border-gray-100 hover:border-brand-red transition-colors duration-500 bg-white flex flex-col h-full relative">
				<div class="mb-12">
					<span class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4 block">Edukacja/Start</span>
					<h3 class="text-4xl font-semi-expanded font-bold text-brand-dark mb-6">Indywidualny Profil RMP®</h3>
					<p class="text-gray-500 text-lg">Dla liderów chcących poznać swoje "dlaczego".</p>
				</div>
				<ul class="space-y-6 mb-16 flex-grow">
					<li class="flex items-start">
						<span class="w-1.5 h-1.5 rounded-full bg-brand-red mt-2.5 mr-4 flex-shrink-0"></span>
						<span class="text-brand-dark text-lg font-medium">Test online</span>
					</li>
					<li class="flex items-start">
						<span class="w-1.5 h-1.5 rounded-full bg-brand-red mt-2.5 mr-4 flex-shrink-0"></span>
						<span class="text-brand-dark text-lg font-medium">90 min sesji debriefingu</span>
					</li>
					<li class="flex items-start">
						<span class="w-1.5 h-1.5 rounded-full bg-brand-red mt-2.5 mr-4 flex-shrink-0"></span>
						<span class="text-brand-dark text-lg font-medium">Raport PDF</span>
					</li>
				</ul>
			</div>
			<div class="p-12 border-2 border-brand-red bg-white flex flex-col h-full relative transform md:-translate-y-4">
				<div class="absolute top-0 right-8 bg-brand-red text-white text-xs font-bold px-4 py-2 uppercase tracking-[0.15em] transform -translate-y-1/2">
					Transformacja/Premium
				</div>
				<div class="mb-12">
					<h3 class="text-4xl font-semi-expanded font-bold text-brand-dark mb-6 mt-4">Mentoring: Architekt Decyzji</h3>
					<p class="text-gray-500 text-lg">Dla właścicieli firm (zespoły 10+ osób).</p>
				</div>
				<ul class="space-y-6 mb-16 flex-grow">
					<li class="flex items-start">
						<span class="w-1.5 h-1.5 rounded-full bg-brand-red mt-2.5 mr-4 flex-shrink-0"></span>
						<span class="text-brand-dark text-lg font-medium">Praca 1:1 nad strukturą firmy</span>
					</li>
					<li class="flex items-start">
						<span class="w-1.5 h-1.5 rounded-full bg-brand-red mt-2.5 mr-4 flex-shrink-0"></span>
						<span class="text-brand-dark text-lg font-medium">Audyt procesów</span>
					</li>
					<li class="flex items-start">
						<span class="w-1.5 h-1.5 rounded-full bg-brand-red mt-2.5 mr-4 flex-shrink-0"></span>
						<span class="text-brand-dark text-lg font-medium">Budowa kultury odpowiedzialności</span>
					</li>
				</ul>
			</div>
		</div>
	</div>
</section>

<!-- 5. SEKCJA: TRUST SIGNALS -->
<section class="py-24 bg-white border-y border-gray-200">
	<div class="max-w-7xl mx-auto px-6 md:px-12">
		<h3 class="text-3xl font-semi-expanded font-bold text-brand-dark mb-16 text-center md:text-left">Liczby i fakty</h3>
		<div class="grid grid-cols-1 md:grid-cols-3 gap-16 text-left mb-24" id="counter-section">
			<div>
				<div class="text-6xl lg:text-7xl font-semi-expanded font-bold text-brand-red mb-4 tracking-tighter">
					<span class="counter" data-target="350">0</span>
				</div>
				<div class="text-sm font-bold text-brand-dark uppercase tracking-widest leading-relaxed">Przeprowadzonych<br>analiz RMP</div>
			</div>
			<div>
				<div class="text-6xl lg:text-7xl font-semi-expanded font-bold text-brand-red mb-4 tracking-tighter">
					<span class="counter" data-target="120">0</span>
				</div>
				<div class="text-sm font-bold text-brand-dark uppercase tracking-widest leading-relaxed">Uwolnionych godzin<br>właścicieli miesięcznie</div>
			</div>
			<div>
				<div class="text-6xl lg:text-7xl font-semi-expanded font-bold text-brand-red mb-4 tracking-tighter">
					<span class="counter" data-target="85">0</span>
				</div>
				<div class="text-sm font-bold text-brand-dark uppercase tracking-widest leading-relaxed">Zoptymalizowanych<br>procesów decyzyjnych</div>
			</div>
		</div>
		<div class="flex flex-wrap items-center gap-12 md:gap-20 opacity-40 grayscale transition-all duration-500 hover:opacity-100 hover:grayscale-0">
			<div class="text-2xl font-bold tracking-tight text-brand-dark">Company<span class="text-brand-red">One</span></div>
			<div class="text-2xl font-bold tracking-tight text-brand-dark">Global<span class="text-brand-red">Tech</span></div>
			<div class="text-2xl font-bold tracking-tight text-brand-dark">Next<span class="text-brand-red">Level</span></div>
			<div class="text-2xl font-bold tracking-tight text-brand-dark">Venture<span class="text-brand-red">Corp</span></div>
		</div>
	</div>
</section>

<!-- 6. SEKCJA: FAQ -->
<section id="faq" class="py-32 bg-white relative">
	<div class="max-w-4xl mx-auto px-6 md:px-12">
		<div class="border-t border-gray-200">
			<div class="border-b border-gray-200">
				<button type="button" class="faq-toggle w-full py-8 text-left flex justify-between items-center focus:outline-none group">
					<span class="font-semi-expanded font-bold text-2xl text-brand-dark group-hover:text-brand-red transition-colors pr-8">Czy RMP to kolejny "test osobowości" jakich wiele w sieci?</span>
					<span class="faq-icon-wrap"><i data-lucide="plus" class="w-6 h-6 text-brand-red flex-shrink-0 transition-transform duration-300"></i></span>
				</button>
				<div class="faq-answer">
					<div class="pb-10 text-gray-500 text-lg leading-relaxed max-w-3xl">
						Nie. Większość testów mówi, jak się zachowujesz. RMP jako jedyne mówi, dlaczego to robisz. To różnica między leczeniem objawów a znalezieniem przyczyny.
					</div>
				</div>
			</div>
			<div class="border-b border-gray-200">
				<button type="button" class="faq-toggle w-full py-8 text-left flex justify-between items-center focus:outline-none group">
					<span class="font-semi-expanded font-bold text-2xl text-brand-dark group-hover:text-brand-red transition-colors pr-8">Kiedy zobaczę pierwsze efekty mentoringu?</span>
					<span class="faq-icon-wrap"><i data-lucide="plus" class="w-6 h-6 text-brand-red flex-shrink-0 transition-transform duration-300"></i></span>
				</button>
				<div class="faq-answer">
					<div class="pb-10 text-gray-500 text-lg leading-relaxed max-w-3xl">
						Pierwsze zmiany w komunikacji i odciążeniu Twojego kalendarza zauważysz już po 3-4 tygodniach pracy systemowej.
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<?php
if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		$content = trim( get_the_content() );
		if ( $content !== '' ) :
			?>
			<section class="py-16 bg-brand-light border-t border-gray-200">
				<div class="max-w-4xl mx-auto px-6 md:px-12">
					<article <?php post_class(); ?>>
						<?php the_content(); ?>
					</article>
				</div>
			</section>
			<?php
		endif;
	endwhile;
endif;
?>

<?php get_footer(); ?>
