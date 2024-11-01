<?php
/*
  Plugin Name: UmbigoThis
  Plugin URI: http://techlive.org/wordpress/umbigothis-social-bookmarking-plugin-for-wordpress
  Description: Plugin social para Wordpress
  Author: Lenon Marcel
  Version: 0.5
  Author URI: http://techlive.org/
  
  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.
  
  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.
*/

// Prefixo da tabela no banco de dados
$umb_pre = "umbigothis";

// Nome da tabela
$umb_tabela = $wpdb->prefix.$umb_pre;

// Pega as urls do banco de dados e armazena
$urls = umb_get();

// Adiciona uma url
function umb_addurl($url, $nome, $target, $icone){
	global $wpdb, $umb_tabela;
	if (!empty($url)):
		$wpdb->query("INSERT INTO {$umb_tabela} (id, nome, url, target, icone) VALUES ('','{$nome}','{$url}','{$target}', '{$icone}')");
		return true;
	else:
		return false;
	endif;
}

// Faz uma busca pelo ID
function umb_search($id) {
	global $wpdb, $umb_tabela;
	if (!empty($id)):
		$nome = $wpdb->get_var("SELECT nome FROM {$umb_tabela} WHERE id = {$id}");
		$url = $wpdb->get_var("SELECT url FROM {$umb_tabela} WHERE id = {$id}");
		return array($nome, $url);
	endif;
}

// Pega as URLs
function umb_get(){
	global $wpdb, $umb_tabela;
	$resultados = $wpdb->get_results("SELECT * FROM {$umb_tabela}");
	return $resultados;
}

// Deleta uma URL
function umb_del($id){
	global $wpdb, $umb_tabela;
	$wpdb->query("DELETE FROM {$umb_tabela} WHERE id = {$id}");
	return "<div class='updated'><p><strong>Site removido</strong></p></div>";
}

// Retorna o evento onclick da janela popup
function umb_onclick($idp) {
	$rt = "onclick=\"umbigo('{$idp}');return false;\"";
	return $rt;
}

// Substitui as tags {url} e {title}
function umb_rep($urlpt, $titulo, $url){
	$fin = str_replace(array("{url}","{title}"), array($urlpt, urlencode($titulo)), $url);
	return $fin;
}

// Adiciona o HTML no conteúdo
function umb_echo($content) {
	global $urls, $post;
	
	$icones = get_option('umb_showicones');
	
	$info = array(
				urlencode(get_permalink($post->ID)),
				umb_onclick($post->ID),
				get_option('umb_txtexibicao'),
				$post->ID
				);
	if ($icones == 'false'):
			$content .= "
		<p class='umbigoThis_show'>
			<a href='#' {$info[1]} rel='nofollow' class='umbigoThis_show_link'>{$info[2]}</a>
		</p>
		<div class='umbigoThis_pop cp-{$info[3]}'>
			<p class='umbigoThis_fechar'>
				<a href='#' {$info[1]} rel='nofollow' class='umbigoThis_fechar_link'>Fechar</a>
				<span class='umbigoThis_fechar_desc'>Compartilhe este post!</span>
			</p>
			<p class='umbigoThis_links'>";
				foreach ($urls as $dados) {
					$nome = str_replace(' ', '', $dados->nome);
					$nome = eregi_replace("([^a-z])","",$nome);
					$urlfn = umb_rep($info[0], get_the_title(), $dados->url);
					$content .= "<a class='umbigoThis_sclink umbigoThis_link_{$nome}' href='{$urlfn}' rel='nofollow' target='" . $dados->target . "'>" . $dados->nome . "</a></li>";
				};
				$content .= "</p><div class='umbigoThis_limpa'></div></div>";
			return $content;
	else:
			$content .= "<p class='umbigoThis_icones'><small>{$info[2]}</small><br/>";
			foreach ($urls as $dados) {
				$nome = str_replace(' ', '', $dados->nome);
				$nome = eregi_replace("([^a-z])","",$nome);
				$urlfn = umb_rep($info[0], get_the_title(), $dados->url);
				$content .= "<a class='umbigoThis_icone umbigoThis_icone_{$nome}' title='{$dados->nome}' href='{$urlfn}' rel='nofollow' target='" . $dados->target . "'><img src='". get_bloginfo('wpurl') . "/wp-content/plugins/umbigothis/imagens/{$dados->icone}' /></a>";
			}
			$content .= "</p>";
			return $content;
	endif;
}

