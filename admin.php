<?php
require_once( dirname(__FILE__).'/form.lib.php' );

define( 'PHPFMG_USER', "paula@rukandapride.co.zw" ); // must be a email address. for sending password to you.
define( 'PHPFMG_PW', "767076" );

?>
<?php
/**
 * GNU Library or Lesser General Public License version 2.0 (LGPLv2)
*/

# main
# ------------------------------------------------------
error_reporting( E_ERROR ) ;
phpfmg_admin_main();
# ------------------------------------------------------




function phpfmg_admin_main(){
    $mod  = isset($_REQUEST['mod'])  ? $_REQUEST['mod']  : '';
    $func = isset($_REQUEST['func']) ? $_REQUEST['func'] : '';
    $function = "phpfmg_{$mod}_{$func}";
    if( !function_exists($function) ){
        phpfmg_admin_default();
        exit;
    };

    // no login required modules
    $public_modules   = false !== strpos('|captcha||ajax|', "|{$mod}|");
    $public_functions = false !== strpos('|phpfmg_ajax_submit||phpfmg_mail_request_password||phpfmg_filman_download||phpfmg_image_processing||phpfmg_dd_lookup|', "|{$function}|") ;   
    if( $public_modules || $public_functions ) { 
        $function();
        exit;
    };
    
    return phpfmg_user_isLogin() ? $function() : phpfmg_admin_default();
}

function phpfmg_ajax_submit(){
    $phpfmg_send = phpfmg_sendmail( $GLOBALS['form_mail'] );
    $isHideForm  = isset($phpfmg_send['isHideForm']) ? $phpfmg_send['isHideForm'] : false;

    $response = array(
        'ok' => $isHideForm,
        'error_fields' => isset($phpfmg_send['error']) ? $phpfmg_send['error']['fields'] : '',
        'OneEntry' => isset($GLOBALS['OneEntry']) ? $GLOBALS['OneEntry'] : '',
    );
    
    @header("Content-Type:text/html; charset=$charset");
    echo "<html><body><script>
    var response = " . json_encode( $response ) . ";
    try{
        parent.fmgHandler.onResponse( response );
    }catch(E){};
    \n\n";
    echo "\n\n</script></body></html>";

}


function phpfmg_admin_default(){
    if( phpfmg_user_login() ){
        phpfmg_admin_panel();
    };
}



function phpfmg_admin_panel()
{    
    if( !phpfmg_user_isLogin() ){
        exit;
    };

    phpfmg_admin_header();
    phpfmg_writable_check();
?>    
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td valign=top style="padding-left:280px;">

<style type="text/css">
    .fmg_title{
        font-size: 16px;
        font-weight: bold;
        padding: 10px;
    }
    
    .fmg_sep{
        width:32px;
    }
    
    .fmg_text{
        line-height: 150%;
        vertical-align: top;
        padding-left:28px;
    }

</style>

<script type="text/javascript">
    function deleteAll(n){
        if( confirm("Are you sure you want to delete?" ) ){
            location.href = "admin.php?mod=log&func=delete&file=" + n ;
        };
        return false ;
    }
</script>


<div class="fmg_title">
    1. Email Traffics
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=1">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=1">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_EMAILS_LOGFILE) ){
            echo '<a href="#" onclick="return deleteAll(1);">delete all</a>';
        };
    ?>
</div>


<div class="fmg_title">
    2. Form Data
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=2">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=2">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_SAVE_FILE) ){
            echo '<a href="#" onclick="return deleteAll(2);">delete all</a>';
        };
    ?>
</div>

<div class="fmg_title">
    3. Form Generator
</div>
<div class="fmg_text">
    <a href="http://www.formmail-maker.com/generator.php" onclick="document.frmFormMail.submit(); return false;" title="<?php echo htmlspecialchars(PHPFMG_SUBJECT);?>">Edit Form</a> &nbsp;&nbsp;
    <a href="http://www.formmail-maker.com/generator.php" >New Form</a>
</div>
    <form name="frmFormMail" action='http://www.formmail-maker.com/generator.php' method='post' enctype='multipart/form-data'>
    <input type="hidden" name="uuid" value="<?php echo PHPFMG_ID; ?>">
    <input type="hidden" name="external_ini" value="<?php echo function_exists('phpfmg_formini') ?  phpfmg_formini() : ""; ?>">
    </form>

		</td>
	</tr>
</table>

<?php
    phpfmg_admin_footer();
}



function phpfmg_admin_header( $title = '' ){
    header( "Content-Type: text/html; charset=" . PHPFMG_CHARSET );
?>
<html>
<head>
    <title><?php echo '' == $title ? '' : $title . ' | ' ; ?>PHP FormMail Admin Panel </title>
    <meta name="keywords" content="sign up, buy online, rukanda pride, rukandapride.co.zw, rukandapride, rukanda, buy shoes, men shoes, leathers, shoes, pure leather, 100%leather, handcrafted shoes, leather belts, buy online in zimbabwe, zimbabwe online store, belts, wallets, leather bags, slip on slides, leather loafer, leather penny loafer, broguess, oxfords, traveling bag, satchel, laptop bag, bags, leather bags">
    <meta name="description" content="order custom leather product, wallets, belts, highcut, lowcut, chelsea boot, tyukka boot, dress boot, combat boo, combat shoes, loafer, penny loafer, brogues">
    <meta name="generator" content="Lether shoes and accessories, www.rukandapride.co.zw">

    <style type='text/css'>
    body, td, label, div, span{
        font-family : Verdana, Arial, Helvetica, sans-serif;
        font-size : 12px;
    }
    </style>
</head>
<body  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">

<table cellspacing=0 cellpadding=0 border=0 width="100%">
    <td nowrap align=center style="background-color:#024e7b;padding:10px;font-size:18px;color:#ffffff;font-weight:bold;width:250px;" >
        Form Admin Panel
    </td>
    <td style="padding-left:30px;background-color:#86BC1B;width:100%;font-weight:bold;" >
        &nbsp;
<?php
    if( phpfmg_user_isLogin() ){
        echo '<a href="admin.php" style="color:#ffffff;">Main Menu</a> &nbsp;&nbsp;' ;
        echo '<a href="admin.php?mod=user&func=logout" style="color:#ffffff;">Logout</a>' ;
    }; 
?>
    </td>
</table>

<div style="padding-top:28px;">

<?php
    
}


function phpfmg_admin_footer(){
?>

</div>

<div style="color:#cccccc;text-decoration:none;padding:18px;font-weight:bold;">
	<a href="product.html" title="Handcrafted leather shoes, belts, wallets, slides and bags" style="color:#cccccc;font-weight:bold;text-decoration:none;">Shop Now</a>
</div>

</body>
</html>
<?php
}


function phpfmg_image_processing(){
    $img = new phpfmgImage();
    $img->out_processing_gif();
}


# phpfmg module : captcha
# ------------------------------------------------------
function phpfmg_captcha_get(){
    $img = new phpfmgImage();
    $img->out();
    //$_SESSION[PHPFMG_ID.'fmgCaptchCode'] = $img->text ;
    $_SESSION[ phpfmg_captcha_name() ] = $img->text ;
}



function phpfmg_captcha_generate_images(){
    for( $i = 0; $i < 50; $i ++ ){
        $file = "$i.png";
        $img = new phpfmgImage();
        $img->out($file);
        $data = base64_encode( file_get_contents($file) );
        echo "'{$img->text}' => '{$data}',\n" ;
        unlink( $file );
    };
}


