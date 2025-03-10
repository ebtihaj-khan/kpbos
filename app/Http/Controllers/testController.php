<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Hash;
use Session;
use Illuminate\Support\Facades\Input;
use Illuminate\Pagination\LengthAwarePaginator;
class testController extends Controller
{
    public function index(){
       $categories=DB::Table('categories')->get();
       $yearlypublications=DB::Table('publication_uploads')->where('file_cover_pic','<>','')->where('type','yearly')->take(4)->get();
        $monthlypublications=DB::Table('publication_uploads')->where('type','Monthly')->take(4)->get();
        $about_us=DB::Table('about_us')->get();

       return view('welcome',array('categories'=>$categories,'yearly_publications'=>$yearlypublications,'monthly_publications'=>$monthlypublications,'about_us'=>$about_us));
    }

    public function getsubcategories($id){
        $subcategories= DB::Table('sub_categories')
            ->select('sub_categories.*','categories.name as cat_name','categories.description as description')
            ->leftjoin('categories','categories.id','=','sub_categories.category_id')
            ->where('category_id',$id)
            ->get();
        if(count($subcategories)==0){
            $subcategories= DB::Table('categories')
                 ->select('categories.name as khan','categories.description as description')
                ->where('id',$id)
                ->get();
        }
        return $subcategories;
    }
    public function allpublications(){
        $allpublications= DB::Table('publication_uploads')->paginate(8);
        $years = ['2010', '2011', '2012', '2013', '2014', '2015', '2016', '2017', '2018', '2019', '2020', '2021', '2022', '2023', '2024', '2025', '2026', '2027', '2028', '2029', '2030', '2031', '2032', '2033', '2034', '2035', '2036', '2037', '2038', '2039', '2040', '2041', '2042', '2043', '2044', '2045', '2046', '2047', '2048', '2049', '2050'];
        $selectedYear="";
        return view('partials.publications',[
            'allpublications' => $allpublications,
            'years' => $years,
            'selectedYear' => $selectedYear
        ]);
    }

    public function publicationbydate($year){
        $allpublications= DB::Table('publication_uploads')->where('period',$year)->paginate(8);

        return view('partials.publications',array('allpublications'=>$allpublications,'year'=>$year));
    }
    public function allpublication($id){
        $allpublications= DB::Table('publication_uploads')->where('publication_id',$id)->paginate(8);
        $years = ['2010', '2011', '2012', '2013', '2014', '2015', '2016', '2017', '2018', '2019', '2020', '2021', '2022', '2023', '2024', '2025', '2026', '2027', '2028', '2029', '2030', '2031', '2032', '2033', '2034', '2035', '2036', '2037', '2038', '2039', '2040', '2041', '2042', '2043', '2044', '2045', '2046', '2047', '2048', '2049', '2050'];
         $publications=DB::Table('publications')->where('id',$id)->get();
         if(count($publications)>0){
             $name=$publications[0]->name;
         }
        $selectedYear="";
        return view('partials.publications',[
            'allpublications'=>$allpublications,
            'id'=>$id,
            'name'=>$name,
            'years' => $years,
            'selectedYear'=>$selectedYear
            ]);
    }
    public function publicationbydat($year,$id){
        $allpublications= DB::Table('publication_uploads')->where('period',$year)->where('publication_id',$id)->paginate(5);

        $publications=DB::Table('publications')->where('id',$id)->get();
        if(count($publications)>0){
            $name=$publications[0]->name;
        }
        return view('partials.publications',array('allpublications'=>$allpublications,'id'=>$id,'name'=>$name,'year'=>$year));
    }

    public function main_menu($id){
        $menu= DB::Table('menus')->where('id',$id)->get();

        if($menu[0]->name=='Home'){
            return redirect('/');
        }

        else{
            if($menu[0]->page_content!=''){
              $page_content=$menu[0]->page_content;


              return view('partials.dynamic',array('page_content'=>$page_content));
            }
            else{
                return  view('partials.page_not_found');
            }
        }
    }
    public function sub_menu($id){
        $sub_menu= DB::Table('sub_menus')->where('id',$id)->get();
        if($sub_menu[0]->page_content!=''){
            $page_content=$sub_menu[0]->page_content;
            return view('partials.dynamic',array('page_content'=>$page_content));
        }
        else{
            return back();
        }
    }