// Verifica o tipo da página
function umb_content($content) {
	global $urls;
	$post_url = urlencode(get_permalink($post->ID));

	if (is_home() && get_option('umb_showhome') == 'true'):
		return umb_echo($content);
	elseif (is_single()):
		return umb_echo($content);
	else:
		return $content;
	endif;
}

// Adiciona as chamadas do CSS e JavaScript no head do documento
function umb_head() {
	global $urls;

	$info = array(
				get_bloginfo('wpurl') . "/wp-content/plugins/umbigothis/css/estilo.css", //0
				get_bloginfo('wpurl') . "/wp-content/plugins/umbigothis/css/estilo_icones.css", //1

				get_bloginfo('wpurl') . "/wp-content/plugins/umbigothis/js/efeitos/fade.js", //2
				get_bloginfo('wpurl') . "/wp-content/plugins/umbigothis/js/efeitos/drop.js", //3
				get_bloginfo('wpurl') . "/wp-content/plugins/umbigothis/js/efeitos/fold.js", //4
				get_bloginfo('wpurl') . "/wp-content/plugins/umbigothis/js/efeitos/nenhum.js", //5

				get_bloginfo('wpurl') . "/wp-content/plugins/umbigothis/js/effects.core.packed.js", //6
				get_bloginfo('wpurl') . "/wp-content/plugins/umbigothis/js/effects.drop.packed.js", //7
				get_bloginfo('wpurl') . "/wp-content/plugins/umbigothis/js/effects.fold.packed.js", //8

				get_bloginfo('wpurl') . "/wp-content/plugins/umbigothis/js/jquery.js" //9
				);

	if (get_option('umb_autojquery') == 'true'):
		echo "<script type='text/javascript' src='{$info[9]}' ></script>";
	endif;
	
	if (get_option('umb_showicones') == 'false'):
		switch (get_option('umb_efeitopopup')) {
			case 'fade':
				print('<script type="text/javascript" src="'. $info[2] .'" ></script>');
			break;
			
			case 'fold':
				print('<script type="text/javascript" src="'. $info[6] .'" ></script>');
				print('<script type="text/javascript" src="'. $info[8] .'" ></script>');
				print('<script type="text/javascript" src="'. $info[4] .'" ></script>');
			break;

			case 'drop':
				print('<script type="text/javascript" src="'. $info[6] .'" ></script>');
				print('<script type="text/javascript" src="'. $info[7] .'" ></script>');
				print('<script type="text/javascript" src="'. $info[3] .'" ></script>');
			break;
			
			case 'nenhum':
				print('<script type="text/javascript" src="'. $info[5] .'" ></script>');
			break;
		}
		print('<link rel="stylesheet" type="text/css" href="'. $info[0] .'" />');
		print('<style type="text/css">');
		foreach ($urls as $dados) {
					$nome = str_replace(' ', '', $dados->nome);
					$nome = eregi_replace("([^a-z])","",$nome);
					print('.umbigoThis_link_' . $nome . '{background: url(' . get_bloginfo('wpurl') . '/wp-content/plugins/umbigothis/imagens/' . $dados->icone . ') left no-repeat;padding-left:18px;}');
				}

		print('</style>');
	else:
		print('<link rel="stylesheet" type="text/css" href="'. $info[1] .'" />');
	endif;
}

