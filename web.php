<?php

Route::group(['prefix' => '{locale}', 'where' => ['locale' => 'en|pl']], function ($locale) {
    Route::get('/', 'HomeController@index')->name('web.home.index');

    Route::group(['prefix' => 'publications'], function () {
        Route::get('/', 'PublicationController@index')->name('web.publications.index');
        Route::get('/orcid', 'PublicationController@index2')->name('web.publications.index2');
        Route::get('/{slug}', 'PublicationController@search_by_author')->where('slug', '^[a-z\-]+$')->name('web.publications.searchbyauthor');
    });

    Route::group(['prefix' => 'seminars'], function () {
        Route::get('/', 'SeminarController@index2')->name('web.seminars.index2');
        Route::get('/search/{name}', 'SeminarController@index2')->name('web.seminars.search');
        Route::get('/{slug}', 'SeminarController@index')->where('slug', '^[a-z\-]+$')->name('web.seminars.index');
        Route::get('/{year}/{month}/{day}/{slug}', 'SeminarController@show')->where('year', '^[1-2][0-9][0-9][0-9]$')->where('month', '^[0-1][0-9]$')->where('day', '^[0-3][0-9]$')->where('slug', '^[0-9a-z\-]+$')->name('web.seminars.show');
        Route::post('/{year}/{month}/{day}/{slug}', 'SeminarController@show')->where('year', '^[1-2][0-9][0-9][0-9]$')->where('month', '^[0-1][0-9]$')->where('day', '^[0-3][0-9]$')->where('slug', '^[0-9a-z\-]+$')->name('web.seminars.show');
    });

    Route::group(['prefix' => 'news'], function () {
        Route::get('/', 'NewsController@index')->name('web.news.index');
        Route::get('/search/{name}', 'NewsController@index')->name('web.news.search');
        Route::get('/{year}/{month}/{day}/{slug}', 'NewsController@show')->where('year', '^[1-2][0-9][0-9][0-9]$')->where('month', '^[0-1][0-9]$')->where('day', '^[0-3][0-9]$')->where('slug', '^[0-9a-z\-]+$')->name('web.news.show');
        Route::post('/{year}/{month}/{day}/{slug}', 'NewsController@show')->where('year', '^[1-2][0-9][0-9][0-9]$')->where('month', '^[0-1][0-9]$')->where('day', '^[0-3][0-9]$')->where('slug', '^[0-9a-z\-]+$')->name('web.news.show');
    });

    Route::group(['prefix' => 'users'], function () {
        Route::get('/', 'UserController@index')->name('web.users.index');

        Route::get('/sign-in', 'Auth\LoginController@sign_in')->name('web.users.login')->middleware('guest:users');
        Route::post('/sign-in', 'Auth\LoginController@sign_in')->name('web.users.login')->middleware('guest:users');

        Route::get('/sign-out', 'Auth\LoginController@sign_out')->name('web.users.logout')->middleware('auth:users');

        Route::get('/password-forgot', 'Auth\ForgotPasswordController@password_forgot')->name('web.passwords.forgot')->middleware('guest:users');
        Route::post('/password-forgot', 'Auth\ForgotPasswordController@password_forgot')->name('web.passwords.forgot')->middleware('guest:users');

        Route::get('/password-reset/{token?}', 'Auth\ResetPasswordController@password_reset')->where('token', '^[a-z0-9]*$')->name('web.passwords.reset')->middleware('guest:users');
        Route::post('/password-reset', 'Auth\ResetPasswordController@password_reset')->name('web.passwords.reset');

        Route::get('/sign-up', 'Auth\RegisterController@sign_up')->name('web.users.register')->middleware('auth:users');
        Route::post('/sign-up', 'Auth\RegisterController@sign_up')->name('web.users.register')->middleware('auth:users');

        Route::get('/{slug}', 'UserController@show')->where('slug', '^[a-z\-]+$')->name('web.users.show');
        Route::get('/{slug}/edit', 'UserController@edit')->where('slug', '^[a-z\-]+$')->name('web.users.edit')->middleware('auth:users');
        Route::post('/{slug}/edit-more', 'UserController@edit_more')->where('slug', '^[a-z\-]+$')->name('web.users.edit-more')->middleware('auth:users');
        Route::post('/{slug}/pbn-id', 'UserController@pbn_id')->where('slug', '^[a-z\-]+$')->name('web.users.pbn-id')->middleware('auth:users');
        Route::post('/{slug}/orcid-id', 'UserController@orcid_id')->where('slug', '^[a-z\-]+$')->name('web.users.orcid-id')->middleware('auth:users');

        Route::get('/sign-in/{service}', 'Auth\LoginController@redirectToProvider')->where('service', '^(google|facebook|twitter|github)$')->name('web.users.login_service')->middleware('guest:users');
        Route::get('/sign-in/{service}/callback', 'Auth\LoginController@handleProviderCallback')->where('service', '^(google|facebook|twitter|github)$')->name('web.users.login_service_callback')->middleware('guest:users');
    });

    Route::group(['prefix' => 'pages'], function () {
        Route::get('/doctorates', 'PageController@doctorates')->name('web.pages.doctorates');
        Route::get('/public-procurements', 'PageController@public_procurements')->name('web.pages.public_procurements');
        Route::get('/work-in-ctp-pas', 'PageController@job_offers')->name('web.pages.work_in_ctp_pas');
        Route::get('/general-information', 'PageController@general_information')->name('web.pages.general_information');
        Route::get('/scientific-research', 'PageController@scientific_research')->name('web.pages.scientific_research');
        Route::get('/internships-and-practices', 'PageController@internships')->name('web.pages.internships');
        Route::get('/grants', 'PageController@grants')->name('web.pages.grants');
        Route::get('/media-about-us', 'PageController@media_about_us')->name('web.pages.media_about_us');
        Route::get('/phd-schools', 'PageController@phd_schools')->name('web.pages.phd_schools');
        Route::get('/site-map', 'PageController@site_map')->name('web.pages.site_map');
        Route::get('/photos', 'PageController@photos')->name('web.pages.photos');

        Route::get('/contact-with-us', function ($locale) {
            return redirect('/' . $locale . '/contact');
        })->name('web.pages.contact_us');

        Route::get('/user-profile-edit', function ($locale) {
            return redirect('/' . $locale . '/users/' . Auth::guard('users')->slug . '/edit');
        })->name('web.users.edit2')->middleware('auth:users');

        Route::get('/employee-publications', function ($locale) {
            return redirect('/' . $locale . '/publications/' . Auth::guard('users')->slug);
        })->name('web.users.publications')->middleware('auth:users');

        Route::get('/{page}', 'PageController@generate')->where('page', '^[a-z0-9\-]+$')->name('web.pages.generate');
    });

    Route::get('/employees', function ($locale) {
        return redirect()->route('web.users.index', ['locale' => $locale]);
    })->name('web.users.index2');

    Route::get('/workers', function ($locale) {
        return redirect()->route('web.users.index', ['locale' => $locale]);
    })->name('web.users.index3');

    Route::get('/admin', function () {
        return redirect('/dashboard');
    })->name('web.admins.index_locale');

    Route::get('/dashboard', function () {
        return redirect('/dashboard');
    })->name('web.admins.index_locale2');

    Route::get('/search', 'HomeController@search_index')->name('web.home.search_index');
    Route::get('/search/{string}', 'HomeController@search')->name('web.home.search');

    Route::match(['get', 'post'], '/contact', [
        'uses' => 'HomeController@contact'
    ])->name('web.home.contact');
});