function phpfmg_dd_lookup(){
    $paraOk = ( isset($_REQUEST['n']) && isset($_REQUEST['lookup']) && isset($_REQUEST['field_name']) );
    if( !$paraOk )
        return;
        
    $base64 = phpfmg_dependent_dropdown_data();
    $data = @unserialize( base64_decode($base64) );
    if( !is_array($data) ){
        return ;
    };
    
    
    foreach( $data as $field ){
        if( $field['name'] == $_REQUEST['field_name'] ){
            $nColumn = intval($_REQUEST['n']);
            $lookup  = $_REQUEST['lookup']; // $lookup is an array
            $dd      = new DependantDropdown(); 
            echo $dd->lookupFieldColumn( $field, $nColumn, $lookup );
            return;
        };
    };
    
    return;
}


function phpfmg_filman_download(){
    if( !isset($_REQUEST['filelink']) )
        return ;
        
    $filelink =  base64_decode($_REQUEST['filelink']);
    $file = PHPFMG_SAVE_ATTACHMENTS_DIR . basename($filelink);

    // 2016-12-05:  to prevent *LFD/LFI* attack. patch provided by Pouya Darabi, a security researcher in cert.org
    $real_basePath = realpath(PHPFMG_SAVE_ATTACHMENTS_DIR); 
    $real_requestPath = realpath($file);
    if ($real_requestPath === false || strpos($real_requestPath, $real_basePath) !== 0) { 
        return; 
    }; 

    if( !file_exists($file) ){
        return ;
    };
    
    phpfmg_util_download( $file, $filelink );
}


class phpfmgDataManager
{
    var $dataFile = '';
    var $columns = '';
    var $records = '';
    
    function __construct(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }

    function phpfmgDataManager(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }
    
    function parseFile(){
        $fp = @fopen($this->dataFile, 'rb');
        if( !$fp ) return false;
        
        $i = 0 ;
        $phpExitLine = 1; // first line is php code
        $colsLine = 2 ; // second line is column headers
        $this->columns = array();
        $this->records = array();
        $sep = chr(0x09);
        while( !feof($fp) ) { 
            $line = fgets($fp);
            $line = trim($line);
            if( empty($line) ) continue;
            $line = $this->line2display($line);
            $i ++ ;
            switch( $i ){
                case $phpExitLine:
                    continue;
                    break;
                case $colsLine :
                    $this->columns = explode($sep,$line);
                    break;
                default:
                    $this->records[] = explode( $sep, phpfmg_data2record( $line, false ) );
            };
        }; 
        fclose ($fp);
    }
    
    function displayRecords(){
        $this->parseFile();
        echo "<table border=1 style='width=95%;border-collapse: collapse;border-color:#cccccc;' >";
        echo "<tr><td>&nbsp;</td><td><b>" . join( "</b></td><td>&nbsp;<b>", $this->columns ) . "</b></td></tr>\n";
        $i = 1;
        foreach( $this->records as $r ){
            echo "<tr><td align=right>{$i}&nbsp;</td><td>" . join( "</td><td>&nbsp;", $r ) . "</td></tr>\n";
            $i++;
        };
        echo "</table>\n";
    }
    
    function line2display( $line ){
        $line = str_replace( array('"' . chr(0x09) . '"', '""'),  array(chr(0x09),'"'),  $line );
        $line = substr( $line, 1, -1 ); // chop first " and last "
        return $line;
    }
    
}
# end of class



# ------------------------------------------------------
class phpfmgImage
{
    var $im = null;
    var $width = 73 ;
    var $height = 33 ;
    var $text = '' ; 
    var $line_distance = 8;
    var $text_len = 4 ;

    function __construct( $text = '', $len = 4 ){
        $this->phpfmgImage( $text, $len );
    }

    function phpfmgImage( $text = '', $len = 4 ){
        $this->text_len = $len ;
        $this->text = '' == $text ? $this->uniqid( $this->text_len ) : $text ;
        $this->text = strtoupper( substr( $this->text, 0, $this->text_len ) );
    }
    
    function create(){
        $this->im = imagecreate( $this->width, $this->height );
        $bgcolor   = imagecolorallocate($this->im, 255, 255, 255);
        $textcolor = imagecolorallocate($this->im, 0, 0, 0);
        $this->drawLines();
        imagestring($this->im, 5, 20, 9, $this->text, $textcolor);
    }
    
    function drawLines(){
        $linecolor = imagecolorallocate($this->im, 210, 210, 210);
    
        //vertical lines
        for($x = 0; $x < $this->width; $x += $this->line_distance) {
          imageline($this->im, $x, 0, $x, $this->height, $linecolor);
        };
    
        //horizontal lines
        for($y = 0; $y < $this->height; $y += $this->line_distance) {
          imageline($this->im, 0, $y, $this->width, $y, $linecolor);
        };
    }
    
    function out( $filename = '' ){
        if( function_exists('imageline') ){
            $this->create();
            if( '' == $filename ) header("Content-type: image/png");
            ( '' == $filename ) ? imagepng( $this->im ) : imagepng( $this->im, $filename );
            imagedestroy( $this->im ); 
        }else{
            $this->out_predefined_image(); 
        };
    }

    function uniqid( $len = 0 ){
        $md5 = md5( uniqid(rand()) );
        return $len > 0 ? substr($md5,0,$len) : $md5 ;
    }
    
    function out_predefined_image(){
        header("Content-type: image/png");
        $data = $this->getImage(); 
        echo base64_decode($data);
    }
    