// Instala o plugin
function umb_install(){
	global $wpdb;

	// Conjunto de URLs padrão
	$umb_social = array(
			array('diHitt', 'http://dihitt.com.br/submit.php?url={url}&titulo={title}', 'dihitt.gif'),
			array('B!Links', 'http://blinks.blogueisso.com/node/add/drigg?url={url}&title={title}', 'blinks.gif'),
			array('Ueba', 'http://ueba.com.br/NovoLink?url={url}&title={title}', 'ueba.gif'),
			array('Linkk', 'http://www.linkk.com.br/submit.php?url={url}', 'linkk.gif'),
			array('Rec6', 'http://rec6.via6.com/link.php?url={url}&titulo={title}', 'rec6.gif'),
			array('LinkTo', 'http://www.linkto.com.br/site.enviaNoticia.php?title={title}&url={url}', 'linkto.gif'),
			array('Eu Curti', 'http://www.eucurti.com.br/submit.php?url={url}', 'eucurti.gif'),
			array('Do Melhor', 'http://www.domelhor.net/submit.php?url={url}', 'domelhor.gif'),
			array('Ouvi Dizer', 'http://www.ouvidizer.com/submit.php?url={url}', 'ouvidizer.gif'),
			array('Link Loko', 'http://www.linkloko.com.br/submit.php?url={url}', 'linkloko.gif'),
			array('del.icio.us', 'http://del.icio.us/post?url={url}&title={title}', 'delicious.gif'),
			array('Digg', 'http://digg.com/submit?phase=2&url={url}&title={title}', 'digg.gif'),
			array('Furl', 'http://furl.net/storeIt.jsp?u={url}&t={title}', 'furl.gif'),
			array('Netscape', 'http://www.netscape.com/submit/?U={url}&T={title}', 'netscape.gif'),
			array('Yahoo! My Web', 'http://myweb2.search.yahoo.com/myresults/bookmarklet?u={url}&t={title}', 'yahoo_myweb.gif'),
			array('StumbleUpon', 'http://www.stumbleupon.com/submit?url={url}&title={title}', 'stumbleupon.gif'),
			array('Google Bookmarks', 'http://www.google.com/bookmarks/mark?op=edit&bkmk={url}&title={title}', 'google_bmarks.gif'),
			array('Technorati', 'http://www.technorati.com/faves?add={url}', 'technorati.gif'),
			array('BlinkList', 'http://blinklist.com/index.php?Action=Blink/addblink.php&Url={url}&Title={title}', 'blinklist.gif'),
			array('Newsvine', 'http://www.newsvine.com/_wine/save?u={url}&h={title}', 'newsvine.gif'),
			array('ma.gnolia', 'http://ma.gnolia.com/bookmarklet/add?url={url}&title={title}', 'magnolia.gif'),
			array('reddit', 'http://reddit.com/submit?url={url}&title={title}', 'reddit.gif'),
			array('Windows Live', 'https://favorites.live.com/quickadd.aspx?marklet=1&mkt=en-us&url={url}&title={title}&top=1', 'windows_live.gif'),
			array('Tailrank', 'http://tailrank.com/share/?link_href={url}&title={title}', 'tailrank.gif'),
			array('Meneame', 'http://www.meneame.net/submit.php?url={url}', 'meneame.gif')
			);

	$umb_pre = "umbigothis";

	$umb_tabela = $wpdb->prefix.$umb_pre;

	$estrutura = "CREATE TABLE $umb_tabela (
		id INT(9) NOT NULL AUTO_INCREMENT,
		nome VARCHAR(255) NOT NULL,
		url VARCHAR(255) NOT NULL,
		target VARCHAR(100) NOT NULL,
		icone VARCHAR(200) NOT NULL,
		UNIQUE KEY id (id)
	);";

	if($wpdb->get_var("SHOW TABLES LIKE '$umb_tabela'") != $umb_tabela) {
		$wpdb->query($estrutura);

		foreach ($umb_social as $ss => $ff) {
    		$wpdb->query("INSERT INTO " . $umb_tabela . " (nome, url, target, icone) VALUES ('" . $ff[0] . "','" . $ff[1] . "','_blank', '". $ff[2] . "')");
		}

		add_option('umb_autojquery', 'true', '', 'yes');
		add_option('umb_txtexibicao', 'UmbigoThis!', '', 'yes');
		add_option('umb_showhome', 'false', '', 'yes');
		add_option('umb_showicones', 'false', '', 'yes');
		add_option('umb_efeitopopup', 'fade', '', 'yes');
	}
}

