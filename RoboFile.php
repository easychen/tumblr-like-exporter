<?php
/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{
    function __construct()
    {
        $this->images = [];
        $this->count = 0;
        $this->max = 100000;
        $this->duration = 0.5 * 1000000 ; // 1000000
    }
    
    
    // define public methods as commands
    public function export( $with_video = false )
    {
        $this->with_video = $with_video;
        
        if( !file_exists('cookie.txt') )
        {
            $this->say("Config cookie first , see https://github.com/easychen/tumblr-like-exporter/ for more info ");
            return false;
        }
        
        $ask = true;

        if( file_exists( 'next.txt' ) )
        {
           $last = file_get_contents("next.txt");
           if( "Y" == $this->askDefault( "found $last , continue ? " , "Y"  ) )
           {
               $first_page_url = $last; 
               $ask = false;
           }
        }

        if( $ask )
        {
            $name = strtolower($this->ask("Please enter tumblr username( which show in url like https://www.tumblr.com/blog/username ) who's like to download , for yourslef's like just press enter :"));

            if( strlen( $name ) < 1 )
            {
                $first_page_url = 'https://www.tumblr.com/likes';
            }
            else
            $first_page_url = 'https://www.tumblr.com/liked/by/' . $name;

        }
        
        $this->parse_url( $first_page_url );
        
        $this->image_save();
        $this->say("image url saved... 🖼 ");
    }

    private function image_save()
    {
        $to_save = $this->images = array_unique( $this->images );
        $images = @json_decode( file_get_contents( "images.json" ) , true );
        if( is_array( $images ) )
        {
            $to_save = array_merge( $images , $to_save );
            $to_save = array_unique( $to_save );
        } 
        file_put_contents( "images.json" , json_encode( $to_save ) );
    }

    private function parse_url( $url )
    {
        $this->count++;
        if( $this->count <= $this->max )
        {
            $this->say("download and parse page $url 🔍 ");
            usleep( $this->duration );
            $this->page_content = get_page( $url ) ;
            $this->parse_images();
        }
    }

    private function parse_images()
    {
        if( $this->with_video )
        {
            $reg = '/<video.+?src="(.+?)"/is';
            if( preg_match_all( $reg , $this->page_content , $out ) )
            {
                $images = array_unique( $out[1] );
                echo "found " . count($images) . " video ... 🎬 ";  
                $this->images = array_merge( $this->images , $images );
                $this->image_save();
            }
        }
        
        
        
        $reg = '/<a.+?href="(.+?)".+?class=".*?photoset_photo.*?"/is';
        if( preg_match_all( $reg , $this->page_content , $out ) )
        {
            $images = array_unique( $out[1] );
            echo "found " . count($images) . " photos ... 🤠 ";  
            $this->images = array_merge( $this->images , $images );
            $this->image_save();
        }

        $reg = '/data-big-photo="(.+?)"/is';
        if( preg_match_all( $reg , $this->page_content , $out ) )
        {
            $images = array_unique( $out[1] );
            echo "found " . count($images) . " photos ... 😃 "; 
            $this->images = array_merge( $this->images , $images  );
            $this->image_save();
        }

        $reg = '/<a[\s]+href="(.+?)"\s+class=".*?photoset_photo.*?"/';
        if( preg_match_all( $reg , $this->page_content , $out ) )
        {
            $images = array_unique( $out[1] );
            echo "found " . count($images) . " photos ... 😃😃 "; 
            $this->images = array_merge( $this->images , $images  );
            $this->image_save();
        }

        $reg = '/<a\s*id="next_page_link"\s+?href="(.+?)"/is';
        if( preg_match( $reg , $this->page_content , $out ) )
        {
            $this->say("next page found ... 🔗 ");
            file_put_contents( 'next.txt' , 'https://www.tumblr.com' . $out[1] );
            if( strlen( $out[1] ) > 0 )
            {
                $this->parse_url( 'https://www.tumblr.com' . $out[1] );
            }
        }
        else
        {
            @unlink("next.txt");
        }

    }

    public function save( $url )
    {
        file_put_contents( 'save.html' , get_page( $url ) );
    }

    public function save2txt()
    {
        $images = @json_decode( file_get_contents( "images.json" ) , true );
        $ret = '';
        foreach( $images as $image )
        {
            $ret .= $image."\r\n";    
        }

        file_put_contents( "images.txt" , $ret );
    }

    public function download()
    {
        while( $this->_download() === true ){};
        echo "done";    
    }

    private function _download()
    {
        if( !file_exists("download.json") )
        {
            copy( "images.json" , "download.json" );
        }

        $images = @json_decode( file_get_contents( "download.json" ) , true );
        
        if( !is_array( $images ) || count( $images ) < 1 )
        {
            $this->say("no more photo to download , to restart download remove download.json and run robo download again");
            return false;
        }
        
        // 每次下载10张图，然后存盘
        $to_download = array_slice( $images , 0 , 10 );
        $to_do = array_slice( $images , 10 );
        @mkdir( "photos" );
        foreach( $to_download as $item )
        {
            $item = str_replace( 'https://' , 'http://' , $item );

            $new_name = strpos( basename( $item ) , "." ) === false ?  "./photos/" . uniqid() . '.mp4' : "./photos/" . basename( $item );
            file_put_contents( $new_name , get_image( $item ) );
            // break;
        }

        file_put_contents( "download.json" , json_encode( $to_do ) );

        
        $count = count( $to_do );
        if( $count > 0 )
        {
            $this->say("10 photos downloaded ... 😀  $count to download " );
            return true;
        }
        else
        {
            $this->say("Image download finished 🤠 ");
            return false;
        }
            
    }

}



function get_image( $url )
{
    $cmd = "curl '" . $url . "' -H 'Connection: keep-alive' -H 'Pragma: no-cache' -H 'Cache-Control: no-cache' -H 'Upgrade-Insecure-Requests: 1' -H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.110 Safari/537.36' -H 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8' -H 'Accept-Encoding: gzip, deflate' -H 'Accept-Language: en-US,en;q=0.9,zh-CN;q=0.8,zh;q=0.7,ja;q=0.6,zh-TW;q=0.5' -L";

    $data = shell_exec( $cmd );
    return $data;
}

function get_page( $url )
{

    $cmd = "curl '" . $url . "' -H 'authority: www.tumblr.com' -H 'pragma: no-cache' -H 'cache-control: no-cache' -H 'upgrade-insecure-requests: 1' -H 'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.110 Safari/537.36' -H 'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8' -H 'accept-language: en-US,en;q=0.9,zh-CN;q=0.8,zh;q=0.7,ja;q=0.6,zh-TW;q=0.5' -H 'cookie: " . file_get_contents( 'cookie.txt' ) . "'";

    $data = shell_exec( $cmd );

    return $data;

}