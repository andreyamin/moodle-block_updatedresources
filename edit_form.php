<?php

class block_updatedresources_edit_form extends block_edit_form {

	protected function specific_definition($mform) {

		//Header
		$mform->addElement('header','configheader','Configurações do bloco');

		//Block title
		$mform->addElement('text', 'config_title', 'Título do bloco');
		$mform->setDefault('config_title','Recursos atualizados');
		$mform->setType('config_title',PARAM_MULTILANG);

		//
		$mform->addElement('text', 'config_listsize', 'Tamanho máximo da lista de recursos');
		$mform->setDefault('config_listsize','10');
		$mform->setType('config_listsize',PARAM_MULTILANG);

	}
}