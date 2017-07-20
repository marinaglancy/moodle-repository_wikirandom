<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This plugin is used to access wikimedia files
 *
 * @since Moodle 2.0
 * @package    repository_wikirandom
 * @copyright  2010 Dongsheng Cai {@link http://dongsheng.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->dirroot . '/repository/lib.php');
require_once(__DIR__ . '/../wikimedia/wikimedia.php');

/**
 * repository_wikirandom class
 * This is a class used to browse images from wikimedia
 *
 * @since Moodle 2.0
 * @package    repository_wikirandom
 * @copyright  2009 Dongsheng Cai {@link http://dongsheng.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class repository_wikirandom extends repository {

    /**
     * Returns maximum width for images
     *
     * Takes the maximum width for images eithre from search form or from
     * user preferences, updates user preferences if needed
     *
     * @return int
     */
    public function get_maxwidth() {
        $param = optional_param('wikimedia_maxwidth', 0, PARAM_INT);
        $pref = get_user_preferences('repository_wikimedia_maxwidth', WIKIMEDIA_IMAGE_SIDE_LENGTH);
        if ($param > 0 && $param != $pref) {
            $pref = $param;
            set_user_preference('repository_wikimedia_maxwidth', $pref);
        }
        return $pref;
    }

    /**
     * Returns maximum height for images
     *
     * Takes the maximum height for images eithre from search form or from
     * user preferences, updates user preferences if needed
     *
     * @return int
     */
    public function get_maxheight() {
        $param = optional_param('wikimedia_maxheight', 0, PARAM_INT);
        $pref = get_user_preferences('repository_wikimedia_maxheight', WIKIMEDIA_IMAGE_SIDE_LENGTH);
        if ($param > 0 && $param != $pref) {
            $pref = $param;
            set_user_preference('repository_wikimedia_maxheight', $pref);
        }
        return $pref;
    }

    public function get_listing($path = '', $page = '') {
        $client = new wikimedia;
        $list = array();
        $list['page'] = (int)$page;
        if ($list['page'] < 1) {
            $list['page'] = 1;
        }
        $list['list'] = $client->search_images($this->keyword, $list['page'] - 1,
            array('iiurlwidth' => $this->get_maxwidth(),
                'iiurlheight' => $this->get_maxheight()));
        $list['nologin'] = true;
        $list['norefresh'] = true;
        $list['nosearch'] = true;
        if (!empty($list['list'])) {
            $list['pages'] = -1; // means we don't know exactly how many pages there are but we can always jump to the next page
        } else if ($list['page'] > 1) {
            $list['pages'] = $list['page']; // no images available on this page, this is the last page
        } else {
            $list['pages'] = 0; // no paging
        }
        return $list;
    }
    // login
    public function check_login() {
        $keywords = [
            'ape',
            'baboon',
            'bat',
            'bear',
            'bird',
            'bison',
            'butterfly',
            'cat',
            'cheetah',
            'cow',
            'deer',
            'dingo',
            'dog',
            'dolphin',
            'duck',
            'eagle',
            'elephant',
            'fish',
            'fox',
            'fox',
            'gazelle',
            'giraffe',
            'hamster',
            'hippo',
            'horse',
            'horse',
            'kangaroo',
            'kangaroo',
            'koala',
            'leopard',
            'lion',
            'lobster',
            'monkey',
            'mouse',
            'ostrich',
            'parrot',
            'penguin',
            'pig',
            'pony',
            'possum',
            'quoll',
            'rabbit',
            'rhino',
            'seal',
            'sheep',
            'spider',
            'tiger',
            'turkey',
            'wallaby',
            'whale',
            'wolf',
            'wombat',
            'zebra',
        ];
        $this->keyword = $keywords[rand(0, count($keywords)-1)];
        return true;
    }

    //search
    // if this plugin support global search, if this function return
    // true, search function will be called when global searching working
    public function global_search() {
        return false;
    }

    public function search($search_text, $page = 0) {
        $client = new wikimedia;
        $search_result = array();
        $search_result['list'] = $client->search_images($search_text);
        return $search_result;
    }

    // when logout button on file picker is clicked, this function will be
    // called.
    public function logout() {
        return $this->print_login();
    }

    public function supported_returntypes() {
        return (FILE_INTERNAL | FILE_EXTERNAL);
    }

    /**
     * Return the source information
     *
     * @param stdClass $url
     * @return string|null
     */
    public function get_file_source_info($url) {
        return $url;
    }

    /**
     * Is this repository accessing private data?
     *
     * @return bool
     */
    public function contains_private_data() {
        return false;
    }
}
