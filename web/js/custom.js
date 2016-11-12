/**
 * Created by abrikos on 12.11.16.
 */
function siteUpdate() {
	$.getJSON('/update/pull',function (json) {
		console.log(json)
	})
}
