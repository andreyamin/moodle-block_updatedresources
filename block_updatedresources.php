<?php
class block_updatedresources extends block_list {
    
    public function init() {
		$this->title = get_string('updatedresources', 'block_updatedresources');
    }

    function has_config() {
		return true;
	}

	public function specialization() {
		if (!empty($this->config->title)) {
			$this->title = $this->config->title;
		}  
	}

    public function instance_allow_multiple() {
  		return false;
	}

	public function applicable_formats() {
    	return array('my-index' => true);
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

            	if (isset($this->config->listsize)) {
            		$listsize = $this->config->listsize;
            	} else {
            		$listsize = '10';
            	}

            	$sql = 'Select cm.id, inst.moduleid, inst.name, cm.course, cm.visible, inst.timemodified, m.name as module
					From mdl_course_modules cm
					join mdl_modules m
					on  m.id = cm.module
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
					from mdl_url u) inst
					on inst.moduleid = cm.module
					and inst.id = cm.instance
					where cm.visible = \'1\'
					and cm.course in (' . $cids . ')
					and inst.timemodified > ' . $lookback . '
					order by inst.timemodified desc
					limit ' . $listsize;


                $resources = $DB->get_records_sql($sql);

                foreach ($resources as $resource) {
                  		$this->content->items[] = html_writer::link($CFG->wwwroot . '/mod/' . $resource->module . '/view.php?id='. $resource->id, $resource->name);
                   		$this->content->icons[] = '<img src="'.$OUTPUT->pix_url('icon',$resource->module) . '" class="iconsmall" alt="" />';
                }
                $this->content->footer =  html_writer::link(new moodle_url('/blocks/updatedresources/view.php'),get_string('advancedsearch', 'block_updatedresources'));
            }

		return $this->content;

	    }
  	
  	}
}