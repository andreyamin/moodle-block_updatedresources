<?php

require_once('../../config.php');
//require_once('updatedresources_form.php');

global $DB, $OUTPUT, $PAGE;

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/blocks/updatedresources/view.php');
$PAGE->set_pagelayout('standard');
$PAGE->set_heading(get_string('advancedsearch', 'block_updatedresources'));


$lookback = optional_param('lookback','0', PARAM_INT);
$cid      = optional_param('cid','0', PARAM_INT);
$page     = optional_param('page', 0, PARAM_INT);
$perpage  = optional_param('perpage', 10, PARAM_INT);

if (empty($lookback)) {
	$lookback = time() - 7*24*60*60;
}

if (empty($cid)) {
	$cid = '0';
}


echo $OUTPUT->header();
//$searchform->display();

echo "<form class=\"logselectform\" action=\"$CFG->wwwroot/blocks/updatedresources/view.php\" method=\"get\">\n";
echo "<div>\n";

$strftimedate = get_string("strftimedate");
$strftimedaydate = get_string("strftimedaydate");

$timenow = time(); // GMT

// What day is it now for the user, and when is midnight that day (in GMT).
$timemidnight = $today = usergetmidnight($timenow);

// Put today up the top of the list
$dates = array("$timemidnight" => get_string("today").", ".userdate($timenow, $strftimedate) );


$numdates = 1;
while ($numdates < 31) {
    $timemidnight = $timemidnight - 86400;
    $timenow = $timenow - 86400;
    $dates["$timemidnight"] = userdate($timenow, $strftimedaydate);
    $numdates++;
}

$courses = enrol_get_my_courses();
foreach ($courses as $course) {
	$courselist[$course->id] = $course->fullname;
}

echo html_writer::label(get_string('courses', 'block_updatedresources'), 'courses');
echo html_writer::select($courselist, "cid", $cid, get_string('allcourses', 'block_updatedresources'));

echo html_writer::label(get_string('showupdatessince', 'block_updatedresources'), 'lookback');
echo html_writer::select($dates, "lookback", "$lookback", get_string('lastsevendays', 'block_updatedresources'));

echo '<input type="submit" value="Buscar" />';
echo '</div>';
echo '</form>';

if (isloggedin() and !isguestuser()) {
	if ($courses) {
		if ($cid == 0) {
			$cs = array();
			foreach ($courses as $course) {
	        	$cs[]= $course->id;
	        }
	        $cids = '';
	        $cids = join(',',$cs);
	    } else {
	    	$cids = $cid;
	    }
	    $where = 'and cm.course in (' . $cids . ') ';
	    $where .= ' and inst.timemodified > ' . $lookback;

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
			and inst.timemodified > ' . $lookback . 
			' order by inst.timemodified desc
			limit 100';
		
        $resources = $DB->get_records_sql($sql);

        if ($resources){
        	$table = new html_table();
        	$table->head = array(
        		get_string('resource', 'block_updatedresources'),
        		get_string('course', 'block_updatedresources'),
        		get_string('lastupdate', 'block_updatedresources')
        	);
        	$table->data = array();
        	$countresources = count($resources);

        	$OUTPUT->paging_bar($countresources,'0', '2' , new moodle_url('/blocks/updatedresources/view.php'));

        	foreach ($resources as $resource) {
        		$resourcename = $resource->name;
        		$resourceurl = new moodle_url($CFG->wwwroot . '/mod/' . $resource->module . '/view.php?id='. $resource->id);
        		$resourcecourse = $resource->shortname;
        		$resourcecourseurl = new moodle_url($CFG->wwwroot . '/course/view.php', array('id'=>$resource->course));
        		$date = userdate($resource->timemodified);
        		$table->data[] = array(html_writer::link($resourceurl,$resourcename), html_writer::link($resourcecourseurl,$resourcecourse), $date);
        	}
        	echo html_writer::table($table);
        }
    }
}
echo $OUTPUT->footer();