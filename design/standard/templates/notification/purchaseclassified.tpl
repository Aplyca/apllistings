{set-block scope=root variable=receiver}{$node.object.owner.data_map.user_account.value.email}{/set-block}
{set-block scope=root variable=subject}{"Anuncio pagado en Vanguardia Clasificados"|i18n('classifieds/notification')}{/set-block}
<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		{literal}
			<style type="text/css">
				body{text-align:justify;}
				h1 {color:#164375;font-family:Arial;font-size:30px;font-weight:normal;}
				p {font-family:Arial;font-size:12px;}
				a {color:#105AA1;font-family:Arial;font-size:12px;}
				a:hover {color:#105AA1;font-family:Arial;font-size:12px;text-decoration:underline;}
				h3 {color:#FC7A16;font-family:Arial;font-size:20px;font-weight:normal;}
			</style>
		{/literal}
	</head>
	<body>  
		<a href={concat('http://', ezini('SiteSettings', 'SiteURL'))}><img src="http://{ezini('SiteSettings','SiteURL')}{'images/vanguardia_bkg.png'|ezdesign('no')}"></a>
		{if eq($status, 'success')}
			<h1 > Confirmaci&oacute;n de pago de Anuncio</h1>		
			{include uri="design:mailorderinfo.tpl" items=$shipping order=$order account_information=$account_information  type = $type}	
			<p>Tu clasificado ser&aacute; PUBLICADO de acuerdo a la fecha de inicio. Para ver los clasificados que has creado, te invitamos a que ingreses a tu perfil.</p> 				
			<div class="link_announcement">
				<a class="link_profile" href={'Mi-Vanguardia'|ezurl}></a>
			</div>
		{else}
			<div class="link_announcement">
				<p>Tu transacci&oacute;n ha fallado por favor intenta nuevamente.</p>
				<a href="/">Volver</a>
			</div>
		{/if}	
		
	</body>
</html> 