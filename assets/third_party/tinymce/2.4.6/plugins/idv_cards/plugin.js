/* idv Cards v 1.0
 * (c) Denis Isaev 05/2014
 * idv@idv.su
 */

tinymce.PluginManager.add('idv_cards', function(editor, url) {
var idv_cards = [
["2c", "3c", "4c", "5c", "6c", "7c", "8c", "9c", "10c", "jc", "qc", "kc", "ac"],
["2d", "3d", "4d", "5d", "6d", "7d", "8d", "9d", "10d", "jd", "qd", "kd", "add"],
["2s", "3s", "4s", "5s", "6s", "7s", "8s", "9s", "10s", "js", "qs", "ks", "as"],
["2h", "3h", "4h", "5h", "6h", "7h", "8h", "9h", "10h", "jh", "qh", "kh", "ah"],
["2x", "3x", "4x", "5x", "6x", "7x", "8x", "9x", "tx", "jx", "qx", "kx", "ax"]
];

function getHtml() {
var idv_cardsHtml;

idv_cardsHtml = '<table role="list" class="mce-grid">';

tinymce.each(idv_cards, function(row) {
idv_cardsHtml += '<tr>';

tinymce.each(row, function(icon) {
var cardUrl = url + '/img/' + icon + '.gif';

idv_cardsHtml += '<td><a href="#" data-mce-url="' + cardUrl + '" data-mce-alt="' + icon + '" tabindex="-1" ' +
'role="option" aria-label="' + icon + '"><img src="' +
cardUrl + '" style="width: 25px; height: 15px" role="presentation" /></a></td>';
});

idv_cardsHtml += '</tr>';
});

idv_cardsHtml += '</table>';

return idv_cardsHtml;
}

editor.addButton('idv_cards', {
type: 'panelbutton',
panel: {
role: 'application',
autohide: true,
html: getHtml,
onclick: function(e) {
var linkElm = editor.dom.getParent(e.target, 'a');

if (linkElm) {
editor.insertContent(
'<img src="' + linkElm.getAttribute('data-mce-url') + '" alt="' + linkElm.getAttribute('data-mce-alt') + '" />'
);

//this.hide();
}
}
},
tooltip: 'idv Cards',
image: url + '/img/cards-icon.png'
});
});

