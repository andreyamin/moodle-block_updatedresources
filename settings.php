<?php

defined('MOODLE_INTERNAL') || die;

$settings->add(new admin_settings_heading(
		'configheader',
		'Configurações do bloco de Recursos Atualziados',
		'Blablabla',
	));

$settings->add(new admin_settings_configtext(
		'updatedresources/lookback',
		'Buscar recursos inseridos a quantos dias',
		'',
		'7'
	));
$settings->add(new admin_settings_configtext(
		'updatedresources/perpage',
		'Itens por página',
		'',
		'10'
	));