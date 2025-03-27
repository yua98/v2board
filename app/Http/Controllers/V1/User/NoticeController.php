<?php

namespace App\Http\Controllers\V1\User;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    public function fetch(Request $request)
    {
        if ($request->has('id')) {
            $id = $request->input('id');
            $notice = Notice::where('id', $id)
                ->where('show', 1)
                ->first();
    
            if (!$notice) {
                return response([
                    'message' => 'Notice not found'
                ], 404);
            }
    
            return response([
                'data' => $notice
            ]);
        }
    
        $current = $request->input('current', 1);
        $pageSize = $request->input('pageSize', 5);
    
        $pageSize = min(max($pageSize, 1), 100);
    
        $model = Notice::orderBy('created_at', 'DESC')
            ->where('show', 1);
    
        $total = $model->count();
        $res = $model->forPage($current, $pageSize)->get();
        // 增加
        if (preg_match('/Electron|okhttp|Moetor/i', $_SERVER['HTTP_USER_AGENT'])) {
        foreach ($res as &$item) {
            $item->content = strip_tags($item->content);
        }
    }
        if (preg_match('/Moetor\/(1\.3\.\d+|4\.9\.2)/i', $_SERVER['HTTP_USER_AGENT'])) {
    foreach ($res as &$item) {
        if (!\Str::startsWith($item->content, '<')) {
            $item->content = str_replace("\n", "<br>", $item->content);
        }
    }
}
        
        // 判断浏览器如果是潮汐或者萌通，则去除掉公告里的html标签,修复Moetor1.3.x以及 4.9.2公告换行问题
    
        return response([
            'data' => $res,
            'total' => $total
        ]);
    }

}
