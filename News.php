<?php

namespace App;

use App;
use App\NewsType;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class News extends Model
{
    use SoftDeletes;

    public $url;
    protected $table = 'cft_news_news';

    public function tutor()
    {
        return $this->belongsTo('App\User', 'tutor_id');
    }

    public function tutor2()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    public function tutorId(){
        return $this->belongsTo(User::class);
    }

    public function anonseId(){
        return $this->belongsTo(News::class);
    }

    public function anonse(){
        return $this->belongsTo('App\Anonse');
    }

    public function language()
    {
        return $this->belongsTo('App\Language');
    }

    public function creator()
    {
        return $this->belongsTo('App\Admin\User', 'created_by');
    }

    public function updator()
    {
        return $this->belongsTo('App\Admin\User', 'updated_by');
    }

    public function deletor()
    {
        return $this->belongsTo('App\Admin\User', 'updated_by');
    }

    public function links()
    {
        return $this->hasMany('App\Link');
    }

    public function files()
    {
        return $this->hasMany('App\File');
    }

    public static function getNewsByTypeName($name) {
        $nt = self::getNewsType($name);

        $news = [];

        if (!empty($nt)) {
            if ($name != 'Scientific projects') {
                if (App::environment('local'))
                    Cache::forget('news.get_news_by_priority_name');

                $news = Cache::remember('news.get_news_by_priority_name', 3600, function () {
                    return self::with(['language', 'creator'])
                        ->where('visible', true)
                        ->orderBy('time_start', 'desc')
                        ->get();
                });
            } else {
                if (App::environment('local'))
                    Cache::forget('news.get_news_by_priority_name2');

                $news = Cache::remember('news.get_news_by_priority_name2', 3600, function () {
                    return self::with(['language', 'tutor'])
                        ->where('visible', true)
                        ->orderBy('id', 'asc')
                        ->get();
                });
            }
        }

        $news2 = [];
        $now = \Carbon\Carbon::now()->toDateTimeString();

        if (!empty($news)) {
            foreach($news as $n) {
                if ($name == 'Normal news' || $name == 'Media about us' || $name == 'Scientific projects' || $name == 'News in jobs' || $name == 'News in procurements' || $name == 'Doctorates' || $name == 'Internships' || $name == 'Contact persons') {
                    if ($n->language->code == App::getLocale() && ((int) $n->type & (int) $nt->value) == (int) $nt->value && $n->time_start <= $now) {
                        $news2[] = $n;
                    }
                } else {
                    if ($n->language->code == App::getLocale() && ((int) $n->type & (int) $nt->value) == (int) $nt->value && $n->time_start <= $now && $n->time_end >= $now) {
                        $news2[] = $n;
                    }
                }
            }
        }

        $news_all = $news;
        $news = $news2;

        if ($name != 'Normal news') {
            foreach($news as $n) {
                if ($n->anonse_id != 'NULL' && $n->anonse_id > 0) {
                    $n->url = self::getMainNewsLink($news_all, $n->anonse_id, __('messages.cft_content.news.more'));
                } else {
                    $ll = $n->links()->first();

                    if (is_object($ll) && $ll != null)
                        $n->url = $ll->link;
                }
            }
        }

        return $news;
    }

    private static function getMainNewsLink($news, $anonse_id, $title) {
        foreach($news as $n)
            if ($n->anonse_id == $anonse_id && $n->title == $title)
                return $n->links()->first()->link;
    }

    private static function getNewsType($type_name) {
        if (App::environment('local')) {
            Cache::forget('news_types');
        }

        $types = Cache::remember('news_types', 3600, function () {
            return NewsType::all();
        });

        foreach($types as $type)
            if ($type->name == $type_name)
                return $type;

        return '';
    }
}
