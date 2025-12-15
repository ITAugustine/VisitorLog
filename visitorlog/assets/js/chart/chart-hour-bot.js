/**
 * VL-Chart_hour_bot - 1.0
 *
 */
class UserChartHourBot {

    static MorrisAreaHourBot() {

        let time_arr = ['00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11',  '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23'];
        let arr_data = [];

        var element_hour_bot = 'area_chart_hour_bot';
        var lineColors_hour_bot = ['#FFE37C', '#666666', '#50C878', '#F07427', '#973DF0'];
        var ykeys_hour_bot  = ['bot_ddos', 'bot_login', 'bot_spam_contact', 'bot_spam_comment', 'bot_404'];
        var labels_hour_bot = [visitorlog_bot.botddos, visitorlog_bot.botlogin, visitorlog_bot.botspamcf, visitorlog_bot.botspamco, visitorlog_bot.bot404];

        for (let i = 0; i < 24; i++) {
            arr_data.push({
                x:                time_arr[i],
                bot_ddos:         parseInt(visitorlog_hourbot.botddos[i]),
                bot_login:        parseInt(visitorlog_hourbot.botlogin[i]),
                bot_spam_contact: parseInt(visitorlog_hourbot.botspamcf[i]),
                bot_spam_comment: parseInt(visitorlog_hourbot.botspamco[i]),
                bot_404:          parseInt(visitorlog_hourbot.bot404[i])
            });
        } 
        Morris.Area({
             element:    element_hour_bot,
             data:       arr_data,
             lineColors: lineColors_hour_bot,
             xkey:   'x',
             ykeys:  ykeys_hour_bot,
             labels: labels_hour_bot,
             pointSize: 3,
             lineWidth: 3,
             smooth: true,
             resize: true,
             parseTime: false,
             fillOpacity: 0.2,
             behaveLikeLine: true,
             gridLineColor: '#e0e0e0',
             hideHover: 'auto'
        });
    }
}
UserChartHourBot.MorrisAreaHourBot();