    // Use predefined captcha random images if web server doens't have GD graphics library installed  
    function getImage(){
        $images = array(
			'8CBC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7WAMYQ1lDGaYGIImJTGFtdG10CBBBEgtoFWlwbQh0YEFRJ9LA2ujogOy+pVHTVi0NXZmF7D40dXDzWIHmoYth2oHpFmxuHqjwoyLE4j4A8h/M0Ess0BYAAAAASUVORK5CYII=',
			'1147' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB0YAhgaHUNDkMRYHRgDGFodGkSQxEQdWAMYpqKKgfUGOjQEILlvZdaqqJWZWUAK4T6QOtZGh1Z0e1lDA6ZgusUhAIv7HJDFRENYQ9HFBir8qAixuA8AlM3HhlaR74AAAAAASUVORK5CYII=',
			'F993' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QkMZQxhCGUIdkMQCGlhbGR0dHQJQxEQaXUEkFrEAJPeFRi1dmpkZtTQLyX0BDYyBDiFwdVAxhkYHDPNYGh0xxLC5BdPNAxV+VIRY3AcA8KbObgYtfuYAAAAASUVORK5CYII=',
			'1C69' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7GB0YQxlCGaY6IImxOrA2Ojo6BAQgiYk6iDS4NjgCSWS9Ig2sYBLhvpVZ01YtnboqKgzJfWB1jg5TMfUGNKCLuTYEoNmBxS0hmG4eqPCjIsTiPgDajMmkbJ0BZwAAAABJRU5ErkJggg==',
			'586D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkMYQxhCGUMdkMQCGlhbGR0dHQJQxEQaXRscHUSQxAIDWFtZGxhhYmAnhU1bGbZ06sqsacjuawWqc0TVy9AKMi8QRSwAi5jIFEy3sAZgunmgwo+KEIv7AC80y0/qBGkpAAAAAElFTkSuQmCC',
			'715E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QkMZAlhDHUMDkEVbGQNYGxgdUFS2smKKTQHqnQoXg7gpalXU0szM0Cwk9wFVBDA0BKLoZW3AFBMBirGiiQHdFcDo6IgmxhrKEMqI6uYBCj8qQizuAwDtFsdIrsVn2AAAAABJRU5ErkJggg==',
			'1E17' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7GB1EQxmmMIaGIImxOog0MIQwNIggiYkCxRjRxBhB6qYwNAQguW9l1tSwVdOAFJL7oOpaGTD1TsEiFoApxuiALCYaIhrKGOqIIjZQ4UdFiMV9AAcqx/NX73iSAAAAAElFTkSuQmCC',
			'2A14' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nM2QsRGAIAxFPwUb4D64QQrSMIJTQMEGOXaQKUWrcFLqaX737if3Lmi3SfhTXvGzBIIgkWJOTEBA1oyKLZ0WzVBc9gIh7VfrvtUWo/ajs2e83jV+4c44aJd03RtdJozZ5ZX9wL7634OZ+B1sTc2RMD0+VAAAAABJRU5ErkJggg==',
			'4A0A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpI37pjAEAHErilgIYwhDKMNUByQxxhDWVkZHh4AAJDHWKSKNrg2BDiJI7ps2bdrK1FWRWdOQ3BeAqg4MQ0NFQ4FioSEobhFpdHR0RFEHEnMIZcQUm4ImNlDhRz2IxX0Ag2bL4jrtRyIAAAAASUVORK5CYII=',
			'7ABB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QkMZAlhDGUMdkEVbGUNYGx0dAlDEWFtZGwIdRJDFpog0uiLUQdwUNW1laujK0Cwk9zE6oKgDQ9YG0VBXNPNEGoDq0MQCGjD1gsXQ3TxA4UdFiMV9AErWzK7BG7KAAAAAAElFTkSuQmCC',
			'047F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7GB0YWllDA0NDkMRYAximMjQEOiCrE5nCEIouFtDK6MrQ6AgTAzspaunSpauWrgzNQnJfQKtIK8MURjS9oqEOAYzodrQyOqCKAd3SytqAKgZ2M5rYQIUfFSEW9wEAPr7IuTV5sGoAAAAASUVORK5CYII=',
			'62DD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDGUMdkMREprC2sjY6OgQgiQW0iDS6NgQ6iCCLNTAgi4GdFBm1aunSVZFZ05DcFzKFYQorut5WhgBMMUYHdDGgWxrQ3cIaIBrqiubmgQo/KkIs7gMAW+bMTmitdAAAAAAASUVORK5CYII=',
			'337A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7RANYQ1hDA1qRxQKmiAD5AVMdkFW2MjQ6NAQEBCCLTQGJOjqIILlvZdSqsFVLV2ZNQ3YfSN0URpg6hHkBjKEhaGKODqjqQG5hbUAVA7sZTWygwo+KEIv7AIv8y0eDN4JQAAAAAElFTkSuQmCC',
			'CA16' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WEMYAhimMEx1QBITaWUMYQCKByCJBTSyAkUZHQSQxRpEGh2mMDoguy9q1bSVWdNWpmYhuQ+qDtW8BtFQkF4RFDsg5omguAUkhuoW1hCRRsdQBxQ3D1T4URFicR8ADAHMfEmG9tIAAAAASUVORK5CYII=',
			'A572' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nM2QwQ2AMAhF6YENcB/c4JuUi9PUQzdoR+jFKa0nafSoiZBAePmQH2i/RaI/5Sf+gk7GhqqOMaRXAI5JOdmi4hiyRNo0ifO3ttr21rvzh9xV5VReu2Z9BmUa722zUhkZZ06EkYXIKVj8wf9ezAd/B2ZyzS5cPo0eAAAAAElFTkSuQmCC',
			'9AEE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7WAMYAlhDHUMDkMREpjCGsDYwOiCrC2hlbcUUE2l0RYiBnTRt6rSVqaErQ7OQ3MfqiqIOAltFQ9HFBFox1YlMwRRjDQCKobl5oMKPihCL+wDxM8nLPVZ9uAAAAABJRU5ErkJggg==',
			'4E58' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpI37poiGsoY6THVAFgsRaWBtYAgIQBJjBIsxOoggibFOAYpNhasDO2natKlhSzOzpmYhuS9gCkhXAIp5oaEgsUAU8xhA5mERY3R0QNELcjNDKAOqmwcq/KgHsbgPAEDRy4xb4GP/AAAAAElFTkSuQmCC',
			'E8C1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpIn7QkMYQxhCHVqRxQIaWFsZHQKmooqJNLo2CISiq2NtYIDpBTspNGpl2NJVq5Yiuw9NHZJ52MQEsLkFRQzq5tCAQRB+VIRY3AcAdPPNRhs4rVgAAAAASUVORK5CYII=',
			'349C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7RAMYWhlCGaYGIIkFTGGYyujoECCCrBKoirUh0IEFWWwKoytIDNl9K6OWLl2ZGZmF4r4pIq0MIXB1UPNEQx0a0MUYWhnR7AC6pRXdLdjcPFDhR0WIxX0A4fDKY4e17usAAAAASUVORK5CYII=',
			'CE47' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WENEQxkaHUNDkMREWkUaGFodGkSQxAIagbypaGIgXqADkEa4L2rV1LCVmVkrs5DcB1LH2ujQyoCmlzU0YAoDuh2NDgEM6G5pdHTA4mYUsYEKPypCLO4DADrRzMt9rY1PAAAAAElFTkSuQmCC',
			'50D4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkMYAlhDGRoCkMQCGhhDWBsdGlHFWFtZGwJakcUCA0QaXRsCpgQguS9s2rSVqauioqKQ3dcKUhfogKwXKhYagmxHK9gOFLeITAG7BUWMNQDTzQMVflSEWNwHAF6mzqT8lrfmAAAAAElFTkSuQmCC',
			'B140' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QgMYAhgaHVqRxQKmMAYwtDpMdUAWa2UNYJjqEBCAog6oN9DRQQTJfaFRq6JWZmZmTUNyH0gdayNcHdQ8oFhoIIYY0C2YdjSiuiUUqBPdzQMVflSEWNwHAEJBzET1uSmSAAAAAElFTkSuQmCC',
			'ED6D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVklEQVR4nGNYhQEaGAYTpIn7QkNEQxhCGUMdkMQCGkRaGR0dHQJQxRpdGxwdRDDEGGFiYCeFRk1bmTp1ZdY0JPeB1Tli0xtIjBiGW7C5eaDCj4oQi/sAW+zNKm4Cgb8AAAAASUVORK5CYII=',
			'FD32' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkNFQxhDGaY6IIkFNIi0sjY6BASgijU6NAQ6iKCLAUVFkNwXGjVtZdbUVauikNwHVdfogGFeQCsDptgUBixuQRUDuZkxNGQQhB8VIRb3AQB0S8+ZPCsifgAAAABJRU5ErkJggg==',
			'21CF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WAMYAhhCHUNDkMREpjAGMDoEOiCrC2hlDWBtEEQRY2hlAIoxwsQgbpq2KmrpqpWhWcjuC0BRB4ZAHoYYawMDhh0iQDF0t4SGsoYC3YzqlgEKPypCLO4DAEruxohUib08AAAAAElFTkSuQmCC',
			'C8FA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WEMYQ1hDA1qRxURaWVtZGximOiCJBTSKNLo2MAQEIIs1gNQxOogguS9q1cqwpaErs6YhuQ9NHVQMZB5jaAiGHajqIG5BFQO7GU1soMKPihCL+wBeKstXXKrMCAAAAABJRU5ErkJggg==',
			'691C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WAMYQximMEwNQBITmcLayhDCECCCJBbQItLoGMLowIIs1iDS6DCF0QHZfZFRS5dmTVuZhey+kCmMgUjqIHpbGRoxxVjAYsh2gN0yBdUtIDczhjqguHmgwo+KEIv7APkGy0sLSVsmAAAAAElFTkSuQmCC',
			'9092' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGaY6IImJTGEMYXR0CAhAEgtoZW1lbQh0EEERE2l0bQhoEEFy37Sp01ZmZkatikJyH6urSKNDSEAjsh0MQL0OQBOQ3SIAtIOxIWAKAxa3YLqZMTRkEIQfFSEW9wEAmg3LmYznzr0AAAAASUVORK5CYII=',
			'C56A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7WENEQxlCGVqRxURaRRoYHR2mOiCJBTSKNLA2OAQEIIs1iISwNjA6iCC5L2rV1KVLp67MmobkPqCeRldHR5g6hFhDYGgIqh0gMRR1Iq2srYxoellDGEMYQhlRxAYq/KgIsbgPAB1EzAe++bb2AAAAAElFTkSuQmCC',
			'2DE3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7WANEQ1hDHUIdkMREpoi0sjYwOgQgiQW0ijS6guSQdUPFApDdN23aytTQVUuzkN0XgKIODBkdMM1jbcAUE2nAdEtoKKabByr8qAixuA8ArEjMlL6jpjUAAAAASUVORK5CYII=',
			'BAEF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7QgMYAlhDHUNDkMQCpjCGsDYwOiCrC2hlbcUQmyLS6IoQAzspNGraytTQlaFZSO5DUwc1TzQUUwyLOix6QwOAYqGOKGIDFX5UhFjcBwDQ4cs/H+RCUAAAAABJRU5ErkJggg==',
			'0F53' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7GB1EQ11DHUIdkMRYA0QaWIEyAUhiIlNAYkAaSSygFSg2FUgjuS9q6dSwpZlZS7OQ3AdSB1IVgKYXJCaCYQeqGMgtjI6OKG5hdACqCGVAcfNAhR8VIRb3AQAyvMwzutipFQAAAABJRU5ErkJggg==',
			'F25D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDHUMdkMQCGlhbWRsYHQJQxEQaXYFiIihiDI2uU+FiYCeFRq1aujQzM2sakvuA6qYwNASi6w3AFGN0YMUQA7rE0RHNLaKhDqGMKG4eqPCjIsTiPgA2fMw0AKiF1QAAAABJRU5ErkJggg==',
			'84B1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WAMYWllDGVqRxUSmMExlbXSYiiwW0MoQytoQEIqqjtEVqA6mF+ykpVFLly4NXbUU2X0iU0RakdRBzRMNdQWZimpHKyuaGNAtGHqhbg4NGAThR0WIxX0AzKXMz/OV93gAAAAASUVORK5CYII=',
			'E205' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QkMYQximMIYGIIkFNLC2MoQyOjCgiIk0Ojo6ookxNLo2BLo6ILkvNGrV0qWrIqOikNwHVDeFFWQCqt4ATDFGB0agHahirA0MoQwByO4LDRENdZjCMNVhEIQfFSEW9wEASRfMZeV8CKgAAAAASUVORK5CYII=',
			'AC3E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7GB0YQ0EwAEmMNYC10bXR0QFZncgUkQaHhkAUsYBWkQYGhDqwk6KWTlu1aurK0Cwk96GpA8PQUKAYFvMw7cB0S0ArppsHKvyoCLG4DwC898wbx5B2oAAAAABJRU5ErkJggg==',
			'C97D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WEMYQ1hDA0MdkMREWllbGRoCHQKQxAIaRRodgGIiyGINQLFGR5gY2ElRq5YuzVq6MmsakvsCGhgDHaYwoullaHQIQBNrZAGahioGcgtrAyOKW8BubmBEcfNAhR8VIRb3AQA8pMv4+yUmeQAAAABJRU5ErkJggg==',
			'BC24' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QgMYQxlCGRoCkMQCprA2Ojo6NKKItYo0uAJJVHUiYDIAyX2hUdNWrVqZFRWF5D6wulZGB3TzGKYwhoagiTkEYHGLA6oYyM2soQEoYgMVflSEWNwHAFrIz3WHRINyAAAAAElFTkSuQmCC',
			'710F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QkMZAhimMIaGIIu2MgYwhDI6oKhsZQ1gdHREFZvCEMDaEAgTg7gpalXU0lWRoVlI7gOahKwODFkbMMVEgGLodgQAxdDdEtDAGgp0M6pbBij8qAixuA8Ak9LG45PXANcAAAAASUVORK5CYII=',
			'049A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpIn7GB0YWhlCgRhJjDWAYSqjo8NUByQxkSkMoawNAQEBSGIBrYyurA2BDiJI7otaunTpyszIrGlI7gtoFWllCIGrg4qJhjo0BIaGoNrRytiAqg7ollZGR0cUMYibGVHEBir8qAixuA8AHC/Ka9eRjNgAAAAASUVORK5CYII=',
			'5127' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7QkMYAhhCGUNDkMQCGhgDGB0dGkRQxFgDWIEkslhgAFAvUCwAyX1h01ZFrVqZtTIL2X2tQHWtQIhsM0hsChAi2wESA5uJgCJTGAIYHRgdkMWALgllDQ1EERuo8KMixOI+ANOVyQ6RWuekAAAAAElFTkSuQmCC',
			'08CC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7GB0YQxhCHaYGIImxBrC2MjoEBIggiYlMEWl0bRB0YEESC2hlbWUFmoDsvqilK8OWrlqZhew+NHVQMZB5qGLY7MDmFmxuHqjwoyLE4j4AEtDKhabY7CoAAAAASUVORK5CYII=',
			'6243' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeElEQVR4nGNYhQEaGAYTpIn7WAMYQxgaHUIdkMREprC2MrQ6OgQgiQW0iDQ6THVoEEEWawDqDHRoCEByX2TUqqUrM7OWZiG5L2QKwxTWRrg6iN5WhgDW0ABU81oZHYAmoogB3QK0BdUtrAGioQ5obh6o8KMixOI+AJY3zhiE2aFuAAAAAElFTkSuQmCC',
			'192A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGVqRxVgdWFsZHR2mOiCJiTqINLo2BAQEoOgVaXRoCHQQQXLfyqylS7NWZmZNQ3If0I5Ah1ZGmDqoGEOjwxTG0BAUMZZGhwB0dUC3OKCKiYYwhrCGBqKIDVT4URFicR8AlbrISlzFNbIAAAAASUVORK5CYII=',
			'D19D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QgMYAhhCGUMdkMQCpjAGMDo6OgQgi7WyBrA2BDqIoIgxIIuBnRS1dFXUyszIrGlI7gOpYwjB1MuAxTxGdLEpDBhuCQ1gDUV380CFHxUhFvcBAPXaymjeHyLdAAAAAElFTkSuQmCC',
			'4B7D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpI37poiGsIYGhjogi4WItDI0BDoEIIkxhog0OgDFRJDEWKcA1TU6wsTATpo2bWrYqqUrs6YhuS8ApG4KI4re0FCgeQGoYgxTRICmYYi1sjYworgF7OYGRlQ3D1T4UQ9icR8AT3PLYDcfY4wAAAAASUVORK5CYII=',
			'066C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGaYGIImxBrC2Mjo6BIggiYlMEWlkbXB0YEESC2gVaWAFmoDsvqil08KWTl2Zhey+gFbRVlZHRwcGVL2Nrg2BKGIgO0BiyHZgcws2Nw9U+FERYnEfAMKYylphxyXGAAAAAElFTkSuQmCC',
			'580E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QkMYQximMIYGIIkFNLC2MoQyOjCgiIk0Ojo6oogFBrC2sjYEwsTATgqbtjJs6arI0Cxk97WiqIOKiTS6ookFtGLaITIF0y2sAZhuHqjwoyLE4j4AubPKHE6NHfoAAAAASUVORK5CYII=',
			'53DF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkNYQ1hDGUNDkMQCGkRaWRsdHRhQxBgaXRsCUcQCAxhaWRFiYCeFTVsVtnRVZGgWsvtaUdTBxDDMC8AiJjIF0y2sAWA3o5o3QOFHRYjFfQCZQ8qw1BfbggAAAABJRU5ErkJggg==',
			'C90A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WEMYQximMLQii4m0srYyhDJMdUASC2gUaXR0dAgIQBZrEGl0bQh0EEFyX9SqpUtTV0VmTUNyX0ADYyCSOqgYA0hvaAiKHSxAOxxR1EHcwogiBnEzqthAhR8VIRb3AQBNkswZQwewUQAAAABJRU5ErkJggg==',
			'A40A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7GB0YWhmmADGSGGsAw1SGUIapDkhiIlMYQhkdHQICkMQCWhldWRsCHUSQ3Be1FAhWRWZNQ3JfQKtIK5I6MAwNFQ11bQgMDUExj6GV0dERRR1IDGgzptgUVLGBCj8qQizuAwATGMt2oxKgtwAAAABJRU5ErkJggg==',
			'0F05' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7GB1EQx2mMIYGIImxBog0MIQyOiCrE5ki0sDo6IgiFtAq0sDaEOjqgOS+qKVTw5auioyKQnIfRF1AgwiGXlQxmB0iGG5hCEB2HyNIxRSGqQ6DIPyoCLG4DwA7y8rLlrrv6gAAAABJRU5ErkJggg==',
			'2A1D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WAMYAhimMIY6IImJTGEMYQhhdAhAEgtoZW1lBIqJIOtuFWl0mAIXg7hp2rSVWSCE7L4AFHVgyOggGoouxtqAqU4EKobsltBQkUbHUEcUNw9U+FERYnEfAP7uys6KW7wkAAAAAElFTkSuQmCC',
			'5CC3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QkMYQxlCHUIdkMQCGlgbHR0CHQJQxEQaXBsEgCRCLDBApIEVLIdwX9i0aauWrlq1NAvZfa0o6lDEkM0LaMW0Q2QKpltYAzDdPFDhR0WIxX0AFHfNjmN12KoAAAAASUVORK5CYII=',
			'4308' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpI37prCGMExhmOqALBYi0soQyhAQgCTGGMLQ6Ojo6CCCJMY6haGVtSEApg7spGnTVoUtXRU1NQvJfQGo6sAwNJSh0bUhEMU8oDsw7GCYgukWrG4eqPCjHsTiPgCzi8vupFKWNAAAAABJRU5ErkJggg==',
			'572F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkNEQx1CGUNDkMQCGhgaHR0dHRjQxFwbAlHEAgMYWhkQYmAnhU1bNW3VyszQLGT3tTIAVTKi6AXzp6CKBbSyNjAEoIqJTBFpYHRAFWMNEGlgDUV1y0CFHxUhFvcBAP7PySmTUBwWAAAAAElFTkSuQmCC',
			'F3FF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAUklEQVR4nGNYhQEaGAYTpIn7QkNZQ1hDA0NDkMQCGkRaWRsYHRhQxBgaXTHFkNWBnRQatSpsaejK0Cwk96Gpw2ceFjFsbgG6GU1soMKPihCL+wDhGMo7yjbQ7wAAAABJRU5ErkJggg==',
			'D9D0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDGVqRxQKmsLayNjpMdUAWaxVpdG0ICAjAEAt0EEFyX9TSpUtTV0VmTUNyX0ArYyCSOqgYQyOmGAumHVjcgs3NAxV+VIRY3AcAzoLPE6olLBgAAAAASUVORK5CYII=',
			'14D8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB0YWllDGaY6IImxOjBMZW10CAhAEhN1YAhlbQh0EEHRy+jK2hAAUwd20sqspUuXroqamoXkPkYHkVYkdVAx0VBXDPOAbsEmhu6WEEw3D1T4URFicR8AQwHJ8jG7pewAAAAASUVORK5CYII=',
			'3C10' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7RAMYQxmmMLQiiwVMYW10CGGY6oCsslWkwTGEISAAWWyKCBAzOogguW9l1LRVq6atzJqG7D5UdXDzsIk5TEG1A+yWKahuAbmZMdQBxc0DFX5UhFjcBwDhVswGwJiIkgAAAABJRU5ErkJggg==',
			'0420' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeklEQVR4nGNYhQEaGAYTpIn7GB0YWhlCgRhJjDWAYSqjo8NUByQxkSkMoawNAQEBSGIBrYyuDA2BDiJI7otaunTpqpWZWdOQ3BfQKtLK0MoIUwcVEw11mIIqBrSjlSGAAcUOoFuAOhlQ3AJyM2toAIqbByr8qAixuA8AL0rKlaL0UIQAAAAASUVORK5CYII=',
			'A5B8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB1EQ1lDGaY6IImxBog0sDY6BAQgiYlMAYo1BDqIIIkFtIqEIKkDOylq6dSlS0NXTc1Ccl9AK0OjK5p5oaFAMUzzsIixtqK7JaCVMQTdzQMVflSEWNwHANuxzdyTsFYlAAAAAElFTkSuQmCC',
			'A8F4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDAxoCkMRYA1hbWRsYGpHFRKaINLo2MLQiiwW0gtVNCUByX9TSlWFLQ1dFRSG5D6KO0QFZb2goyDzG0BAU88B2NGCxA00M6GY0sYEKPypCLO4DAOj3zdpG4jgQAAAAAElFTkSuQmCC',
			'1ED7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7GB1EQ1lDGUNDkMRYHUQaWBsdGkSQxERBYg0BKGKMULEAJPetzJoatnRV1MosJPdB1bUyYOqdgkUsAEOs0dEBWUw0BOxmFLGBCj8qQizuAwDrHMlksVoeTwAAAABJRU5ErkJggg==',
			'7BDF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QkNFQ1hDGUNDkEVbRVpZGx0dGFDFGl0bAlHFpgDVIcQgboqaGrZ0VWRoFpL7GB1Q1IEhawOmeSJYxAIaMN0S0AB2M6pbBij8qAixuA8AUH/Ks9mEmnQAAAAASUVORK5CYII=',
			'846B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WAMYWhlCGUMdkMREpjBMZXR0dAhAEgsAqmJtcHQQQVHH6MrawAhTB3bS0qilS5dOXRmaheQ+kSkirawY5omGujYEopgHtKOVFU0M6JZWdLdgc/NAhR8VIRb3AQC7mcsdxA0IkAAAAABJRU5ErkJggg==',
			'B43F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QgMYWhlDGUNDkMQCpjBMZW10dEBWF9DKEMrQEIgqNoXRlQGhDuyk0KilS1dNXRmaheS+gCkirQwY5omGOqCb18rQimkHQyu6W6BuRhEbqPCjIsTiPgCd2sunbCUA+QAAAABJRU5ErkJggg==',
			'B77C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QgNEQ11DA6YGIIkFTGFodGgICBBBFmsFiQU6sKCqA4o6OiC7LzRq1bRVS1dmIbsPqC6AYQqjAwOKeUB+ALoYawOjAyOaHSINrA0MKG4JDQCLobh5oMKPihCL+wBCdsynS2Zw+QAAAABJRU5ErkJggg==',
			'918E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGUMDkMREpjAGMDo6OiCrC2hlDWBtCEQTY0BWB3bStKmrolaFrgzNQnIfqysDhnkMQL3o5glgEROZgqkX6JJQdDcPVPhREWJxHwDUeccNozTsIwAAAABJRU5ErkJggg==',
			'6E9A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WANEQxlCGVqRxUSmiDQwOjpMdUASC2gRaWBtCAgIQBZrAIkFOogguS8yamrYyszIrGlI7gsBmscQAlcH0dsK4gWGhqCJMTagqoO4xRFFDOJmRhSxgQo/KkIs7gMARMbLTmnxZHwAAAAASUVORK5CYII=',
			'F2C1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QkMZQxhCHVqRxQIaWFsZHQKmooqJNLo2CISiijEAxRhgesFOCo1atXTpqlVLkd0HVDeFFaEOJhaAKcbowNoggO4WoGgAmphoqAMQBgyC8KMixOI+AGzezTa+DOMpAAAAAElFTkSuQmCC',
			'7391' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkNZQxhCGVpRRFtFWhkdHaaiijE0ujYEhKKITWFoZW0IgOmFuClqVdjKzKilyO5jdADqDglAsYO1gaHRoQFVTAQo5ogmFtAAdguaGNjNoQGDIPyoCLG4DwByDcvHEneHLwAAAABJRU5ErkJggg==',
			'CBD8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7WENEQ1hDGaY6IImJtIq0sjY6BAQgiQU0ijS6NgQ6iCCLAVWyNgTA1IGdFLVqatjSVVFTs5Dch6YOJoZpHhY7sLkFm5sHKvyoCLG4DwCQjc4fpNMg8QAAAABJRU5ErkJggg==',
			'6D81' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7WANEQxhCGVqRxUSmiLQyOjpMRRYLaBFpdG0ICEURaxBpdHR0gOkFOykyatrKrNBVS5HdFzIFRR1EbyvYPIJiULegiEHdHBowCMKPihCL+wBN580wtYpOzwAAAABJRU5ErkJggg==',
			'DA18' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QgMYAhimMEx1QBILmMIYwhDCEBCALNbK2soYwugggiIm0ugwBa4O7KSopdNWZk1bNTULyX1o6qBioqEOU7CZhyY2BVNvaIBIo2OoA4qbByr8qAixuA8AZCPOT14oxe8AAAAASUVORK5CYII=',
			'9EFF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAU0lEQVR4nGNYhQEaGAYTpIn7WANEQ1lDA0NDkMREpog0sDYwOiCrC2glKAZ20rSpU8OWhq4MzUJyH6srpl4GLOYJYBHD5hawm9HNG6DwoyLE4j4AfxTIL1eZwu4AAAAASUVORK5CYII=',
			'CA48' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WEMYAhgaHaY6IImJtDKGMLQ6BAQgiQU0srYyTHV0EEEWaxBpdAiEqwM7KWrVtJWZmVlTs5DcB1Ln2ohmXoNoqGtoIKp5jUDzGlHtEGkFiaHqZQ0Bi6G4eaDCj4oQi/sAv0DOiQlC5eQAAAAASUVORK5CYII=',
			'CAFA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WEMYAlhDA1qRxURaGUNYGximOiCJBTSytgLFAgKQxRpEGl0bGB1EkNwXtWraytTQlVnTkNyHpg4qJhoKFAsNQbEDU51IK6YYawim2ECFHxUhFvcBAOYzzAhWY5TMAAAAAElFTkSuQmCC',
			'C41E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WEMYWhmmMIYGIImJtDJMZQhhdEBWF9DIEMqILtbA6ArUCxMDOylq1dKlq6atDM1Ccl8AyMQp6HpFQx3QxRoZMNQBdWKIgdzMGOqI4uaBCj8qQizuAwC208mIcP/AAwAAAABJRU5ErkJggg==',
			'3AD7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7RAMYAlhDGUNDkMQCpjCGsDY6NIggq2xlbWVtCEAVmyLS6AoUC0By38qoaStTV0WtzEJ2H0RdK4rNraKhQLEpqGJgdQEMKG4BijU6OqC6GSgWyogiNlDhR0WIxX0AfSDNSpEdBMkAAAAASUVORK5CYII=',
			'3705' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7RANEQx2mMIYGIIkFTGFodAhldEBR2crQ6OjoiCo2haGVtSHQ1QHJfSujVk1buioyKgrZfVMYAlgbAhpEUMxjdMAUY21gBNohguIWIC+UIQDZfaIBIiAzpzoMgvCjIsTiPgDFP8sXTKA8HAAAAABJRU5ErkJggg==',
			'15F3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7GB1EQ1lDA0IdkMRYHUQaWIEyAUhiomAxhgYRFL0iISCxACT3rcyaunRp6KqlWUjuY3RgaHRFqEMRQzMPixhrK4ZbQhhB9qK4eaDCj4oQi/sAwg7Jfhmsju8AAAAASUVORK5CYII=',
			'FE8A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QkNFQxlCGVqRxQIaRBoYHR2mOqCJsTYEBARgqHN0EEFyX2jU1LBVoSuzpiG5D00dknmBoSGYYhjqMPWC3MyIIjZQ4UdFiMV9AC5azBVpLohpAAAAAElFTkSuQmCC',
			'F561' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QkNFQxlCGVqRxQIaRBoYHR2moouxNjiEoomFsDbA9YKdFBo1denSqauWIrsvoIGh0dXRAc0OoFhDALq9WMRYWxkx9DKGAN0cGjAIwo+KEIv7ALpPzZYHinIaAAAAAElFTkSuQmCC',
			'E1AA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkMYAhimMLQiiwU0MAYwhDJMdUARYw1gdHQICEARYwhgbQh0EEFyX2jUqqilqyKzpiG5D00dQiw0MDQEt3k4xUJDWEPRxQYq/KgIsbgPALgWyxP0A4O1AAAAAElFTkSuQmCC',
			'44E3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpI37pjC0soY6hDogi4UwTGVtYHQIQBJjDGEIZQXSIkhirFMYXUFiAUjumzZt6dKloauWZiG5L2CKSCuSOjAMDRUNdUUzD+wWrGKobsHq5oEKP+pBLO4DAEKHy5rLBXwmAAAAAElFTkSuQmCC',
			'61F6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WAMYAlhDA6Y6IImJTGEMYG1gCAhAEgtoYQWKMToIIIsB1YDEkN0XGbUqamnoytQsJPeFTAGrQzWvFaJXhICYCFgvqluALgkFiqG4eaDCj4oQi/sAVcHJHfIQFhgAAAAASUVORK5CYII=',
			'BA42' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7QgMYAhgaHaY6IIkFTGEMYWh1CAhAFmtlbWWY6ugggqJOpNEh0KFBBMl9oVHTVmZmZq2KQnIfSJ1ro0Mjih2toqGuoQGtDChiIiBVUxjQ7Wh0CEB1M0jMMTRkEIQfFSEW9wEANWjPpf97LaYAAAAASUVORK5CYII=',
			'22DB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDGUMdkMREprC2sjY6OgQgiQW0ijS6NgQ6iCDrbmUAiwUgu2/aqqVLV0WGZiG7L4BhCitCHRgyOjAEsKKZxwoURRcTAYqiuyU0VDTUFc3NAxV+VIRY3AcAc6/Ljakhh/0AAAAASUVORK5CYII=',
			'43E4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpI37prCGsIY6NAQgi4WItLI2MDQiizGGMDS6NjC0IouxTmEAqZsSgOS+adNWhS0NXRUVheS+ALA6RgdkvaGhIPMYQ0NQ3AK2A9UtU8BuQRPD4uaBCj/qQSzuAwAAxMz8LYrEjwAAAABJRU5ErkJggg==',
			'6D09' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WANEQximMEx1QBITmSLSyhDKEBCAJBbQItLo6OjoIIIs1iDS6NoQCBMDOykyatrK1FVRUWFI7guZAlIXMBVFbytYrAFdDGgFih3Y3ILNzQMVflSEWNwHAFJRzS24WEURAAAAAElFTkSuQmCC',
			'964D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WAMYQxgaHUMdkMREprC2MrQ6OgQgiQW0ijQyTHV0EEEVa2AIhIuBnTRt6rSwlZmZWdOQ3MfqKtrK2oiqlwFonmtoIIqYAFDMAU0d2C2NqG7B5uaBCj8qQizuAwCDpMumHBZkZwAAAABJRU5ErkJggg==',
			'2BA5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpIn7WANEQximMIYGIImJTBFpZQhldEBWF9Aq0ujo6IgixtAq0sraEOjqgOy+aVPDlq6KjIpCdl8ASF1AgwiSXkYHkUbXUFQx1gagWEOgA7KYSANYbwCy+0JDRUOAYlMdBkH4URFicR8Ag0DMEAjHng8AAAAASUVORK5CYII=',
			'89C9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WAMYQxhCHaY6IImJTGFtZXQICAhAEgtoFWl0bRB0EEFRBxJjhImBnbQ0aunS1FWrosKQ3CcyhTHQtYFhqgiKeQxAvUA5FDEWoJgAmh2YbsHm5oEKPypCLO4DAHQZzHB4R7Y+AAAAAElFTkSuQmCC',
			'9164' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGRoCkMREpjAGMDo6NCKLBbSyBrA2OLSiijEAxRimBCC5b9rUVVFLgTgKyX2srkB1jo4OyHoZwHoDQ0OQxATAYgFobmEAuQVFDOiSUHQ3D1T4URFicR8A62vLJxV/fGUAAAAASUVORK5CYII=',
			'D3CC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QgNYQxhCHaYGIIkFTBFpZXQICBBBFmtlaHRtEHRgQRVrZW1gdEB2X9TSVWFLV63MQnYfmjok87CJodmBxS3Y3DxQ4UdFiMV9AMZkzKaEikpDAAAAAElFTkSuQmCC',
			'B6C4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QgMYQxhCHRoCkMQCprC2MjoENKKItYo0sjYItKKqE2lgbWCYEoDkvtCoaWFLV62KikJyX8AU0VbWBqCJaOa5NjCGhmCICWBzC4oYNjcPVPhREWJxHwDGjs8hIFl2sgAAAABJRU5ErkJggg==',
			'C42D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WEMYWhlCGUMdkMREWhmmMjo6OgQgiQU0MoSyNgQ6iCCLNTC6MiDEwE6KWrV06aqVmVnTkNwXADKxlRFNr2iowxQ0sUagWwJQxSA6GVHcAnIza2ggipsHKvyoCLG4DwA7HsqZD96M1AAAAABJRU5ErkJggg==',
			'3752' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpIn7RANEQ11DHaY6IIkFTGFodG1gCAhAVtkKEmN0EEEWm8LQyjqVoUEEyX0ro1ZNW5qZtSoK2X1TGAKApjY6oJjH6AAUa0VxTStrAyvIdhS3iDQwOjoEoLoZaGMoY2jIIAg/KkIs7gMAMMXL/qPyPBYAAAAASUVORK5CYII=',
			'AAB3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7GB0YAlhDGUIdkMRYAxhDWBsdHQKQxESmsLayNgQ0iCCJBbSKNLo2OjQEILkvaum0lamhq5ZmIbkPTR0YhoaKhrpiMw+rHahuAYuhuXmgwo+KEIv7APVKzwKrVlKtAAAAAElFTkSuQmCC'        
        );
        $this->text = array_rand( $images );
        return $images[ $this->text ] ;    
    }
    