Route::get('/', 'HomeController@index2')->name('web.root_redirect');

Route::get('/links/{name}/display/{name2?}', 'LinkController@display')->where('name', '^[0-9a-zA-Z=]+$')->where('name2', '^[^\']*$')->name('web.links.display');
Route::delete('/links/{id}/delete', 'LinkController@deletel')->where('id', '^[1-9][0-9]*$')->name('web.links.delete');

Route::get('/files/{hash}/download', 'FileController@download')->where('hash', '^[0-9a-zA-Z]{25,100}$')->name('web.files.download');
Route::get('/files/{hash}/display/{name?}', 'FileController@display')->where('hash', '^[0-9a-zA-Z]{25,100}$')->where('name', '^[^\']*$')->name('web.files.display');
Route::delete('/files/{id}/delete', 'FileController@delete')->where('id', '^[1-9][0-9]*$')->name('web.files.delete');

Route::delete('/phones/{id}/delete', 'LinkController@deletep')->where('id', '^[1-9][0-9]*$')->name('web.phones.delete');
Route::delete('/emails/{id}/delete', 'LinkController@deletee')->where('id', '^[1-9][0-9]*$')->name('web.emails.delete');

Route::get('/admin', function () {
    return redirect('/dashboard');
})->name('web.admins.index');

Route::group(['prefix' => '{folder}', 'where' => ['folder' => 'js|css']], function ($folder) {
    Route::get('/{hash}/{file}', function ($folder, $hash, $file) {
        return response(file_get_contents(getcwd() . "/{$folder}/{$file}"), 200)->header('Content-Type', 'text/' . $folder);
        //return redirect("/{$folder}/{$file}");
    })->where('hash', '^[0-9a-zA-Z]{10}$')->name('web.assets');
});