    public function allnews(){
      $news=  DB::Table('news_and_events')->orderBY('id','DESC')->get();
      return view('partials.news',array('news'=>$news));
    }
    public function singlenews($id){
        $news= DB::Table('news_and_events')->where('id',$id)->get();
        if($news[0]->page_content!=''){
            $page_content=$news[0]->page_content;
            return view('partials.single_news',array('page_content'=>$page_content,'news'=>$news));
        }
        else{
            return back();
        }
    }
    public function sub_sectors($id){
       $data=DB::Table('sub_categories')->where('id',$id)->get();
        echo "<pre>";
        print_r($data);
        exit();
    }
    public function admin_login(Request $request){
        $user=DB::Table('users')->where('email',$request['email'])->get();
        if(count($user)>0){
            if (Hash::check($request['password'], $user[0]->password))
            {
                $request->session()->put('admin_email',$request['email']);
                return redirect('admindashboard');
            }
            else{
                Session::flash('msg','The Email Or Password is Wrong');
                return back();
            }
        }
        else{
            Session::flash('msg','The Email Or Password is Wrong');
            return back();
        }


    }

    public function department($id,Request $request){
        $subcategory=DB::Table('sub_categories')->where('id',$id)->get();
        if(count($subcategory)>0){
            $keyword = preg_replace('/\s+/', '_', $subcategory[0]->name);


            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => env('CKAN_URL') . "api/3/action/package_search?q=".$keyword."&rows=200",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "Cache-Control: no-cache",
                    "Postman-Token: 17c52f14-c925-4d38-a339-34d02ac9e239"
                ),
            ));

            $response = curl_exec($curl);
            $response_data_pg = json_decode($response);
            $page = Input::get('page', 1);
        $perPage = 10;
        $offset = ($page * $perPage) - $perPage;
        $response_data = new LengthAwarePaginator(array_slice($response_data_pg->result->results, $offset, $perPage, true),
            count($response_data_pg->result->results), // Total items
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        
        return view('partials.specific_data',array('response_data'=>$response_data,'sub_category'=>$subcategory[0]->name,'filename'=>$subcategory[0]->file_name,'total_datasets'=>$response_data_pg));
        }
    }

    public function images(){
        $images=DB::Table('media')
            ->where('file_type','image')
            ->groupBy('event_name')
            ->get();
       return view('partials.images',array('images'=>$images));
    }

    public function all_images($id){
       $images=DB::Table('media')->where('id',$id)->get();
       $event_name=$images[0]->event_name;
       $images= DB::table('media')->where('file_type','image')->where('event_name',$event_name)->get();
       return view('partials.all_images',array('images'=>$images));
    }

    public function videos(){
        $videos=DB::Table('media')
            ->where('file_type','video')
            ->groupBy('event_name')
            ->get();
        return view('partials.videos',array('videos'=>$videos));
    }

    public function all_videos(){
        $videos=DB::Table('media')->where('id',1)->get();
        $event_name=$videos[0]->event_name;
        $videos= DB::table('media')->where('file_type','video')->where('event_name',$event_name)->get();
        return view('partials.all_videos',array('videos'=>$videos));
    }

    public function about_kpbos(){
        $about_us= DB::Table('about_us')->take(1)->orderBY('id','DESC')->get();
        $about_us_section=DB::Table('about_us_sections')->get();
        return view('partials.about_kpbos',array('about_us'=>$about_us,'about_us_sections'=>$about_us_section));
    }

    public function hierarchy(){
        $members=DB::Table('members')->get();
        return view('partials.members',array('members'=>$members));
    }
    public function contact_us(){
        return view('partials.contact_us');
    }
    public function all_achievements(){
       $achievements= DB::Table('achievements')->get();
        return view('partials.achievements',array('achievements'=>$achievements));
    }

}

