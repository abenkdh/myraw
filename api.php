<?php

class Api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model("model_image");
    }

    public function index()
    {
        $post = $this->input->get('data');
        $method =  json_decode(urldecode(base64_decode($post)),true);
        if($method['method_name'] == 'wallpaper_by_cat'){
            $jsonObj = array();
            $page_limit = $method['limit'];
            $page = $method['page'];
            $cat_id = $method['cat_id'];
            $limit = ($page - 1) * $page_limit;
            $total_pages = $this->db->get('tbl_wallpaper')->num_rows();
            $this->db->where('cat_id', $cat_id);
            $this->db->order_by('id', 'DESC');
            $this->db->limit($page_limit,$limit);
            $data_wallpaper = $this->db->get('tbl_wallpaper')->result_array();
            foreach($data_wallpaper as $data){
                $row['post_total'] = $total_pages;
                $row['wall_id'] = $data['id'];
                $row['cat_id'] = $data['cat_id'];
                $row['wallpaper_title'] = $data['wallpaper_image'];
                $this->model_image->get_thumb($data['wallpaper_image'], '<250');
                $row['wallpaper_image_thumb'] = $this->model_image->get_thumb($data['wallpaper_image'], "<250");
                $row['wallpaper_image_detail'] = $this->model_image->get_thumb($data['wallpaper_image'], "<800");
                $row['wallpaper_image_original'] = site_url('upload/image/'.$data['wallpaper_image']);
                $row['wallpaper_tags'] = $data['wallpaper_tags'];
                $row['wallpaper_type'] = $data['wallpaper_type'];
                $row['wallpaper_premium'] = $data['wallpaper_premium'];
                array_push($jsonObj, $row);
            }
            $set['BENKKSTUDIO'] = $jsonObj;
        } else  if($method['method_name'] == 'wallpaper_all') {
            $jsonObj = array();
            $page_limit = $method['limit'];
            $page = $method['page'];
            $filter = $method['filter'];
            $limit = ($page - 1) * $page_limit;
            $total_pages = $this->db->get('tbl_wallpaper')->num_rows();
            if($filter == "FILTER_LATEST"){
                $this->db->order_by('id', 'DESC');
                $this->db->limit($page_limit,$limit);
            } else if($filter == "FILTER_POPULAR"){
                $this->db->order_by('wallpaper_views', 'DESC');
                $this->db->limit($page_limit,$limit);
            } else if($filter == "FILTER_IMAGE"){
                $this->db->where('wallpaper_type', 'normal');
                $this->db->order_by('wallpaper_views', 'DESC');
                $this->db->limit($page_limit,$limit);
            } else if($filter == "FILTER_GIF"){
                $this->db->where('wallpaper_type', 'gif');
                $this->db->order_by('wallpaper_views', 'DESC');
                $this->db->limit($page_limit,$limit);
            } else if($filter == "FILTER_PREMIUM"){
                $this->db->where('wallpaper_premium', 'premium');
                $this->db->order_by('wallpaper_views', 'DESC');
                $this->db->limit($page_limit,$limit);
            } else if($filter == "FILTER_FREE"){
                $this->db->where('wallpaper_premium', 'free');
                $this->db->order_by('wallpaper_views', 'DESC');
                $this->db->limit($page_limit,$limit);
            }
            $data_wallpaper = $this->db->get('tbl_wallpaper')->result_array();
            foreach($data_wallpaper as $data){
                $row['post_total'] = $total_pages;
                $row['wall_id'] = $data['id'];
                $row['cat_id'] = $data['cat_id'];
                $row['wallpaper_title'] = $data['wallpaper_image'];
                $this->model_image->get_thumb($data['wallpaper_image'], '<250');
                $row['wallpaper_image_thumb'] = $this->model_image->get_thumb($data['wallpaper_image'], "<250");
                $row['wallpaper_image_detail'] = $this->model_image->get_thumb($data['wallpaper_image'], "<800");
                $row['wallpaper_image_original'] = site_url('upload/image/'.$data['wallpaper_image']);
                $row['wallpaper_tags'] = $data['wallpaper_tags'];
                $row['wallpaper_type'] = $data['wallpaper_type'];
                $row['wallpaper_premium'] = $data['wallpaper_premium'];
                array_push($jsonObj, $row);
            }
            $set['BENKKSTUDIO'] = $jsonObj;
        } else if($method['method_name'] == 'wallpaper_gif'){
            $jsonObj = array();
            $page_limit = $method['limit'];
            $page = $method['page'];
            $limit = ($page - 1) * $page_limit;
            $total_pages = $this->db->get('tbl_wallpaper')->num_rows();
            $this->db->where('wallpaper_type', 'gif');
            $this->db->order_by('wallpaper_views', 'DESC');
            $this->db->limit($page_limit,$limit);
            $data_wallpaper = $this->db->get('tbl_wallpaper')->result_array();
            foreach($data_wallpaper as $data){
                $row['post_total'] = $total_pages;
                $row['wall_id'] = $data['id'];
                $row['cat_id'] = $data['cat_id'];
                $row['wallpaper_title'] = $data['wallpaper_image'];
                $this->model_image->get_thumb($data['wallpaper_image'], '<250');
                $row['wallpaper_image_thumb'] = $this->model_image->get_thumb($data['wallpaper_image'], "<250");
                $row['wallpaper_image_detail'] = $this->model_image->get_thumb($data['wallpaper_image'], "<800");
                $row['wallpaper_image_original'] = site_url('upload/image/'.$data['wallpaper_image']);
                $row['wallpaper_tags'] = $data['wallpaper_tags'];
                $row['wallpaper_type'] = $data['wallpaper_type'];
                $row['wallpaper_premium'] = $data['wallpaper_premium'];
                array_push($jsonObj, $row);
            }
            $set['BENKKSTUDIO'] = $jsonObj;
        } else if($method['method_name'] == 'wallpaper_search'){
            $jsonObj = array();
            $page_limit = $method['limit'];
            $page = $method['page'];
            $limit = ($page - 1) * $page_limit;
            $total_pages = $this->db->get('tbl_wallpaper')->num_rows();
            $this->db->order_by('rand()');
            $this->db->limit($page_limit,$limit);
            $data_wallpaper = $this->db->get('tbl_wallpaper')->result_array();
            foreach($data_wallpaper as $data){
                $row['post_total'] = $total_pages;
                $row['wall_id'] = $data['id'];
                $row['cat_id'] = $data['cat_id'];
                $row['wallpaper_title'] = $data['wallpaper_image'];
                $this->model_image->get_thumb($data['wallpaper_image'], '<250');
                $row['wallpaper_image_thumb'] = $this->model_image->get_thumb($data['wallpaper_image'], "<250");
                $row['wallpaper_image_detail'] = $this->model_image->get_thumb($data['wallpaper_image'], "<800");
                $row['wallpaper_image_original'] = site_url('upload/image/'.$data['wallpaper_image']);
                $row['wallpaper_tags'] = $data['wallpaper_tags'];
                $row['wallpaper_type'] = $data['wallpaper_type'];
                $row['wallpaper_premium'] = $data['wallpaper_premium'];
                array_push($jsonObj, $row);
            }
            $set['BENKKSTUDIO'] = $jsonObj;
        } else if($method['method_name'] == 'category_all'){
            $jsonObj = array();
            $page_limit = $method['limit'];
            $page = $method['page'];
            $limit = ($page - 1) * $page_limit;
            $total_pages = $this->db->get('tbl_category')->num_rows();
            $this->db->order_by('cid', 'DESC');
            $this->db->limit($page_limit,$limit);
            $data_category = $this->db->get('tbl_category')->result_array();
            foreach($data_category as $data){
                $total_wallpaper = $this->db->get_where('tbl_wallpaper',['cat_id' => $data['cid']])->num_rows();
                $row['post_total'] = $total_pages;
                $row['id'] = $data['cid'];
                $row['category_name'] = $data['category_name'];
                $row['category_image_thumb'] = $this->model_image->get_thumb($data['category_image'], "<500");
                $row['category_image_original'] = site_url('upload/image/'.$data['category_image']);
                $row['total_wallpaper'] = $total_wallpaper;
                array_push($jsonObj, $row);
            }
            $set['BENKKSTUDIO'] = $jsonObj;
        } else if ($method['method_name'] == "search_suggestion") {
            $jsonObj = array();
            $this->db->order_by('suggestion', 'ASC');
            $data_suggestion = $this->db->get('tbl_suggestion')->result_array();
            foreach($data_suggestion as $data){
                $row['suggestion'] = $data['suggestion'];
                array_push($jsonObj, $row);
            }
            $set['BENKKSTUDIO'] = $jsonObj;
        } else if ($method['method_name'] == "settings") {
            $jsonObj = array();
            $data_setting = $this->db->get('tbl_settings')->result_array();
            foreach($data_setting as $data){
                $row[$data['identifier']] = $data['value'];
            }
            array_push($jsonObj, $row);
            $set['BENKKSTUDIO'] = $jsonObj;
        } else if ($method['method_name'] == "send_view") {
            $wallpaper = $this->db->get_where('tbl_wallpaper', ['id' => $method['id']])->row();
            $data_array['wallpaper_views'] = $wallpaper->wallpaper_views + 1;
            $view_query = $this->db->update('tbl_wallpaper', $data_array, ['id' => $method['id']]);
            if ($view_query) {
                $set['BENKKSTUDIO'][] = array('msg' => "Thanks for like...!", 'success' => '1');
            }
        }
        header('Content-Type: application/json; charset=utf-8');
        echo $val = str_replace('\\/', '/', json_encode($set, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        die();
        echo json_encode($set);
    }

}
