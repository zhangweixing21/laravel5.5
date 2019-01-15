<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Entities\CostaNews;
use App\Jobs\Queue;

class CeshiController extends Controller
{
    public function ceshi(){
        for($i=0; $i<100; $i++) {
            $data['title'] = 'asd';
            $data['author_id'] = $i;
            $data['content'] = 'aaaaaaaaaa';
            $data['description'] = 'ceshi';

            $this->dispatch(new Queue($data));
        }
        return response()->json(['code'=>0, 'msg'=>"success"]);
    }

}
