<?php
/**
 * VisitorLog Updates Page.
 *
 */
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods VisitorLog_Updates.
 *
 * @method  public static function render_updates() 
 */
class VisitorLog_Updates
{
    /**
     * Render Updates.
     *
     * @uses VisitorLog_Utility::get_data_select()
     * @uses VisitorLog_System_View::show_message()
     * @uses VisitorLog_System_View::show_select_badge()
     */
    public static function render_updates()
    {
        $result = VisitorLog_Utility::get_data_select( 'update', 'viewall', $n_rows, $err_ );
        if ( $err_ ) { 
            VisitorLog_System_View::show_message( 'orange', $err_ );
        }    
        ?>
        <div class="row clearfix">
            <div class="col-lg-12">
                <div class="body">
                    <div class="header" style="margin-top:0px;background-color:#E2E5DE;">
                        <h4><p style="text-align:right;"><?php esc_html_e('rows', 'visitorlog');?>:&nbsp;&nbsp;&nbsp;
                            <?php echo esc_html($n_rows);?>
                            </p>
                        </h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover js-basic-example dataTable">
                            <thead>
                                <tr>                                       
                    <td class="vl-thead" style="width:220px;"><?php esc_html_e('Date / Time','visitorlog');?></td>
                    <td class="vl-thead"><?php esc_html_e('Mode','visitorlog');?></td>
                    <td class="vl-thead"><?php esc_html_e('Plugin Version','visitorlog');?></td>
                    <td class="vl-thead"><?php esc_html_e('Database version','visitorlog');?></td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 0;
                                while ( $i < $n_rows ) {
                                    $date_time = $result[$i]->date_time;
                                    $category  = $result[$i]->category;
                                    $p_vers    = $result[$i]->plugin_version;
                                    $db_vers   = $result[$i]->db_version;

                                    $date_time = gmdate('Y-m-d H:i:s', $date_time);
                                    $i++;
                                    ?> 
                                <tr style="font-size:13px;">
                                    <td><?php echo esc_html($date_time);?></td>
                                    <td><span <?php VisitorLog_System_View::show_badge( $category );?>>
                                        <?php echo esc_html($category);?>
                                        </span>
                                    </td>
                                    <td><strong><?php echo esc_html($p_vers);?></strong></td>
                                    <td><strong><?php echo esc_html($db_vers);?></strong></td>
                                </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div> 
            </div> 
        </div>
        <?php

    } // END func 


} // END class