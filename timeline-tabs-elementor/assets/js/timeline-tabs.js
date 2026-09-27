/*!
 * Timeline Tabs for Elementor — frontend script
 * Vanilla JS, no dependencies. Idempotent init (safe in the Elementor editor).
 */
(function () {
	'use strict';

	var REDUCED = window.matchMedia ? window.matchMedia('(prefers-reduced-motion: reduce)') : null;

	function init(root) {
		if (!root || root.ttReady) {
			return;
		}
		root.ttReady = true;

		var rail   = root.querySelector('.tt__rail');
		var track  = root.querySelector('.tt__track');
		var fill   = root.querySelector('.tt__fill');
		var tabsEl = root.querySelector('.tt__tabs');
		var panels = root.querySelector('.tt__panels');
		var tabs   = Array.prototype.slice.call(root.querySelectorAll('.tt__tab'));
		var panelList = Array.prototype.slice.call(root.querySelectorAll('.tt__panel'));

		if (!tabs.length) {
			return;
		}

		var animOn   = root.getAttribute('data-anim') === '1';
		var scrollOn = root.getAttribute('data-scroll') !== '0';
		var fadeOn   = root.getAttribute('data-fade') === '1';
		var swipeOn  = root.getAttribute('data-swipe') === '1';
		var active   = -1;
		var rafId    = 0;

		function canAnimate() {
			return animOn && !(REDUCED && REDUCED.matches);
		}

		function smooth() {
			return (REDUCED && REDUCED.matches) ? 'auto' : 'smooth';
		}

		/* مرکز نشانگر نسبت به ریل تایم‌لاین */
		function centerOf(tab) {
			var node = tab.querySelector('.tt__marker') || tab;
			var t = track.getBoundingClientRect();
			var r = node.getBoundingClientRect();
			return r.left - t.left + r.width / 2;
		}

		/* ارتفاع خط را از مرکز نشانگر حساب می‌کنیم تا با هر تایپوگرافی درست بنشیند */
		function measureLine() {
			if (!track) {
				return;
			}
			/* اولین نشانگر موجود؛ ممکن است تب اول نشانگر نداشته باشد */
			var marker = root.querySelector('.tt__tab .tt__marker');
			if (!marker) {
				return;
			}
			var t = track.getBoundingClientRect();
			var r = marker.getBoundingClientRect();
			if (r.height) {
				track.style.setProperty('--tt-line-top', (r.top - t.top + r.height / 2) + 'px');
			}
		}

		function placeFill() {
			if (!fill || active < 0 || !track) {
				return;
			}
			var c1 = centerOf(tabs[0]);
			var c2 = centerOf(tabs[active]);
			fill.style.left  = Math.min(c1, c2) + 'px';
			fill.style.width = Math.abs(c1 - c2) + 'px';
		}

		/* وضعیت لبه‌های ریل — هندسی حساب می‌شود تا در RTL هم درست باشد. */
		function edges() {
			if (!rail || !track || !fadeOn) {
				return;
			}
			var rr = rail.getBoundingClientRect();
			var tr = track.getBoundingClientRect();
			var over = tr.width - rr.width > 1;

			rail.classList.toggle('is-fade', over);
			rail.classList.toggle('has-prev', over && rr.left - tr.left > 1);
			rail.classList.toggle('has-next', over && tr.right - rr.right > 1);
		}

		function refresh() {
			if (rafId) {
				return;
			}
			rafId = window.requestAnimationFrame(function () {
				rafId = 0;
				measureLine();
				placeFill();
				edges();
			});
		}

		/* تب را وسط ریل می‌آورد بدون آنکه صفحه عمودی بپرد
		   (رفتار آزاردهندهٔ scrollIntoView روی موبایل). */
		function centerTab(index, behavior) {
			if (!rail || !tabs[index]) {
				return;
			}
			if (rail.scrollWidth - rail.clientWidth < 2) {
				return;
			}
			var rr = rail.getBoundingClientRect();
			var tb = tabs[index].getBoundingClientRect();
			var delta = (tb.left + tb.width / 2) - (rr.left + rr.width / 2);

			if (Math.abs(delta) < 1) {
				return;
			}
			if (rail.scrollBy) {
				rail.scrollBy({ left: delta, behavior: behavior || 'auto' });
			} else {
				rail.scrollLeft += delta;
			}
		}

		/* تصاویر پنل فعال دیگر نیازی به تعویق بارگذاری ندارند */
		function promoteImages(panel) {
			if (!panel) {
				return;
			}
			var imgs = panel.querySelectorAll('img[loading="lazy"]');
			for (var i = 0; i < imgs.length; i++) {
				imgs[i].setAttribute('loading', 'eager');
			}
		}

		function select(index, userAction) {
			if (index === active || index < 0 || index >= tabs.length) {
				return;
			}

			var animate = active !== -1 && canAnimate();
			active = index;

			for (var i = 0; i < tabs.length; i++) {
				var on = i === index;
				tabs[i].classList.toggle('is-active', on);
				tabs[i].setAttribute('aria-selected', on ? 'true' : 'false');
				tabs[i].tabIndex = on ? 0 : -1;

				if (panelList[i]) {
					panelList[i].classList.toggle('is-active', on);
					panelList[i].setAttribute('aria-hidden', on ? 'false' : 'true');
					if (!on) {
						panelList[i].classList.remove('is-anim');
					}
				}
			}

			var panel = panelList[index];
			promoteImages(panel);

			/* پس از اولین تعویض، تصاویر تب‌های مجاور را هم از قبل می‌گیریم
			   تا روی موبایل هنگام سوایپ، کادر خالی دیده نشود. */
			if (userAction) {
				promoteImages(panelList[index - 1]);
				promoteImages(panelList[index + 1]);
			}

			if (panel && animate) {
				panel.classList.remove('is-anim');
				void panel.offsetWidth; /* restart animations */
				panel.classList.add('is-anim');
			}

			refresh();

			if (userAction && scrollOn) {
				centerTab(index, smooth());
			}
		}

		function indexOfTab(node) {
			var tab = node && node.closest ? node.closest('.tt__tab') : null;
			return tab ? tabs.indexOf(tab) : -1;
		}

		function step(dir) {
			select((active + dir + tabs.length) % tabs.length, true);
		}

		/* یک شنونده برای همهٔ تب‌ها */
		tabsEl.addEventListener('click', function (e) {
			var i = indexOfTab(e.target);
			if (i > -1) {
				select(i, true);
			}
		});

		tabsEl.addEventListener('keydown', function (e) {
			var rtl = getComputedStyle(root).direction === 'rtl';
			var next = null;

			switch (e.key) {
				case 'ArrowLeft':
					next = rtl ? active + 1 : active - 1;
					break;
				case 'ArrowRight':
					next = rtl ? active - 1 : active + 1;
					break;
				case 'Home':
					next = 0;
					break;
				case 'End':
					next = tabs.length - 1;
					break;
				case 'Enter':
				case ' ':
					var i = indexOfTab(e.target);
					if (i > -1) {
						e.preventDefault();
						select(i, true);
					}
					return;
				default:
					return;
			}

			next = (next + tabs.length) % tabs.length;
			e.preventDefault();
			select(next, true);
			/* وقتی خودمان ریل را جابه‌جا می‌کنیم، اجازه نمی‌دهیم focus هم صفحه را بپراند */
			tabs[next].focus({ preventScroll: scrollOn });
		});

		/* ------------------------------------------------ سوایپ روی پنل‌ها */
		if (swipeOn && panels) {
			var sx = 0, sy = 0, st = 0, valid = false;

			panels.addEventListener('touchstart', function (e) {
				if (e.touches.length !== 1) {
					valid = false;
					return;
				}
				/* داخل عنصرهای اسکرول‌شوندهٔ خودِ متن دخالت نمی‌کنیم */
				valid = !(e.target.closest && e.target.closest('.tt__text table, .tt__text pre'));
				sx = e.touches[0].clientX;
				sy = e.touches[0].clientY;
				st = Date.now();
			}, { passive: true });

			panels.addEventListener('touchend', function (e) {
				if (!valid || !e.changedTouches.length) {
					return;
				}
				valid = false;

				var dx = e.changedTouches[0].clientX - sx;
				var dy = e.changedTouches[0].clientY - sy;

				if (Date.now() - st > 700) {
					return;
				}
				if (Math.abs(dx) < 45 || Math.abs(dx) < Math.abs(dy) * 1.6) {
					return;
				}

				var rtl = getComputedStyle(root).direction === 'rtl';
				/* کشیدن به چپ در LTR یعنی «بعدی»، در RTL یعنی «قبلی» */
				step(dx < 0 ? (rtl ? -1 : 1) : (rtl ? 1 : -1));
			}, { passive: true });
		}

		if (rail && fadeOn) {
			rail.addEventListener('scroll', edges, { passive: true });
		}

		if (window.ResizeObserver) {
			var ro = new ResizeObserver(refresh);
			ro.observe(track);
			if (rail) {
				ro.observe(rail);
			}
		} else {
			window.addEventListener('resize', refresh, { passive: true });
			window.addEventListener('orientationchange', refresh, { passive: true });
		}

		if (document.fonts && document.fonts.ready) {
			document.fonts.ready.then(function () {
				refresh();
				centerTab(active, 'auto');
			});
		}

		var start = parseInt(root.getAttribute('data-default') || '0', 10);
		select(isNaN(start) ? 0 : start, false);
		refresh();

		/* اگر تب پیش‌فرض بیرون از کادر دید باشد (موبایل با تب‌های زیاد)،
		   بدون انیمیشن به وسط می‌آید تا کاربر آن را ببیند. */
		if (scrollOn) {
			window.requestAnimationFrame(function () {
				centerTab(active, 'auto');
				edges();
			});
		}
	}

	function initAll(scope) {
		var nodes = (scope || document).querySelectorAll('.tt');
		for (var i = 0; i < nodes.length; i++) {
			init(nodes[i]);
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () { initAll(); });
	} else {
		initAll();
	}

	/* یکپارچگی با المنتور (ویرایشگر و فرانت) */
	window.addEventListener('elementor/frontend/init', function () {
		if (!window.elementorFrontend || !elementorFrontend.hooks) {
			return;
		}
		elementorFrontend.hooks.addAction('frontend/element_ready/tt_timeline_tabs.default', function ($scope) {
			var el = $scope && $scope[0] ? $scope[0].querySelector('.tt') : null;
			if (el) {
				init(el);
			}
		});
	});

	window.ttTimelineTabs = { init: init, initAll: initAll };
})();
