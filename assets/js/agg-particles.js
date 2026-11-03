/**
 * AllGeekGear — фонові частинки чакри (canvas)
 * Шлях: assets/js/agg-particles.js
 *
 * Підключається до будь-якого <canvas data-agg-particles>.
 * Налаштування через data-атрибути:
 *   data-density="11000"   менше число = більше частинок
 *   data-max="110"         максимум частинок
 *   data-link="90"         дистанція зв'язків між частинками (0 = вимкнути)
 *
 * Ефект сам зупиняється, коли секція поза екраном — тому він
 * не вантажить решту сторінки.
 */
(function () {
	'use strict';

	function startCanvas(canvas) {
		var ctx = canvas.getContext('2d');
		if (!ctx) return;

		var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		var density = parseFloat(canvas.dataset.density || '11000');
		var maxCount = parseInt(canvas.dataset.max || '110', 10);
		var linkDist = parseFloat(canvas.dataset.link || '90');

		var width = 0;
		var height = 0;
		var particles = [];
		var raf = 0;
		var running = false;
		var mouse = { x: -9999, y: -9999 };

		function seed() {
			var count = Math.min(maxCount, Math.floor((width * height) / density));
			particles = [];
			for (var i = 0; i < count; i++) {
				particles.push({
					x: Math.random() * width,
					y: Math.random() * height,
					vx: (Math.random() - 0.5) * 0.25,
					vy: -(Math.random() * 0.45 + 0.1),
					r: Math.random() * 1.8 + 0.6,
					cyan: Math.random() > 0.5,
					phase: Math.random() * Math.PI * 2
				});
			}
		}

		function resize() {
			var dpr = Math.min(window.devicePixelRatio || 1, 2);
			width = canvas.clientWidth;
			height = canvas.clientHeight;
			if (!width || !height) return;
			canvas.width = width * dpr;
			canvas.height = height * dpr;
			ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
			seed();
		}

		function draw(t) {
			ctx.clearRect(0, 0, width, height);
			ctx.globalCompositeOperation = 'lighter';

			for (var i = 0; i < particles.length; i++) {
				var p = particles[i];

				if (!reduced) {
					var dx = p.x - mouse.x;
					var dy = p.y - mouse.y;
					var dist = Math.hypot(dx, dy);
					if (dist < 120 && dist > 0) {
						p.x += (dx / dist) * 1.6;
						p.y += (dy / dist) * 1.6;
					}
					p.x += p.vx;
					p.y += p.vy;
					if (p.y < -10) {
						p.y = height + 10;
						p.x = Math.random() * width;
					}
					if (p.x < -10) p.x = width + 10;
					if (p.x > width + 10) p.x = -10;
				}

				var twinkle = 0.55 + Math.sin(t * 0.002 + p.phase) * 0.45;
				var color = p.cyan ? '70, 220, 242' : '255, 140, 40';

				var glow = ctx.createRadialGradient(p.x, p.y, 0, p.x, p.y, p.r * 6);
				glow.addColorStop(0, 'rgba(' + color + ', ' + 0.9 * twinkle + ')');
				glow.addColorStop(1, 'rgba(' + color + ', 0)');
				ctx.fillStyle = glow;
				ctx.beginPath();
				ctx.arc(p.x, p.y, p.r * 6, 0, Math.PI * 2);
				ctx.fill();

				if (linkDist > 0) {
					for (var j = i + 1; j < particles.length; j++) {
						var q = particles[j];
						var d = Math.hypot(p.x - q.x, p.y - q.y);
						if (d < linkDist) {
							ctx.strokeStyle = 'rgba(' + color + ', ' + (1 - d / linkDist) * 0.18 + ')';
							ctx.lineWidth = 0.6;
							ctx.beginPath();
							ctx.moveTo(p.x, p.y);
							ctx.lineTo(q.x, q.y);
							ctx.stroke();
						}
					}
				}
			}

			if (!reduced && running) raf = requestAnimationFrame(draw);
		}

		function start() {
			if (running) return;
			running = true;
			raf = requestAnimationFrame(draw);
		}

		function stop() {
			running = false;
			cancelAnimationFrame(raf);
		}

		function onPointer(e) {
			var rect = canvas.getBoundingClientRect();
			mouse.x = e.clientX - rect.left;
			mouse.y = e.clientY - rect.top;
		}

		new ResizeObserver(resize).observe(canvas);
		resize();
		window.addEventListener('pointermove', onPointer, { passive: true });

		/* Малюємо лише поки секція у в'юпорті */
		new IntersectionObserver(function (entries) {
			entries[0].isIntersecting ? start() : stop();
		}).observe(canvas);

		document.addEventListener('visibilitychange', function () {
			document.hidden ? stop() : start();
		});
	}

	function init() {
		document.querySelectorAll('canvas[data-agg-particles]').forEach(startCanvas);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