    function out_processing_gif(){
        $image = dirname(__FILE__) . '/processing.gif';
        $base64_image = "R0lGODlhFAAUALMIAPh2AP+TMsZiALlcAKNOAOp4ANVqAP+PFv///wAAAAAAAAAAAAAAAAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh+QQFCgAIACwAAAAAFAAUAAAEUxDJSau9iBDMtebTMEjehgTBJYqkiaLWOlZvGs8WDO6UIPCHw8TnAwWDEuKPcxQml0Ynj2cwYACAS7VqwWItWyuiUJB4s2AxmWxGg9bl6YQtl0cAACH5BAUKAAgALAEAAQASABIAAAROEMkpx6A4W5upENUmEQT2feFIltMJYivbvhnZ3Z1h4FMQIDodz+cL7nDEn5CH8DGZhcLtcMBEoxkqlXKVIgAAibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkphaA4W5upMdUmDQP2feFIltMJYivbvhnZ3V1R4BNBIDodz+cL7nDEn5CH8DGZAMAtEMBEoxkqlXKVIg4HibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpjaE4W5tpKdUmCQL2feFIltMJYivbvhnZ3R0A4NMwIDodz+cL7nDEn5CH8DGZh8ONQMBEoxkqlXKVIgIBibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpS6E4W5spANUmGQb2feFIltMJYivbvhnZ3d1x4JMgIDodz+cL7nDEn5CH8DGZgcBtMMBEoxkqlXKVIggEibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpAaA4W5vpOdUmFQX2feFIltMJYivbvhnZ3V0Q4JNhIDodz+cL7nDEn5CH8DGZBMJNIMBEoxkqlXKVIgYDibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpz6E4W5tpCNUmAQD2feFIltMJYivbvhnZ3R1B4FNRIDodz+cL7nDEn5CH8DGZg8HNYMBEoxkqlXKVIgQCibbK9YLBYvLtHH5K0J0IACH5BAkKAAgALAEAAQASABIAAAROEMkpQ6A4W5spIdUmHQf2feFIltMJYivbvhnZ3d0w4BMAIDodz+cL7nDEn5CH8DGZAsGtUMBEoxkqlXKVIgwGibbK9YLBYvLtHH5K0J0IADs=";
        $binary = is_file($image) ? join("",file($image)) : base64_decode($base64_image); 
        header("Cache-Control: post-check=0, pre-check=0, max-age=0, no-store, no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: image/gif");
        echo $binary;
    }

}
# end of class phpfmgImage
# ------------------------------------------------------
# end of module : captcha


