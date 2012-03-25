/* Author:

*/

// Mobile-only scripts (<= 420px)
if (Modernizr.mq('(max-width: 420px)')) {
	$(function () {
		var agenda_href = $('#ai1ec-view-agenda').attr('href');
		if (location.href.indexOf(agenda_href) < 0) {
			location.href = agenda_href;
		}
	});
}