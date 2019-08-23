function json_to_html(json) {
    try {
        out = '<!DOCTYPE html><html lang="en"><body>'; //header
        var obj = JSON.parse(json);

       //get all of the people in the body and add to XML
       out += '<table>';
       out += '<thead><tr><th>First Name</th><th>Last Name</th><th>Email</th><th>Gender</th><th>IP</th></tr></thead>'; //header
       for(var i = 0; i < obj.body.length; i++){
            out += '<tr>';
                out += '<td>' + obj.body[i][0] +'</td>';
                out += '<td>' + obj.body[i][1] +'</td>';
                out += '<td>' + obj.body[i][2] +'</td>';
                out += '<td>' + obj.body[i][3] +'</td>';
                out += '<td>' + obj.body[i][4] +'</td>';
            out += '</tr>';
        }
       
            
        
        out += "</table></body>"
        return out;
    } catch (e) {
        return "Error : \n" + e; //No I18N
    }
}