# module user
# ------------------------------------------------------
function phpfmg_user_isLogin(){
    return ( isset($_SESSION['authenticated']) && true === $_SESSION['authenticated'] );
}


function phpfmg_user_logout(){
    session_destroy();
    header("Location: admin.php");
}

function phpfmg_user_login()
{
    if( phpfmg_user_isLogin() ){
        return true ;
    };
    
    $sErr = "" ;
    if( 'Y' == $_POST['formmail_submit'] ){
        if(
            defined( 'PHPFMG_USER' ) && strtolower(PHPFMG_USER) == strtolower($_POST['Username']) &&
            defined( 'PHPFMG_PW' )   && strtolower(PHPFMG_PW) == strtolower($_POST['Password']) 
        ){
             $_SESSION['authenticated'] = true ;
             return true ;
             
        }else{
            $sErr = 'Login failed. Please try again.';
        }
    };
    
    // show login form 
    phpfmg_admin_header();
?>
<form name="frmFormMail" action="" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:380px;height:260px;">
<fieldset style="padding:18px;" >
<table cellspacing='3' cellpadding='3' border='0' >
	<tr>
		<td class="form_field" valign='top' align='right'>Email :</td>
		<td class="form_text">
            <input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" class='text_box' >
		</td>
	</tr>

	<tr>
		<td class="form_field" valign='top' align='right'>Password :</td>
		<td class="form_text">
            <input type="password" name="Password"  value="" class='text_box'>
		</td>
	</tr>

	<tr><td colspan=3 align='center'>
        <input type='submit' value='Login'><br><br>
        <?php if( $sErr ) echo "<span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
        <a href="admin.php?mod=mail&func=request_password">I forgot my password</a>   
    </td></tr>
</table>
</fieldset>
</div>
<script type="text/javascript">
    document.frmFormMail.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();
}


