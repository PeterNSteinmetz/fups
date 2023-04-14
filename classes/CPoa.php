<?php

require_once "CXenForo.php";
class PoaFUPS extends XenForoFUPS {

    /**
     * Add entries for first post in each thread which has appeared in posts found.
     */
     protected function hook_after__extract_per_thread_info() {
        $this->write_status('Made it to hook_after in subclass.');
    }

}
?>