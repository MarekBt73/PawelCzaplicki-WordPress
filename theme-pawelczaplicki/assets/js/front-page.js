/**
 * Strona główna – Protokół 17:00™
 * Inicjalizacja ikon Lucide, sticky header, FAQ, liczniki.
 */
(function () {
	'use strict';

	if (typeof lucide !== 'undefined') {
		lucide.createIcons();
	}

	var header = document.getElementById('main-header');
	if (header) {
		window.addEventListener('scroll', function () {
			if (window.scrollY > 20) {
				header.classList.add('glass-header');
				header.classList.remove('bg-white', 'border-gray-100');
			} else {
				header.classList.remove('glass-header');
				header.classList.add('bg-white', 'border-gray-100');
			}
		});
	}

	var mobileToggle = document.getElementById('p17-mobile-toggle');
	var mobileNav = document.querySelector('.p17-nav-mobile');
	if (mobileToggle && mobileNav) {
		mobileToggle.addEventListener('click', function () {
			var isOpen = mobileNav.classList.contains('hidden') === false;
			if (isOpen) {
				mobileNav.classList.add('hidden');
				mobileToggle.setAttribute('aria-expanded', 'false');
			} else {
				mobileNav.classList.remove('hidden');
				mobileToggle.setAttribute('aria-expanded', 'true');
			}
		});
	}

	var faqToggles = document.querySelectorAll('.faq-toggle');
	faqToggles.forEach(function (toggle) {
		toggle.addEventListener('click', function () {
			var answer = toggle.nextElementSibling;
			var isOpen = answer.classList.contains('open');

			document.querySelectorAll('.faq-answer').forEach(function (ans) {
				ans.style.maxHeight = null;
				ans.classList.remove('open');
			});
			document.querySelectorAll('.faq-toggle').forEach(function (btn) {
				var span = btn.querySelector('.faq-icon-wrap');
				if (span) {
					span.innerHTML = '<i data-lucide="plus" class="w-6 h-6 text-brand-red flex-shrink-0 transition-transform duration-300"></i>';
				}
			});

			if (!isOpen) {
				answer.classList.add('open');
				answer.style.maxHeight = answer.scrollHeight + 'px';
				var iconWrap = toggle.querySelector('.faq-icon-wrap');
				if (iconWrap) {
					iconWrap.innerHTML = '<i data-lucide="minus" class="w-6 h-6 text-brand-red flex-shrink-0 transition-transform duration-300"></i>';
				}
				if (typeof lucide !== 'undefined') {
					lucide.createIcons();
				}
			}
		});
	});

	var counterSection = document.getElementById('counter-section');
	if (counterSection) {
		var counters = document.querySelectorAll('.counter');
		var speed = 200;
		function animateCounters() {
			counters.forEach(function (counter) {
				function updateCount() {
					var target = parseInt(counter.getAttribute('data-target'), 10);
					var count = parseInt(counter.innerText, 10) || 0;
					var inc = target / speed;
					if (count < target) {
						counter.innerText = Math.ceil(count + inc);
						setTimeout(updateCount, 15);
					} else {
						counter.innerText = target;
					}
				}
				updateCount();
			});
		}
		var observer = new IntersectionObserver(
			function (entries) {
				if (entries[0].isIntersecting) {
					animateCounters();
					observer.disconnect();
				}
			},
			{ threshold: 0.5 }
		);
		observer.observe(counterSection);
	}
})();