function phpfmg_mail_request_password(){
    $sErr = '';
    if( $_POST['formmail_submit'] == 'Y' ){
        if( strtoupper(trim($_POST['Username'])) == strtoupper(trim(PHPFMG_USER)) ){
            phpfmg_mail_password();
            exit;
        }else{
            $sErr = "Failed to verify your email.";
        };
    };
    
    $n1 = strpos(PHPFMG_USER,'@');
    $n2 = strrpos(PHPFMG_USER,'.');
    $email = substr(PHPFMG_USER,0,1) . str_repeat('*',$n1-1) . 
            '@' . substr(PHPFMG_USER,$n1+1,1) . str_repeat('*',$n2-$n1-2) . 
            '.' . substr(PHPFMG_USER,$n2+1,1) . str_repeat('*',strlen(PHPFMG_USER)-$n2-2) ;


    phpfmg_admin_header("Request Password of Email Form Admin Panel");
?>
<form name="frmRequestPassword" action="admin.php?mod=mail&func=request_password" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:580px;height:260px;text-align:left;">
<fieldset style="padding:18px;" >
<legend>Request Password</legend>
Enter Email Address <b><?php echo strtoupper($email) ;?></b>:<br />
<input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" style="width:380px;">
<input type='submit' value='Verify'><br>
The password will be sent to this email address. 
<?php if( $sErr ) echo "<br /><br /><span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
</fieldset>
</div>
<script type="text/javascript">
    document.frmRequestPassword.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();    
}


