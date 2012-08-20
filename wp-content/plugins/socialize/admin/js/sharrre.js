function shareChart(bardataID, url, title) {
    jQuery('#' + bardataID ).sharrre({
        share: {
            googlePlus: true,
            facebook: true,
            twitter: true,
            digg: true,
            delicious: true,
            stumbleupon: true,
            linkedin: true,
            pinterest: true
        },
        url: url,
        enableHover: false,
        render: function(api, options) {
            var output = '';
            output += '<tr>';
            output += '<td>' + title+ '</td>';
            output += '<td>' + options.total+ '</td>';
            output += '<td>' + options.count.googlePlus + '</td>';
            output += '<td>' + options.count.facebook+ '</td>';
            output += '<td>' + options.count.twitter+ '</td>';
            output += '<td>' + options.count.digg+ '</td>';
            output += '<td>' + options.count.delicious+ '</td>';
            output += '<td>' + options.count.stumbleupon+ '</td>';
            output += '<td>' + options.count.linkedin+ '</td>';
            output += '<td>' + options.count.pinterest+ '</td>';        
            output += '</tr>';
            jQuery('#metrics-table tr:last').after(output);
            jQuery('#' + bardataID ).hide();
        }
    });
}