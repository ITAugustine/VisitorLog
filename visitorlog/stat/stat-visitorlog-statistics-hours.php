<?php
/**
 * Statistics of visits by the hour
 *
 */
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit;

/**
 * Methods of VisitorLog_Statistics_Hours class.
 *
 * @method public static function render_visitors_report_hours() 
 */
class VisitorLog_Statistics_Hours
{
    /**
     * Render visitors report hours.
     *
     * @uses VisitorLog_Utility::get_data_table_nolimit()
     * @uses VisitorLog_System_View::show_message()
     */
    public static function render_visitors_report_hours()
    {
        global $visitorlog_users_resident_parameters;

        $сurrent_time = $visitorlog_users_resident_parameters['datetime'];
        $H = substr($сurrent_time, 11, 2);
        $сurrent_time = mysql2date( 'd M, Y H:i', $сurrent_time );

        $result = VisitorLog_Utility::get_data_table_nolimit( 'hour', $num_rows, $err );
        if ( $err ) {
            $msg_err = __('Error reading data from a database table', 'visitorlog');
            VisitorLog_System_View::show_message( 'orange', $msg_err );
        }
        $all_user = $all_admin = $all_block = $all_bot = $all_admin_login = $all_admin_404 = 0;
        $all_bot_ddos = $all_bot_login = $all_bot_spamco = $all_bot_spamcf = $all_bot_404 = $all_botother = 0;
        $all_lockddos = $all_locklogin = $all_lockspamco = $all_lockspamcf = $all_lock404 = $all_lockother = 0;

        $i = 0;
        foreach ($result as $res) {
            //----- Line
            $res_user [$i] = $res->user;
            $res_admin[$i] = $res->admin;
            $res_bot  [$i] = $res->bot;
            $res_block[$i] = $res->block;

            $sum_line[$i] = $res->user + $res->admin + $res->bot;

            $admin_login[$i] = $res->adminlogin;
            $admin_404  [$i] = $res->admin404;

            $res_botddos  [$i] = $res->botddos;
            $res_botlogin [$i] = $res->botlogin;
            $res_botspamcf[$i] = $res->botspamcf;
            $res_botspamco[$i] = $res->botspamco;
            $res_bot404   [$i] = $res->bot404;
            $res_botother [$i] = $res->botother;

            $block_ddos  [$i] = $res->blockddos;
            $block_login [$i] = $res->blocklogin;
            $block_spamcf[$i] = $res->blockcf;
            $block_spamco[$i] = $res->blockco;
            $block_404   [$i] = $res->block404;
            $block_other [$i] = $res->blockother;

            //----- Sum
            $all_user  = $all_user  + $res->user;
            $all_admin = $all_admin + $res->admin;
            $all_bot   = $all_bot   + $res->bot;
            $all_block = $all_block + $res->block;

            $all_admin_login = $all_admin_login + $res->adminlogin;
            $all_admin_404   = $all_admin_404   + $res->admin404;

            $all_bot_ddos   = $all_bot_ddos   + $res->botddos;
            $all_bot_login  = $all_bot_login  + $res->botlogin;
            $all_bot_spamco = $all_bot_spamco + $res->botspamco;
            $all_bot_spamcf = $all_bot_spamcf + $res->botspamcf;
            $all_bot_404    = $all_bot_404    + $res->bot404;
            $all_botother   = $all_botother   + $res->botother;

            $all_lockddos   = $all_lockddos   + $res->blockddos;
            $all_locklogin  = $all_locklogin  + $res->blocklogin;
            $all_lockspamco = $all_lockspamco + $res->blockco;
            $all_lockspamcf = $all_lockspamcf + $res->blockcf;
            $all_lock404    = $all_lock404    + $res->block404;
            $all_lockother  = $all_lockother  + $res->blockother;

            $i++;
        }

        $total = $all_user + $all_admin + $all_bot;

        wp_localize_script( 'visitorlog-chart1', 'visitorlog_hour', [
            'user'  => $res_user,
            'bot'   => $res_bot,
            'admin' => $res_admin,
            'block' => $res_block,
        ] );
        wp_localize_script( 'visitorlog-chart1', 'visitorlog_names', array( 
            'users'  => __( 'Users', 'visitorlog' ),
            'bots'   => __( 'Bots', 'visitorlog' ),
            'blocks' => __( 'Blocks', 'visitorlog' ),
            'admins' => __( 'Admins', 'visitorlog' ),
        ) );
        wp_localize_script( 'visitorlog-chart2', 'visitorlog_hourbot', [
            'botddos'   => $res_botddos,
            'botlogin'  => $res_botlogin,
            'botspamcf' => $res_botspamcf,
            'botspamco' => $res_botspamco,
            'bot404'    => $res_bot404,
        ] );
        wp_localize_script( 'visitorlog-chart2', 'visitorlog_bot', array( 
            'botddos'   => __( 'Bots Ddos', 'visitorlog' ),
            'botlogin'  => __( 'Bots Login', 'visitorlog' ),
            'botspamcf' => __( 'Bots SpamContact', 'visitorlog' ),
            'botspamco' => __( 'Bots SpamComment', 'visitorlog' ),
            'bot404'    => __( 'Bots 404', 'visitorlog' ),
        ) );
        ?>
        <div class="row clearfix" >
            <div class="col-lg-12">
                <div class="card1">    
                    <div class="header">
                        <font style="font-size:16px;">
                            <em><b>
                                <?php esc_html_e('Visitors','visitorlog');?>.&nbsp;&nbsp;&nbsp;
                                <?php esc_html_e('Date / Time','visitorlog');?>:&nbsp;&nbsp;&nbsp;
                                <?php echo esc_html($сurrent_time);?>
                            </b></em>
                        </font>
                        <p style="text-align:right;font-size:13px;margin-top:-20px;">
                            <?php esc_html_e('hours scale','visitorlog');?>
                        </p>
                    </div>
                    <div class="body" style="margin-top:-20px;">
                        <div class="row">
                            <table style="margin-left:18px;">
                                <thead>
                                    <tr>
                                        <td width="200"></td>
                                        <td width="200"></td>
                                        <td width="200"></td>
                                        <td width="200"></td>
                                    </tr>
                                </thead>
                                <tbody style="font-size:13px;"> 
                                    <tr>
                                        <td>
                                            <?php esc_html_e('Users','visitorlog');?>&nbsp; 
                                            <?php echo esc_html($all_user);?>
                                            <div style="width:50px;height:5px;background-color:#666666;"/>
                                        </td>
                                        <td>
                                            <?php esc_html_e('Bots','visitorlog');?>&nbsp; 
                                            <?php echo esc_html($all_bot);?>
                                            <div style="width:50px;height:5px;background-color:#FFDB00;"/>
                                        </td>
                                        <td>
                                            <?php esc_html_e('Blocks','visitorlog');?>&nbsp;
                                            <?php echo esc_html($all_block);?>
                                            <div style="width:50px;height:5px;background-color:#F06D66;"/>
                                        </td>
                                        <td>
                                            <?php esc_html_e('Admins','visitorlog');?>&nbsp; 
                                            <?php echo esc_html($all_admin);?>
                                            <div style="width:50px;height:5px;background-color:#318CE7;"/>
                                        </td>
                                    </tr>
                                </tbody>    
                            </table>
                        </div>
                        <div id="area_chart_hour" class="graph" style="margin-top:10px; min-height: 250px;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row clearfix" style="margin-top:-20px;">
            <div class="col-lg-12">
                <div class="card1">    
                    <div class="header">
                        <font style="font-size:16px;">
                            <em><b>
                                <?php esc_html_e('Event bots on the site','visitorlog');?>.&nbsp;&nbsp;&nbsp;
                                <?php esc_html_e('Date / Time','visitorlog');?>:&nbsp;&nbsp;&nbsp;
                                <?php echo esc_html($сurrent_time);?>
                            </b></em>
                        </font>
                        <p style="text-align:right;font-size:13px;margin-top:-20px;">
                            <?php esc_html_e('hours scale','visitorlog');?>
                        </p>
                    </div>
                    <div class="body" style="margin-top:-20px;">
                        <div class="row">
                            <table style="margin-left:18px;">
                                <thead>
                                    <tr>
                                        <td width="200"></td>
                                        <td width="200"></td>
                                        <td width="200"></td>
                                        <td width="200"></td>
                                        <td width="200"></td>
                                    </tr>
                                </thead>
                                <tbody style="font-size:13px;"> 
                                    <tr>
                                        <td>
                                            <?php esc_html_e('Bots Ddos','visitorlog');?>&nbsp;
                                            <?php echo esc_html($all_bot_ddos);?>
                                            <div style="width:50px;height:5px;background-color:#FFE37C;"/>
                                        </td>
                                        <td>
                                            <?php esc_html_e('Bots Login','visitorlog');?>&nbsp;
                                            <?php echo esc_html($all_bot_login);?>
                                            <div style="width:50px;height:5px;background-color:#666666;"/>
                                        </td>
                                        <td>
                                            <?php esc_html_e('Bots Spam contact form','visitorlog');?>&nbsp;
                                            <?php echo esc_html($all_bot_spamcf);?>
                                            <div style="width:50px;height:5px;background-color:#50C878;"/>
                                        </td>
                                        <td>
                                            <?php esc_html_e('Bots Spam comments','visitorlog');?>&nbsp;
                                            <?php echo esc_html($all_bot_spamco);?>
                                            <div style="width:50px;height:5px;background-color:#F07427;"/> 
                                        </td>
                                        <td>
                                            <?php esc_html_e('Bots event 404','visitorlog');?>&nbsp;
                                            <?php echo esc_html($all_bot_404);?>
                                            <div style="width:50px;height:5px;background-color:#973DF0;"/>
                                        </td>
                                    </tr>
                                </tbody>    
                            </table>
                        </div>
                        <div id="area_chart_hour_bot" class="graph" style="margin-top:10px; min-height:250px;"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-------------------- Table -------------------------------->
        <div  style="margin-top:-13px;"></div>
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="header">
                    <font style="font-size:16px;">
                        <em><b>
            <?php esc_html_e('Visitors and bots of events on the site','visitorlog');?>.&nbsp;&nbsp;&nbsp;
            <?php esc_html_e('Date / Time','visitorlog');?>:&nbsp;&nbsp;&nbsp;
            <?php echo esc_html($сurrent_time);?>
                        </b></em>
                    </font>
                </div>
                <div style="margin-top:10px;"></div>
                <div class="body table-responsive card1 project_list">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr align="center">
<td class="vl-thead" width="60"  rowspan="2"><?php esc_html_e('Hours','visitorlog');?></td>
<td class="vl-thead"             rowspan="2"><?php esc_html_e('Total','visitorlog');?></td>
<td class="vl-thead" width="140" rowspan="2"><?php esc_html_e('Users','visitorlog');?></td>
<td class="vl-thead" width="140" rowspan="2"><?php esc_html_e('Admins','visitorlog');?></td>
<td class="vl-thead" colspan="7"><?php esc_html_e('Bots or (Bots - Block) or (Bots - Block - Admin)','visitorlog');?></td>
                            </tr>
                            <tr align="center">
<td class="vl-thead" width="140"><?php esc_html_e('All bots','visitorlog');?></td>
<td class="vl-thead" width="140">DDoS</td>
<td class="vl-thead" width="140"><?php esc_html_e('Login','visitorlog');?></td>
<td class="vl-thead" width="140"><?php esc_html_e('Spam comments','visitorlog');?></td>
<td class="vl-thead" width="140"><?php esc_html_e('Spam contact form','visitorlog');?></td>
<td class="vl-thead" width="140"><?php esc_html_e('Event 404','visitorlog');?></td>
<td class="vl-thead" width="140"><?php esc_html_e('Other bots','visitorlog');?></td>
                            </tr>
                        </thead>
                        <tbody> 
                            <?php
                            $bot_disp = $ddos_disp = $login_disp = '';
                            $spamco_disp = $spamcf_disp = $bot_404_disp = $other_disp = '';
                            if ( 0 != $all_bot )   $bot_disp .= $all_bot;
                            if ( 0 != $all_block ) $bot_disp .= '-'. $all_block;

                            if ( 0 != $all_bot_ddos ) $ddos_disp .= $all_bot_ddos;
                            if ( 0 != $all_lockddos ) $ddos_disp .= '-'. $all_lockddos;

                            $mask_al = '';
                            if ( 0 != $all_bot_login ) {
                                $login_disp .= $all_bot_login;
                                $mask_al = '1';
                            }    
                            if ( 0 != $all_locklogin ) {
                                $login_disp .= '-'. $all_locklogin;
                                $mask_al = '11';
                            }    
                            if ( 0 != $all_admin_login ) {
                                switch ( $mask_al ) {
                                    case '':
                                        $login_disp = '0-0-' . $all_admin_login;
                                        break;
                                    case '1':
                                        $login_disp = $login_disp . '-0-' . $all_admin_login;
                                        break;
                                    case '11':
                                        $login_disp = $login_disp . '-' . $all_admin_login;
                                        break;
                                }
                            }
                            $mask_a404 = '';
                            if ( 0 != $all_bot_404 ) {
                                $bot_404_disp .= $all_bot_404;
                                $mask_a404 = '1';
                            }    
                            if ( 0 != $all_lock404 ) {
                                $bot_404_disp .= '-'. $all_lock404;
                                $mask_a404 = '11';
                            }    
                            if ( 0 !=  $all_admin_404 ) {
                                switch ( $mask_a404 ) {
                                    case '':
                                        $bot_404_disp = '0-0-' . $all_admin_404;
                                        break;
                                    case '1':
                                        $bot_404_disp = $bot_404_disp . '-0-' . $all_admin_404;
                                        break;
                                    case '11':
                                        $bot_404_disp = $bot_404_disp . '-' . $all_admin_404;
                                        break;
                                }
                            }                          
                            if ( 0 != $all_bot_spamco ) $spamco_disp .= $all_bot_spamco;
                            if ( 0 != $all_lockspamco ) $spamco_disp .= '-'. $all_lockspamco;

                            if ( 0 != $all_bot_spamcf ) $spamcf_disp .= $all_bot_spamcf;
                            if ( 0 != $all_lockspamcf ) $spamcf_disp .= '-'. $all_lockspamcf;

                            if ( 0 != $all_botother )  $other_disp .= $all_botother;
                            if ( 0 != $all_lockother ) $other_disp .= '-'. $all_lockother;
                            ?>
                            <tr>
                                <td class="vl-thead"></td>
                                <td class="vl-thead"><?php echo esc_html($total);?></td>
                                <td class="vl-thead"><?php echo esc_html($all_user);?></td>
                                <td class="vl-thead"><?php echo esc_html($all_admin);?></td>
                                <td class="vl-thead"><?php echo esc_html($bot_disp);?></td>
                                <td class="vl-thead"><?php echo esc_html($ddos_disp);?></td>
                                <td class="vl-thead"><?php echo esc_html($login_disp);?></td>
                                <td class="vl-thead"><?php echo esc_html($spamco_disp);?></td>
                                <td class="vl-thead"><?php echo esc_html($spamcf_disp);?></td>
                                <td class="vl-thead"><?php echo esc_html($bot_404_disp);?></td>
                                <td class="vl-thead"><?php echo esc_html($other_disp);?></td>
                            </tr>
                            <?php
                            for ( $i=0; $i < 24; $i++ ) { 
                                $bot = $ddos = $login = $spamco = $spamcf = $bot404 = $other = '';
                                if ( 0 != $res_bot[$i] )   $bot .= $res_bot[$i];
                                if ( 0 != $res_block[$i] ) $bot .= '-'. $res_block[$i];

                                if ( 0 != $res_botddos[$i] ) $ddos .= $res_botddos[$i];
                                if ( 0 != $block_ddos[$i] )  $ddos .= '-'. $block_ddos[$i];

                                $mask_al = '';
                                if ( 0 != $res_botlogin[$i] ) {
                                    $login .= $res_botlogin[$i];
                                    $mask_al = '1';
                                }    
                                if ( 0 != $block_login[$i] ) {
                                    $login .= '-'. $block_login[$i];
                                    $mask_al = '11';
                                }    
                                if ( 0 != $admin_login[$i] ) {
                                    switch ( $mask_al ) {
                                        case '':
                                            $login = '0-0-' . $admin_login[$i];
                                            break;
                                        case '1':
                                            $login = $login . '-0-' . $admin_login[$i];
                                            break;
                                        case '11':
                                            $login = $login . '-' . $admin_login[$i];
                                            break;
                                    }
                                }
                                if ( 0 != $res_botspamco[$i] ) $spamco .= $res_botspamco[$i];
                                if ( 0 != $block_spamco[$i] )  $spamco .= '-'. $block_spamco[$i];

                                if ( 0 != $res_botspamcf[$i] ) $spamcf .= $res_botspamcf[$i];
                                if ( 0 != $block_spamcf[$i] )  $spamcf .= '-'. $block_spamcf[$i];

                                $mask_a404 = '';
                                if ( 0 != $res_bot404[$i] ) {
                                    $bot404 .= $res_bot404[$i];
                                    $mask_a404 = '1';
                                }    
                                if ( 0 != $block_404[$i] ) {
                                    $bot404 .= '-'. $block_404[$i];
                                    $mask_a404 = '11';
                                }    
                                if ( 0 != $admin_404[$i] ) {
                                    switch ( $mask_a404 ) {
                                        case '':
                                            $bot404 = '0-0-' . $admin_404[$i];
                                            break;
                                        case '1':
                                            $bot404 = $bot404 . '-0-' . $admin_404[$i];
                                            break;
                                        case '11':
                                            $bot404 = $bot404 . '-' . $admin_404[$i];
                                            break;
                                    }
                                }
                                if ( 0 != $res_botother[$i] ) $other .= $res_botother[$i];
                                if ( 0 != $block_other[$i] )  $other .= '-'. $block_other[$i];
                            ?>
                            <tr>
                                <td class="vl-thead"><?php echo esc_html($i);?></td>
                                <td class="vl-thead"><?php echo esc_html($sum_line[$i]);?></td>
                                <td><?php echo(0 != $res_user[$i])  ? esc_html($res_user[$i])  : '';?></td>
                                <td><?php echo(0 != $res_admin[$i]) ? esc_html($res_admin[$i]) : '';?></td>
                                <td><?php echo esc_html($bot);?></td>
                                <td><?php echo esc_html($ddos);?></td>
                                <td><?php echo esc_html($login);?></td>
                                <td><?php echo esc_html($spamco);?></td>
                                <td><?php echo esc_html($spamcf);?></td>
                                <td><?php echo esc_html($bot404);?></td>
                                <td><?php echo esc_html($other);?></td>
                            </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div> 
        </div> 
        <?php    

    } // END func

} // END class