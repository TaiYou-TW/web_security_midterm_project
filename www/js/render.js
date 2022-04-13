const escapeHTMLPolicy = trustedTypes.createPolicy("myEscapePolicy", {
    createHTML: (string) => string
});

let content = document.querySelector('#content');
let tmp = content.innerHTML;
tmp = tmp.replace(/\[b\](.*)\[\/b\]/g, '<b>$1</b>');
tmp = tmp.replace(/\[i\](.*)\[\/i\]/g, '<i>$1</i>');
tmp = tmp.replace(/\[u\](.*)\[\/u\]/g, '<u>$1</u>');
tmp = tmp.replace(/\[img\](.*)\[\/img\]/g, '<img src="$1">');
tmp = tmp.replace(/\[color=([a-zA-Z]*|#[a-zA-Z0-9]{3}|#[a-zA-Z0-9]{6})\](.*)\[\/color\]/g, '<span style="color: $1;">$2</span>');

const escaped = escapeHTMLPolicy.createHTML(tmp);
content.innerHTML = escaped;
