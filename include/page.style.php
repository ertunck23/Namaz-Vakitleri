<?php if( !defined('NV_NAME') ) die('You can not access this file directly!');

if (isset($_POST['submit']))
{
	if(isset($_POST['renk_secenegi']))
	{
		update_option( NV_DB_WIDGET_COLORSET, $_POST['renk_secenegi'] );
	}
}

$renkler = array(
	'#C4364C|#F04862|#F04862|#364363|#50587C'	=> __('Pembe Rüya', 'namazvakti'),
	'#1C4E93|#3668AF|#ffffff|#364363|#50587C'	=> __('Uzay', 'namazvakti'),
	'#607848|#789048|#789048|#604848|#655643'	=> __('Orman', 'namazvakti'),
	'#e14d43|#69a8bb|#e14d43|#25282b|#363b3f'	=> __('Gece Yarısı', 'namazvakti'),
	'#0074a2|#2ea2cc|#ffffff|#222222|#333333'	=> __('Deniz', 'namazvakti'),
	'#b43c38|#cf4944|#363b3f|#dd823b|#ccaf0b'	=> __('Gün Batımı', 'namazvakti'),
	'#c7a589|#9ea476|#ffffff|#46403c|#59524c'	=> __('Kahve', 'namazvakti'),
	'#627c83|#738e96|#363b3f|#9ebaa0|#aa9d88'	=> __('Okyanus', 'namazvakti')
);

$varsayilan_renk = get_option( NV_DB_WIDGET_COLORSET );
?>

<form method="post" action="">
<table class="form-table">
	<tbody>
    
    <tr valign="top" id="renk_alani">
    	<th scope="row"><?php _e('Widget Rengi', 'namazvakti'); ?></th>
        
        <td>

            <?php
				foreach($renkler as $renk => $adi)
				{
					
					$color = explode('|', $renk);
					$selected	= $renk == $varsayilan_renk ? ' selected' : '';
					$checked	= $renk == $varsayilan_renk ? ' checked' : '';
			?>
            <div class="color-option<?php echo $selected; ?>">
            <input type="radio" class="tog" name="renk_secenegi"<?php echo $checked; ?> value="<?php echo $renk; ?>"> <?php echo $adi; ?>
            <label>
            <table class="color-palette">
				<tbody>
                	<tr>
                    	<td style="background-color: <?php echo $color[0]; ?>">&nbsp;</td>
                        <td style="background-color: <?php echo $color[1]; ?>">&nbsp;</td>
                        <td style="background-color: <?php echo $color[2]; ?>">&nbsp;</td>
                        <td style="background-color: <?php echo $color[3]; ?>">&nbsp;</td>
                        <td style="background-color: <?php echo $color[4]; ?>">&nbsp;</td>
					</tr>
				</tbody>
            </table>
            </label>
            </div> 
            <?php
				}
			?>
        
		</td>
    </tr>
        
    </tbody>
    
</table>

<input type="submit" name="submit" class="button-primary" value="<?php _e('Kaydet', 'namazvakti'); ?>" />
</form>