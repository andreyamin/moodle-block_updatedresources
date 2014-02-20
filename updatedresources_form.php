<?php


if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/formslib.php');

class updatedresources_form extends moodleform {
	function definition(){
		global $CFG, $COURSE, $USER;

		$mform =& $this->_form;

		$mform->addElement('header','search','Busca Avançada');

		//Start date
		$mform->addElement('date_time_selector','startdate','Buscar a partir de',  array('optional' => false));
		$mform->addRule('startdate',null,'required',null,'client');

		//Courses
		$mycourses = enrol_get_my_courses($fields = NULL, $sort = 'fullname ASC', $limit = 0);
		$list = array();
		foreach ($mycourses as $course){
			$list[$course->id] = $course->fullname;
		}
		$mform->addElement('select','coursetitle','Curso');
		$mform->getElement('coursetitle')->setMultiple(true);
		$mform->setAdvanced('coursetitle');

		//Resource name
		$mform->addElement('text','resourcename','Nome do recurso');
		$mform->setType('resourcename',PARAM_MULTILANG);
		$mform->setAdvanced('resourcename');

		$this->add_action_buttons(false,'Buscar');
	}
}