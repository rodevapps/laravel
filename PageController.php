<?php

namespace App\Http\Controllers;

use App;
use App\Page;
use App\News;
use App\Anonse;
use Illuminate\Http\Request;
use Sunra\PhpSimple\HtmlDomParser;
use Illuminate\Support\Facades\Route;

class PageController extends Controller
{
    public function generate(Request $request, $locale, $slug)
    {
        $this->preparePage($request, $locale);

        $page = Page::getPageBySlug($slug);

        if (!empty($page)) {
            if ($page->slug != $slug) {
                return redirect('/' . App::getLocale() . '/pages/' . $page->slug);
            } else if ($page->body != '') {
                Route::getFacadeRoot()->current()->name(str_replace('web.pages.generate', '', $page->route_name));

                $this->preparePage($request, $locale);

                $data = preg_replace('/\&apos\;/', '\'', html_entity_decode($page->body));

                $dom = HtmlDomParser::str_get_html($data);

                $css = '';
                $js = '';
                $php = '';

                if (is_object($dom->find('#stylesheets', 0))) {
                 $css = trim($dom->find('#stylesheets', 0)->innertext);
                 $dom->find('#stylesheets', 0)->outertext = '';
                }

                if (is_object($dom->find('#javascripts', 0))) {
                 $js = trim($dom->find('#javascripts', 0)->innertext);
                 $dom->find('#javascripts', 0)->outertext = '';
                }

                $d = [];

                if (is_object($dom->find('#php', 0))) {
                 $php = trim($dom->find('#php', 0)->innertext);
                 $dom->find('#php', 0)->outertext = '';

                 eval($php);
                }

                if (!empty($d))
                 return view('pages.generate', ['data' => $this->bladeCompile($dom->outertext, ['__env' => app(\Illuminate\View\Factory::class), 'd' => $d]), 'title' => $page->title, 'stylesheets' => $this->bladeCompile($css), 'javascripts' => $this->bladeCompile($js)]);
                else
                 return view('pages.generate', ['data' => $this->bladeCompile($dom->outertext), 'title' => $page->title, 'stylesheets' => $this->bladeCompile($css), 'javascripts' => $this->bladeCompile($js)]);
            } else if ($page->url != '') {
                return redirect($page->url);
            } else if ($page->slug == 'main-page' || $page->slug == 'strona-glowna') {
                return redirect($locale);
            } else {
                if (Route::has($page->route_name)) {
                    if ($page->route_name == 'web.seminars.show' || $page->route_name == 'web.seminars.search')
                        return redirect()->route('web.seminars.index2', ['locale' => $locale]);
                    else if ($page->route_name == 'web.seminars.index')
                        return redirect()->route('web.seminars.index', ['locale' => $locale, 'slug' => $page->slug]);
                    else if ($page->route_name == 'web.users.show' || $page->route_name == 'web.users.edit')
                        return redirect()->route('web.users.index', ['locale' => $locale]);
                    else if ($page->route_name == 'web.publications.searchbyauthor')
                        return redirect()->route('web.publications.index', ['locale' => $locale]);
                    else if ($page->route_name == 'web.news.show')
                        return redirect()->route('web.news.index', ['locale' => $locale]);
                    else if ($page->route_name == 'web.home.search')
                        return redirect()->route('web.home.search_index', ['locale' => $locale]);
                    else
                        return redirect()->route($page->route_name, ['locale' => $locale]);
                } else {
                    if (preg_match('/^web\.seminars\.index[3-9]/', $page->route_name) || preg_match('/^web\.seminars\.index[1-9][0-9]/', $page->route_name))
                        return redirect()->route('web.seminars.index', ['locale' => $locale, 'slug' => $page->slug]);
                    else
                        return redirect($locale)->with('messages.info', ['Page to render not created yet!']);
                }
            }
        } else {
            return redirect($locale)->with('messages.info', ['Page to render not found!']);
        }
    }

    public function doctorates(Request $request, $locale)
    {
        $this->preparePage($request, $locale);

        $doctorates = Anonse::getAnonseByType('doctorat');

        return view('pages.doctorates', ['doctorates' => $doctorates]);
    }

    public function public_procurements(Request $request, $locale)
    {
        $this->preparePage($request, $locale);

        $pp = Anonse::getAnonseByType('procurement');

        return view('pages.public_procurements', ['pp' => $pp]);
    }

    public function job_offers(Request $request, $locale)
    {
        $this->preparePage($request, $locale);

        $jo = Anonse::getAnonseByType('job');

        return view('pages.job_offers', ['jo' => $jo]);
    }

    public function media_about_us(Request $request, $locale)
    {
        $this->preparePage($request, $locale);

        $news = News::getNewsByTypeName('Media about us');

        return view('pages.media_about_us', ['news' => $news]);
    }

    public function photos(Request $request, $locale)
    {
        $this->preparePage($request, $locale);

        return view('pages.photos');
    }

    public function internships(Request $request, $locale)
    {
        $this->preparePage($request, $locale);

        $internships = News::getNewsByTypeName('Internships');

        return view('pages.internships', ['internships' => $internships]);
    }

    public function grants(Request $request, $locale)
    {
        $this->preparePage($request, $locale);

        $grants = App\Grant::getGrants($locale);

        return view('pages.grants', ['grants' => $grants]);
    }

    public function scientific_research(Request $request, $locale)
    {
        $this->preparePage($request, $locale);

        $research = News::getNewsByTypeName('Scientific projects');

        $grants = App\Grant::getGrants($locale);

        return view('pages.scientific_research', ['research' => $research, 'grants' => $grants, 'users' => App\User::getUsers3()]);
    }

    public function phd_schools(Request $request, $locale)
    {
        $this->preparePage($request, $locale);

        return view('pages.phd_schools');
    }

    public function site_map(Request $request, $locale)
    {
        $this->preparePage($request, $locale);

        $pages = Page::getSiteMap();

        return view('pages.site_map', ['site_map' => $pages]);
    }

    public function general_information(Request $request, $locale)
    {
        $this->preparePage($request, $locale);

        $users = App\User::getUsers();

        $nemployees = 0;
        $nproffessors = 0;

        foreach($users as $u) {
            if ($u->position->name == 'Profesor')
                $nproffessors++;

            if ($u->position->name != 'Administracja' && $u->position->name != 'Księgowość' && $u->position->name != 'Pracownik Techniczny')
                $nemployees++;
        }

        $research = News::getNewsByTypeName('Scientific projects');

        return view('pages.general_information', ['nemployee' => $nemployees, 'nproffessors' => $nproffessors, 'research' => $research, 'users' => App\User::getUsers3()]);
    }

    private function bladeCompile($value, array $args = array())
    {
        $generated = \Blade::compileString($value);

        ob_start() and extract($args, EXTR_SKIP);

        try
        {
            eval('?>' . $generated);
        } catch (\Exception $e) {
            ob_get_clean();
            throw $e;
        }

        $content = ob_get_clean();

        return $content;
    }
}
