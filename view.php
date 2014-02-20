<?php

require_once('../../config.php');
require_once('updatedresources_form.php');

global $DB, $OUTPUT, $PAGE;

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/blocks/updatedresources/view.php');
$PAGE->set_pagelayout('standard');
$PAGE->set_heading('Busca Avançada');

//Form defalults
$param = new stdClass();
$param->perpage = '10';
$param->startdate = time()-7*24*60*60;

$searchform = new updatedresources_form();


echo $OUTPUT->header();
$searchform->display();

if (isloggedin() and !isguestuser()) {
	if ($courses = enrol_get_my_courses()) {
		$cs = array();
        foreach ($courses as $course) {
        	$cs[]= $course->id;
        }
        $cids = '';
        $cids = join(',',$cs);

       	$lookback = time() - 7*24*60*60;

       	$sql = 'Select cm.id, inst.moduleid, inst.name, cm.course, cm.visible, inst.timemodified, c.shortname, m.name as module
			From mdl_course_modules cm
			join mdl_modules m
			on  m.id = cm.module
			join mdl_course c
			on cm.course = c.id
			join (
			SELECT \'3\' AS moduleid, id, course, name, timemodified
			from mdl_book b
			UNION all
			SELECT \'8\' AS moduleid, id, course, name,  timemodified
			from mdl_folder f
			union all
			SELECT \'11\' AS moduleid, id, course, name, timemodified
			from mdl_imscp i 
			Union all
			SELECT \'15\' AS moduleid, id, course, name,  timemodified
			from mdl_page p
			union all
			SELECT \'17\' AS moduleid, id, course, name, timemodified
			from mdl_resource r
			union all
			SELECT \'18\' AS moduleid, id, course, name, timemodified
			from mdl_scorm s
			union all
			SELECT \'20\' AS moduleid, id, course, name, timemodified
			from mdl_url u
			) inst
			on inst.moduleid = cm.module
			and inst.id = cm.instance
			where cm.visible = \'1\'
			and cm.course in (' . $cids . ')
			and inst.timemodified > ' . $lookback . '
			order by inst.timemodified desc
			limit 20';

		

        $resources = $DB->get_records_sql($sql);

        if ($resources){
        	$table = new html_table();
        	$table->head = array('Recurso','Curso', 'Data de atualização');
        	$table->data = array();
        	$countresources = count($resources);

        	$OUTPUT->paging_bar($countresources,'0', '2' , new moodle_url('/blocks/updatedresources/view.php'));

        	foreach ($resources as $resource) {
        		$resourcename = $resource->name;
        		$resourceurl = new moodle_url($CFG->wwwroot . '/mod/' . $resource->module . '/view.php?id='. $resource->id);
        		$resourcecourse = $resource->shortname;
        		$resourcecourseurl = new moodle_url($CFG->wwwroot . '/course/view.php', array('id'=>$resource->course));
        		$timemodified = usergetdate($resource->timemodified);
        		//var_dump($timemodified);
        		$date = $timemodified['mday'] . '/' . $timemodified['mon'] . '/' . $timemodified['year']. ' ' . $timemodified['hours'] . ':' . $timemodified['minutes'] ;
        		$table->data[] = array(html_writer::link($resourceurl,$resourcename), html_writer::link($resourcecourseurl,$resourcecourse), $date);
        	}
        	echo html_writer::table($table);
        }
    }
}
echo $OUTPUT->footer();