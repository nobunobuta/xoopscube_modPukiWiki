var modPukiWiki_elem;
var modPukiWiki_crl;
var modPukiWiki_scrx;
var modPukiWiki_scry;
var modPukiWiki_rngx;
var modPukiWiki_rngy;
var modPukiWiki_WinIE=(document.all&&!window.opera&&navigator.platform=="Win32");

function modPukiWiki_pos(){
	if(!modPukiWiki_WinIE)return;
	if (!(document.activeElement.type == "text" || document.activeElement.type == "textarea")) return;

	var r=document.selection.createRange();
	modPukiWiki_rngx=r.offsetLeft;
	modPukiWiki_rngy=r.offsetTop;
	r.moveEnd("textedit");
	modPukiWiki_crl =r.text.length;
	modPukiWiki_elem = document.activeElement;
	modPukiWiki_scrx=document.body.scrollLeft;
	modPukiWiki_scry=document.body.scrollTop;
}

function modPukiWiki_eclr(){
	if(!modPukiWiki_WinIE)return;
	modPukiWiki_elem = NULL;
}

function modPukiWiki_face(v)
{
	if(!modPukiWiki_WinIE || !modPukiWiki_elem)return;

	if (modPukiWiki_elem.type=="textarea")
	{
		document.body.scrollLeft=modPukiWiki_scrx;
		document.body.scrollTop=modPukiWiki_scry;
		var r=modPukiWiki_elem.createTextRange();
		r.moveToPoint(modPukiWiki_rngx,modPukiWiki_rngy);
		r.text= ' ' + v;
		modPukiWiki_elem.focus();
		modPukiWiki_pos();
	}
	else if (modPukiWiki_elem.type=="text")
	{
		var r=modPukiWiki_elem.createTextRange();
		r.collapse();
		r.moveStart("character",modPukiWiki_elem.value.length-modPukiWiki_crl);
		r.text= ' ' + v;
		modPukiWiki_elem.focus();
	}
}

function modPukiWiki_tag(v) {
	if (!modPukiWiki_WinIE || !document.selection) return;
	var str =
		document.selection.createRange().text;
	if (!str)
	{
		alert('対象範囲を選択してください。');
		return;
	}
	if ( v == 'size' )
	{
		var default_size = "%";
		v = prompt('文字の大きさ ( % または pt[省略可] で指定): ', default_size);
		if (!v) return;
		if (!v.match(/(%|pt)$/))
			v += "pt";
		if (!v.match(/\d+(%|pt)/))
			return;
	}
	if (str.match(/^&font\(.*?\){.*};$/))
	{
		str = str.replace(/^(&font\(.*?)(\){.*};)$/,"$1," + v + "$2");
	}
	else
	{
		str = '&font(' + v + '){' + str + '};';
	}
	document.selection.createRange().text = str;
	if (modPukiWiki_elem != null) modPukiWiki_elem = null;
}

function modPukiWiki_linkPrompt(v) {
	if (!modPukiWiki_WinIE || !document.selection) return;
	var str = document.selection.createRange().text;
	if (!str)
	{
		alert('対象範囲を選択してください。');
		return;
	}
	var default_url = "http://";
	regex = "^s?https?://[-_.!~*'()a-zA-Z0-9;/?:@&=+$,%#]+$";
	var cbText = clipboardData.getData("Text");
	if(cbText && cbText.match(regex))
		default_url = cbText;
	var my_link = prompt('URL: ', default_url);
	if (my_link != null)
		document.selection.createRange().text = '[[' + str + ':' + my_link + ']]';
	if (modPukiWiki_elem != null) modPukiWiki_elem = null;
}

function modPukiWiki_show_fontset_img(mapurl,faceurl)
{
	if (!mapurl) {
		mapurl = '.';
	}
	if (!faceurl) {
		faceurl = '.';
	}
	if ( modPukiWiki_WinIE )
	{
		var str = '<img src="'+mapurl+'/buttons.gif" width="103" height="16" border="0" usemap="#map_button">&nbsp;<img src="'+mapurl+'/colors.gif" width="64" height="16" border="0" usemap="#map_color">&nbsp;<span style="cursor:hand;"><img src="'+faceurl+'/smile.gif" width="15" height="15" border="0" alt=":)" onClick="javascript:modPukiWiki_face(\':)\'); return false;"><img src="'+faceurl+'/bigsmile.gif" width="15" height="15" border="0" alt=":D" onClick="javascript:modPukiWiki_face(\':D\'); return false;"><img src="'+faceurl+'/huh.gif" width="15" height="15" border="0" alt=":p" onClick="javascript:modPukiWiki_face(\':p\'); return false;"><img src="'+faceurl+'/oh.gif" width="15" height="15" border="0" alt="XD" onClick="javascript:modPukiWiki_face(\'XD\'); return false;"><img src="'+faceurl+'/wink.gif" width="15" height="15" border="0" alt=";)" onClick="javascript:modPukiWiki_face(\';)\'); return false;"><img src="'+faceurl+'/sad.gif" width="15" height="15" border="0" alt=";(" onClick="javascript:modPukiWiki_face(\';(\'); return false;"><img src="'+faceurl+'/heart.gif" width="15" height="15" border="0" alt="&amp;heart;" onClick="javascript:modPukiWiki_face(\'&amp;heart;\'); return false;"></span>';
		document.write(str);
	}
}