Route::group(['prefix' => 'dashboard'], function () {
    Voyager::routes();

    Route::group(['prefix' => 'emails'], function () {
        Route::group(['prefix' => 'seminars'], function () {
            Route::get('{id}/edit', 'Admin\SeminarController@email')->where('id', '^[1-9][0-9]*$')->name('web.admins.emails.seminars.edit')->middleware('auth:admins');
            Route::post('{id}/send', 'Admin\SeminarController@email')->where('id', '^[1-9][0-9]*$')->name('web.admins.emails.seminars.send')->middleware('auth:admins');

            Route::get('/', function () {
                return redirect('/dashboard/seminars');
            })->name('web.admins.emails.seminars.index')->middleware('auth:admins');
        });

        Route::get('/', function () {
            return redirect('/dashboard/seminars');
        })->name('web.admins.emails.index')->middleware('auth:admins');
    });

    Route::put('/employee-properties/{id}', 'Admin\EmployeeController@properties')->where('id', '^[1-9][0-9]*$')->name('web.admins.employee.properties')->middleware('auth:admins');
    Route::put('/password-reset/{id}', 'Admin\EmployeeController@password')->where('id', '^[1-9][0-9]*$')->name('web.admins.employee.password')->middleware('auth:admins');
    Route::get('/password-reset2/{id}', 'Admin\EmployeeController@password2')->where('id', '^[1-9][0-9]*$')->name('web.admins.employee.password2')->middleware('auth:admins');
    Route::put('/employee-phones/{id}', 'Admin\EmployeeController@phones')->where('id', '^[1-9][0-9]*$')->name('web.admins.employee.phones')->middleware('auth:admins');
    Route::put('/employee-emails/{id}', 'Admin\EmployeeController@emails')->where('id', '^[1-9][0-9]*$')->name('web.admins.employee.emails')->middleware('auth:admins');
    Route::put('/employee-links/{id}', 'Admin\EmployeeController@links')->where('id', '^[1-9][0-9]*$')->name('web.admins.employee.links')->middleware('auth:admins');
    Route::put('/employee-files/{id}', 'Admin\EmployeeController@files')->where('id', '^[1-9][0-9]*$')->name('web.admins.employee.files')->middleware('auth:admins');
});

Route::group(['prefix' => 'api/v1'], function () {
    Route::post('/captcha/verify', 'Api\CaptchaController@verify')->name('api.v1.captcha.verify');
    Route::post('/profile/photo', 'Api\ProfileController@photo')->name('api.v1.profile.photo')->middleware('auth:users');

    Route::group(['prefix' => '{locale}', 'where' => ['locale' => 'en|pl']], function ($locale) {
        Route::post('/profile/interests', 'Api\ProfileController@interests')->name('api.v1.profile.interests')->middleware('auth:users');
        Route::post('/profile/research', 'Api\ProfileController@research')->name('api.v1.profile.research')->middleware('auth:users');
        Route::post('/profile/others', 'Api\ProfileController@others')->name('api.v1.profile.others')->middleware('auth:users');
        Route::get('/profile/seminars', 'Api\ProfileController@seminars')->name('api.v1.profile.seminars')->middleware('auth:users');
    });

    Route::group(['prefix' => 'dashboard'], function () {
        Route::get('/speakers/{column}', 'Api\Admin\SpeakerController@index')->where('column', '^(name|surname|affiliation)$')->name('api.v1.dashboard.speaker.index')->middleware('auth:admins');
        Route::get('/seminars/latest', 'Api\Admin\SeminarController@latest')->name('api.v1.dashboard.seminars.latest')->middleware('auth:admins');
        Route::get('/degrees', 'Api\Admin\DegreeController@index')->name('api.v1.dashboard.degree.index')->middleware('auth:admins');
        Route::get('/users', 'Api\Admin\UserController@index')->name('api.v1.dashboard.user.index')->middleware('auth:admins');

        Route::group(['prefix' => 'actions'], function () {
            Route::get('/{item}/visible/{id}', 'Api\Admin\AdminController@set_visible')->where('item', '^[a-z\_]+$')->where('id', '^[1-9][0-9]*$')->name('api.v1.dashboard.actions.visible')->middleware('auth:admins');
            Route::get('/{item}/hidden/{id}', 'Api\Admin\AdminController@set_hidden')->where('item', '^[a-z\_]+$')->where('id', '^[1-9][0-9]*$')->name('api.v1.dashboard.actions.hidden')->middleware('auth:admins');
        });
    });

    Route::get('/{name}', 'Api\ApiController@index')->name('api.v1.api.index');
});