function phpfmg_mail_password(){
    phpfmg_admin_header();
    if( defined( 'PHPFMG_USER' ) && defined( 'PHPFMG_PW' ) ){
        $body = "Here is the password for your form admin panel:\n\nUsername: " . PHPFMG_USER . "\nPassword: " . PHPFMG_PW . "\n\n" ;
        if( 'html' == PHPFMG_MAIL_TYPE )
            $body = nl2br($body);
        mailAttachments( PHPFMG_USER, "Password for Your Form Admin Panel", $body, PHPFMG_USER, 'You', "You <" . PHPFMG_USER . ">" );
        echo "<center>Your password has been sent.<br><br><a href='admin.php'>Click here to login again</a></center>";
    };   
    phpfmg_admin_footer();
}


function phpfmg_writable_check(){
 
    if( is_writable( dirname(PHPFMG_SAVE_FILE) ) && is_writable( dirname(PHPFMG_EMAILS_LOGFILE) )  ){
        return ;
    };
?>
<style type="text/css">
    .fmg_warning{
        background-color: #F4F6E5;
        border: 1px dashed #ff0000;
        padding: 16px;
        color : black;
        margin: 10px;
        line-height: 180%;
        width:80%;
    }
    
    .fmg_warning_title{
        font-weight: bold;
    }

</style>
<br><br>
<div class="fmg_warning">
    <div class="fmg_warning_title">Your form data or email traffic log is NOT saving.</div>
    The form data (<?php echo PHPFMG_SAVE_FILE ?>) and email traffic log (<?php echo PHPFMG_EMAILS_LOGFILE?>) will be created automatically when the form is submitted. 
    However, the script doesn't have writable permission to create those files. In order to save your valuable information, please set the directory to writable.
     If you don't know how to do it, please ask for help from your web Administrator or Technical Support of your hosting company.   
</div>
<br><br>
<?php
}


