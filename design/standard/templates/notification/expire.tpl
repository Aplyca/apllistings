{set-block scope=root variable=receiver}{$node.object.owner.data_map.user_account.value.email}{/set-block}
{set-block scope=root variable=subject}{"your classified is about to expire"|i18n('classifieds/notification')}{/set-block}

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
		
		<h1>{"Good day Mr(s)"|i18n('classifieds/notification')}. {$node.object.owner.data_map.first_name.value} {$node.object.owner.data_map.last_name.value}</h1> 
		
		<p>{"Your classified announcement"|i18n('classifieds/notification')} <a href={concat('http://', ezini('SiteSettings', 'SiteURL'),$node.url_alias|ezurl(no))}>{attribute_view_gui attribute=$node.data_map.title}</a>, {"expire the day"|i18n('classifieds/notification')} ({attribute_view_gui attribute=$node.data_map.web_end_date}).</p>
		
		<p>{"This is it a notification of"|i18n('classifieds/notification')} <a href={concat('http://', ezini('SiteSettings', 'SiteURL'))}>Vanguardia - Clasificados</a>, {"if you wish re-publish your announcement, please go to your user account through this link"|i18n('classifieds/notification')}. <a href={concat('http://', ezini('SiteSettings', 'SiteURL'),'/Mi-Vanguardia'|ezurl(no))}> Mi-Vanguardia</a>.</p>
		
		<p>{"Cordially"|i18n('classifieds/notification')}.</p>
		
		<h3>Vanguardia - Clasificados</h3>
		
	</body>
</html> 