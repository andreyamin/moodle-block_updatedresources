<?php

class block_updatedresources_edit_form extends block_edit_form {

	protected function specific_definition($mform) {

		//Header
		$mform->addElement('header','configheader','Configurações do bloco');

		//Block title
		$mform->addElement('text', 'title', 'Título do bloco');
		$mform->setDefault('title','Recursos atualizados');
		$mform->setType('title','PARAM_MULTILANG');

		//
		$mform->addElement('text', 'listsize', 'Tamanho máximo da lista de recursos');
		$mform->setDefault('listsize','10');
		$mform->setType('listsize','PARAM_MULTILANG');

	}
}