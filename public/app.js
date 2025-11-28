// Using Bootstrap's native collapse behavior for the navbar; no custom toggle required.
// Keep this file for small site-specific JS in future.
document.addEventListener('DOMContentLoaded', function(){
	const banner = document.querySelector('.parallax-banner');
	if (!banner) return;
	const media = banner.querySelector('.parallax-media');

	// Simple scroll-based translate for parallax feel
	function onScroll(){
		const rect = banner.getBoundingClientRect();
		const winH = window.innerHeight || document.documentElement.clientHeight;
		const progress = Math.min(Math.max((winH - rect.top) / (winH + rect.height), 0), 1);
		const translate = (progress - 0.5) * 40; // stronger +/-20px translate
		if (media) media.style.transform = `translate(-50%,-50%) translateY(${translate}px)`;
	}
	window.addEventListener('scroll', onScroll, {passive:true});
	window.addEventListener('resize', onScroll);
	onScroll();

	// Pause/play video when in viewport to save resources
	if (media && media.tagName === 'VIDEO'){
		const io = new IntersectionObserver((entries)=>{
			entries.forEach(en => {
				if (en.isIntersecting) media.play().catch(()=>{});
				else media.pause();
			});
		}, {threshold:0.5});
		io.observe(banner);
	}
	// Inverse cursor parallax: media moves opposite to cursor within banner
		let supportsPointer = window.matchMedia('(pointer:fine)').matches;
		if (supportsPointer && media){
			banner.addEventListener('pointermove', function(e){
				const rect = banner.getBoundingClientRect();
				const px = (e.clientX - rect.left) / rect.width; // 0..1
				const py = (e.clientY - rect.top) / rect.height; // 0..1
				const offsetX = (0.5 - px) * 36; // inverse stronger offset
				const offsetY = (0.5 - py) * 36;
				media.style.transform = `translate(-50%,-50%) translateY(${offsetY}px) translateX(${offsetX}px)`;
			});
			banner.addEventListener('pointerleave', function(){ media.style.transform = 'translate(-50%,-50%)'; });
		}

	// Card preview modal handlers
		const previewButtons = document.querySelectorAll('.preview-btn');
		if (previewButtons.length){
			const previewModalEl = document.getElementById('previewModal');
			const pmTitle = document.getElementById('pm-title');
			const pmContent = document.getElementById('pm-content');
			const pmLink = document.getElementById('pm-link');
			const bsModal = new bootstrap.Modal(previewModalEl);

			previewButtons.forEach(btn => {
				btn.addEventListener('click', function(e){
					const card = e.target.closest('.card-preview');
					if (!card) return;
					pmTitle.textContent = card.dataset.title || '';
					pmContent.textContent = card.dataset.content || '';
					pmLink.href = card.dataset.href || '#';
					bsModal.show();
				});
			});
		}

	// object-fit fallback: for browsers that don't support object-fit, move <img> into background on its parent
	(function(){
		function supportsObjectFit(){
			return 'objectFit' in document.documentElement.style;
		}
		function applyObjectFitFallback(){
			if (supportsObjectFit()) return;
			document.querySelectorAll('img[data-object-fit]').forEach(function(img){
				var parent = img.parentElement;
				if (!parent) return;
				var src = img.currentSrc || img.src;
				parent.style.backgroundImage = 'url("' + src + '")';
				parent.style.backgroundPosition = img.getAttribute('data-object-position') || 'center';
				parent.style.backgroundSize = img.getAttribute('data-object-fit') || 'cover';
				parent.style.backgroundRepeat = 'no-repeat';
				img.style.opacity = '0';
				img.style.width = '100%';
				img.style.height = '100%';
				img.style.position = 'absolute';
				img.style.left = '0';
				img.style.top = '0';
				if (getComputedStyle(parent).position === 'static') parent.style.position = 'relative';
			});
		}
		if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', applyObjectFitFallback); else applyObjectFitFallback();
	})();
});
