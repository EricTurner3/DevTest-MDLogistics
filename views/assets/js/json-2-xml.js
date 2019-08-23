//derived from: https://www.site24x7.com/tools/json-to-xml.html
function json_to_xml(json) {
    try {
        out = "<root>"; //No I18N
        var obj = JSON.parse(json);

       //get all of the people in the body and add to XML
       for(var i = 0; i < obj.body.length; i++){
            out += '<person>';
                out += '<firstName>' + obj.body[i][0] +'</firstName>';
                out += '<lastName>' + obj.body[i][1] +'</lastName>';
                out += '<email>' + obj.body[i][2] +'</email>';
                out += '<gender>' + obj.body[i][3] +'</gender>';
                out += '<ip>' + obj.body[i][4] +'</ip>';
            out += '</person>';
        }
       
            
        
        out += "</root>"
        out = '<' + '?xml version="1.0" encoding="UTF-8" ?' + '>\n' + formatXml(out); //No I18N
        return out;
    } catch (e) {
        return "Error : \n" + e; //No I18N
    }
}

function formatXml(xml) {
    var reg = /(>)\s*(<)(\/*)/g;
    var wsexp = / *(.*) +\n/g;
    var contexp = /(<.+>)(.+\n)/g;
    xml = xml.replace(reg, '$1\n$2$3').replace(wsexp, '$1\n').replace(contexp, '$1\n$2');
    var pad = 0;
    var formatted = '';
    var lines = xml.split('\n');
    var indent = 0;
    var lastType = 'other';
    // 4 types of tags - single, closing, opening, other (text, doctype, comment) - 4*4 = 16 transitions 
    var transitions = {
        'single->single': 0,
        'single->closing': -1,
        'single->opening': 0,
        'single->other': 0,
        'closing->single': 0,
        'closing->closing': -1,
        'closing->opening': 0,
        'closing->other': 0,
        'opening->single': 1,
        'opening->closing': 0,
        'opening->opening': 1,
        'opening->other': 1,
        'other->single': 0,
        'other->closing': -1,
        'other->opening': 0,
        'other->other': 0
    };

    for (var i = 0; i < lines.length; i++) {
        var ln = lines[i];
        var single = Boolean(ln.match(/<.+\/>/)); // is this line a single tag? ex. <br />
        var closing = Boolean(ln.match(/<\/.+>/)); // is this a closing tag? ex. </a>
        var opening = Boolean(ln.match(/<[^!].*>/)); // is this even a tag (that's not <!something>)
        var type = single ? 'single' : closing ? 'closing' : opening ? 'opening' : 'other';
        var fromTo = lastType + '->' + type;
        lastType = type;
        var padding = ' ';

        indent += transitions[fromTo];
        for (var j = 0; j < indent; j++) {
            padding += '    ';
        }
        if (fromTo == 'opening->closing')
            formatted = formatted.substr(0, formatted.length - 1) + ln + '\n'; // substr removes line break (\n) from prev loop
        else
            formatted += padding + ln + '\n';
    }

    return formatted;
}
