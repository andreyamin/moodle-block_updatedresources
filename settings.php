<?php

defined('MOODLE_INTERNAL') || die;

$settings->add(new admin_setting_heading(
		'configheader',
		'Configurações do bloco de Recursos Atualziados',
		''
	));

$settings->add(new admin_setting_configtext(
		'updatedresources/lookback',
		'Buscar recursos inseridos a quantos dias',
		'',
		'7'
	));
$settings->add(new admin_setting_configtext(
		'updatedresources/perpage',
		'Itens por página',
		'',
		'10'
	));