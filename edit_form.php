<?php
 
class block_updatedresources_edit_form extends block_edit_form {
 
    protected function specific_definition($mform) {
 
        // Section header title according to language file.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block_updatedresources'));
 
        // A sample string variable with a default value.
        $mform->addElement('text', 'instancetitle', get_string('blocktitle', 'block_updatedresources'));
        $mform->setDefault('instancetitle', get_string('updatedresources', 'block_updatedresources'));
        $mform->setType('instancetitle', PARAM_MULTILANG);        
 
    }
}