<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
class BusTimeTables extends Controller
{
    public function index(){
        $data['dbdata'] = DB::table('routesections AS rs')
            ->select('rs.private_code as route_section', 'rl.private_code as route_link', 'fsp.common_name as from_stop', 'tsp.common_name as to_stop')
            ->join('routelinks AS rl', 'rs.id', '=', 'rl.route_section_id')
            ->join('stoppoints AS fsp', 'fsp.id', '=', 'rl.from_stop_point_id')
            ->join('stoppoints AS tsp', 'tsp.id', '=', 'rl.to_stop_point_id')
            ->orderBy('rl.id')
            ->get();
        return view('index')->with($data);
    }
}