// Desinstala o plugin
function umb_uninstall(){
	global $wpdb, $umb_tabela;

    $wpdb->query("DROP TABLE $umb_tabela");
}

// Adiciona o menu de configurações
function umb_menu(){
	add_options_page('UmbigoThis', 'UmbigoThis', 8, __FILE__, 'umb_painel');
}

// Menu de configurações
function umb_painel(){
	if (isset($_POST['url_add']) ) {
		$info = array(
					$_POST['url_site'],
					$_POST['nome_site'],
					$_POST['target_site'],
					$_POST['imagem_site']
				);
		if(umb_addurl($info[0], $info[1], $info[2], $info[3])):
			echo "<div class='updated'><p><strong>Site adicionado</strong></p></div>";
		else:
			echo "<div class='updated'><p>URL ou Nome do site em branco</p></div>";
		endif;
	};
	
	if (isset($_POST['opts_salvar']) ) {
		if (isset($_POST['box_jquery'])) :
			update_option('umb_autojquery', 'true');
		else:
			update_option('umb_autojquery', 'false');
		endif;

		if (isset($_POST['box_showhome'])) :
			update_option('umb_showhome', 'true');
		else:
			update_option('umb_showhome', 'false');
		endif;
		
		if (isset($_POST['box_showicones'])) :
			update_option('umb_showicones', 'true');
		else:
			update_option('umb_showicones', 'false');
		endif;
		
		if (isset($_POST['box_efeitopopup'])) :
			update_option('umb_efeitopopup', $_POST['box_efeitopopup']);
		endif;

		update_option('umb_txtexibicao', $_POST['opts_texto']);

		echo "<div class='updated'><p><b>Salvo</b></p></div>";
	}
	
	if (isset($_POST['url_del_conf']) ) {
		echo umb_del($_POST['url_cid']);
	};

	if ( isset($_POST['url_del']) ) :
		$id = $_POST['url_id'];
		$nm = umb_search($id);
		echo "<div class='updated'><p>Você tem certeza que deseja remover o site <b>{$nm[0]}</b> (URL: <b>{$nm[1]}</b>) ?</p>
	<p><form method='post'>
		<input type='hidden' name='url_cid' value='{$id}'/>
		<input type='submit' value='Remover' name='url_del_conf' class='button'/>
		<input type='submit' value='Cancelar' name='' class='button'/>
	</form></p>
</div>";
	else:
		$vl_efeitos = array(
			array('fade', 'FadeIn/FadeOut'),
			array('nenhum', 'Nenhum'),
			array('drop', 'Drop'),
			array('fold', 'Fold')
		);
?>
<div style="padding:0 15px 15px 15px;">
	<h2>UmbigoThis</h2>
	<p>Nesta página você poderá editar as opções do plugin UmbigoThis</p>
	<p>
		<form method="post">
		<?php if (get_option('umb_autojquery') == 'true'): $check = 'checked'; else: $check = ''; endif; ?>

		<input type="checkbox" name="box_jquery" id="box_jquery" <?php echo $check;?>/>Chamar jQuery<br/>
		<small>Desmarque caso você já use jQuery no seu Wordpress</small><br/>

		<?php if (get_option('umb_showhome') == 'true'): $check2 = 'checked'; else: $check2 = ''; endif; ?>

		<input type="checkbox" name="box_showhome" id="box_showhome" <?php echo $check2;?>/>Exibir na página principal<br/>
		<small>Marque se deseja que o UmbigoThis seja exibido na home</small><br/>

		<?php if (get_option('umb_showicones') == 'true'): $check3 = 'checked'; else: $check3 = ''; endif; ?>

		<input type="checkbox" name="box_showicones" id="box_showicones" <?php echo $check3;?>/>Modo ícones<br/>
		<small>Exibir ícones ao invés da janela popup</small><br/><br/>

		<label for="opts_texto">Texto de exibição:</label>
		<input type="text" name="opts_texto" id="opts_texto" size="25" value="<?php echo get_option('umb_txtexibicao');?>" /><br/>
		<small>Exemplo: "Compartilhe", "Social Bookmarks"...</small><br/><br/>

		<label for="box_efeitopopup">Efeito janela popup:</label>
		<select name="box_efeitopopup">
<?php foreach ($vl_efeitos as $eft=>$eftt){
			if ($eftt[0] == get_option('umb_efeitopopup')):
				$selected = "selected='selected'";
			else:
				$selected = "";
			endif;
	?>
			<option <?php echo $selected; ?> value='<?php echo $eftt[0]; ?>'><?php echo $eftt[1]; ?></option>
<?php } ?>
		</select>
		
		<br/>
		<small>Efeito ao abrir/fechar a janela popup</small><br/><br/>

		<input type="submit" value="Salvar" name="opts_salvar" class="button"/>
		</form>
	</p>
    <table class="form-table">
    	<thead>
    		<tr>
    			<th scope="col">ID</th>
    			<th scope="col">Site</th>
    			<th scope="col">Url</th>
    			<th scope="col">Ícone</th>
    			<th scope="col"></th>
    		</tr>
    	</thead>
    	<tbody>
<?php
    	$res = umb_get();
    	$url_icone = get_bloginfo('wpurl') . '/wp-content/plugins/umbigothis/imagens/';
    	if (!empty($res)):
    		foreach ($res as $rst) {
?>
    		<tr>
    			<td><?php echo $rst->id;?></td>
    			<td><?php echo $rst->nome;?></td>
    			<td><?php echo $rst->url;?></td>
    			<td><?php echo $rst->icone; ?><br/>
    			<img src="<?php echo $url_icone . $rst->icone; ?>" /></td>
    			<td>
    				<form method="post">
    					<input type="hidden" name="url_id" value="<?php echo $rst->id;?>"/>
    					<input type="submit" value="Remover" name="url_del" class="button"/>
    				</form>
    			</td>
    		</tr>
<?php
			}
			?>
    	</tbody>
    </table>
    <?php
    else:
    ?>
    	</tbody>
    </table>
    <p style="text-align:center;"><b>Nenhum site cadastrado</b></p>
    <?php    
    endif; ?>
<form method="post">
<p><b>Adicionar url:</b></p>
	<table class="form-table">
		<tbody>
			<tr>
				<th><label for="nome_site">Nome</label></th>
				<td><input type="text" name="nome_site" id="nome_site" size="40" value="" /></td>
			</tr>
			<tr>
				<th><label for="url_site">Url</label></th>
				<td>
					<input type="text" name="url_site" id="url_site" size="40" value="" />
					<br/>
					<small>Tags: Url - <b>{url}</b> | Título - <b>{title}</b></small><br/>
					<small>Exemplo: http://dihitt.com.br/submit.php?url=<b>{url}</b>&titulo=<b>{title}</b></small>
				</td>
			</tr>
			<tr>
				<th><label for="imagem_site">Ícone</label></th>
				<td>
					<input type="text" name="imagem_site" id="imagem_site" size="40" value="padrao.png" />
					<br/>
					<small>Todos os ícones devem estar na pasta <em>icones</em> dentro do diretório deste plugin</small>
				</td>
			</tr>
			<tr>
				<th><label for="target_site">Target</label></th>
				<td>
					<input type="text" name="target_site" id="target_site" size="40" value="_blank" />
				</td>
			</tr>
			<tr>
				<td></td>
				<td>
					<input type="submit" value="Adicionar" name="url_add" class="button"/>
					<input type="reset" value="Limpar" name="url_limpa" class="button"/>
				</td>
			</tr>
		</tbody>
	</table>
</form>
</div>
<?php
	endif;
}

// Filtro conteúdo
add_action('the_content', 'umb_content');

// Adiciona as chamadas do CSS e JavaScript no head do documento
add_action('wp_head', 'umb_head');

// Menu de configuração
add_action('admin_menu', 'umb_menu');

// Função de instalação
register_activation_hook(__FILE__, 'umb_install');

// Desinstalação
register_deactivation_hook(__FILE__, 'umb_uninstall');
?>
