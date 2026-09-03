(function () {
	'use strict';

	function initSlider(root) {
		var track = root.querySelector('.trusthalo-track');
		var slides = Array.prototype.slice.call(root.querySelectorAll('.trusthalo-review-card'));
		var previous = root.querySelector('.trusthalo-prev');
		var next = root.querySelector('.trusthalo-next');
		var dotsWrap = root.querySelector('.trusthalo-dots');
		var interval = parseInt(root.getAttribute('data-autoplay'), 10) || 5000;
		var page = 0;
		var pages = 1;
		var timer = null;
		var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

		function perView() {
			if (window.innerWidth <= 600) {
				return 1;
			}
			if (window.innerWidth <= 900) {
				return 2;
			}
			return 3;
		}

		function renderDots() {
			dotsWrap.innerHTML = '';
			for (var index = 0; index < pages; index += 1) {
				(function (dotPage) {
					var dot = document.createElement('button');
					dot.type = 'button';
					dot.className = 'trusthalo-dot';
					dot.setAttribute('aria-label', 'Show review page ' + (dotPage + 1));
					dot.addEventListener('click', function () {
						goTo(dotPage);
						restart();
					});
					dotsWrap.appendChild(dot);
				}(index));
			}
		}

		function update() {
			track.style.transform = 'translateX(-' + (page * 100) + '%)';
			previous.disabled = pages <= 1;
			next.disabled = pages <= 1;

			Array.prototype.forEach.call(dotsWrap.children, function (dot, index) {
				dot.classList.toggle('is-active', index === page);
				dot.setAttribute('aria-current', index === page ? 'true' : 'false');
			});
		}

		function goTo(target) {
			page = (target + pages) % pages;
			update();
		}

		function calculate() {
			var newPages = Math.max(1, Math.ceil(slides.length / perView()));
			if (newPages !== pages) {
				pages = newPages;
				page = Math.min(page, pages - 1);
				renderDots();
			}
			update();
		}

		function stop() {
			if (timer) {
				window.clearInterval(timer);
				timer = null;
			}
		}

		function start() {
			if (!reduceMotion && pages > 1) {
				timer = window.setInterval(function () {
					goTo(page + 1);
				}, interval);
			}
		}

		function restart() {
			stop();
			start();
		}

		previous.addEventListener('click', function () {
			goTo(page - 1);
			restart();
		});
		next.addEventListener('click', function () {
			goTo(page + 1);
			restart();
		});
		root.addEventListener('mouseenter', stop);
		root.addEventListener('mouseleave', start);
		root.addEventListener('focusin', stop);
		root.addEventListener('focusout', start);
		window.addEventListener('resize', function () {
			calculate();
			restart();
		});

		calculate();
		start();
	}

	function boot() {
		Array.prototype.forEach.call(document.querySelectorAll('.trusthalo-slider'), initSlider);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
}());
