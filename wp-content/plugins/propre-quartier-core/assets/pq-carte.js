/**
 * Carte interactive Propre Quartier (Leaflet + OpenStreetMap).
 *
 * Deux usages :
 *  1. Carte d'affichage : un div .pq-carte porteur d'un attribut data-points
 *     -> on pose un marqueur colore par signalement.
 *  2. Carte du formulaire (#pq-carte-form) : clic = on place un marqueur et on
 *     remplit les champs caches pq_lat / pq_lng.
 */
(function () {
	'use strict';

	function couleurMarqueur(couleur) {
		return L.divIcon({
			className: 'pq-marqueur',
			html: '<span style="background:' + couleur + '"></span>',
			iconSize: [22, 22],
			iconAnchor: [11, 11],
		});
	}

	function initCarteAffichage(el) {
		var points = [];
		try {
			points = JSON.parse(el.getAttribute('data-points') || '[]');
		} catch (e) {
			points = [];
		}

		var carte = L.map(el).setView([PQ_CONFIG.centreLat, PQ_CONFIG.centreLng], PQ_CONFIG.zoom);
		// Fond de carte epure (CartoDB Positron) : gris clair, peu de bruit visuel.
		L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
			attribution: '&copy; OpenStreetMap &copy; CARTO',
			subdomains: 'abcd',
			maxZoom: 20,
		}).addTo(carte);

		var bornes = [];
		points.forEach(function (pt) {
			var lat = parseFloat(pt.lat), lng = parseFloat(pt.lng);
			if (isNaN(lat) || isNaN(lng)) { return; }

			var popup =
				'<div class="pq-popup">' +
				(pt.image ? '<img src="' + pt.image + '" alt="">' : '') +
				'<span class="pq-badge" style="background:' + pt.couleur + '">' + pt.statut_label + '</span>' +
				'<strong>' + pt.titre + '</strong>' +
				(pt.adresse ? '<em>' + pt.adresse + '</em>' : '') +
				'<p>' + pt.extrait + '</p>' +
				'<a href="' + pt.lien + '">Voir le detail &rarr;</a>' +
				'</div>';

			L.marker([lat, lng], { icon: couleurMarqueur(pt.couleur) })
				.addTo(carte)
				.bindPopup(popup);
			bornes.push([lat, lng]);
		});

		if (bornes.length > 1) {
			carte.fitBounds(bornes, { padding: [40, 40] });
		}
	}

	function initCarteFormulaire(el) {
		var carte = L.map(el).setView([PQ_CONFIG.centreLat, PQ_CONFIG.centreLng], PQ_CONFIG.zoom);
		// Fond de carte epure (CartoDB Positron) : gris clair, peu de bruit visuel.
		L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
			attribution: '&copy; OpenStreetMap &copy; CARTO',
			subdomains: 'abcd',
			maxZoom: 20,
		}).addTo(carte);

		var marqueur = null;
		carte.on('click', function (e) {
			if (marqueur) {
				marqueur.setLatLng(e.latlng);
			} else {
				marqueur = L.marker(e.latlng, { icon: couleurMarqueur('#2563eb') }).addTo(carte);
			}
			document.getElementById('pq_lat').value = e.latlng.lat.toFixed(6);
			document.getElementById('pq_lng').value = e.latlng.lng.toFixed(6);
		});
	}

	document.addEventListener('DOMContentLoaded', function () {
		var form = document.getElementById('pq-carte-form');
		if (form) { initCarteFormulaire(form); }

		document.querySelectorAll('.pq-carte').forEach(function (el) {
			if (el.id !== 'pq-carte-form') { initCarteAffichage(el); }
		});
	});
})();
