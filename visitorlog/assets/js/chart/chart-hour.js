/**
 * VL-Chart_hour - 1.0
 *
 */
class UserChartHour {

    static MorrisAreaHour() {

        let time_arr = ['00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11',  '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23'];
        let arr_data = [];
        let lineColors_hour = ['#666666', '#FFDB00', '#F06D66', '#318CE7'];
        let ykeys_hour      = ['users', 'bots', 'blocks', 'admins'];
        let labels_hour     = [visitorlog_names.users, visitorlog_names.bots, visitorlog_names.blocks, visitorlog_names.admins];
        let element_hour = 'area_chart_hour';

        for (let i = 0; i < 24; i++) {
            arr_data.push({
                x:      time_arr[i],
                users:  parseInt(visitorlog_hour.user[i]),
                bots:   parseInt(visitorlog_hour.bot[i]),
                admins: parseInt(visitorlog_hour.admin[i]),
                blocks: parseInt(visitorlog_hour.block[i])
            });
        } 
        Morris.Area({
             element:    element_hour,
             data:       arr_data,
             lineColors: lineColors_hour,
             xkey:    'x',
             ykeys:   ykeys_hour,
             labels:  labels_hour,
             pointSize:  3,
             lineWidth:  3,
             smooth:    true,
             resize:    true,
             parseTime: false,
             fillOpacity: 0.2,
             behaveLikeLine: true,
             gridLineColor: '#e0e0e0',
             hideHover: 'auto'
        });
    }
}
UserChartHour.MorrisAreaHour();