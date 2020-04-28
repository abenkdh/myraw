<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Api extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('model_api');
        $this->load->library('image_lib');
    }

    // index function
    public function index()
    {
        $post = $this->input->get('data');
        $method =  json_decode(urldecode(base64_decode($post)),true);
        //$method =  json_decode(urldecode($post),true);
        if($method['method_name'] == 'all_tv'){
            $jsonObj = array();
            $this->db->order_by("livetv_category_id","DESC");
            $data_tv_category = $this->db->get('tbl_livetv_category')->result_array();
            foreach ($data_tv_category as $data){
                $row['livetv_category_id'] = $data['livetv_category_id'];
                $row['livetv_category_name'] = $data['livetv_category_name'];
                $this->db->where('livetv_cid', $data['livetv_category_id']);
                $this->db->order_by("livetv_id","ASC");
                $data_tv = $this->db->get('tbl_livetv')->result_array();
                if ($data_tv) {
                    foreach ($data_tv as $datas){
                        $row_tv['livetv_name'] = $datas['livetv_name'];
                        $row_tv['livetv_url'] = $datas['livetv_url'];
                        $row_tv['livetv_image'] = $this->makeThumb($datas['livetv_image'], 250, false);
                        $tv_data[] = $row_tv;
                    }
                    $row['ALL_TV'] = $tv_data;
                    unset($tv_data);
                } else {
                    $row['ALL_TV'] = array();
                }
                array_push($jsonObj, $row);
            }
            $set['BENKKSTUDIO'] = $jsonObj;
        } else if ($method['method_name'] == "all_series") {
            $jsonObj = array();
            $this->db->order_by("series_id","DESC");
            $data_series = $this->db->get('tbl_series')->result_array();
            foreach ($data_series as $data){
                $row['series_id'] = $data['series_id'];
                $row['series_tmdb'] = $data['series_tmdb'];
                $row['series_backdrop_path'] = $this->checkImageFound($data['series_backdrop_path']);
                $row['series_first_air_date'] = $data['series_first_air_date'];
                $row['series_genres'] = $data['series_genres'];
                $row['series_homepage'] = $data['series_homepage'];
                $row['series_in_production'] = $data['series_in_production'];
                $row['series_languages'] = $data['series_languages'];
                $row['series_number_of_episodes'] = $data['series_number_of_episodes'];
                $row['series_number_of_seasons'] = $data['series_number_of_seasons'];
                $row['series_origin_country'] = $data['series_origin_country'];
                $row['series_original_language'] = $data['series_original_language'];
                $row['series_original_name'] = $data['series_original_name'];
                $row['series_overview'] = $data['series_overview'];
                $row['series_poster_path'] = $this->checkImageFound($data['series_poster_path']);
                $row['series_status'] = $data['series_status'];
                $row['series_type'] = $data['series_type'];
                $row['series_vote_average'] = $data['series_vote_average'];
                $row['series_vote_count'] = $data['series_vote_count'];

                array_push($jsonObj, $row);
            }

            $set['BENKKSTUDIO'] = $jsonObj;
        } else if ($method['method_name'] == "detail_series") {
            $jsonObj = array();
            $this->db->where("series_id", $method['series_id']);
            $this->db->order_by("series_id","DESC");
            $data_series = $this->db->get('tbl_series')->result_array();
            foreach ($data_series as $data){
                $row['series_id'] = $data['series_id'];
                $row['series_tmdb'] = $data['series_tmdb'];
                $row['series_backdrop_path'] = $this->checkImageFound($data['series_backdrop_path']);
                $row['series_first_air_date'] = $data['series_first_air_date'];
                $row['series_genres'] = $data['series_genres'];
                $row['series_homepage'] = $data['series_homepage'];
                $row['series_in_production'] = $data['series_in_production'];
                $row['series_languages'] = $data['series_languages'];
                $row['series_number_of_episodes'] = $data['series_number_of_episodes'];
                $row['series_number_of_seasons'] = $data['series_number_of_seasons'];
                $row['series_origin_country'] = $data['series_origin_country'];
                $row['series_original_language'] = $data['series_original_language'];
                $row['series_original_name'] = $data['series_original_name'];
                $row['series_overview'] = $data['series_overview'];
                $row['series_poster_path'] = $this->checkImageFound($data['series_poster_path']);
                $row['series_status'] = $data['series_status'];
                $row['series_type'] = $data['series_type'];
                $row['series_vote_average'] = $data['series_vote_average'];
                $row['series_vote_count'] = $data['series_vote_count'];

                //SERIES TRAILER
                $this->db->where("trailer_tmdb", $data['series_tmdb']);
                $this->db->order_by("trailer_id","ASC");
                $data_trailer = $this->db->get('tbl_series_trailer')->result_array();

                if ($data_trailer) {
                    foreach ($data_trailer as $datas_trailer){
                        $row_trailer['trailer_key'] = $datas_trailer['trailer_key'];
                        $row_trailer['trailer_name'] = $datas_trailer['trailer_name'];
                        $row_trailer['trailer_site'] = $datas_trailer['trailer_site'];
                        $row_trailer['trailer_type'] = $datas_trailer['trailer_type'];
                        $trailer_data[] = $row_trailer;
                    }
                    $row['SERIES_TRAILER'] = $trailer_data;
                } else {
                    $row['SERIES_TRAILER'] = array();
                }

                //SERIES BACKDROPS
                $this->db->where("backdrops_tmdb", $data['series_tmdb']);
                $this->db->order_by("backdrops_id","ASC");
                $data_backdrops = $this->db->get('tbl_series_backdrops')->result_array();

                if ($data_backdrops) {
                    foreach ($data_backdrops as $datas_backdrops){
                        $row_backdrops['backdrops_path'] = $datas_backdrops['backdrops_path'];
                        $trailer_backdrops[] = $row_backdrops;
                    }
                    $row['SERIES_BACKDROPS'] = $trailer_backdrops;
                } else {
                    $row['SERIES_BACKDROPS'] = array();
                }

                //SERIES POSTERS
                $this->db->where("posters_tmdb", $data['series_tmdb']);
                $this->db->order_by("posters_id","ASC");
                $data_posters = $this->db->get('tbl_series_posters')->result_array();
                if ($data_posters) {
                    foreach ($data_posters as $datas_posters){
                        $row_posters['posters_path'] = $datas_posters['posters_path'];
                        $trailer_posters[] = $row_posters;
                    }
                    $row['SERIES_POSTERS'] = $trailer_posters;
                } else {
                    $row['SERIES_POSTERS'] = array();
                }

                //SERIES CREDITS
                $this->db->where("credits_tmdb", $data['series_tmdb']);
                $this->db->order_by("credits_id","ASC");
                $this->db->limit(5);
                $data_credits = $this->db->get('tbl_series_credits')->result_array();
                if ($data_credits) {
                    foreach ($data_credits as $datas_credits){
                        $row_credits['credits_character'] = $datas_credits['credits_character'];
                        $row_credits['credits_name'] = $datas_credits['credits_name'];
                        $row_credits['credits_path'] = $datas_credits['credits_path'];
                        $trailer_credits[] = $row_credits;
                    }
                    $row['SERIES_CREDITS'] = $trailer_credits;
                } else {
                    $row['SERIES_CREDITS'] = array();
                }

                //RELATED SERIES
                $this->db->where("series_id", $data['series_id']);
                $this->db->order_by(20, 'RANDOM');
                $this->db->limit(20);
                $data_related = $this->db->get('tbl_series')->result_array();

                if ($data_related) {
                    foreach ($data_related as $datas_related){
                        $row_related['series_id'] = $datas_related['series_id'];
                        $row_related['series_tmdb'] = $datas_related['series_tmdb'];
                        $row_related['series_backdrop_path'] = $this->checkImageFound($datas_related['series_backdrop_path']);
                        $row_related['series_first_air_date'] = $datas_related['series_first_air_date'];
                        $row_related['series_genres'] = $datas_related['series_genres'];
                        $row_related['series_homepage'] = $datas_related['series_homepage'];
                        $row_related['series_in_production'] = $datas_related['series_in_production'];
                        $row_related['series_languages'] = $datas_related['series_languages'];
                        $row_related['series_number_of_episodes'] = $datas_related['series_number_of_episodes'];
                        $row_related['series_number_of_seasons'] = $datas_related['series_number_of_seasons'];
                        $row_related['series_origin_country'] = $datas_related['series_origin_country'];
                        $row_related['series_original_language'] = $datas_related['series_original_language'];
                        $row_related['series_original_name'] = $datas_related['series_original_name'];
                        $row_related['series_overview'] = $datas_related['series_overview'];
                        $row_related['series_poster_path'] = $this->checkImageFound($datas_related['series_poster_path']);
                        $row_related['series_status'] = $datas_related['series_status'];
                        $row_related['series_type'] = $datas_related['series_type'];
                        $row_related['series_vote_average'] = $datas_related['series_vote_average'];
                        $row_related['series_vote_count'] = $datas_related['series_vote_count'];

                        $related_data[] = $row_related;
                    }
                    $row['SERIES_RELATED'] = $related_data;
                } else {
                    $row['SERIES_RELATED'] = array();
                }

                //COMMENT
                $this->db->select('*');
                $this->db->from('tbl_series_comments');
                $this->db->join('tbl_users', 'tbl_series_comments.comment_user_id = tbl_users.user_id ');
                $this->db->where("comment_movies_id", $data['series_id']);
                $data_comments = $this->db->get()->result_array();

                if ($data_comments) {
                    foreach ($data_comments as $datas_comments){
                        $row_comment['comment_user_id'] = $datas_comments['comment_user_id'];
                        $row_comment['comment_message'] = $datas_comments['comment_message'];
                        $row_comment['comment_time'] = $datas_comments['comment_time'];
                        $row_comment['user_name'] = $datas_comments['user_name'];
                        $row_comment['user_image'] = $this->makeThumb($datas_comments['user_image'], 100, false);

                        $comment_data[] = $row_comment;
                    }
                    $row['SERIES_COMMENT'] = $comment_data;
                } else {

                    $row['SERIES_COMMENT'] = array();
                }

                array_push($jsonObj, $row);
            }

            $set['BENKKSTUDIO'] = $jsonObj;
        } else if ($method['method_name'] == "series_sesion") {
            $jsonObj = array();
            $this->db->where("sesion_tmdb", $method['sesion_tmdb']);
            $this->db->order_by("sesion_id", 'ASC');
            $data_sesion = $this->db->get('tbl_sesion')->result_array();
                foreach ($data_sesion as $data){
                    $row['sesion_id'] = $data['sesion_id'];
                    $row['sesion_tmdb'] = $data['sesion_tmdb'];
                    $row['sesion_season_number'] = $data['sesion_season_number'];
                    $row['sesion_episode_count'] = $data['sesion_episode_count'];
                    $row['sesion_poster_path'] = $this->checkImageFound($data['sesion_poster_path']);
                    $row['sesion_overview'] = $data['sesion_overview'];

                    array_push($jsonObj, $row);
                }
  

            $set['BENKKSTUDIO'] = $jsonObj;
        } else if ($method['method_name'] == "series_episode") {
            $jsonObj = array();
            $this->db->where("episode_sesion", $method['episode_sesion']);
            $this->db->where("episode_tmdb", $method['sesion_tmdb']);
            $this->db->order_by("episode_id", 'ASC');
            $data_episode = $this->db->get('tbl_episode')->result_array();
            foreach ($data_episode as $data){
                $row['episode_id'] = $data['episode_id'];
                $row['episode_tmdb'] = $data['episode_tmdb'];
                $row['episode_sesion'] = $data['episode_sesion'];
                $row['episode_title'] = $data['episode_title'];
                $row['episode_still_path'] = $this->checkImageFound($data['episode_still_path']);
                $row['episode_stream'] = $data['episode_stream'];
                $row['episode_vote_average'] = $data['episode_vote_average'];
                $row['episode_vote_count'] = $data['episode_vote_count'];

                array_push($jsonObj, $row);
            }

            $set['BENKKSTUDIO'] = $jsonObj;
        } else if ($method['method_name'] == "movies_stream") {
            $jsonObj = array();
            $this->db->where("video_tmdb", $method['video_tmdb']);
            $data_video = $this->db->get('tbl_video')->result_array();
            foreach ($data_video as $data){
                $row['video_id'] = $data['video_id'];
                $row['video_name'] = $data['video_name'];
                $row['video_tmdb'] = $data['video_tmdb'];
                $row['video_type'] = $data['video_type'];
                $row['video_url'] = $data['video_url'];

                array_push($jsonObj, $row);
            }

            $set['BENKKSTUDIO'] = $jsonObj;
        } else if ($method['method_name'] == "series_stream") {
            $jsonObj = array();
            $this->db->where("video_tmdb", $method['series_tmdb']);
            $this->db->where("video_sesion_id", $method['video_sesion_id']);
            $this->db->where("video_episode_Id", $method['video_episode_Id']);
            $data_video = $this->db->get('tbl_series_video')->result_array();
            foreach ($data_video as $data){
                $row['video_id'] = $data['video_id'];
                $row['video_name'] = $data['video_name'];
                $row['video_tmdb'] = $data['video_tmdb'];
                $row['video_type'] = $data['video_type'];
                $row['video_url'] = $data['video_url'];

                array_push($jsonObj, $row);
            }

            $set['BENKKSTUDIO'] = $jsonObj;
        } else if ($method['method_name'] == "home_movies") {
            $jsonObj_latest = array();
            $this->db->select('*');
            $this->db->from('tbl_movies');
            $this->db->join('tbl_category', 'tbl_movies.movies_cid = tbl_category.categories_id');
            $this->db->order_by("movies_id", 'DESC');
            $this->db->limit(20);
            $data_latest = $this->db->get()->result_array();
            foreach ($data_latest as $datas_latest){
                $row_latest['movies_id'] = $datas_latest['movies_id'];
                $row_latest['movies_cid'] = $datas_latest['movies_cid'];
                $row_latest['movies_stream'] = $datas_latest['movies_stream'];
                $row_latest['movies_tmdb'] = $datas_latest['movies_tmdb'];
                $row_latest['movies_genre'] = $datas_latest['movies_genre'];
                $row_latest['movies_type'] = $datas_latest['movies_type'];
                $row_latest['tmdb_backdrop_path'] = $this->checkImageFound($datas_latest['tmdb_backdrop_path']);
                $row_latest['tmdb_budget'] = $datas_latest["tmdb_budget"];
                $row_latest['tmdb_homepage'] = $datas_latest["tmdb_homepage"];
                $row_latest['tmdb_original_language'] = $datas_latest["tmdb_original_language"];
                $row_latest['tmdb_original_title'] = $datas_latest["tmdb_original_title"];
                $row_latest['tmdb_overview'] = $datas_latest["tmdb_overview"];
                $row_latest['tmdb_poster_path'] = $this->checkImageFound($datas_latest['tmdb_poster_path']);
                $row_latest['tmdb_release_date'] = $datas_latest["tmdb_release_date"];
                $row_latest['tmdb_revenue'] = $datas_latest["tmdb_revenue"];
                $row_latest['tmdb_runtime'] = $datas_latest["tmdb_runtime"];
                $row_latest['tmdb_vote_average'] = $datas_latest["tmdb_vote_average"];
                $row_latest['tmdb_vote_count'] = $datas_latest["tmdb_vote_count"];
                $row_latest['tmdb_production_countries'] = $datas_latest["tmdb_production_countries"];
                $row_latest['tmdb_spoken_languages'] = $datas_latest["tmdb_spoken_languages"];

                array_push($jsonObj_latest, $row_latest);
            }

            $row['latest_movies'] = $jsonObj_latest;

            $jsonObj_series = array();
            $this->db->limit(20);
            $this->db->order_by("series_id", 'DESC');
            $data_series = $this->db->get('tbl_series')->result_array();
            foreach ($data_series as $datas_series){
                $row_series['series_id'] = $datas_series['series_id'];
                $row_series['series_tmdb'] = $datas_series['series_tmdb'];
                $row_series['series_backdrop_path'] = $this->checkImageFound($datas_series['series_backdrop_path']);
                $row_series['series_first_air_date'] = $datas_series['series_first_air_date'];
                $row_series['series_genres'] = $datas_series['series_genres'];
                $row_series['series_homepage'] = $datas_series['series_homepage'];
                $row_series['series_in_production'] = $datas_series['series_in_production'];
                $row_series['series_languages'] = $datas_series['series_languages'];
                $row_series['series_number_of_episodes'] = $datas_series['series_number_of_episodes'];
                $row_series['series_number_of_seasons'] = $datas_series['series_number_of_seasons'];
                $row_series['series_origin_country'] = $datas_series['series_origin_country'];
                $row_series['series_original_language'] = $datas_series['series_original_language'];
                $row_series['series_original_name'] = $datas_series['series_original_name'];
                $row_series['series_overview'] = $datas_series['series_overview'];
                $row_series['series_poster_path'] = $this->checkImageFound($datas_series['series_poster_path']);
                $row_series['series_status'] = $datas_series['series_status'];
                $row_series['series_type'] = $datas_series['series_type'];
                $row_series['series_vote_average'] = $datas_series['series_vote_average'];
                $row_series['series_vote_count'] = $datas_series['series_vote_count'];

                array_push($jsonObj_series, $row_series);
            }
            $row['series_movies'] = $jsonObj_series;

            $jsonObj_popular = array();
            $this->db->select('*');
            $this->db->from('tbl_movies');
            $this->db->join('tbl_category', 'tbl_movies.movies_cid = tbl_category.categories_id');
            $this->db->order_by("tmdb_vote_count", 'DESC');
            $this->db->limit(20);
            $data_popular = $this->db->get()->result_array();
            foreach ($data_popular as $datas_popular){
                $row_popular['movies_id'] = $datas_popular['movies_id'];
                $row_popular['movies_cid'] = $datas_popular['movies_cid'];
                $row_popular['movies_stream'] = $datas_popular['movies_stream'];
                $row_popular['movies_tmdb'] = $datas_popular['movies_tmdb'];
                $row_popular['movies_genre'] = $datas_popular['movies_genre'];
                $row_popular['movies_type'] = $datas_popular['movies_type'];
                $row_popular['tmdb_backdrop_path'] = $this->checkImageFound($datas_popular['tmdb_backdrop_path']);
                $row_popular['tmdb_budget'] = $datas_popular["tmdb_budget"];
                $row_popular['tmdb_homepage'] = $datas_popular["tmdb_homepage"];
                $row_popular['tmdb_original_language'] = $datas_popular["tmdb_original_language"];
                $row_popular['tmdb_original_title'] = $datas_popular["tmdb_original_title"];
                $row_popular['tmdb_overview'] = $datas_popular["tmdb_overview"];
                $row_popular['tmdb_poster_path'] = $this->checkImageFound($datas_popular['tmdb_poster_path']);
                $row_popular['tmdb_release_date'] = $datas_popular["tmdb_release_date"];
                $row_popular['tmdb_revenue'] = $datas_popular["tmdb_revenue"];
                $row_popular['tmdb_runtime'] = $datas_popular["tmdb_runtime"];
                $row_popular['tmdb_vote_average'] = $datas_popular["tmdb_vote_average"];
                $row_popular['tmdb_vote_count'] = $datas_popular["tmdb_vote_count"];
                $row_popular['tmdb_production_countries'] = $datas_popular["tmdb_production_countries"];
                $row_popular['tmdb_spoken_languages'] = $datas_popular["tmdb_spoken_languages"];


                array_push($jsonObj_popular, $row_popular);
            }

            $row['popular_movies'] = $jsonObj_popular;


            $jsonObj_slider = array();
            $this->db->order_by('rand()');
            $this->db->limit(5);
            $data_slider = $this->db->get('tbl_movies')->result_array();
            foreach ($data_slider as $datas_slider){
                $row_slider['movies_id'] = $datas_slider['movies_id'];
                $row_slider['tmdb_backdrop_path'] = $this->checkImageFound($datas_slider["tmdb_backdrop_path"]);

                array_push($jsonObj_slider, $row_slider);
            }

            $row['slider_movies'] = $jsonObj_slider;

            $jsonObj_categories = array();
            $this->db->order_by('categories_id', 'DESC');
            $this->db->limit(20);
            $data_categories = $this->db->get('tbl_category')->result_array();
            foreach ($data_categories as $datas_categories){
                $row_categories['categories_id'] = $datas_categories['categories_id'];
                $row_categories['category_name'] = $datas_categories['category_name'];
                $row_categories['category_image'] = $this->makeThumb($datas_categories['category_image'], 250, false);

                array_push($jsonObj_categories, $row_categories);
            }

            $row['home_categories'] = $jsonObj_categories;

            $jsonObj_tv = array();
            $this->db->order_by('livetv_id', 'DESC');
            $this->db->limit(20);
            $data_tv = $this->db->get('tbl_livetv')->result_array();
            foreach ($data_tv as $datas_tv){
                $row_tv['livetv_name'] = $datas_tv['livetv_name'];
                $row_tv['livetv_url'] = $datas_tv['livetv_url'];
                $row_tv['livetv_image'] = $this->makeThumb($datas_tv['livetv_image'], 250, false);

                array_push($jsonObj_tv, $row_tv);
            }

            $row['home_tv'] = $jsonObj_tv;

            $jsonObj_genre = array();
            $this->db->order_by('genre_name', 'ASC');
            $data_genre = $this->db->get('tbl_genre')->result_array();
            foreach ($data_genre as $datas_genre){
                $this->db->like('movies_genre', $datas_genre['genre_name']);
                $check_movie = $this->db->get('tbl_movies')->row();
                if($check_movie){
                    $row_genre['genre_id'] = $datas_genre['genre_id'];
                    $row_genre['genre_name'] = $datas_genre['genre_name'];
                    array_push($jsonObj_genre, $row_genre);
                }
          
            }

            $row['genre_movies'] = $jsonObj_genre;

            $set['BENKKSTUDIO'] = $row;
        } else if ($method['method_name'] == "all_live") {
            $jsonObj = array();
            $this->db->order_by('livetv_id', 'DESC');
            $data_live = $this->db->get('tbl_livetv')->result_array();
            foreach ($data_live as $data){
                $row['livetv_name'] = $data['livetv_name'];
                $row['livetv_url'] = $data['livetv_url'];
                $row['livetv_image'] = $this->makeThumb($data['livetv_image'], 250, false);

                array_push($jsonObj, $row);
            }

            $set['BENKKSTUDIO'] = $jsonObj;
        } else if ($method['method_name'] == "detail_movies") {

            $jsonObj = array();
            $this->db->select('*');
            $this->db->from('tbl_movies');
            $this->db->join('tbl_category', 'tbl_movies.movies_cid = tbl_category.categories_id');
            $this->db->where('movies_id', $method['movies_id']);
            $this->db->order_by("movies_id", 'DESC');
            $data_movie = $this->db->get()->result_array();
            foreach ($data_movie as $data){
                $row['movies_id'] = $data['movies_id'];
                $row['movies_cid'] = $data['movies_cid'];
                $row['movies_stream'] = $data['movies_stream'];
                $row['movies_tmdb'] = $data['movies_tmdb'];
                $row['movies_genre'] = $data['movies_genre'];
                $row['movies_type'] = $data['movies_type'];
                $row['tmdb_backdrop_path'] = $this->checkImageFound($data['tmdb_backdrop_path']);
                $row['tmdb_budget'] = $data["tmdb_budget"];
                $row['tmdb_homepage'] = $data["tmdb_homepage"];
                $row['tmdb_original_language'] = $data["tmdb_original_language"];
                $row['tmdb_original_title'] = $data["tmdb_original_title"];
                $row['tmdb_overview'] = $data["tmdb_overview"];
                $row['tmdb_poster_path'] = $this->checkImageFound($data['tmdb_poster_path']);
                $row['tmdb_release_date'] = $data["tmdb_release_date"];
                $row['tmdb_revenue'] = $data["tmdb_revenue"];
                $row['tmdb_runtime'] = $data["tmdb_runtime"];
                $row['tmdb_vote_average'] = $data["tmdb_vote_average"];
                $row['tmdb_vote_count'] = $data["tmdb_vote_count"];
                $row['tmdb_production_countries'] = $data["tmdb_production_countries"];
                $row['tmdb_spoken_languages'] = $data["tmdb_spoken_languages"];

                //MOVIES TRAILER
                $this->db->where('trailer_tmdb', $data['movies_tmdb']);
                $this->db->order_by('trailer_id', 'ASC');
                $data_trailer = $this->db->get('tbl_trailer')->result_array();
                if($data_trailer){
                    foreach ($data_trailer as $datas_trailer){
                        $row_trailer['trailer_key'] = $datas_trailer['trailer_key'];
                        $row_trailer['trailer_name'] = $datas_trailer['trailer_name'];
                        $row_trailer['trailer_site'] = $datas_trailer['trailer_site'];
                        $row_trailer['trailer_type'] = $datas_trailer['trailer_type'];
                        $trailer_data[] = $row_trailer;
                    }
                    $row['DETAIL_TRAILER'] = $trailer_data;
                } else {
                    $row['DETAIL_TRAILER'] = array();
                }

                //MOVIES BACKDROPS
                $this->db->where('backdrops_tmdb', $data['movies_tmdb']);
                $this->db->order_by('backdrops_id', 'ASC');
                $data_backdrops = $this->db->get('tbl_backdrops')->result_array();
                if ($data_backdrops) {
                    foreach ($data_backdrops as $datas_backdrops){
                        $row_backdrops['backdrops_path'] = $datas_backdrops['backdrops_path'];
                        $trailer_backdrops[] = $row_backdrops;
                    }

                    $row['DETAIL_BACKDROPS'] = $trailer_backdrops;
                } else {
                    $row['DETAIL_BACKDROPS'] = array();
                }

                //MOVIES POSTERS
                $this->db->where('posters_tmdb', $data['movies_tmdb']);
                $this->db->order_by('posters_id', 'ASC');
                $data_posters = $this->db->get('tbl_posters')->result_array();
                if ($data_posters) {
                    foreach ($data_posters as $datas_posters){
                        $row_posters['posters_path'] = $datas_posters['posters_path'];
                        $trailer_posters[] = $row_posters;
                    }
                    $row['DETAIL_POSTERS'] = $trailer_posters;
                } else {
                    $row['DETAIL_POSTERS'] = array();
                }

                //MOVIES CREDITS
                $this->db->where('credits_tmdb', $data['movies_tmdb']);
                $this->db->order_by('credits_id', 'ASC');
                $this->db->limit(5);
                $data_credits = $this->db->get('tbl_credits')->result_array();
                if ($data_credits) {
                    foreach ($data_credits as $datas_credits){
                        $row_credits['credits_character'] = $datas_credits['credits_character'];
                        $row_credits['credits_name'] = $datas_credits['credits_name'];
                        $row_credits['credits_path'] = $datas_credits['credits_path'];
                        $trailer_credits[] = $row_credits;
                    }
                    $row['DETAIL_CREDITS'] = $trailer_credits;
                } else {
                    $row['DETAIL_CREDITS'] = array();
                }

                //RELATED MOVIES
                $this->db->select('*');
                $this->db->from('tbl_movies');
                $this->db->join('tbl_category', 'tbl_movies.movies_cid = tbl_category.categories_id');
                $this->db->where('movies_cid', $data['movies_cid']);
                $this->db->where('movies_id !=', $data['movies_id']);
                $this->db->order_by("movies_id", 'DESC');
                $this->db->limit(20);
                $data_related = $this->db->get()->result_array();
                if ($data_related) {
                    foreach ($data_related as $datas_related){
                        $row_related['movies_id'] = $datas_related['movies_id'];
                        $row_related['movies_cid'] = $datas_related['movies_cid'];
                        $row_related['movies_stream'] = $datas_related['movies_stream'];
                        $row_related['movies_tmdb'] = $datas_related['movies_tmdb'];
                        $row_related['movies_genre'] = $datas_related['movies_genre'];
                        $row_related['movies_type'] = $datas_related['movies_type'];
                        $row_related['tmdb_backdrop_path'] = $this->checkImageFound($datas_related['tmdb_backdrop_path']);
                        $row_related['tmdb_budget'] = $datas_related["tmdb_budget"];
                        $row_related['tmdb_homepage'] = $datas_related["tmdb_homepage"];
                        $row_related['tmdb_original_language'] = $datas_related["tmdb_original_language"];
                        $row_related['tmdb_original_title'] = $datas_related["tmdb_original_title"];
                        $row_related['tmdb_overview'] = $datas_related["tmdb_overview"];
                        $row_related['tmdb_poster_path'] = $this->checkImageFound($datas_related['tmdb_poster_path']);
                        $row_related['tmdb_release_date'] = $datas_related["tmdb_release_date"];
                        $row_related['tmdb_revenue'] = $datas_related["tmdb_revenue"];
                        $row_related['tmdb_runtime'] = $datas_related["tmdb_runtime"];
                        $row_related['tmdb_vote_average'] = $datas_related["tmdb_vote_average"];
                        $row_related['tmdb_vote_count'] = $datas_related["tmdb_vote_count"];
                        $row_related['tmdb_production_countries'] = $datas_related["tmdb_production_countries"];
                        $row_related['tmdb_spoken_languages'] = $datas_related["tmdb_spoken_languages"];

                        $related_data[] = $row_related;
                    }
                    $row['DETAIL_RELATED'] = $related_data;
                } else {
                    $row['DETAIL_RELATED'] = array();
                }

                //COMMENT
                $this->db->select('*');
                $this->db->from('tbl_comments');
                $this->db->join('tbl_users', 'tbl_comments.comment_user_id = tbl_users.user_id');
                $this->db->where('comment_movies_id', $data['movies_id']);
                $data_comments = $this->db->get()->result_array();
                if ($data_comments) {
                    foreach ($data_comments as $datas_comments){
                        $row_comment['comment_user_id'] = $datas_comments['comment_user_id'];
                        $row_comment['comment_message'] = $datas_comments['comment_message'];
                        $row_comment['comment_time'] = $datas_comments['comment_time'];
                        $row_comment['user_name'] = $datas_comments['user_name'];
                        $row_comment['user_image'] = $this->makeThumb($datas_comments['user_image'], 250, false);

                        $comment_data[] = $row_comment;
                    }
                    $row['DETAIL_COMMENT'] = $comment_data;
                } else {

                    $row['DETAIL_COMMENT'] = array();
                }
                array_push($jsonObj, $row);
            }

            $set['BENKKSTUDIO'] = $jsonObj;
        } else if ($method['method_name'] == "movies_by_genre") {
            $jsonObj = array();
            $this->db->select('*');
            $this->db->from('tbl_movies');
            $this->db->join('tbl_category', 'tbl_movies.movies_cid = tbl_category.categories_id');
            $this->db->like('movies_genre', $method['movies_genre']);
            $this->db->order_by('movies_id', 'DESC');
            $data_movie = $this->db->get()->result_array();
            foreach ($data_movie as $data){
                $row['movies_id'] = $data['movies_id'];
                $row['movies_cid'] = $data['movies_cid'];
                $row['movies_stream'] = $data['movies_stream'];
                $row['movies_tmdb'] = $data['movies_tmdb'];
                $row['movies_genre'] = $data['movies_genre'];
                $row['movies_type'] = $data['movies_type'];
                $row['tmdb_backdrop_path'] = $this->checkImageFound($data['tmdb_backdrop_path']);
                $row['tmdb_budget'] = $data["tmdb_budget"];
                $row['tmdb_homepage'] = $data["tmdb_homepage"];
                $row['tmdb_original_language'] = $data["tmdb_original_language"];
                $row['tmdb_original_title'] = $data["tmdb_original_title"];
                $row['tmdb_overview'] = $data["tmdb_overview"];
                $row['tmdb_poster_path'] = $this->checkImageFound($data['tmdb_poster_path']);
                $row['tmdb_release_date'] = $data["tmdb_release_date"];
                $row['tmdb_revenue'] = $data["tmdb_revenue"];
                $row['tmdb_runtime'] = $data["tmdb_runtime"];
                $row['tmdb_vote_average'] = $data["tmdb_vote_average"];
                $row['tmdb_vote_count'] = $data["tmdb_vote_count"];
                $row['tmdb_production_countries'] = $data["tmdb_production_countries"];
                $row['tmdb_spoken_languages'] = $data["tmdb_spoken_languages"];

                array_push($jsonObj, $row);
            }

            $set['BENKKSTUDIO'] = $jsonObj;
        } else if ($method['method_name'] == "movies_all") {
            $MOVIES_IDENTIFIER = $method['movies_identifier'];
            $jsonObj = array();

            $this->db->select('*');
            $this->db->from('tbl_movies');
            $this->db->join('tbl_category', 'tbl_movies.movies_cid = tbl_category.categories_id');
            if ($MOVIES_IDENTIFIER == 'Latest Movie') {
                $this->db->order_by('movies_id', 'DESC');
            } else if ($MOVIES_IDENTIFIER == 'Popular Movie') {
                $this->db->order_by('movies_id', 'DESC');
            } else if ($MOVIES_IDENTIFIER == 'Featured Movie') {
                $this->db->order_by('rand()');
            } else if ($MOVIES_IDENTIFIER == 'Category') {
                $this->db->where('movies_cid', $method['movies_cid']);
                $this->db->order_by('movies_id', 'DESC');
            } else if ($MOVIES_IDENTIFIER == 'ALL') {
                $this->db->order_by('movies_id', 'DESC');
            }
            $data_movie = $this->db->get()->result_array();
            foreach ($data_movie as $data){
                $row['movies_id'] = $data['movies_id'];
                $row['movies_cid'] = $data['movies_cid'];
                $row['movies_stream'] = $data['movies_stream'];
                $row['movies_tmdb'] = $data['movies_tmdb'];
                $row['movies_genre'] = $data['movies_genre'];
                $row['movies_type'] = $data['movies_type'];
                $row['tmdb_backdrop_path'] = $this->checkImageFound($data['tmdb_backdrop_path']);
                $row['tmdb_budget'] = $data["tmdb_budget"];
                $row['tmdb_homepage'] = $data["tmdb_homepage"];
                $row['tmdb_original_language'] = $data["tmdb_original_language"];
                $row['tmdb_original_title'] = $data["tmdb_original_title"];
                $row['tmdb_overview'] = $data["tmdb_overview"];
                $row['tmdb_poster_path'] = $this->checkImageFound($data['tmdb_poster_path']);
                $row['tmdb_release_date'] = $data["tmdb_release_date"];
                $row['tmdb_revenue'] = $data["tmdb_revenue"];
                $row['tmdb_runtime'] = $data["tmdb_runtime"];
                $row['tmdb_vote_average'] = $data["tmdb_vote_average"];
                $row['tmdb_vote_count'] = $data["tmdb_vote_count"];
                $row['tmdb_production_countries'] = $data["tmdb_production_countries"];
                $row['tmdb_spoken_languages'] = $data["tmdb_spoken_languages"];

                array_push($jsonObj, $row);
            }

            $set['BENKKSTUDIO'] = $jsonObj;
        } else if ($method['method_name'] == "all_categories") {
            $jsonObj = array();
            $this->db->order_by('categories_id', 'DESC');
            $data_categories = $this->db->get('tbl_category')->result_array();
            foreach ($data_categories as $data){
                $row['categories_id'] = $data['categories_id'];
                $row['category_name'] = $data['category_name'];
                $row['category_image'] = $this->makeThumb($data['category_image'], 250, false);

                array_push($jsonObj, $row);
            }

            $set['BENKKSTUDIO'] = $jsonObj;
        } else if ($method['method_name'] == "all_tv") {
            $jsonObj = array();
            $this->db->order_by('livetv_id', 'DESC');
            $data_tv = $this->db->get('tbl_livetv')->result_array();
            foreach($data_tv as $data){
                $row['livetv_name'] = $data['livetv_name'];
                $row['livetv_url'] = $data['livetv_url'];
                $row['livetv_image'] = $this->makeThumb($data['livetv_image'], 250, false);
        
                array_push($jsonObj, $row);
            }

            $set['BENKKSTUDIO'] = $jsonObj;
        } else if ($method['method_name'] == "user_profile") {
            $jsonObj = array();
            $this->db->where('user_id', $method['user_id']);
            $data_user = $this->db->get('tbl_users')->result_array();
            foreach($data_user as $data){
                $row['user_name'] = $data['user_name'];
                $row['user_email'] = $data['user_email'];
                $row['user_password'] = $data['user_password'];
                $row['user_image'] = $this->makeThumb($data['user_image'], 250, false);
        
                array_push($jsonObj, $row);
            }
            $set['BENKKSTUDIO'] = $jsonObj;
        } else if ($method['method_name'] == "send_comment") {
            $data = array(
                'comment_user_id' => $method['comment_user_id'],
                'comment_message' =>  $method['comment_message'],
                'comment_movies_id' => $method['comment_movies_id'],
                'comment_time' => $method['comment_time']
            );
            $this->db->insert('tbl_comments', $data);
            $set['BENKKSTUDIO'][] = array('msg' => "Comment Send!", 'success' => '1');
        } else if ($method['method_name'] == "send_series_comment") {
            $data = array(
                'comment_user_id' => $method['comment_user_id'],
                'comment_message' =>  $method['comment_message'],
                'comment_movies_id' => $method['comment_movies_id'],
                'comment_time' => $method['comment_time']
            );
            $this->db->insert('tbl_series_comments', $data);
            $set['BENKKSTUDIO'][] = array('msg' => "Comment Send!", 'success' => '1');
        } else if ($method['method_name'] == "user_register") {
            $check_email = $this->db->get_where('tbl_users', ["user_email" => $method['user_email']])->row();
            if($check_email){
                $set['BENKKSTUDIO'][] = array('msg' => "Email address already used!", 'success' => '0');
            } else {
                $data = array(
                    'user_id' => $method['user_id'],
                    'user_name' =>  $method['user_name'],
                    'user_email' => $method['user_email'],
                    'user_password' => $method['user_password'],
                    'user_image' => $method['user_image'],
                );
                $this->db->insert('tbl_users', $data);
                $set['BENKKSTUDIO'][] = array('msg' => "Register successfully...!", 'success' => '1');
            }
        } else if ($method['method_name'] == "user_login") {
            $this->db->where('user_email', $method['user_email']);
            $this->db->where('user_password', $method['user_password']);
            $check_login = $this->db->get('tbl_users')->row();
            if($check_login){
                $set['BENKKSTUDIO'][] = array('user_id' => $check_login->user_id, 'msg' => $check_login,'success' => '1');
            } else {
                $set['BENKKSTUDIO'][] = array('msg' => 'Please check username and password.', 'success' => '0');
            }
        } else if ($method['method_name'] == "upload_profile_image") {
            $temp_file = $_FILES['file']['tmp_name'];
            $wallpaper_image = $method['user_id'].'.jpg';
            $folder = 'upload/image/';
            $target_folder = $folder.$wallpaper_image;
            $success = move_uploaded_file($temp_file, $target_folder);
            if($success){
                $data = array(
                    'user_image' =>  $wallpaper_image
                );
                $this->db->update('tbl_users', $data, array('user_id' => $method['user_id']));
                $set['BENKKSTUDIO'][] = array('image' => $this->makeThumb($wallpaper_image, 250, true), 'msg' => 'Image Changed' ,'success' => '1');
            } else {
                $set['BENKKSTUDIO'][] = array('msg' => 'Server not respond, please try again later' ,'success' => '0');
            }
        } else if ($method['method_name'] == "edit_profile") {
            if($method['user_password'] != ''){
                $data = array(
                    'user_id' => $method['user_id'],
                    'user_name' =>  $method['user_name'],
                    'user_email' => $method['user_email'],
                    'user_password' => $method['user_password']
                );
            } else {
                $data = array(
                    'user_id' => $method['user_id'],
                    'user_name' =>  $method['user_name'],
                    'user_email' => $method['user_email']
                );
            }
            $this->db->update('tbl_users', $data, array('user_id' => $method['user_id']));
            $set['BENKKSTUDIO'][] = array();
        } else if ($method['method_name'] == "settings") {
            $jsonObj = array();
            $data_setting = $this->db->get('tbl_settings')->result_array();
            foreach($data_setting as $data){
                $row[$data['identifier']] = $data['value'];
            }
            array_push($jsonObj, $row);
            $set['BENKKSTUDIO'] = $jsonObj;
        } else if ($method['method_name'] == "report") {
            $data = array(
                'report_type' => $method['report_type'],
                'report_movie_name' =>  $method['report_movie_name'],
                'report_date' => $method['report_date'],
                'report_name' => $method['report_name'],
                'report_description' => $method['report_description']
            );
            $this->db->insert('tbl_report', $data);
            $set['BENKKSTUDIO'][] = array('msg' => "Thanks for your report !", 'success' => '1');
        }
        header('Content-Type: application/json; charset=utf-8');
        echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        die();
        echo json_encode($set);

    }

    public function checkImageFound($image){
        if (file_exists('upload/image/' .$image)) {
            return $this->makeThumb($image, 500, false);
        } else {
            return 'https://image.tmdb.org/t/p/w500'.$image;
        }
    }

    public function makeThumb($path, $widht, $replace){
        
        $check_image = 'upload/image/thumbs/'.$path;
        if($replace){
            $source_image  = "upload/image/".$path;
            $config['image_library'] = 'gd2';
            $config['source_image'] = $source_image;
            $config['create_thumb'] = FALSE;
            $config['maintain_ratio'] = TRUE;
            $config['master_dim'] = 'width';
            $config['width'] = $widht;
            $config['height'] = '1';
            $config['new_image'] = 'upload/image/thumbs/'.$path;
            $this->image_lib->clear();
            $this->image_lib->initialize($config);
            $this->image_lib->resize();
            return site_url($config['new_image']); 
        } else {
            if (!file_exists($check_image))
            {
                $source_image  = "upload/image/".$path;
                $config['image_library'] = 'gd2';
                $config['source_image'] = $source_image;
                $config['create_thumb'] = FALSE;
                $config['maintain_ratio'] = TRUE;
                $config['master_dim'] = 'width';
                $config['width'] = $widht;
                $config['height'] = '1';
                $config['new_image'] = 'upload/image/thumbs/'.$path;
                $this->image_lib->clear();
                $this->image_lib->initialize($config);
                $this->image_lib->resize();
                return site_url($config['new_image']);
            } else {
                return site_url($check_image);
            }
        }

    }

}
