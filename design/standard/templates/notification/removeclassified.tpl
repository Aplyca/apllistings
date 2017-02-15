{set-block scope=root variable=subject}Solictud de eliminaci&oacute;n de clasificado publicado{/set-block}

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
		{def $object = fetch( 'content', 'object', hash( 'object_id', $notification_data.object_id ) ) }
		<a href={concat('http://', ezini('SiteSettings', 'SiteURL'))}><img src="http://{ezini('SiteSettings','SiteURL')}{'images/vanguardia_bkg.png'|ezdesign('no')}"></a>
		<h1 >Solicitud de eliminaci&oacute;n de clasificado publicado</h1>		
			<p>El usuario <b>[{$notification_data.user_email}]</b> ha solicitado eliminar el siguiente clasificado publicado.</p> 	
			<p><a class="link_profile" href={$object.assigned_nodes.0.url_alias|ezurl}>{$object.assigned_nodes.0.name}</a></p>
	</body>
</html> 