function phpfmg_log_view(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    
    phpfmg_admin_header();
   
    $file = $files[$n];
    if( is_file($file) ){
        if( 1== $n ){
            echo "<pre>\n";
            echo join("",file($file) );
            echo "</pre>\n";
        }else{
            $man = new phpfmgDataManager();
            $man->displayRecords();
        };
     

    }else{
        echo "<b>No form data found.</b>";
    };
    phpfmg_admin_footer();
}


function phpfmg_log_download(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );

    $file = $files[$n];
    if( is_file($file) ){
        phpfmg_util_download( $file, PHPFMG_SAVE_FILE == $file ? 'form-data.csv' : 'email-traffics.txt', true, 1 ); // skip the first line
    }else{
        phpfmg_admin_header();
        echo "<b>No email traffic log found.</b>";
        phpfmg_admin_footer();
    };

}


function phpfmg_log_delete(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    phpfmg_admin_header();

    $file = $files[$n];
    if( is_file($file) ){
        echo unlink($file) ? "It has been deleted!" : "Failed to delete!" ;
    };
    phpfmg_admin_footer();
}


function phpfmg_util_download($file, $filename='', $toCSV = false, $skipN = 0 ){
    if (!is_file($file)) return false ;

    set_time_limit(0);


    $buffer = "";
    $i = 0 ;
    $fp = @fopen($file, 'rb');
    while( !feof($fp)) { 
        $i ++ ;
        $line = fgets($fp);
        if($i > $skipN){ // skip lines
            if( $toCSV ){ 
              $line = str_replace( chr(0x09), ',', $line );
              $buffer .= phpfmg_data2record( $line, false );
            }else{
                $buffer .= $line;
            };
        }; 
    }; 
    fclose ($fp);
  

    
    /*
        If the Content-Length is NOT THE SAME SIZE as the real conent output, Windows+IIS might be hung!!
    */
    $len = strlen($buffer);
    $filename = basename( '' == $filename ? $file : $filename );
    $file_extension = strtolower(substr(strrchr($filename,"."),1));

    switch( $file_extension ) {
        case "pdf": $ctype="application/pdf"; break;
        case "exe": $ctype="application/octet-stream"; break;
        case "zip": $ctype="application/zip"; break;
        case "doc": $ctype="application/msword"; break;
        case "xls": $ctype="application/vnd.ms-excel"; break;
        case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
        case "gif": $ctype="image/gif"; break;
        case "png": $ctype="image/png"; break;
        case "jpeg":
        case "jpg": $ctype="image/jpg"; break;
        case "mp3": $ctype="audio/mpeg"; break;
        case "wav": $ctype="audio/x-wav"; break;
        case "mpeg":
        case "mpg":
        case "mpe": $ctype="video/mpeg"; break;
        case "mov": $ctype="video/quicktime"; break;
        case "avi": $ctype="video/x-msvideo"; break;
        //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
        case "php":
        case "htm":
        case "html": 
                $ctype="text/plain"; break;
        default: 
            $ctype="application/x-download";
    }
                                            

    //Begin writing headers
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public"); 
    header("Content-Description: File Transfer");
    //Use the switch-generated Content-Type
    header("Content-Type: $ctype");
    //Force the download
    header("Content-Disposition: attachment; filename=".$filename.";" );
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$len);
    
    while (@ob_end_clean()); // no output buffering !
    flush();
    echo $buffer ;
    
    return true;
 
    
}
?>