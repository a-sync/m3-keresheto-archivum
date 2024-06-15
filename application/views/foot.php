<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
	</main>

	<footer class="mdc-typography--caption">
		{elapsed_time} mp
		<br>
		<a href="https://github.com/a-sync/m3-keresheto-archivum" style="fill:#c6cbd1"><svg width="16" height="16" viewBox="0 0 16 16" version="1.1" aria-hidden="true"><path fill-rule="evenodd" d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z"></path></svg></a>
	</footer>

	<script type="text/javascript">var exports = {};</script>
	<script src="https://unpkg.com/video.js@latest/dist/video.min.js"></script>
	<script src="https://unpkg.com/video.js@latest/dist/lang/hu.js"></script>
	<script src="https://unpkg.com/srt-webvtt@latest/lib/index.js"></script>
	<script type="text/javascript">
		async function startPlayer(videoElement) {
			console.log('init player', videoElement.dataset.programid);

			const vidRes = await fetch(String('\x68\x74\x74\x70\x73\x3a\x2f\x2f\x6e\x65\x6d\x7a\x65\x74\x69\x61\x72\x63\x68\x69\x76\x75\x6d\x2e\x68\x75\x2f\x6d\x33\x2f\x73\x74\x72\x65\x61\x6d\x3f\x6e\x6f\x5f\x6c\x62\x3d\x31\x26\x74\x61\x72\x67\x65\x74\x3d')+videoElement.dataset.programid);
			const resJson = await vidRes.json();
			const url = await getPlaylistBlob(resJson.url);

			const player = videojs(videoElement.id);
			player.src({
				src: url,
				type: 'application/x-mpegURL'
  			});

			if (videoElement.dataset.hassubtitle === '1') {
				const subRes = await fetch(String('\x68\x74\x74\x70\x73\x3a\x2f\x2f\x6e\x65\x6d\x7a\x65\x74\x69\x61\x72\x63\x68\x69\x76\x75\x6d\x2e\x68\x75\x2f\x73\x75\x62\x74\x69\x74\x6c\x65\x2f')+videoElement.dataset.programid+String('\x2e\x73\x72\x74'));
				const resBlob = await subRes.blob();
				const trackUrl = await toWebVTT(resBlob);

				player.addRemoteTextTrack({src: trackUrl, srclang: 'hu', label: 'Magyar'});
			}

			player.ready(() => {
				console.log('player ready', videoElement.dataset.programid);
				player.play();
			});

			return false;
		}

		function initPlayer(el) {
			el.controls = true;
			const player = videojs(el.id, {
				"language": "hu",
				"fullscreen": {
					"options": {
						"navigationUI": "show"
					}
				},
				"fluid": true
			}, () => {
				player.one('click', () => startPlayer(el));
			});
		}
		
		async function getPlaylistBlob(url) {
			const response = await fetch(url, {
				headers: { 'User-Agent': '' }
			});

			if (!response.ok) {
				throw new Error(`Failed to fetch playlist: ${response.statusText}`);
			}

			const playlistText = await response.text();
			const blob = new Blob([playlistText], { type: 'application/x-mpegURL' });
			return URL.createObjectURL(blob);
		}

		const domLoaded = () => {
			console.log('DOM loaded');
			const videoElements = document.querySelectorAll('.video-js');
			
			for (const el of videoElements) {
				const overlay = el.parentNode.querySelector('.m3player-overlay');
				if (!overlay) initPlayer(el);
				else el.addEventListener('dblclick',ev=>{ev.preventDefault();if(overlay){overlay.remove()};initPlayer(el);},{'once':true});// DEBUG
			}
		};

		if (document.readyState === 'complete' ||
			(document.readyState !== 'loading' && !document.documentElement.doScroll)) domLoaded();
		else document.addEventListener('DOMContentLoaded', domLoaded);
	</script>
</body>
</html>
