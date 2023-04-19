<?php

require_once "CXenForo.php";
class PoaFUPS extends XenForoFUPS {

    /**
     * Add entries for first post in each thread which has appeared in posts found.
     *
     * Iterate over $this->posts_data, which is an array for each topic (or thread),
     * obtain the url and then obtain page 1 of the topic.
     * Check if that is already in the posts subarray.
     * If not, add the content of the first topic post to the topic array.
     *
     * Code here is modified from the superclass scrape_topic_page method.
     */
     protected function hook_after__extract_per_thread_info() {
        $this->write_status('Now scraping missing first posts of topics.');

        foreach ($this->posts_data as $topicid => $dummy) {
            $topic =& $this->posts_data[$topicid];
            $url = $this->get_topic_url($topic['forumid'], $topicid, 0); # first page of topic
            $this->set_url($url);

            $redirect_url = false;
            $html = $this->do_send($redirect_url);

            if ($redirect_url) {
                $redirect_topic_id = $this->get_topic_id_from_topic_url($redirect_url);
                $original_topic_id = $topic;
                if ($redirect_topic_id != $original_topic_id) {
                    if ($this->dbg) $this->write_err("Topic page was redirected from topic with ID '$original_topic_id' to topic with ID '$redirect_topic_id'. Skipping to next topic.", __FILE__, __METHOD__, __LINE__);
                    continue;
                }
            }

            if (!$this->skins_preg_match_all('post_contents_ext', $html, $matches, 'post_contents_ext_order')) {
                $this->write_and_record_err_admin('Error: couldn\'t find any posts on topic with ID "'.$topic.'" and page counter set to "'.$this->topic_pg_counter.'".  The URL of the page is '.$this->last_url, __FILE__, __METHOD__, __LINE__, $html);
                continue;
            }

            $match = $matches[0];

            $title    = $match[$match['match_indexes']['title'   ]];
            $ts_raw   = $match[$match['match_indexes']['ts'      ]];
            $postid   = $match[$match['match_indexes']['postid'  ]];
            $contents = $match[$match['match_indexes']['contents']];

            if (array_key_exists($postid, $topic['posts'])) continue;

            $this->ts_raw_hook($ts_raw);

            $ts = $this->strtotime_intl($ts_raw);
            if ($ts === false) {
                $err_msg = "Error: strtotime_intl failed for '$ts_raw'.";
                if ((!isset($this->settings['non_us_date_format']) || !$this->settings['non_us_date_format']) && strpos($ts_raw, '/') !== false) {
                    $err_msg .= ' Hint: Perhaps you need to check the "Non-US date format" box on the previous page.';
                }
                $this->write_err($err_msg);
            }

            $this->posts_data[$topicid]['posts'][$postid] = array(
                'posttitle' => $title,
                'ts'        => trim($ts_raw),
                'timestamp' => $ts,
                'content' => $contents,
                'forum' => $topic['forum'],
                'topic' => $topic['topic'],
                'forumid' => $topic['forumid']
            );

         }
    }

}
?>