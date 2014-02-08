<?php
class block_updatedresources extends block_list {
    
	public function init() {
        $this->title = get_string('updatedresources', 'block_updatedresources');
    }

    public function instance_allow_multiple() {
  		return false;
	}
    
    public function get_content() {
        global $CFG, $USER, $DB, $OUTPUT;

    	if ($this->content !== null) {
      		return $this->content;
    	}
 
	    $this->content         =  new stdClass;
	    $this->content->items  =  array();
	    $this->content->icons  =  array();
	    $this->content->footer =  '';

	    if (isloggedin() and !isguestuser()) {
	    	if ($courses = enrol_get_my_courses()) {
	    		$cs = array();
                foreach ($courses as $course) {
                	$cs[]= $course->id;
                }
                $cids = '';
                $cids = join(',',$cs);

            	$lookback = time() - 7*24*60*60;

            	$sql = 'Select cm.id, inst.moduleid, inst.name, cm.course, cm.visible, inst.timemodified, m.name as module
					From mdl_course_modules cm
					join mdl_modules m
					on  m.id = cm.module
					join (
					SELECT \'3\' AS moduleid, id, course, name, timemodified
					from mdl_book
					UNION all
					SELECT \'8\' AS moduleid, id, course, name,  timemodified
					from mdl_folder
					union all
					SELECT \'11\' AS moduleid, id, course, name, timemodified
					from mdl_imscp
					Union all
					SELECT \'15\' AS moduleid, id, course, name,  timemodified
					from mdl_page
					union all
					SELECT \'17\' AS moduleid, id, course, name, timemodified
					from mdl_resource
					union all
					SELECT \'18\' AS moduleid, id, course, name, timemodified
					from mdl_scorm
					union all
					SELECT \'20\' AS moduleid, id, course, name, timemodified
					from mdl_url
					) inst
					on inst.moduleid = cm.module
					and inst.id = cm.instance
					where cm.visible = \'1\'
					and cm.course in (?)
					and inst.timemodified > ?
					order by inst.timemodified desc
					limit 10';

                $resources = $DB->get_records_sql($sql, array($cids, $lookback));

                foreach ($resources as $resource) {
                  		$this->content->items[] = html_writer::link($CFG->wwwroot . '/mod/' . $resource->module . '/view.php?id='. $resource->id, $resource->name);
                   		$this->content->icons[] = '<img src="'.$OUTPUT->pix_url('icon',$resource->module) . '" class="iconsmall" alt="" />';
                }
            }
	    }

		return $this->content;
  	
  	}
}